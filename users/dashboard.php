<?php
include('includes/config.php');
include('includes/header.php');

// Include sidebar
include('includes/sidebar.php');
// Get complaint statistics
$pending = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints where userId='".$_SESSION['id']."' and status is null"));
$in_process = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints where userId='".$_SESSION['id']."' and status='in Process'"));
$closed = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints where userId='".$_SESSION['id']."' and status='closed'"));
$total = $pending + $in_process + $closed;

// Get recent complaints
$recent_complaints = mysqli_query($bd, "SELECT * FROM tblcomplaints where userId='".$_SESSION['id']."' ORDER BY regDate DESC LIMIT 3");

// Calculate percentages for charts
$pending_percent = $total > 0 ? round(($pending/$total)*100, 1) : 0;
$in_process_percent = $total > 0 ? round(($in_process/$total)*100, 1) : 0;
$closed_percent = $total > 0 ? round(($closed/$total)*100, 1) : 0;

// Get complaints by month for chart
$monthly_complaints = array_fill(1, 12, 0); // Initialize all months with 0
$monthly_query = mysqli_query($bd, "SELECT MONTH(regDate) as month, COUNT(*) as count FROM tblcomplaints WHERE userId='".$_SESSION['id']."' AND YEAR(regDate) = YEAR(CURDATE()) GROUP BY MONTH(regDate) ORDER BY month");
while($row = mysqli_fetch_assoc($monthly_query)) {
    $monthly_complaints[$row['month']] = $row['count'];
}

$total_complaints_current_year = array_sum($monthly_complaints);

