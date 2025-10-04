<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['login'])==0) {	
    header('location:index.php');
    exit;
}

include('includes/header.php');
include('includes/sidebar.php');

// Get current user ID
$user_id = $_SESSION['id'];

// Count unread notifications FIRST (before marking as read)
$unread_query = mysqli_query($bd, "SELECT COUNT(*) as unread_count FROM notifications WHERE (to_user = '$user_id' OR to_user IS NULL) AND is_read = 0");
$unread_data = mysqli_fetch_assoc($unread_query);
$unread_notifications = $unread_data['unread_count'];

// Count total notifications
$total_query = mysqli_query($bd, "SELECT COUNT(*) as total_count FROM notifications WHERE to_user = '$user_id' OR to_user IS NULL");
$total_data = mysqli_fetch_assoc($total_query);
$total_notifications = $total_data['total_count'];

// Count today's notifications
$today_query = mysqli_query($bd, "SELECT COUNT(*) as today_count FROM notifications WHERE (to_user = '$user_id' OR to_user IS NULL) AND DATE(created_at) = CURDATE()");
$today_data = mysqli_fetch_assoc($today_query);
$today_notifications = $today_data['today_count'];

// Count admin notifications
$admin_query = mysqli_query($bd, "SELECT COUNT(*) as admin_count FROM notifications WHERE (to_user = '$user_id' OR to_user IS NULL) AND from_user IS NULL");
$admin_data = mysqli_fetch_assoc($admin_query);
$admin_notifications = $admin_data['admin_count'];

// Mark notifications as read when page loads (AFTER counting)
if($total_notifications > 0) {
    mysqli_query($bd, "UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE (to_user = '$user_id' OR to_user IS NULL) AND is_read = 0");
}

// Fetch notifications for display
$query = mysqli_query($bd, "SELECT * FROM notifications WHERE to_user = '$user_id' OR to_user IS NULL ORDER BY created_at DESC");
?>

<div class="main-content" style="margin-left: 250px; margin-top: 3px; padding: 20px; background: #f4f6f9; min-height: 100vh;">
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title"><i class="fas fa-bell me-2"></i>Notifications</h1>
            <p class="dashboard-subtitle">Stay updated with important alerts and announcements</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-total">
                    <div class="stat-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_notifications; ?></div>
                    <div class="stat-label">Total Notifications</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card stat-pending">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-number" id="unreadCount"><?php echo $unread_notifications; ?></div>
                    <div class="stat-label">Unread</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card stat-process">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-number"><?php echo $admin_notifications; ?></div>
                    <div class="stat-label">From Admin</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card stat-closed">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-number"><?php echo $today_notifications; ?></div>
                    <div class="stat-label">Today</div>
                </div>
            </div>
        </div>

        <div class="recent-complaints">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-list-ol me-2"></i>Your Notifications</h4>
                <div>
                    <?php if($unread_notifications > 0): ?>
                    <button class="btn btn-warning me-2" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-1"></i>Mark All as Read
                    </button>
                    <?php endif; ?>
                    <a href="dashboard.php" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </div>
            </div>

            <?php if($total_notifications > 0): ?>
                <!-- Notification Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filter by Status:</label>
                        <select class="form-select" id="statusFilter" onchange="filterNotifications()">
                            <option value="all">All Notifications</option>
                            <option value="unread">Unread Only</option>
                            <option value="read">Read Only</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filter by Type:</label>
                        <select class="form-select" id="typeFilter" onchange="filterNotifications()">
                            <option value="all">All Types</option>
                            <option value="admin_to_user">Admin Messages</option>
                            <option value="announcement">Announcements</option>
                        </select>
                    </div>
                </div>

                <div class="notifications-list">
                    <?php 
                    while($row = mysqli_fetch_array($query)): 
                        $is_read = $row['is_read'];
                        $type = $row['type'];
                        $from_user = $row['from_user'];
                        $notification_class = $is_read == 0 ? 'notification-unread' : '';
                        
                        // Determine icon and badge based on type and sender
                        $icon = 'fas fa-bell';
                        $badge_text = '';
                        $badge_class = '';
                        
                        if($from_user === NULL) {
                            $icon = 'fas fa-user-shield';
                            $badge_text = 'Admin';
                            $badge_class = 'bg-primary';
                        } elseif($type == 'announcement') {
                            $icon = 'fas fa-bullhorn';
                            $badge_text = 'Announcement';
                            $badge_class = 'bg-success';
                        }
                    ?>
                        <div class="notification-item <?php echo $notification_class; ?>" 
                             data-status="<?php echo $is_read == 0 ? 'unread' : 'read'; ?>" 
                             data-type="<?php echo $type; ?>"
                             data-id="<?php echo $row['id']; ?>">
                            <div class="row">
                                <div class="col-md-10">
                                    <h5 class="mb-2">
                                        <i class="<?php echo $icon; ?> me-2"></i>
                                        <?php echo htmlentities($row['title']); ?>
                                        <?php if($badge_text): ?>
                                            <span class="badge <?php echo $badge_class; ?> ms-2"><?php echo $badge_text; ?></span>
                                        <?php endif; ?>
                                        <?php if($is_read == 0): ?>
                                            <span class="badge bg-warning ms-1">New</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="mb-2 text-muted"><?php echo htmlentities($row['message']); ?></p>
                                    <small class="text-muted">
                                        <?php 
                                        if($from_user === NULL) {
                                            echo 'From: System Administrator';
                                        } else {
                                            echo 'From: User';
                                        }
                                        ?>
                                    </small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <span class="notification-time">
                                        <?php 
                                        echo date('M j, Y g:i A', strtotime($row['created_at']));
                                        ?>
                                    </span>
                                    <?php if($is_read == 0): ?>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-success" onclick="markAsRead(<?php echo $row['id']; ?>, this)">
                                                <i class="fas fa-check me-1"></i>Mark Read
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Read
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Notifications</h4>
                    <p class="text-muted mb-4">You don't have any notifications at the moment.</p>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function filterNotifications() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const notifications = document.querySelectorAll('.notification-item');
    
    notifications.forEach(notification => {
        const status = notification.getAttribute('data-status');
        const type = notification.getAttribute('data-type');
        
        let statusMatch = statusFilter === 'all' || status === statusFilter;
        let typeMatch = typeFilter === 'all' || type === typeFilter;
        
        if (statusMatch && typeMatch) {
            notification.style.display = 'block';
        } else {
            notification.style.display = 'none';
        }
    });
}

