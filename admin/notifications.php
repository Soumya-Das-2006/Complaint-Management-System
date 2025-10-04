<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
    $adminId = $_SESSION['id']; // Assuming admin ID is stored in session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Notifications</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .notification-item { 
            padding: 15px; 
            border-left: 4px solid #007bff; 
            background: white; 
            margin-bottom: 10px; 
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .notification-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .notification-unread { 
            border-left-color: #dc3545; 
            background: #fff5f5; 
            font-weight: 500;
        }
        .notification-read { 
            border-left-color: #28a745; 
            background: #f8f9fa;
            opacity: 0.8;
        }
        .notification-system { border-left-color: #ffc107; background: #fffbf0; }
        .notification-user { border-left-color: #17a2b8; background: #e3f2fd; }
        .notification-time { color: #6c757d; font-size: 12px; }
        .mark-all-read { cursor: pointer; }
        .notification-count {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }
        .user-badge {
            background: #6c757d;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 5px;
        }
        .type-badge {
            background: #17a2b8;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 5px;
        }
        .tab-content {
            margin-top: 20px;
        }
        .nav-tabs {
            margin-bottom: 0;
        }
        .stats-card {
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .stats-new { background: #fff3cd; border: 1px solid #ffeaa7; }
        .stats-process { background: #cce7ff; border: 1px solid #a8d6ff; }
        .stats-users { background: #d1f7d3; border: 1px solid #b4f2b6; }
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
                            <div class="module-head clearfix">
                                <h3>
                                    <i class="icon-bell"></i> Notifications 
                                    <?php 
                                    // Count unread notifications for admin
                                    $unreadCountQuery = mysqli_query($bd, "SELECT COUNT(*) as count FROM notifications WHERE (to_user = '$adminId' OR to_user IS NULL) AND is_read = 0");
                                    $unreadCount = mysqli_fetch_array($unreadCountQuery)['count'];
                                    if($unreadCount > 0) {
                                        echo '<span class="notification-count">'.$unreadCount.'</span>';
                                    }
                                    ?>
                                </h3>
                                <div class="pull-right">
                                    <?php if($unreadCount > 0) { ?>
                                    <a href="?action=markallread" class="btn btn-small btn-success">
                                        <i class="icon-check"></i> Mark All as Read
                                    </a>
                                    <?php } ?>
                                    <a href="?action=clearall" class="btn btn-small btn-danger" onclick="return confirm('Clear all your notifications?')">
                                        <i class="icon-trash"></i> Clear All
                                    </a>
                                </div>
                            </div>
                            <div class="module-body">
                                <?php
                                // Handle actions
                                if(isset($_GET['action'])) {
                                    $action = $_GET['action'];
                                    $id = isset($_GET['id']) ? $_GET['id'] : '';
                                    
                                    if($action == 'markread' && !empty($id)) {
                                        mysqli_query($bd, "UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE id = '$id' AND (to_user = '$adminId' OR to_user IS NULL)");
                                        echo '<div class="alert alert-success">Notification marked as read.</div>';
                                    }
                                    
                                    if($action == 'markallread') {
                                        mysqli_query($bd, "UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE (to_user = '$adminId' OR to_user IS NULL) AND is_read = 0");
                                        echo '<div class="alert alert-success">All notifications marked as read.</div>';
                                    }
                                    
                                    if($action == 'clearall') {
                                        mysqli_query($bd, "DELETE FROM notifications WHERE to_user = '$adminId'");
                                        echo '<div class="alert alert-success">All your notifications cleared.</div>';
                                    }
                                    
                                    if($action == 'delete' && !empty($id)) {
                                        mysqli_query($bd, "DELETE FROM notifications WHERE id = '$id' AND to_user = '$adminId'");
                                        echo '<div class="alert alert-success">Notification deleted.</div>';
                                    }
                                }

                                // Get system statistics for admin dashboard
                                $newComplaints = mysqli_query($bd, "SELECT COUNT(*) as count FROM tblcomplaints WHERE status IS NULL OR status = ''");
                                $newCount = mysqli_fetch_array($newComplaints)['count'];
                                
                                $processComplaints = mysqli_query($bd, "SELECT COUNT(*) as count FROM tblcomplaints WHERE status='in process'");
                                $processCount = mysqli_fetch_array($processComplaints)['count'];
                                
                                $newUsers = mysqli_query($bd, "SELECT COUNT(*) as count FROM users WHERE DATE(regDate) = CURDATE()");
                                $userCount = mysqli_fetch_array($newUsers)['count'];

                                // Get feedback statistics
                                $newFeedback = mysqli_query($bd, "SELECT COUNT(*) as count FROM tblfeedback WHERE DATE(submissionDate) = CURDATE()");
                                $feedbackCount = mysqli_fetch_array($newFeedback)['count'];
                                ?>

                                <!-- System Status Cards -->
                                <div class="row-fluid" style="margin-bottom: 20px;">
                                    <div class="span3">
                                        <div class="stats-card stats-new">
                                            <h4><?php echo $newCount; ?></h4>
                                            <p><strong>New Complaints</strong></p>
                                            <small>Waiting for action</small>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-process">
                                            <h4><?php echo $processCount; ?></h4>
                                            <p><strong>In Process</strong></p>
                                            <small>Being resolved</small>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-users">
                                            <h4><?php echo $userCount; ?></h4>
                                            <p><strong>New Users</strong></p>
                                            <small>Registered today</small>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card" style="background: #f8d7da; border: 1px solid #f5c6cb;">
                                            <h4><?php echo $feedbackCount; ?></h4>
                                            <p><strong>Today's Feedback</strong></p>
                                            <small>User responses</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabs for different notification types -->
                                <ul class="nav nav-tabs" id="notificationTabs">
                                    <li class="active"><a href="#all" data-toggle="tab">All Notifications</a></li>
                                    <li><a href="#unread" data-toggle="tab">Unread <span class="notification-count"><?php echo $unreadCount; ?></span></a></li>
                                    <li><a href="#system" data-toggle="tab">System</a></li>
                                    <li><a href="#users" data-toggle="tab">From Users</a></li>
                                </ul>

                                <div class="tab-content">
                                    <!-- All Notifications Tab -->
                                    <div class="tab-pane active" id="all">
                                        <?php
                                        // Get all notifications for this admin (both personal and broadcast)
                                        $notificationsQuery = mysqli_query($bd, "
                                            SELECT n.*, u1.fullName as from_user_name, u2.fullName as to_user_name 
                                            FROM notifications n 
                                            LEFT JOIN users u1 ON n.from_user = u1.id 
                                            LEFT JOIN users u2 ON n.to_user = u2.id 
                                            WHERE n.to_user = '$adminId' OR n.to_user IS NULL 
                                            ORDER BY n.is_read ASC, n.created_at DESC
                                        ");
                                        $totalNotifications = mysqli_num_rows($notificationsQuery);
                                        
                                        if($totalNotifications > 0) {
                                            while($notification = mysqli_fetch_array($notificationsQuery)) {
                                                $notificationClass = $notification['is_read'] ? 'notification-read' : 'notification-unread';
                                                $typeClass = $notification['from_user'] ? 'notification-user' : 'notification-system';
                                                $icon = $notification['from_user'] ? 'icon-user' : 'icon-bell';
                                        ?>
                                        <div class="notification-item <?php echo $notificationClass . ' ' . $typeClass; ?>" data-id="<?php echo $notification['id']; ?>">
                                            <div class="row-fluid">
                                                <div class="span10">
                                                    <h5>
                                                        <i class="<?php echo $icon; ?>"></i> 
                                                        <?php echo htmlentities($notification['title']); ?>
                                                        <?php if(!$notification['is_read']) { ?>
                                                        <span class="label label-important">New</span>
                                                        <?php } ?>
                                                        <span class="type-badge"><?php echo $notification['type']; ?></span>
                                                        <?php if($notification['from_user_name']) { ?>
                                                        <span class="user-badge">From: <?php echo $notification['from_user_name']; ?></span>
                                                        <?php } ?>
                                                    </h5>
                                                    <p><?php echo htmlentities($notification['message']); ?></p>
                                                </div>
                                                <div class="span2 text-right">
                                                    <span class="notification-time">
                                                        <?php echo date('M j, g:i A', strtotime($notification['created_at'])); ?>
                                                    </span>
                                                    <div class="btn-group" style="margin-top: 5px;">
                                                        <?php if(!$notification['is_read']) { ?>
                                                        <a href="?action=markread&id=<?php echo $notification['id']; ?>" class="btn btn-mini btn-success" title="Mark as Read">
                                                            <i class="icon-check"></i>
                                                        </a>
                                                        <?php } ?>
                                                        <?php if($notification['to_user'] == $adminId) { ?>
                                                        <a href="?action=delete&id=<?php echo $notification['id']; ?>" class="btn btn-mini btn-danger" 
                                                           onclick="return confirm('Delete this notification?')" title="Delete">
                                                            <i class="icon-trash"></i>
                                                        </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<div class="alert alert-info text-center"><i class="icon-info-sign"></i> No notifications found.</div>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Unread Notifications Tab -->
                                    <div class="tab-pane" id="unread">
                                        <?php
                                        $unreadQuery = mysqli_query($bd, "
                                            SELECT n.*, u1.fullName as from_user_name 
                                            FROM notifications n 
                                            LEFT JOIN users u1 ON n.from_user = u1.id 
                                            WHERE (n.to_user = '$adminId' OR n.to_user IS NULL) AND n.is_read = 0 
                                            ORDER BY n.created_at DESC
                                        ");
                                        $unreadNotifications = mysqli_num_rows($unreadQuery);
                                        
                                        if($unreadNotifications > 0) {
                                            while($notification = mysqli_fetch_array($unreadQuery)) {
                                                $typeClass = $notification['from_user'] ? 'notification-user' : 'notification-system';
                                                $icon = $notification['from_user'] ? 'icon-user' : 'icon-bell';
                                        ?>
                                        <div class="notification-item notification-unread <?php echo $typeClass; ?>" data-id="<?php echo $notification['id']; ?>">
                                            <div class="row-fluid">
                                                <div class="span10">
                                                    <h5>
                                                        <i class="<?php echo $icon; ?>"></i> 
                                                        <?php echo htmlentities($notification['title']); ?>
                                                        <span class="label label-important">New</span>
                                                        <span class="type-badge"><?php echo $notification['type']; ?></span>
                                                        <?php if($notification['from_user_name']) { ?>
                                                        <span class="user-badge">From: <?php echo $notification['from_user_name']; ?></span>
                                                        <?php } ?>
                                                    </h5>
                                                    <p><?php echo htmlentities($notification['message']); ?></p>
                                                </div>
                                                <div class="span2 text-right">
                                                    <span class="notification-time">
                                                        <?php echo date('M j, g:i A', strtotime($notification['created_at'])); ?>
                                                    </span>
                                                    <div class="btn-group" style="margin-top: 5px;">
                                                        <a href="?action=markread&id=<?php echo $notification['id']; ?>" class="btn btn-mini btn-success" title="Mark as Read">
                                                            <i class="icon-check"></i>
                                                        </a>
                                                        <?php if($notification['to_user'] == $adminId) { ?>
                                                        <a href="?action=delete&id=<?php echo $notification['id']; ?>" class="btn btn-mini btn-danger" 
                                                           onclick="return confirm('Delete this notification?')" title="Delete">
                                                            <i class="icon-trash"></i>
                                                        </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<div class="alert alert-success text-center"><i class="icon-ok"></i> All notifications are read.</div>';
                                        }
                                        ?>
                                    </div>

                                    <!-- System Notifications Tab -->
                                    <div class="tab-pane" id="system">
                                        <?php
                                        $systemQuery = mysqli_query($bd, "
                                            SELECT n.*, u2.fullName as to_user_name 
                                            FROM notifications n 
                                            LEFT JOIN users u2 ON n.to_user = u2.id 
                                            WHERE n.from_user IS NULL AND (n.to_user = '$adminId' OR n.to_user IS NULL)
                                            ORDER BY n.created_at DESC
                                        ");
                                        $systemNotifications = mysqli_num_rows($systemQuery);
                                        
                                        if($systemNotifications > 0) {
                                            while($notification = mysqli_fetch_array($systemQuery)) {
                                                $notificationClass = $notification['is_read'] ? 'notification-read' : 'notification-unread';
                                        ?>
                                        <div class="notification-item <?php echo $notificationClass; ?> notification-system" data-id="<?php echo $notification['id']; ?>">
                                            <div class="row-fluid">
                                                <div class="span10">
                                                    <h5>
                                                        <i class="icon-bell"></i> 
                                                        <?php echo htmlentities($notification['title']); ?>
                                                        <?php if(!$notification['is_read']) { ?>
                                                        <span class="label label-important">New</span>
                                                        <?php } ?>
                                                        <span class="type-badge">System</span>
                                                    </h5>
                                                    <p><?php echo htmlentities($notification['message']); ?></p>
                                                </div>
                                                <div class="span2 text-right">
                                                    <span class="notification-time">
                                                        <?php echo date('M j, g:i A', strtotime($notification['created_at'])); ?>
                                                    </span>
                                                    <div class="btn-group" style="margin-top: 5px;">
                                                        <?php if(!$notification['is_read']) { ?>
                                                        <a href="?action=markread&id=<?php echo $notification['id']; ?>" class="btn btn-mini btn-success" title="Mark as Read">
                                                            <i class="icon-check"></i>
                                                        </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<div class="alert alert-info text-center"><i class="icon-info-sign"></i> No system notifications.</div>';
                                        }
                                        ?>
                                    </div>

                                    <!-- User Notifications Tab -->
                                    <div class="tab-pane" id="users">
                                        <?php
                                        $userQuery = mysqli_query($bd, "
                                            SELECT n.*, u1.fullName as from_user_name 
                                            FROM notifications n 
                                            JOIN users u1 ON n.from_user = u1.id 
                                            WHERE n.to_user IS NULL OR n.to_user = '$adminId'
                                            ORDER BY n.created_at DESC
                                        ");
                                        $userNotifications = mysqli_num_rows($userQuery);
                                        
                                        if($userNotifications > 0) {
                                            while($notification = mysqli_fetch_array($userQuery)) {
                                                $notificationClass = $notification['is_read'] ? 'notification-read' : 'notification-unread';
                                        ?>
                                        <div class="notification-item <?php echo $notificationClass; ?> notification-user" data-id="<?php echo $notification['id']; ?>">
                                            <div class="row-fluid">
                                                <div class="span10">
                                                    <h5>
                                                        <i class="icon-user"></i> 
                                                        <?php echo htmlentities($notification['title']); ?>
                                                        <?php if(!$notification['is_read']) { ?>
                                                        <span class="label label-important">New</span>
                                                        <?php } ?>
                                                        <span class="user-badge">From: <?php echo $notification['from_user_name']; ?></span>
                                                    </h5>
                                                    <p><?php echo htmlentities($notification['message']); ?></p>
                                                </div>
                                                <div class="span2 text-right">
                                                    <span class="notification-time">
                                                        <?php echo date('M j, g:i A', strtotime($notification['created_at'])); ?>
                                                    </span>
                                                    <div class="btn-group" style="margin-top: 5px;">
                                                        <?php if(!$notification['is_read']) { ?>
                                                        <a href="?action=markread&id=<?php echo $notification['id']; ?>" class="btn btn-mini btn-success" title="Mark as Read">
                                                            <i class="icon-check"></i>
                                                        </a>
                                                        <?php } ?>
                                                        <?php if($notification['to_user'] == $adminId) { ?>
                                                        <a href="?action=delete&id=<?php echo $notification['id']; ?>" class="btn btn-mini btn-danger" 
                                                           onclick="return confirm('Delete this notification?')" title="Delete">
                                                            <i class="icon-trash"></i>
                                                        </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<div class="alert alert-info text-center"><i class="icon-info-sign"></i> No notifications from users.</div>';
                                        }
                                        ?>
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
            // Mark individual notification as read on click
            $('.notification-item').click(function(e) {
                // Don't trigger if clicking on action buttons
                if (!$(e.target).closest('.btn-group').length && !$(e.target).is('a')) {
                    var notificationId = $(this).data('id');
                    var isUnread = $(this).hasClass('notification-unread');
                    
                    if (notificationId && isUnread) {
                        window.location.href = '?action=markread&id=' + notificationId;
                    }
                }
            });
            
            // Tab functionality
            $('#notificationTabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
</body>
</html>
<?php } ?>