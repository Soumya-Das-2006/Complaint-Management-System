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

// Mark message as read when viewing
if(isset($_GET['message_id'])) {
    $message_id = mysqli_real_escape_string($bd, $_GET['message_id']);
    
    // Mark message as read
    $update_sql = "UPDATE user_messages SET is_read = 1 WHERE id = '$message_id' AND user_id = '$user_id'";
    mysqli_query($bd, $update_sql);
}

// Handle message deletion
if(isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($bd, $_GET['delete_id']);
    
    $delete_sql = "DELETE FROM user_messages WHERE id = '$delete_id' AND user_id = '$user_id'";
    if(mysqli_query($bd, $delete_sql)) {
        $successmsg = "Message deleted successfully!";
    } else {
        $errormsg = "Error deleting message: " . mysqli_error($bd);
    }
}

// Handle bulk actions
if(isset($_POST['bulk_action'])) {
    if(isset($_POST['selected_messages']) && !empty($_POST['selected_messages'])) {
        $selected_messages = $_POST['selected_messages'];
        $message_ids = implode(",", $selected_messages);
        
        if($_POST['bulk_action'] == 'mark_read') {
            $bulk_sql = "UPDATE user_messages SET is_read = 1 WHERE id IN ($message_ids) AND user_id = '$user_id'";
            if(mysqli_query($bd, $bulk_sql)) {
                $successmsg = "Messages marked as read!";
            }
        } elseif($_POST['bulk_action'] == 'mark_unread') {
            $bulk_sql = "UPDATE user_messages SET is_read = 0 WHERE id IN ($message_ids) AND user_id = '$user_id'";
            if(mysqli_query($bd, $bulk_sql)) {
                $successmsg = "Messages marked as unread!";
            }
        } elseif($_POST['bulk_action'] == 'delete') {
            $bulk_sql = "DELETE FROM user_messages WHERE id IN ($message_ids) AND user_id = '$user_id'";
            if(mysqli_query($bd, $bulk_sql)) {
                $successmsg = "Messages deleted successfully!";
            }
        }
    } else {
        $errormsg = "Please select messages to perform this action!";
    }
}

// Get filter parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';

// Build query based on filters
$where_conditions = ["user_id = '$user_id'"];

if($filter == 'unread') {
    $where_conditions[] = "is_read = 0";
} elseif($filter == 'read') {
    $where_conditions[] = "is_read = 1";
}

if($type_filter == 'admin_to_user') {
    $where_conditions[] = "direction = 'admin_to_user'";
} elseif($type_filter == 'user_to_admin') {
    $where_conditions[] = "direction = 'user_to_admin'";
} elseif($type_filter == 'user_to_user') {
    $where_conditions[] = "direction = 'user_to_user'";
}

$where_clause = implode(" AND ", $where_conditions);

// Get messages count for stats
$all_count_query = mysqli_query($bd, "SELECT COUNT(*) as count FROM user_messages WHERE user_id = '$user_id'");
$all_count = mysqli_fetch_array($all_count_query)['count'];

$unread_count_query = mysqli_query($bd, "SELECT COUNT(*) as count FROM user_messages WHERE user_id = '$user_id' AND is_read = 0");
$unread_count = mysqli_fetch_array($unread_count_query)['count'];

$read_count_query = mysqli_query($bd, "SELECT COUNT(*) as count FROM user_messages WHERE user_id = '$user_id' AND is_read = 1");
$read_count = mysqli_fetch_array($read_count_query)['count'];

