<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['login'])==0) {	
    header('location:index.php');
    exit;
}

$user_id = $_SESSION['id'];

// Handle sending new message
if(isset($_POST['send_message'])) {
    $subject = mysqli_real_escape_string($bd, $_POST['subject']);
    $message = mysqli_real_escape_string($bd, $_POST['message']);
    
    $sql = "INSERT INTO user_messages (user_id, subject, message, direction, created_at) 
            VALUES ('$user_id', '$subject', '$message', 'user_to_admin', NOW())";
    
    if(mysqli_query($bd, $sql)) {
        $_SESSION['msg'] = "Message sent to admin successfully!";
    } else {
        $_SESSION['msg'] = "Error sending message: " . mysqli_error($bd);
    }
    
    header('location:user-messages.php');
    exit;
}

// Count unread messages from admin
$unread_query = mysqli_query($bd, "SELECT COUNT(*) as unread_count FROM user_messages WHERE user_id = '$user_id' AND direction = 'admin_to_user' AND is_read = 0");
$unread_data = mysqli_fetch_assoc($unread_query);
$unread_messages = $unread_data['unread_count'];

// Mark admin messages as read when page loads
if($unread_messages > 0) {
    mysqli_query($bd, "UPDATE user_messages SET is_read = 1 WHERE user_id = '$user_id' AND direction = 'admin_to_user' AND is_read = 0");
}

include('includes/header.php');
include('includes/sidebar.php');
?>

<div class="main-content" style="margin-left: 250px; margin-top: 50px; padding: 20px; background: #f4f6f9; min-height: 100vh;">
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title"><i class="fas fa-envelope me-2"></i>Messages</h1>
            <p class="dashboard-subtitle">Communicate with the administrator</p>
        </div>
    </div>

    <div class="container-fluid">
        <?php if(isset($_SESSION['msg'])) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlentities($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['msg']); } ?>

        <div class="row">
            <!-- Send Message Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Send Message to Admin</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="Enter message subject" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Message</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
                            </div>
                            <button type="submit" name="send_message" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title">Message Statistics</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1" id="unreadCount"><?php echo $unread_messages; ?></h4>
                                    <small class="text-muted">Unread</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">
                                    <?php 
                                    $total_sent = mysqli_query($bd, "SELECT COUNT(*) as count FROM user_messages WHERE user_id = '$user_id' AND direction = 'user_to_admin'");
                                    echo mysqli_fetch_assoc($total_sent)['count'];
                                    ?>
                                </h4>
                                <small class="text-muted">Sent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages History -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Message History</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light active" onclick="filterMessages('all')">All</button>
                            <button class="btn btn-sm btn-light" onclick="filterMessages('sent')">Sent</button>
                            <button class="btn btn-sm btn-light" onclick="filterMessages('received')">Received</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="messages-list" style="max-height: 500px; overflow-y: auto;">
                            <?php
                            // Get all messages for this user
                            $messages_query = mysqli_query($bd, "SELECT * FROM user_messages WHERE user_id = '$user_id' ORDER BY created_at DESC");
                            
                            if(mysqli_num_rows($messages_query) > 0) {
                                while($message = mysqli_fetch_assoc($messages_query)) {
                                    $is_sent = $message['direction'] == 'user_to_admin';
                                    $is_read = $message['is_read'];
                                    ?>
                                    <div class="message-item <?php echo $is_sent ? 'sent-message' : 'received-message'; ?>" 
                                         data-type="<?php echo $is_sent ? 'sent' : 'received'; ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">
                                                <?php if($is_sent): ?>
                                                    <i class="fas fa-share text-primary me-2"></i>
                                                    <strong>To: Admin</strong>
                                                <?php else: ?>
                                                    <i class="fas fa-reply text-success me-2"></i>
                                                    <strong>From: Admin</strong>
                                                    <?php if(!$is_read): ?>
                                                        <span class="badge bg-warning ms-2">New</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                            </small>
                                        </div>
                                        <h6 class="text-dark"><?php echo htmlentities($message['subject']); ?></h6>
                                        <p class="mb-2"><?php echo htmlentities($message['message']); ?></p>
                                        <div class="message-footer">
                                            <?php if($is_sent): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-check <?php echo $is_read ? 'text-success' : 'text-muted'; ?> me-1"></i>
                                                    <?php echo $is_read ? 'Read by admin' : 'Sent - waiting for response'; ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Admin response
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <hr>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="text-center py-4 text-muted">
                                    <i class="fas fa-envelope-open fa-3x mb-3"></i>
                                    <p>No messages yet. Start a conversation with the admin!</p>
                                </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterMessages(type) {
    const messages = document.querySelectorAll('.message-item');
    const buttons = document.querySelectorAll('.btn-group .btn');
    
    // Update active button
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    messages.forEach(message => {
        if(type === 'all') {
            message.style.display = 'block';
        } else {
            const messageType = message.getAttribute('data-type');
            message.style.display = messageType === type ? 'block' : 'none';
        }
    });
}

// Auto-refresh messages every 30 seconds
setInterval(function() {
    // You can implement AJAX refresh here if needed
    console.log('Auto-refresh messages...');
}, 30000);
</script>

<style>
.message-item {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.sent-message {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
}

.received-message {
    background: #f3e5f5;
    border-left: 4px solid #9c27b0;
}

.message-item:hover {
    transform: translateX(5px);
}

.messages-list::-webkit-scrollbar {
    width: 6px;
}

.messages-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}
</style>

<?php
include('includes/footer.php');
?>