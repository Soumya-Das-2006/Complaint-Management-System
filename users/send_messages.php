<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0) { 
    header('location:index.php');
    exit;
}
// Get current user ID
$user_id = $_SESSION['id'];

// Handle form submission
if(isset($_POST['send_message'])) {
    $to_user = mysqli_real_escape_string($bd, $_POST['to_user']);
    $title = mysqli_real_escape_string($bd, $_POST['title']);
    $message = mysqli_real_escape_string($bd, $_POST['message']);
    $message_type = mysqli_real_escape_string($bd, $_POST['message_type']);
    
    if(!empty($to_user) && !empty($title) && !empty($message)) {
        // Determine direction based on recipient
        if($to_user == 'admin') {
            // Message to admin
            $direction = 'user_to_admin';
            $admin_id = 'NULL'; // Set as string for SQL
            $user_id_value = $user_id;
        } else {
            // Message to another user - adjust this based on your actual table structure
            $direction = 'user_to_user';
            $admin_id = $to_user; // In your table structure, admin_id seems to be used for recipient
            $user_id_value = $user_id;
        }

        // Use the correct column names from your table structure
        $sql = "INSERT INTO user_messages (user_id, admin_id, subject, message, direction, is_read, created_at) 
                VALUES ('$user_id_value', $admin_id, '$title', '$message', '$direction', 0, NOW())";
        
        if(mysqli_query($bd, $sql)) {
            $successmsg = "Message sent successfully!";
        } else {
            $errormsg = "Error sending message: " . mysqli_error($bd);
        }
    } else {
        $errormsg = "Please fill in all required fields!";
    }
}

// Check for new unread messages from admin
$new_messages_query = mysqli_query($bd, "SELECT COUNT(*) as new_count FROM user_messages WHERE user_id = '$user_id' AND direction = 'admin_to_user' AND is_read = 0");
$new_messages_data = mysqli_fetch_array($new_messages_query);
$new_message_count = $new_messages_data['new_count'];

// Get new messages from admin for display
$new_messages_list = mysqli_query($bd, "SELECT * FROM user_messages WHERE user_id = '$user_id' AND direction = 'admin_to_user' AND is_read = 0 ORDER BY created_at DESC LIMIT 5");

// Let's first check what columns exist in users table
$check_columns = mysqli_query($bd, "SHOW COLUMNS FROM users");
$user_columns = array();
while($column = mysqli_fetch_array($check_columns)) {
    $user_columns[] = $column['Field'];
}

// Debug: Show available columns (you can remove this after testing)
echo "<!-- Available columns in users table: " . implode(', ', $user_columns) . " -->";

// Build query based on available columns
$select_columns = array('id');
if(in_array('fullName', $user_columns)) {
    $select_columns[] = 'fullName';
} elseif(in_array('username', $user_columns)) {
    $select_columns[] = 'username';
} elseif(in_array('name', $user_columns)) {
    $select_columns[] = 'name';
}

if(in_array('email', $user_columns)) {
    $select_columns[] = 'email';
}

$column_string = implode(', ', $select_columns);
$users_query = mysqli_query($bd, "SELECT $column_string FROM users WHERE id != '$user_id' ORDER BY id");