// Get messages with pagination
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$messages_query = mysqli_query($bd, "SELECT * FROM user_messages WHERE $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$total_messages_query = mysqli_query($bd, "SELECT COUNT(*) as total FROM user_messages WHERE $where_clause");
$total_messages = mysqli_fetch_array($total_messages_query)['total'];
$total_pages = ceil($total_messages / $limit);

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

// Get message type label
function get_message_type_label($direction) {
    switch($direction) {
        case 'admin_to_user':
            return '<span class="badge bg-primary">From Admin</span>';
        case 'user_to_admin':
            return '<span class="badge bg-success">To Admin</span>';
        case 'user_to_user':
            return '<span class="badge bg-info">User Message</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
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

    <title>Message Inbox | GUVNL Complaint System</title>

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

        .message-item {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .message-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .message-item.unread {
            border-left-color: #007bff;
            background-color: #f0f8ff;
        }

        .message-item.unread .message-subject {
            font-weight: 600;
        }

        .message-preview {
            color: #6c757d;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .stats-card {
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .badge-new {
            background: linear-gradient(45deg, #dc3545, #e74c3c);
            animation: blink 2s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .message-content {
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .attachment-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            margin: 5px 0;
            background: #f8f9fa;
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

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
        }

        .bulk-actions {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
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
                <h1 class="dashboard-title"><i class="fas fa-inbox me-2"></i>Message Inbox</h1>
                <p class="dashboard-subtitle">View and manage your messages</p>
            </div>
        </div>

        <div class="container-fluid">
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

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                            <h3><?php echo $all_count; ?></h3>
                            <p class="text-muted mb-0">Total Messages</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-envelope-open fa-2x text-success mb-2"></i>
                            <h3><?php echo $read_count; ?></h3>
                            <p class="text-muted mb-0">Read Messages</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-bell fa-2x text-warning mb-2"></i>
                            <h3><?php echo $unread_count; ?></h3>
                            <p class="text-muted mb-0">Unread Messages</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card shadow-sm border-0">
                        <div class="card-body text-center">
                            <a href="send_messages.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Compose
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Filters and Bulk Actions -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <form method="get" class="row g-2">
                                        <div class="col-auto">
                                            <select class="form-select" name="filter" onchange="this.form.submit()">
                                                <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Messages</option>
                                                <option value="unread" <?php echo $filter == 'unread' ? 'selected' : ''; ?>>Unread Only</option>
                                                <option value="read" <?php echo $filter == 'read' ? 'selected' : ''; ?>>Read Only</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <select class="form-select" name="type" onchange="this.form.submit()">
                                                <option value="all" <?php echo $type_filter == 'all' ? 'selected' : ''; ?>>All Types</option>
                                                <option value="admin_to_user" <?php echo $type_filter == 'admin_to_user' ? 'selected' : ''; ?>>From Admin</option>
                                                <option value="user_to_admin" <?php echo $type_filter == 'user_to_admin' ? 'selected' : ''; ?>>To Admin</option>
                                                <option value="user_to_user" <?php echo $type_filter == 'user_to_user' ? 'selected' : ''; ?>>User Messages</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="post" id="bulkForm">
                                        <div class="row g-2">
                                            <div class="col-auto">
                                                <select class="form-select" name="bulk_action" id="bulkAction">
                                                    <option value="">Bulk Actions</option>
                                                    <option value="mark_read">Mark as Read</option>
                                                    <option value="mark_unread">Mark as Unread</option>
                                                    <option value="delete">Delete</option>
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-outline-primary">Apply</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Messages
                                <?php if($total_messages > 0): ?>
                                    <span class="badge bg-light text-dark ms-2"><?php echo $total_messages; ?> messages</span>
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if(mysqli_num_rows($messages_query) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while($message = mysqli_fetch_array($messages_query)): ?>
                                        <div class="list-group-item message-item <?php echo $message['is_read'] == 0 ? 'unread' : ''; ?>">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <input type="checkbox" class="form-check-input message-checkbox" name="selected_messages[]" value="<?php echo $message['id']; ?>">
                                                </div>
                                                <div class="col-auto">
                                                    <?php if($message['is_read'] == 0): ?>
                                                        <i class="fas fa-envelope text-primary"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-envelope-open text-muted"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 message-subject">
                                                                <a href="inbox.php?message_id=<?php echo $message['id']; ?>" class="text-decoration-none">
                                                                    <?php echo htmlentities($message['subject']); ?>
                                                                </a>
                                                                <?php if($message['is_read'] == 0): ?>
                                                                    <span class="badge badge-new ms-2">NEW</span>
                                                                <?php endif; ?>
                                                            </h6>
                                                            <p class="mb-1 message-preview">
                                                                <?php echo substr(htmlentities($message['message']), 0, 100); ?>
                                                                <?php if(strlen($message['message']) > 100): ?>...<?php endif; ?>
                                                            </p>
                                                            <small class="text-muted">
                                                                <?php echo get_message_type_label($message['direction']); ?>
                                                                <span class="ms-2">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    <?php echo time_elapsed_string($message['created_at']); ?>
                                                                </span>
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="btn-group">
                                                                <a href="inbox.php?message_id=<?php echo $message['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="inbox.php?delete_id=<?php echo $message['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this message?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No messages found</h4>
                                    <p class="text-muted">You don't have any messages in your inbox.</p>
                                    <a href="send_messages.php" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Compose New Message
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if($total_pages > 1): ?>
                    <nav aria-label="Message pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="inbox.php?page=<?php echo $page - 1; ?>&filter=<?php echo $filter; ?>&type=<?php echo $type_filter; ?>">Previous</a>
                            </li>
                            
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="inbox.php?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>&type=<?php echo $type_filter; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="inbox.php?page=<?php echo $page + 1; ?>&filter=<?php echo $filter; ?>&type=<?php echo $type_filter; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>

                    <!-- Message Detail View -->
                    <?php if(isset($_GET['message_id'])): 
                        $message_id = mysqli_real_escape_string($bd, $_GET['message_id']);
                        $message_detail_query = mysqli_query($bd, "SELECT * FROM user_messages WHERE id = '$message_id' AND user_id = '$user_id'");
                        if(mysqli_num_rows($message_detail_query) > 0):
                            $message_detail = mysqli_fetch_array($message_detail_query);
                    ?>
                    <div class="modal fade show" id="messageModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?php echo htmlentities($message_detail['subject']); ?>
                                    </h5>
                                    <a href="inbox.php" class="btn-close btn-close-white"></a>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>From:</strong> 
                                            <?php echo $message_detail['direction'] == 'admin_to_user' ? 'Administrator' : 'You'; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>To:</strong> 
                                            <?php echo $message_detail['direction'] == 'user_to_admin' ? 'Administrator' : 'You'; ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Date:</strong> 
                                            <?php echo date('F j, Y g:i A', strtotime($message_detail['created_at'])); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Type:</strong> 
                                            <?php echo get_message_type_label($message_detail['direction']); ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="message-content">
                                        <?php echo nl2br(htmlentities($message_detail['message'])); ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="inbox.php?delete_id=<?php echo $message_detail['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a>
                                    <a href="inbox.php" class="btn btn-secondary">Close</a>
                                    <?php if($message_detail['direction'] == 'admin_to_user'): ?>
                                        <a href="send_messages.php" class="btn btn-primary">
                                            <i class="fas fa-reply me-2"></i>Reply
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; endif; ?>
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
    // Bulk actions functionality
    document.getElementById('bulkForm').addEventListener('submit', function(e) {
        const selectedMessages = document.querySelectorAll('.message-checkbox:checked');
        const bulkAction = document.getElementById('bulkAction').value;
        
        if(selectedMessages.length === 0) {
            e.preventDefault();
            alert('Please select at least one message to perform bulk action.');
            return;
        }
        
        if(!bulkAction) {
            e.preventDefault();
            alert('Please select a bulk action.');
            return;
        }
        
        if(bulkAction === 'delete') {
            if(!confirm('Are you sure you want to delete the selected messages?')) {
                e.preventDefault();
                return;
            }
        }
    });

    // Select all checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.createElement('input');
        selectAllCheckbox.type = 'checkbox';
        selectAllCheckbox.className = 'form-check-input';
        selectAllCheckbox.id = 'selectAll';
        
        const selectAllLabel = document.createElement('label');
        selectAllLabel.className = 'form-check-label ms-2';
        selectAllLabel.htmlFor = 'selectAll';
        selectAllLabel.textContent = 'Select All';
        
        const selectAllContainer = document.createElement('div');
        selectAllContainer.className = 'form-check';
        selectAllContainer.appendChild(selectAllCheckbox);
        selectAllContainer.appendChild(selectAllLabel);
        
        // Add select all to bulk actions area
        const bulkActionsDiv = document.querySelector('.bulk-actions .row .col-md-6');
        if(bulkActionsDiv) {
            bulkActionsDiv.prepend(selectAllContainer);
        }
        
        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.message-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    });

    // Auto-close modal on background click
    document.addEventListener('DOMContentLoaded', function() {
        const messageModal = document.getElementById('messageModal');
        if(messageModal) {
            messageModal.addEventListener('click', function(e) {
                if(e.target === this) {
                    window.location.href = 'inbox.php';
                }
            });
        }
    });

    // Keyboard navigation for modal
    document.addEventListener('keydown', function(e) {
        if(e.key === 'Escape') {
            window.location.href = 'inbox.php';
        }
    });
    </script>
</body>
</html>