function markAsRead(notificationId, button) {
    // Update UI immediately
    const notificationItem = button.closest('.notification-item');
    notificationItem.classList.remove('notification-unread');
    notificationItem.setAttribute('data-status', 'read');
    
    // Remove "New" badge
    const newBadge = notificationItem.querySelector('.badge.bg-warning');
    if(newBadge) {
        newBadge.remove();
    }
    
    // Update button to show read status
    button.outerHTML = '<small class="text-success"><i class="fas fa-check-circle me-1"></i>Read</small>';
    
    // Update counter
    updateUnreadCount(-1);
    
    // Send AJAX request to update database
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_notification.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('action=mark_read&id=' + notificationId);
}

function markAllAsRead() {
    if(confirm('Mark all notifications as read?')) {
        const unreadCount = document.getElementById('unreadCount');
        const currentCount = parseInt(unreadCount.textContent);
        
        // Update all notifications in UI
        document.querySelectorAll('.notification-unread').forEach(item => {
            item.classList.remove('notification-unread');
            item.setAttribute('data-status', 'read');
            
            // Remove "New" badges
            const newBadge = item.querySelector('.badge.bg-warning');
            if(newBadge) {
                newBadge.remove();
            }
            
            // Update buttons to show read status
            const button = item.querySelector('.btn-outline-success');
            if(button) {
                button.outerHTML = '<small class="text-success"><i class="fas fa-check-circle me-1"></i>Read</small>';
            }
        });
        
        // Update counter to zero
        updateUnreadCount(-currentCount);
        
        // Hide the "Mark All as Read" button
        document.querySelector('button[onclick="markAllAsRead()"]').style.display = 'none';
        
        // Send AJAX request to update all in database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_notification.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('action=mark_all_read&user_id=<?php echo $user_id; ?>');
        
        alert('All notifications marked as read!');
    }
}

function updateUnreadCount(change) {
    const unreadCount = document.getElementById('unreadCount');
    let currentCount = parseInt(unreadCount.textContent);
    currentCount += change;
    
    if(currentCount < 0) currentCount = 0;
    unreadCount.textContent = currentCount;
    
    // Hide "Mark All as Read" button if no unread notifications
    const markAllBtn = document.querySelector('button[onclick="markAllAsRead()"]');
    if(markAllBtn && currentCount === 0) {
        markAllBtn.style.display = 'none';
    }
}

// Add click event to mark as read when clicking anywhere on notification
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't trigger if user clicked the mark read button
            if(!e.target.closest('.btn')) {
                const notificationId = this.getAttribute('data-id');
                const isUnread = this.classList.contains('notification-unread');
                
                if(isUnread) {
                    const markReadBtn = this.querySelector('.btn-outline-success');
                    if(markReadBtn) {
                        markAsRead(notificationId, markReadBtn);
                    }
                }
            }
        });
    });
});
</script>

<style>
.notification-item {
    padding: 20px;
    border-left: 4px solid #007bff;
    background: white;
    margin-bottom: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.3s ease;
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.notification-unread {
    border-left-color: #dc3545;
    background: #fff5f5;
}

.notification-time {
    color: #6c757d;
    font-size: 12px;
    font-weight: 500;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.stat-total .stat-icon { color: #6c757d; }
.stat-pending .stat-icon { color: #dc3545; }
.stat-process .stat-icon { color: #007bff; }
.stat-closed .stat-icon { color: #28a745; }

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-weight: 500;
}

.badge {
    font-size: 0.7em;
    padding: 0.4em 0.6em;
}

.notification-item h5 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.notification-item p {
    color: #495057;
    line-height: 1.5;
}
</style>

<?php
include('includes/footer.php');
?>