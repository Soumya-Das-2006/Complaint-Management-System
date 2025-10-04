<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

// Send notification to users
if(isset($_POST['send_notification'])) {
    $title = mysqli_real_escape_string($bd, $_POST['title']);
    $message = mysqli_real_escape_string($bd, $_POST['message']);
    $to_user = $_POST['to_user'];
    
    if($to_user == 'all') {
        // Send to all users
        $users = mysqli_query($bd, "SELECT id FROM users");
        while($user = mysqli_fetch_array($users)) {
            $sql = mysqli_query($bd, "INSERT INTO notifications (title, message, type, to_user) 
                                     VALUES ('$title', '$message', 'admin_to_user', '{$user['id']}')");
        }
        $_SESSION['msg'] = "Notification sent to all users!";
    } else {
        // Send to specific user
        $sql = mysqli_query($bd, "INSERT INTO notifications (title, message, type, to_user) 
                                 VALUES ('$title', '$message', 'admin_to_user', '$to_user')");
        $_SESSION['msg'] = "Notification sent to user!";
    }
}

// Send reply to user message
if(isset($_POST['send_reply'])) {
    $to_user = mysqli_real_escape_string($bd, $_POST['reply_to_user']);
    $subject = mysqli_real_escape_string($bd, $_POST['reply_subject']);
    $message = mysqli_real_escape_string($bd, $_POST['reply_message']);
    
    // Insert into user_messages as admin reply
    $sql = mysqli_query($bd, "INSERT INTO user_messages (user_id, admin_id, subject, message, direction) 
                             VALUES ('$to_user', 1, '$subject', '$message', 'admin_to_user')");
    
    if($sql) {
        $_SESSION['msg'] = "Reply sent successfully!";
    } else {
        $_SESSION['msg'] = "Error sending reply: " . mysqli_error($bd);
    }
}

// Mark notification as read
if(isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    mysqli_query($bd, "UPDATE notifications SET is_read=1 WHERE id='$id'");
    $_SESSION['msg'] = "Notification marked as read!";
}

// Mark message as read
if(isset($_GET['mark_message_read'])) {
    $id = intval($_GET['mark_message_read']);
    mysqli_query($bd, "UPDATE user_messages SET is_read=1 WHERE id='$id'");
    $_SESSION['msg'] = "Message marked as read!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Notifications</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .notification-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
        }
        .notification-unread { border-left-color: #dc3545; background: #fff5f5; }
        .notification-admin { border-left-color: #28a745; }
        .notification-user { border-left-color: #ffc107; }
        .notification-system { border-left-color: #6c757d; }
        .tab-content { padding: 20px 0; }
        .message-thread { max-height: 400px; overflow-y: auto; padding: 10px; }
    </style>
</head>
<body>
    <?php include('include/header.php');?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php');?>                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-bell"></i> Notification & Message Management</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php } ?>

                                <!-- Tabs -->
                                <ul class="nav nav-tabs" id="notificationTabs">
                                    <li class="active"><a href="#send" data-toggle="tab"><i class="icon-send"></i> Send Notification</a></li>
                                    <li><a href="#received" data-toggle="tab"><i class="icon-inbox"></i> User Messages</a></li>
                                    <li><a href="#sent" data-toggle="tab"><i class="icon-envelope"></i> Sent Notifications</a></li>
                                    <li><a href="#messaging" data-toggle="tab"><i class="icon-comments"></i> Direct Messaging</a></li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Send Notification Tab -->
                                    <div class="tab-pane active" id="send">
                                        <form class="form-horizontal" method="post">
                                            <div class="control-group">
                                                <label class="control-label">Send To</label>
                                                <div class="controls">
                                                    <select name="to_user" class="span6" required>
                                                        <option value="all">All Users</option>
                                                        <?php
                                                        $users = mysqli_query($bd, "SELECT id, fullName, userEmail FROM users");
                                                        while($user = mysqli_fetch_array($users)) {
                                                            
                                                            echo "<option value='{$user['id']}'>{$user['fullName']} ({$user['userEmail']})</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Title</label>
                                                <div class="controls">
                                                    <input type="text" style="width: 500px" name="title" class="span6" placeholder="Notification title" required>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Message</label>
                                                <div class="controls">
                                                    <textarea name="message" style="width: 500px" class="span6" rows="5" placeholder="Enter your message..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <button type="submit" name="send_notification" class="btn btn-primary">
                                                        <i class="icon-send"></i> Send Notification
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Received Messages Tab -->
                                    <div class="tab-pane" id="received">
                                        <h4>Messages from Users</h4>
                                        <?php
                                        $messages = mysqli_query($bd, "SELECT um.*, u.fullName, u.userEmail 
                                                                     FROM user_messages um 
                                                                     JOIN users u ON um.user_id = u.id 
                                                                     WHERE um.direction = 'user_to_admin' 
                                                                     ORDER BY um.created_at DESC");
                                        while($msg = mysqli_fetch_array($messages)) {
                                            $readClass = $msg['is_read'] ? '' : 'notification-unread';
                                        ?>
                                        <div class="notification-card notification-user <?php echo $readClass; ?>">
                                            <div class="row-fluid">
                                                <div class="span8">
                                                    <h5><strong>From: <?php echo htmlentities($msg['fullName']); ?> (<?php echo htmlentities($msg['userEmail']); ?>)</strong></h5>
                                                    <h6><?php echo htmlentities($msg['subject']); ?></h6>
                                                    <p><?php echo htmlentities($msg['message']); ?></p>
                                                    <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?></small>
                                                </div>
                                                <div class="span4 text-right">
                                                    <?php if(!$msg['is_read']) { ?>
                                                    <a href="manage-notifications.php?mark_message_read=<?php echo $msg['id']; ?>" class="btn btn-small btn-success">
                                                        <i class="icon-check"></i> Mark Read
                                                    </a>
                                                    <?php } else { ?>
                                                    <span class="label label-success">Read</span>
                                                    <?php } ?>
                                                    <br><br>
                                                    <button class="btn btn-small btn-primary reply-btn" 
                                                            data-userid="<?php echo $msg['user_id']; ?>"
                                                            data-username="<?php echo htmlentities($msg['fullName']); ?>"
                                                            data-subject="Re: <?php echo htmlentities($msg['subject']); ?>">
                                                        <i class="icon-reply"></i> Reply
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php if(mysqli_num_rows($messages) == 0) { ?>
                                        <div class="alert alert-info">
                                            <i class="icon-info-sign"></i> No messages from users yet.
                                        </div>
                                        <?php } ?>
                                    </div>

                                    <!-- Sent Notifications Tab -->
                                    <div class="tab-pane" id="sent">
                                        <h4>Notifications Sent by Admin</h4>
                                        <?php
                                        $sentNotifications = mysqli_query($bd, "SELECT n.*, u.fullName as to_user_name 
                                                                              FROM notifications n 
                                                                              LEFT JOIN users u ON n.to_user = u.id 
                                                                              WHERE n.type = 'admin_to_user' 
                                                                              ORDER BY n.created_at DESC");
                                        while($notification = mysqli_fetch_array($sentNotifications)) {
                                            $toUser = $notification['to_user_name'] ?: 'All Users';
                                        ?>
                                        <div class="notification-card notification-admin">
                                            <div class="row-fluid">
                                                <div class="span10">
                                                    <h5><strong><?php echo htmlentities($notification['title']); ?></strong></h5>
                                                    <p><?php echo htmlentities($notification['message']); ?></p>
                                                    <small class="text-muted">
                                                        To: <?php echo htmlentities($toUser); ?> | 
                                                        Sent: <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                                    </small>
                                                </div>
                                                <div class="span2 text-right">
                                                    <span class="label label-info">Sent</span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php if(mysqli_num_rows($sentNotifications) == 0) { ?>
                                        <div class="alert alert-info">
                                            <i class="icon-info-sign"></i> No notifications sent yet.
                                        </div>
                                        <?php } ?>
                                    </div>

                                    <!-- Direct Messaging Tab -->
                                    <div class="tab-pane" id="messaging">
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <h4>Send Direct Message</h4>
                                                <form class="form-horizontal" method="post">
                                                    <div class="control-group">
                                                        <label class="control-label">To User</label>
                                                        <div class="controls">
                                                            <select name="reply_to_user" id="reply_to_user" class="span10" required>
                                                                <option value="">Select User</option>
                                                                <?php
                                                                $users = mysqli_query($bd, "SELECT id, fullName, userEmail FROM users");
                                                                while($user = mysqli_fetch_array($users)) {
                                                                    echo "<option value='{$user['id']}'>{$user['fullName']} ({$user['userEmail']})</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Subject</label>
                                                        <div class="controls">
                                                            <input type="text" name="reply_subject" id="reply_subject" class="span10" placeholder="Message subject" required>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Message</label>
                                                        <div class="controls">
                                                            <textarea name="reply_message" id="reply_message" class="span10" rows="6" placeholder="Type your message..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="send_reply" class="btn btn-success">
                                                                <i class="icon-reply"></i> Send Message
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="span6">
                                                <h4>Message History</h4>
                                                <div class="message-thread">
                                                    <?php
                                                    $all_messages = mysqli_query($bd, "SELECT um.*, u.fullName, u.userEmail 
                                                                                     FROM user_messages um 
                                                                                     JOIN users u ON um.user_id = u.id 
                                                                                     ORDER BY um.created_at DESC 
                                                                                     LIMIT 10");
                                                    while($msg = mysqli_fetch_array($all_messages)) {
                                                        $direction = $msg['direction'];
                                                        $bg_class = $direction == 'user_to_admin' ? 'background: #fff3cd;' : 'background: #d1ecf1;';
                                                        $align_class = $direction == 'user_to_admin' ? '' : 'text-right';
                                                    ?>
                                                    <div class="notification-card" style="<?php echo $bg_class; ?> margin-bottom: 10px;">
                                                        <div class="<?php echo $align_class; ?>">
                                                            <strong>
                                                                <?php echo $direction == 'user_to_admin' ? 'From: ' . htmlentities($msg['fullName']) : 'To: ' . htmlentities($msg['fullName']); ?>
                                                            </strong>
                                                            <br>
                                                            <small><strong><?php echo htmlentities($msg['subject']); ?></strong></small>
                                                            <p style="margin: 5px 0;"><?php echo htmlentities($msg['message']); ?></p>
                                                            <small class="text-muted">
                                                                <?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('include/footer.php');?>
    
    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            // Activate tab from URL hash
            var hash = window.location.hash;
            if(hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
            }

            // Update URL when tab is clicked
            $('.nav-tabs a').on('shown', function(e) {
                window.location.hash = e.target.hash;
            });

            // Reply button functionality
            $('.reply-btn').click(function() {
                var userId = $(this).data('userid');
                var userName = $(this).data('username');
                var subject = $(this).data('subject');
                
                $('#reply_to_user').val(userId);
                $('#reply_subject').val(subject);
                $('#reply_message').focus();
                
                // Switch to messaging tab
                $('.nav-tabs a[href="#messaging"]').tab('show');
            });
        });
    </script>
</body>
</html>
<?php } ?>