// Get recent notifications
$user_id = $_SESSION['id'];
$notifications = mysqli_query($bd, "SELECT * FROM notifications 
                                    WHERE to_user = '$user_id' OR to_user IS NULL 
                                    ORDER BY created_at DESC LIMIT 3");

// Check for new unread messages from admin
$new_message_count = 0;
$msg_query = mysqli_query($bd, "SELECT COUNT(*) AS unread_count FROM user_messages 
                                WHERE user_id = '$user_id' AND direction = 'admin_to_user' AND is_read = 0");
if($msg_query && mysqli_num_rows($msg_query) > 0) {
    $msg_data = mysqli_fetch_assoc($msg_query);
    $new_message_count = $msg_data['unread_count'];
}
?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 250px; margin-top: 50px; padding: 20px; background: #f4f6f9; min-height: 100vh;">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title">Welcome, <?php echo htmlentities($_SESSION['login']); ?>!</h1>
            <p class="dashboard-subtitle">GUVNL Consumer Complaint Tracking & Monitoring System</p>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Welcome Card -->
        <div class="welcome-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3><i class="fas fa-bolt me-2"></i>GUVNL Complaint Management System</h3>
                    <p class="mb-0">Track and manage your electricity complaints efficiently. Submit new complaints, check real-time status updates, and monitor resolution progress with complete transparency.</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="register-complaint.php" class="btn btn-light btn-lg" style="border-radius: 30px; padding: 12px 30px; font-weight: 600;">
                        <i class="fas fa-plus-circle me-2"></i>Submit New Complaint
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-total" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; transition: transform 0.3s;">
                    <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #4e54c8, #8f94fb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-file-alt" style="font-size: 28px; color: white;"></i>
                    </div>
                    <div class="stat-number" style="font-size: 32px; font-weight: 700; color: #2c3e50;"><?php echo $total; ?></div>
                    <div class="stat-label" style="font-size: 14px; color: #7f8c8d; font-weight: 500;">Total Complaints</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-pending" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; transition: transform 0.3s;">
                    <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #ff9a9e, #fad0c4); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-clock" style="font-size: 28px; color: white;"></i>
                    </div>
                    <div class="stat-number" style="font-size: 32px; font-weight: 700; color: #2c3e50;"><?php echo $pending; ?></div>
                    <div class="stat-label" style="font-size: 14px; color: #7f8c8d; font-weight: 500;">Pending Complaints</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-process" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; transition: transform 0.3s;">
                    <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #a1c4fd, #c2e9fb); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-cog" style="font-size: 28px; color: white;"></i>
                    </div>
                    <div class="stat-number" style="font-size: 32px; font-weight: 700; color: #2c3e50;"><?php echo $in_process; ?></div>
                    <div class="stat-label" style="font-size: 14px; color: #7f8c8d; font-weight: 500;">In Process</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-closed" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: center; transition: transform 0.3s;">
                    <div class="stat-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #56ab2f, #a8e6cf); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-check-circle" style="font-size: 28px; color: white;"></i>
                    </div>
                    <div class="stat-number" style="font-size: 32px; font-weight: 700; color: #2c3e50;"><?php echo $closed; ?></div>
                    <div class="stat-label" style="font-size: 14px; color: #7f8c8d; font-weight: 500;">Resolved Complaints</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Quick Actions and Charts -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="quick-actions" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px;">
                    <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;"><i class="fas fa-bolt me-2" style="color: #f39c12;"></i>Quick Actions</h4>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="register-complaint.php" class="action-btn d-block text-center p-3" style="background: #f8f9fa; border-radius: 10px; color: #2c3e50; text-decoration: none; transition: all 0.3s;">
                                <i class="fas fa-plus-circle mb-2" style="font-size: 24px; color: #3498db;"></i>
                                <div style="font-size: 13px; font-weight: 500;">Submit Complaint</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="complaint-history.php" class="action-btn d-block text-center p-3" style="background: #f8f9fa; border-radius: 10px; color: #2c3e50; text-decoration: none; transition: all 0.3s;">
                                <i class="fas fa-history mb-2" style="font-size: 24px; color: #9b59b6;"></i>
                                <div style="font-size: 13px; font-weight: 500;">Complaint History</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="track-complaint.php" class="action-btn d-block text-center p-3" style="background: #f8f9fa; border-radius: 10px; color: #2c3e50; text-decoration: none; transition: all 0.3s;">
                                <i class="fas fa-search mb-2" style="font-size: 24px; color: #e74c3c;"></i>
                                <div style="font-size: 13px; font-weight: 500;">Track Complaint</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="profile.php" class="action-btn d-block text-center p-3" style="background: #f8f9fa; border-radius: 10px; color: #2c3e50; text-decoration: none; transition: all 0.3s;">
                                <i class="fas fa-user mb-2" style="font-size: 24px; color: #2ecc71;"></i>
                                <div style="font-size: 13px; font-weight: 500;">Update Profile</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="send_messages.php" class="action-btn d-block text-center p-3" style="background: #f8f9fa; border-radius: 10px; color: #2c3e50; text-decoration: none; transition: all 0.3s;">
                                <i class="fas fa-paper-plane mb-2" style="font-size: 24px; color: #f39c12;"></i>
                                <div style="font-size: 13px; font-weight: 500;">Send Message</div>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="inbox.php" class="action-btn d-block text-center p-3" style="background: #f8f9fa; border-radius: 10px; color: #2c3e50; text-decoration: none; transition: all 0.3s;">
                                <i class="fas fa-inbox mb-2" style="font-size: 24px; color: #1abc9c;"></i>
                                <div style="font-size: 13px; font-weight: 500;">View Messages</div>
                            </a>
                        </div>
                    </div>
                </div>
                
                
                <!-- Complaint Status Chart -->
                <div class="chart-container" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;"><i class="fas fa-chart-pie me-2" style="color: #9b59b6;"></i>Complaint Status</h4>
                    <div style="position: relative; height: 300px;">
                        <canvas id="complaintStatusChart"></canvas>
                    </div>
                </div>
                <!-- GUVNL Emergency Contacts -->
                <div class="quick-actions" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-top: 25px;">
                    <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;"><i class="fas fa-phone-alt me-2" style="color: #e74c3c;"></i>Emergency Contacts</h4>
                    <p class="mb-3" style="color: #7f8c8d;">In case of any emergency, please reach out 24/7 to the following contacts:</p>
                    <div class="d-flex align-items-center p-3 mb-3" style="background: #fff5f5; border-radius: 10px;">
                        <div class="me-3">
                            <i class="fas fa-phone fa-2x" style="color: #e74c3c;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #2c3e50;">24x7 Emergency</div>
                            <small style="color: #7f8c8d;">1800-233-3333</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center p-3" style="background: #f0f8ff; border-radius: 10px;">
                        <div class="me-3">
                            <i class="fas fa-envelope fa-2x" style="color: #3498db;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #2c3e50;">Email Support</div>
                            <small style="color: #7f8c8d;">complaints@guvnl.com</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recent Complaints, Charts and Notifications -->
            <div class="col-lg-8">
                <!-- Recent Complaints -->
                <div class="recent-complaints" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 style="color: #2c3e50; font-weight: 600;"><i class="fas fa-list me-2" style="color: #3498db;"></i>Recent Complaints</h4>
                        <a href="complaint-history.php" class="btn btn-outline-primary btn-sm" style="border-radius: 20px;">View All</a>
                    </div>
                    
                    <?php if(mysqli_num_rows($recent_complaints) > 0): ?>
                        <?php while($complaint = mysqli_fetch_array($recent_complaints)): 
                            $status_class = '';
                            $status_badge = '';
                            if($complaint['status'] === null) {
                                $status_class = 'status-pending';
                                $status_badge = 'bg-warning';
                            } else if($complaint['status'] === 'in Process') {
                                $status_class = 'status-process';
                                $status_badge = 'bg-primary';
                            } else if($complaint['status'] === 'closed') {
                                $status_class = 'status-closed';
                                $status_badge = 'bg-success';
                            }
                        ?>
                            <div class="complaint-item d-flex align-items-center p-3 mb-3" style="background: #f8f9fa; border-radius: 10px; transition: all 0.3s;">
                                <div class="complaint-status me-3 <?php echo $status_class; ?>" style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                <div class="complaint-details flex-grow-1">
                                    <div class="complaint-title" style="font-weight: 600; color: #2c3e50;"><?php echo htmlentities($complaint['complaintTitle']); ?></div>
                                    <div class="complaint-date" style="font-size: 13px; color: #7f8c8d;">Complaint No: <?php echo htmlentities($complaint['complaintNumber']); ?> | Submitted: <?php echo htmlentities($complaint['regDate']); ?></div>
                                </div>
                                <div>
                                    <?php 
                                        if($complaint['status'] === null) echo '<span class="badge bg-warning" style="border-radius: 20px;">Pending</span>';
                                        else if($complaint['status'] === 'in Process') echo '<span class="badge bg-primary" style="border-radius: 20px;">In Process</span>';
                                        else if($complaint['status'] === 'closed') echo '<span class="badge bg-success" style="border-radius: 20px;">Resolved</span>';
                                    ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No complaints submitted yet.</p>
                            <a href="register-complaint.php" class="btn btn-warning" style="border-radius: 20px;">Submit Your First Complaint</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Monthly Complaints Chart -->
                <div class="chart-container" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px;">
                    <h4 class="mb-4" style="color: #2c3e50; font-weight: 600;"><i class="fas fa-chart-bar me-2" style="color: #e74c3c;"></i>Monthly Complaints</h4>
                    <p class="mb-4" style="color: #7f8c8d;"><i class="fas fa-calendar me-2" style="color: #3498db;"></i>Complaints submitted in the current year: <b><?php echo $total_complaints_current_year . " (" . date("Y") . ")";?></b></p>
                    <div style="position: relative; height: 250px;">
                        <canvas id="monthlyComplaintsChart"></canvas>
                    </div>
                </div>

                <!-- Notifications and Messages Section -->
                <div class="row">
                    <!-- Notifications -->
                    <div class="col-md-6 mb-4">
                        <div class="recent-notifications" style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); height: 448px; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-bell me-2" style="color: #f39c12;"></i>Notifications
                                </h4>
                                <a href="notification.php" class="btn btn-outline-primary btn-sm" style="border-radius: 20px;">View All</a>
                            </div>
                            
                            <?php if(mysqli_num_rows($notifications) > 0):
                                while($note = mysqli_fetch_array($notifications)):
                                    $is_read = $note['is_read'];
                                    $status_class = $is_read ? 'notification-read' : 'notification-unread';
                            ?>
                                <div class="notification-item <?php echo $status_class; ?> p-3 mb-2 rounded" style="transition: all 0.3s;">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon me-3">
                                            <i class="fas fa-info-circle" style="color: #3498db;"></i>
                                        </div>
                                        <div class="notification-content flex-grow-1">
                                            <div class="notification-title fw-bold text-dark mb-1" style="font-size: 14px;">
                                                <?php echo htmlentities($note['title']); ?>
                                                <?php if(!$is_read): ?>
                                                    <span class="badge bg-danger ms-2" style="font-size: 10px;">New</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="notification-message text-muted mb-2" style="font-size: 13px;">
                                                <?php echo htmlentities($note['message']); ?>
                                            </div>
                                            <div class="notification-date small text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('d M Y, h:i A', strtotime($note['created_at'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                endwhile;
                            else:
                            ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No notifications at the moment.</p>
                                    <small class="text-muted">You'll see important updates here</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="col-md-6 mb-4">
                        <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); height: 100%;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold" style="color: #2c3e50;">Messages</h6>
                                <a href="inbox.php" class="btn btn-outline-primary btn-sm" style="border-radius: 20px;">View Inbox</a>
                            </div>
                            
                            <?php if($new_message_count > 0): ?>
                            <div class="card shadow-sm border-0 new-message-alert mb-3" style="border-radius: 15px;">
                                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #56ab2f, #a8e6cf); border-radius: 15px 15px 0 0; color: white;">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-bell me-2"></i>New Message Alert
                                    </h6>
                                    <span class="badge bg-light text-dark"><?php echo $new_message_count; ?> new</span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-envelope fa-2x" style="color: #56ab2f;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1" style="font-size: 16px;">You have <?php echo $new_message_count; ?> new message(s)!</h5>
                                            <p class="mb-2 text-muted" style="font-size: 13px;">From Administrator</p>
                                            <div class="d-flex gap-2">
                                                <a href="inbox.php?filter=unread&type=admin_to_user" class="btn btn-sm btn-success" style="border-radius: 20px;">
                                                    <i class="fas fa-inbox me-1"></i>View Messages
                                                </a>
                                                <a href="send_messages.php" class="btn btn-sm btn-outline-success" style="border-radius: 20px;">
                                                    <i class="fas fa-reply me-1"></i>Reply
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick preview of new messages -->
                            <?php
                            $new_messages_preview = mysqli_query($bd, "SELECT * FROM user_messages 
                                                                      WHERE user_id = '$user_id' AND direction = 'admin_to_user' AND is_read = 0 
                                                                      ORDER BY created_at DESC LIMIT 2");
                            if(mysqli_num_rows($new_messages_preview) > 0):
                            ?>
                            <div class="mt-3">
                                <small class="text-muted mb-2 d-block">Recent unread messages:</small>
                                <?php while($new_msg = mysqli_fetch_array($new_messages_preview)): ?>
                                <div class="new-message-preview p-2 mb-2 rounded" style="background: #f0f8ff; border-left: 3px solid #3498db; transition: all 0.3s;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong class="d-block text-primary small">
                                                <?php echo substr(htmlentities($new_msg['subject']), 0, 30); ?>
                                                <?php if(strlen($new_msg['subject']) > 30): ?>...<?php endif; ?>
                                            </strong>
                                            <small class="text-muted">
                                                <?php echo substr(htmlentities($new_msg['message']), 0, 40); ?>
                                                <?php if(strlen($new_msg['message']) > 40): ?>...<?php endif; ?>
                                            </small>
                                        </div>
                                        <a href="inbox.php?message_id=<?php echo $new_msg['id']; ?>" class="btn btn-sm btn-outline-primary ms-2" style="border-radius: 20px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo time_elapsed_string($new_msg['created_at']); ?>
                                    </small>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php else: ?>
                            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                    <h6 class="text-success mb-1">No New Messages</h6>
                                    <p class="text-muted mb-2">You're all caught up!</p>
                                    <a href="send_messages.php" class="btn btn-outline-primary btn-sm" style="border-radius: 20px;">
                                        <i class="fas fa-paper-plane me-1"></i>Send a Message
                                    </a>
                                    <br>
                                    <a href="inbox.php" class="btn btn-secondary btn-sm" style="border-radius: 20px; margin-top: 5px;">
                                        <i class="fas fa-inbox me-1"></i> &nbsp;View Messages
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All News Section -->
        <div class="card mt-4 shadow-sm border-0" style="border-radius: 14px; overflow: hidden; width: 100%; margin: 40px auto;">
            <div class="card-header" style="background: linear-gradient(90deg, #68dff0 0%, #38b6ff 100%); color: #fff; border-radius: 0;">
                <h4 class="mb-0" style="font-weight:700; letter-spacing: 0.5px;">
                    <i class="fa fa-newspaper-o me-2"></i> Latest News & Updates
                </h4>
            </div>
            <div class="card-body p-0" style="background: #fafdff;">
                <?php
                    // Fetch all news from tblnews
                    $news_query = mysqli_query($bd, "SELECT * FROM news ORDER BY created_at DESC");
                    if(!$news_query) {
                        echo '<div class="alert alert-danger m-4">Error fetching news: ' . mysqli_error($bd) . '</div>';
                    } elseif(mysqli_num_rows($news_query) > 0) {
                        while($news = mysqli_fetch_array($news_query)) {
                ?>
                <div class="news-item">
                    <div class="news-block d-flex flex-wrap align-items-start border-bottom px-4 py-3" style="background: #fff; transition: background 0.2s;">

                <!-- LEFT: News text section -->
                <div class="news-text flex-grow-1" style="flex: 1; min-width: 60%;">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge rounded-pill" style="background: #68dff0; color: #fff; font-size: 12px; margin-right: 10px;">
                            <i class="fa fa-bullhorn"></i>
                        </span>
                        <span class="news-title" style="font-weight: 600; color: #1a2233; font-size: 16px;">
                            <?php echo htmlentities($news['title']); ?>
                        </span>
                    </div>

                    <div class="news-date text-muted mb-2" style="font-size: 12px;">
                        <i class="fa fa-calendar me-1"></i>
                        <?php echo date('d M Y', strtotime($news['created_at'])); ?>
                    </div>

                    <div class="news-content" style="font-size: 14px; line-height: 1.7; color: #444;">
                        <?php echo nl2br(htmlentities($news['content'])); ?>
                    </div>
                </div>
                <?php if(!empty($news['image'])): ?>
                    <div class="news-image text-center" style="flex: 0 0 35%; padding: 15px 20px; background: white; border-left: 1px solid #e0e0e0;">
                        <?php 
                        $ss = "http://localhost/Complaint%20Management%20System/admin/news_images/";
                        ?>
                        <img src="<?php echo $ss . htmlentities($news['image']); ?>" alt="News Image" class="img-fluid" style="max-height: 120px; border-radius: 8px; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>

                </div>
                <?php
                        }
                    } else {
                ?>
                    <div class="text-center py-5">
                        <i class="fa fa-info-circle text-muted" style="font-size: 32px;"></i>
                        <div class="text-muted mt-3" style="font-size: 15px;">No news updates available at the moment.</div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Complaint Status Pie Chart
    const complaintStatusCtx = document.getElementById('complaintStatusChart').getContext('2d');
    const complaintStatusChart = new Chart(complaintStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Process', 'Resolved'],
            datasets: [{
                data: [<?php echo $pending; ?>, <?php echo $in_process; ?>, <?php echo $closed; ?>],
                backgroundColor: [
                    '#ff9a9e',
                    '#a1c4fd',
                    '#56ab2f'
                ],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // Monthly Complaints Bar Chart
    const monthlyComplaintsCtx = document.getElementById('monthlyComplaintsChart').getContext('2d');
    const monthlyComplaintsChart = new Chart(monthlyComplaintsCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Complaints',
                data: [<?php echo implode(',', $monthly_complaints); ?>],
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Add hover effects to cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add hover effects to action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // Mark notification as read when clicked
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            // You can implement AJAX to mark notification as read
            this.classList.remove('notification-unread');
            this.classList.add('notification-read');
            
            // Remove the "New" badge
            const badge = this.querySelector('.badge');
            if(badge) {
                badge.remove();
            }
        });
    });
});