// Function to show time elapsed
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GUVNL Complaint Management System">
    <meta name="author" content="Gujarat Urja Vikas Nigam Limited">
    <meta name="keyword" content="GUVNL, Complaint, Management, System">

    <title>Send Message | GUVNL Complaint System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --info: #1abc9c;
            --guvnl-primary: #1a5f7a;
            --guvnl-secondary: #2c7873;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--guvnl-primary), var(--guvnl-secondary));
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
        }
        
        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }
        }
        
        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .dashboard-subtitle {
            color: #bdc3c7;
            margin-bottom: 0;
        }
        
        .card {
            border-radius: 15px;
            border: none;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
        }

        .btn {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .preview-container {
            border-radius: 10px;
            min-height: 150px;
            background: white;
        }

        .tip-icon {
            width: 20px;
            text-align: center;
        }

        .recent-message {
            transition: transform 0.2s ease;
        }

        .recent-message:hover {
            transform: translateX(5px);
        }

        .tip-item {
            transition: transform 0.2s ease;
        }

        .tip-item:hover {
            transform: translateX(5px);
        }

        .preview-message {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .new-message-alert {
            animation: pulse 2s infinite;
            border-left: 4px solid #28a745 !important;
        }

        .new-message-item {
            border-left: 4px solid #007bff;
            background-color: #f0f8ff;
            transition: all 0.3s ease;
        }

        .new-message-item:hover {
            background-color: #e3f2fd;
            transform: translateX(5px);
        }

        .badge-new {
            background: linear-gradient(45deg, #dc3545, #e74c3c);
            animation: blink 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .message-preview {
            color: #6c757d;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
            }
            
            .card-body {
                padding: 20px !important;
            }
        }

        /* Animation for alerts */
        .alert {
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
  <?php include('includes/header.php'); ?>
    <!-- Sidebar -->
  <?php include('includes/sidebar.php'); ?>
    <!-- Main Content -->
    <div class="main-content" style="margin-left: 250px; margin-top: 50px; padding: 20px; background: #f4f6f9; min-height: 100vh;">
        <div class="dashboard-header">
            <div class="container-fluid">
                <h1 class="dashboard-title"><i class="fas fa-paper-plane me-2"></i>Send Message</h1>
                <p class="dashboard-subtitle">Communicate with other users and administrators</p>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Alert Messages -->
                    <?php if(isset($successmsg)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $successmsg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($errormsg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $errormsg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Message Form -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit me-2"></i>Compose New Message
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" id="messageForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="to_user" class="form-label fw-bold">
                                                <i class="fas fa-user me-1"></i>Recipient <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="to_user" name="to_user"  required>
                                                <option value="admin">Administrator</option>
                                            </select>
                                            <div class="form-text">Select a user or choose "Administrator" for admin messages</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="message_type" class="form-label fw-bold">
                                                <i class="fas fa-tag me-1"></i>Message Type
                                            </label>
                                            <select class="form-select" id="message_type" name="message_type">
                                                <option value="user_message">Personal Message</option>
                                                <option value="complaint_related">Complaint Related</option>
                                                <option value="general_query">General Query</option>
                                                <option value="feedback">Feedback</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label fw-bold">
                                        <i class="fas fa-heading me-1"></i>Subject <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           placeholder="Enter message subject..." required
                                           oninput="updateCharCount('title', 'titleCharCount', 100)">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <div class="form-text">Brief and descriptive subject line</div>
                                        <small class="text-muted">
                                            <span id="titleCharCount">0</span>/100 characters
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label fw-bold">
                                        <i class="fas fa-comment-dots me-1"></i>Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="message" name="message" rows="8" 
                                              placeholder="Type your message here..." required
                                              oninput="updateCharCount('message', 'messageCharCount', 1000)"></textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <div class="form-text">Be clear and concise in your communication</div>
                                        <small class="text-muted">
                                            <span id="messageCharCount">0</span>/1000 characters
                                        </small>
                                    </div>
                                </div>

                                <!-- Message Preview -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-eye me-1"></i>Message Preview
                                        </label>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="togglePreview()">
                                            <i class="fas fa-sync me-1"></i>Refresh Preview
                                        </button>
                                    </div>
                                    <div class="preview-container border rounded p-3 bg-light" id="messagePreview">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-envelope-open fa-2x mb-2"></i>
                                            <p>Your message preview will appear here</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                            <i class="fas fa-eraser me-1"></i>Clear Form
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-info me-2" onclick="saveDraft()">
                                            <i class="fas fa-save me-1"></i>Save Draft
                                        </button>
                                        <button type="submit" name="send_message" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-1"></i>Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- New Message Notification -->
                    <?php if($new_message_count > 0): ?>
                    <div class="card shadow-sm border-0 mb-4 new-message-alert">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-bell me-2"></i>New Message Alert
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-envelope fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">You have <?php echo $new_message_count; ?> new message(s)!</h5>
                                    <p class="mb-0 text-muted">From Administrator</p>
                                    <a href="inbox.php" class="btn btn-sm btn-success mt-2">
                                        <i class="fas fa-inbox me-1"></i>View All Messages
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- New Messages Section -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-star me-2"></i>New Messages from Admin
                                <?php if($new_message_count > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo $new_message_count; ?> new</span>
                                <?php endif; ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if(mysqli_num_rows($new_messages_list) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while($new_msg = mysqli_fetch_array($new_messages_list)): ?>
                                        <div class="list-group-item new-message-item p-3 mb-2">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong class="d-block text-primary">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        <?php echo htmlentities($new_msg['subject']); ?>
                                                    </strong>
                                                    <span class="badge badge-new">NEW</span>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo time_elapsed_string($new_msg['created_at']); ?>
                                                </small>
                                            </div>
                                            <p class="message-preview mb-2">
                                                <?php echo substr(htmlentities($new_msg['message']), 0, 80); ?>
                                                <?php if(strlen($new_msg['message']) > 80): ?>...<?php endif; ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-user-tie me-1"></i>From Administrator
                                                </small>
                                                <div>
                                                    <a href="inbox.php?message_id=<?php echo $new_msg['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="send_messages.php?reply_to=<?php echo $new_msg['id']; ?>" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-reply me-1"></i>Reply
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <?php if($new_message_count > 5): ?>
                                    <div class="text-center mt-3">
                                        <a href="inbox.php?filter=unread&type=admin_to_user" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-list me-1"></i>View All New Messages
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No new messages from admin</p>
                                    <small class="text-muted">You're all caught up!</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-lightbulb me-2"></i>Message Tips
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="tips-list">
                                <div class="tip-item d-flex mb-3">
                                    <div class="tip-icon me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <strong>Be Specific</strong>
                                        <p class="mb-0 text-muted small">Include relevant complaint numbers or reference IDs</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex mb-3">
                                    <div class="tip-icon me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <strong>Clear Subject</strong>
                                        <p class="mb-0 text-muted small">Use descriptive subject lines for quick understanding</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex mb-3">
                                    <div class="tip-icon me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <strong>Professional Tone</strong>
                                        <p class="mb-0 text-muted small">Maintain a respectful and professional communication style</p>
                                    </div>
                                </div>
                                <div class="tip-item d-flex">
                                    <div class="tip-icon me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <strong>Response Time</strong>
                                        <p class="mb-0 text-muted small">Typically 24-48 hours for responses</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>Recent Messages
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php
                            // Fetch recent messages sent by current user - using correct column names
                            $recent_messages = mysqli_query($bd, "SELECT * FROM user_messages WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 4");
                            
                            if(mysqli_num_rows($recent_messages) > 0) {
                                while($msg = mysqli_fetch_array($recent_messages)) {
                                    $recipient = ($msg['direction'] == 'user_to_admin') ? 'Administrator' : 'User';
                                    $time_ago = time_elapsed_string($msg['created_at']);
                                    $subject = htmlentities($msg['subject']);
                                    $message_preview = substr(htmlentities($msg['message']), 0, 50);
                                    if(strlen($msg['message']) > 50) {
                                        $message_preview .= '...';
                                    }
                                    
                                    echo '
                                    <div class="recent-message mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="d-block">To: ' . $recipient . '</strong>
                                                <small class="text-muted">Subject: ' . $subject . '</small>
                                            </div>
                                            <small class="text-muted">' . $time_ago . '</small>
                                        </div>
                                        <p class="mb-0 small text-muted mt-1">' . $message_preview . '</p>
                                    </div>';
                                }
                            } else {
                                echo '<p class="text-muted text-center mb-0">No recent messages</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <?php include('includes/footer.php'); ?>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Character counter
    function updateCharCount(fieldId, counterId, maxLength) {
        const field = document.getElementById(fieldId);
        const counter = document.getElementById(counterId);
        const currentLength = field.value.length;
        
        counter.textContent = currentLength;
        
        if (currentLength > maxLength * 0.8) {
            counter.className = 'text-warning';
        } else {
            counter.className = 'text-muted';
        }
        
        if (currentLength > maxLength) {
            counter.className = 'text-danger';
            field.value = field.value.substring(0, maxLength);
            counter.textContent = maxLength;
        }
        
        // Update preview if needed
        if (fieldId === 'title' || fieldId === 'message') {
            updatePreview();
        }
    }

    // Message preview
    function updatePreview() {
        const title = document.getElementById('title').value;
        const message = document.getElementById('message').value;
        const to_user = document.getElementById('to_user');
        const recipient = to_user.options[to_user.selectedIndex].text;
        const preview = document.getElementById('messagePreview');
        
        if (title || message) {
            let previewHTML = `
                <div class="preview-message">
                    <div class="preview-header border-bottom pb-2 mb-3">
                        <h6 class="mb-1"><strong>Subject:</strong> ${title || 'No subject'}</h6>
                        <small class="text-muted"><strong>To:</strong> ${recipient}</small><br>
                        <small class="text-muted"><strong>From:</strong> You</small>
                    </div>
                    <div class="preview-body">
                        <p class="mb-0">${message || 'No message content'}</p>
                    </div>
                    <div class="preview-footer border-top pt-2 mt-3">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>Sent: Just now
                        </small>
                    </div>
                </div>
            `;
            preview.innerHTML = previewHTML;
        }
    }

    function togglePreview() {
        updatePreview();
    }

    // Clear form
    function clearForm() {
        if (confirm('Are you sure you want to clear the form? All unsaved changes will be lost.')) {
            document.getElementById('messageForm').reset();
            document.getElementById('titleCharCount').textContent = '0';
            document.getElementById('messageCharCount').textContent = '0';
            document.getElementById('messagePreview').innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-envelope-open fa-2x mb-2"></i>
                    <p>Your message preview will appear here</p>
                </div>
            `;
        }
    }

    // Save draft (local storage)
    function saveDraft() {
        const formData = {
            to_user: document.getElementById('to_user').value,
            message_type: document.getElementById('message_type').value,
            title: document.getElementById('title').value,
            message: document.getElementById('message').value
        };
        
        localStorage.setItem('messageDraft', JSON.stringify(formData));
        
        // Show success notification
        showNotification('Draft saved successfully!', 'success');
    }

    // Load draft if exists
    document.addEventListener('DOMContentLoaded', function() {
        const draft = localStorage.getItem('messageDraft');
        if (draft) {
            if (confirm('You have a saved draft. Would you like to load it?')) {
                const formData = JSON.parse(draft);
                document.getElementById('to_user').value = formData.to_user;
                document.getElementById('message_type').value = formData.message_type;
                document.getElementById('title').value = formData.title;
                document.getElementById('message').value = formData.message;
                
                // Update character counts
                updateCharCount('title', 'titleCharCount', 100);
                updateCharCount('message', 'messageCharCount', 1000);
                updatePreview();
            }
        }
        
        // Initialize character counts
        updateCharCount('title', 'titleCharCount', 100);
        updateCharCount('message', 'messageCharCount', 1000);
    });

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }

    // Form validation
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const message = document.getElementById('message').value.trim();
        const to_user = document.getElementById('to_user').value;
        
        if (!to_user) {
            e.preventDefault();
            showNotification('Please select a recipient!', 'danger');
            document.getElementById('to_user').focus();
            return;
        }
        
        if (!title) {
            e.preventDefault();
            showNotification('Please enter a subject!', 'danger');
            document.getElementById('title').focus();
            return;
        }
        
        if (!message) {
            e.preventDefault();
            showNotification('Please enter your message!', 'danger');
            document.getElementById('message').focus();
            return;
        }
        
        // Clear draft on successful send
        localStorage.removeItem('messageDraft');
    });

    // Update preview when recipient changes
    document.getElementById('to_user').addEventListener('change', updatePreview);

    // Auto-refresh new messages every 30 seconds
    setInterval(function() {
        // Simple page refresh to check for new messages
        // You can implement AJAX here for a better user experience
        location.reload();
    }, 30000);
    </script>
</body>
</html>