// Auto-refresh notifications and messages every 60 seconds
setInterval(function() {
    // You can implement AJAX here to refresh only the notifications section
    // For now, we'll do a full page refresh
    console.log('Checking for new notifications and messages...');
}, 60000);

// Add CSS for status indicators
const style = document.createElement('style');
style.textContent = `
    .status-pending { background-color: #ff9a9e; }
    .status-process { background-color: #a1c4fd; }
    .status-closed { background-color: #56ab2f; }
    
    .notification-item {
        transition: all 0.3s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .notification-item:hover {
        background: #f8f9fa !important;
        transform: translateX(5px);
        border-color: #e9ecef;
    }

    .notification-unread {
        background: #e7f1ff;
        border-left: 4px solid #3498db !important;
    }

    .notification-read {
        background: #ffffff;
        border-left: 4px solid #bdc3c7 !important;
    }

    .notification-icon {
        font-size: 18px;
        margin-top: 2px;
    }

    .notification-title {
        font-size: 14px;
    }

    .notification-message {
        font-size: 13px;
        line-height: 1.4;
    }

    .notification-date {
        font-size: 11px;
    }

    .new-message-alert {
        animation: gentlePulse 3s infinite;
        border: 1px solid #56ab2f;
    }

    @keyframes gentlePulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.01); }
        100% { transform: scale(1); }
    }

    .new-message-preview {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .new-message-preview:hover {
        background: #e3f2fd !important;
        transform: translateX(3px);
    }

    .badge {
        font-size: 0.7em;
    }
    
    .complaint-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
`;
document.head.appendChild(style);
</script>

<?php
// Include footer
include('includes/footer.php');

// Function to format time elapsed
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