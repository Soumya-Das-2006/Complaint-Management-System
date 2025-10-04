<?php
// Include config file to get database connection
include('include/config.php');
?>

<div class="span3">
    <div class="sidebar">
        <div class="widget widget-profile unstyled" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
            <div class="profile-avatar">
                <img src="images/user.png" style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid white; margin-bottom: 10px;" />
            </div>
            <h4 style="margin: 10px 0 5px 0; color: white;">Admin Panel</h4>
            <p style="margin: 0; opacity: 0.8;">Complaint Management System</p>
        </div>

        <!-- Dashboard -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a href="dashboard.php" style="background: #28a745; color: white;">
                    <i class="menu-icon icon-dashboard"></i>
                    Dashboard
                </a>
            </li>
        </ul>

        <!-- Complaint Management -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#toggleComplaints" style="background: #007bff; color: white;">
                    <i class="menu-icon icon-cog"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    Complaint Management
                </a>
                <ul id="toggleComplaints" class="collapse unstyled">
                    <li>
                        <a href="notprocess-complaint.php">
                            <i class="icon-warning-sign" style="color: #ffc107;"></i>
                            New Complaints
                            <?php
                            $rt = mysqli_query($bd, "SELECT * FROM tblcomplaints where status is null");
                            $num1 = mysqli_num_rows($rt);
                            ?>
                            <b class="label label-warning pull-right"><?php echo htmlentities($num1); ?></b>
                        </a>
                    </li>
                    <li>
                        <a href="inprocess-complaint.php">
                            <i class="icon-refresh" style="color: #17a2b8;"></i>
                            In Process
                            <?php 
                            $status="in process";                   
                            $rt = mysqli_query($bd, "SELECT * FROM tblcomplaints where status='$status'");
                            $num1 = mysqli_num_rows($rt);
                            ?>
                            <b class="label label-info pull-right"><?php echo htmlentities($num1); ?></b>
                        </a>
                    </li>
                    <li>
                        <a href="closed-complaint.php">
                            <i class="icon-ok-sign" style="color: #28a745;"></i>
                            Closed Complaints
                            <?php 
                            $status="closed";                   
                            $rt = mysqli_query($bd, "SELECT * FROM tblcomplaints where status='$status'");
                            $num1 = mysqli_num_rows($rt);
                            ?>
                            <b class="label label-success pull-right"><?php echo htmlentities($num1); ?></b>
                        </a>
                    </li>
                    <li>
                        <a href="all-complaints.php">
                            <i class="icon-list-alt" style="color: #6c757d;"></i>
                            All Complaints
                            <?php 
                            $rt = mysqli_query($bd, "SELECT * FROM tblcomplaints");
                            $num1 = mysqli_num_rows($rt);
                            ?>
                            <b class="label label-default pull-right"><?php echo htmlentities($num1); ?></b>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Work Management -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#toggleWorkManagement" style="background: #ff6b6b; color: white;">
                    <i class="menu-icon icon-tasks"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    Work Management
                </a>
                <ul id="toggleWorkManagement" class="collapse unstyled">
                    <li>
                        <a href="assign-work.php">
                            <i class="icon-plus-sign" style="color: #28a745;"></i>
                            Assign New Work
                            <?php
                            // Check if works table exists and get pending count
                            $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'works'");
                            if(mysqli_num_rows($table_check) > 0) {
                                $pending_works = mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='pending'");
                                if($pending_works) {
                                    $pending_count = mysqli_fetch_array($pending_works)['total'];
                                    if($pending_count > 0): ?>
                                    <b class="label label-warning pull-right"><?php echo $pending_count; ?></b>
                                    <?php endif;
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="manage-works.php">
                            <i class="icon-list" style="color: #007bff;"></i>
                            All Works
                            <?php 
                            $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'works'");
                            if(mysqli_num_rows($table_check) > 0) {
                                $total_works = mysqli_query($bd, "SELECT COUNT(*) as total FROM works");
                                if($total_works) {
                                    $works_count = mysqli_fetch_array($total_works)['total'];
                                    ?>
                                    <b class="label label-primary pull-right"><?php echo $works_count; ?></b>
                                    <?php
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="pending-works.php">
                            <i class="icon-time" style="color: #ffc107;"></i>
                            Pending Works
                            <?php 
                            $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'works'");
                            if(mysqli_num_rows($table_check) > 0) {
                                $pending_works = mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='pending'");
                                if($pending_works) {
                                    $pending_count = mysqli_fetch_array($pending_works)['total'];
                                    ?>
                                    <b class="label label-warning pull-right"><?php echo $pending_count; ?></b>
                                    <?php
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="assigned-works.php">
                            <i class="icon-user" style="color: #17a2b8;"></i>
                            Assigned Works
                            <?php 
                            $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'works'");
                            if(mysqli_num_rows($table_check) > 0) {
                                $assigned_works = mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='assigned'");
                                if($assigned_works) {
                                    $assigned_count = mysqli_fetch_array($assigned_works)['total'];
                                    ?>
                                    <b class="label label-info pull-right"><?php echo $assigned_count; ?></b>
                                    <?php
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="inprogress-works.php">
                            <i class="icon-refresh" style="color: #fd7e14;"></i>
                            In Progress
                            <?php 
                            $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'works'");
                            if(mysqli_num_rows($table_check) > 0) {
                                $inprogress_works = mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='in_progress'");
                                if($inprogress_works) {
                                    $inprogress_count = mysqli_fetch_array($inprogress_works)['total'];
                                    ?>
                                    <b class="label label-orange pull-right"><?php echo $inprogress_count; ?></b>
                                    <?php
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="completed-works.php">
                            <i class="icon-ok-sign" style="color: #28a745;"></i>
                            Completed Works
                            <?php 
                            $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'works'");
                            if(mysqli_num_rows($table_check) > 0) {
                                $completed_works = mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='completed'");
                                if($completed_works) {
                                    $completed_count = mysqli_fetch_array($completed_works)['total'];
                                    ?>
                                    <b class="label label-success pull-right"><?php echo $completed_count; ?></b>
                                    <?php
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li>
                        <a href="urgent-works.php">
                            <i class="icon-exclamation-sign" style="color: #dc3545;"></i>
                            Urgent Works
                            <?php 
                            $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'works'");
                            if(mysqli_num_rows($table_check) > 0) {
                                $urgent_works = mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE priority='urgent' AND status NOT IN ('completed', 'cancelled')");
                                if($urgent_works) {
                                    $urgent_count = mysqli_fetch_array($urgent_works)['total'];
                                    ?>
                                    <b class="label label-danger pull-right"><?php echo $urgent_count; ?></b>
                                    <?php
                                }
                            }
                            ?>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Manage Workers -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a href="manage-workers.php" style="background: #6f42c1; color: white;">
                    <i class="menu-icon icon-briefcase"></i>
                    Manage Workers
                    <?php 
                    $table_check = mysqli_query($bd, "SHOW TABLES LIKE 'workers'");
                    if(mysqli_num_rows($table_check) > 0) {
                        $active_workers = mysqli_query($bd, "SELECT COUNT(*) as total FROM workers WHERE status='active'");
                        if($active_workers) {
                            $workers_count = mysqli_fetch_array($active_workers)['total'];
                            ?>
                            <b class="label label-primary pull-right"><?php echo $workers_count; ?></b>
                            <?php
                        }
                    }
                    ?>
                </a>
            </li>
        </ul>

        <!-- User Management -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a href="manage-users.php">
                    <i class="menu-icon icon-group" style="color: #e83e8c;"></i>
                    User Management
                    <?php 
                    $rt = mysqli_query($bd, "SELECT * FROM users");
                    $num1 = mysqli_num_rows($rt);
                    ?>
                    <b class="label label-primary pull-right"><?php echo htmlentities($num1); ?></b>
                </a>
            </li>
        </ul>
        
        <!-- System Configuration -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#toggleSystemConfig" style="background: #fd7e14; color: white;">
                    <i class="menu-icon icon-wrench"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    System Configuration
                </a>
                <ul id="toggleSystemConfig" class="collapse unstyled">
                    <li><a href="category.php"><i class="icon-tags"></i> Categories</a></li>
                    <li><a href="subcategory.php"><i class="icon-tags"></i> Sub-Categories</a></li>
                    <li><a href="state.php"><i class="icon-map-marker"></i> States</a></li>
                    <li><a href="priority.php"><i class="icon-flag"></i> Priority Levels</a></li>
                </ul>
            </li>
        </ul>

        <!-- Communication -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#toggleCommunication" style="background: #17a2b8; color: white;">
                    <i class="menu-icon icon-bullhorn"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    Communication
                </a>
                <ul id="toggleCommunication" class="collapse unstyled">
                    <li><a href="manage-notifications.php"><i class="icon-bell"></i> Notifications</a></li>
                    <li><a href="manage-news.php"><i class="icon-news"></i> News Management</a></li>
                    <li><a href="sms-logs.php"><i class="icon-phone"></i> SMS Logs</a></li>
                    <li><a href="work-sms-templates.php"><i class="icon-envelope"></i> SMS Templates</a></li>
                </ul>
            </li>
        </ul>

        <!-- Notifications -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a href="notifications.php" style="background: #17a2b8; color: white;">
                    <i class="menu-icon icon-bell"></i>
                    Notifications
                    <?php 
                    $rt = mysqli_query($bd, "SELECT * FROM tblcomplaints where status is null");
                    $num1 = mysqli_num_rows($rt);
                    ?>
                    <b class="label label-info pull-right"><?php echo htmlentities($num1); ?></b>
                </a>
            </li>
        </ul>

        <!-- Feedback Management -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a href="manage-feedback.php" style="background: #28a745; color: white;">
                    <i class="menu-icon icon-comments"></i>
                    Feedback Management
                </a>
            </li>
        </ul>

        <!-- Reports & Analytics -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#toggleReports" style="background: #20c997; color: white;">
                    <i class="menu-icon icon-bar-chart"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    Reports & Analytics
                </a>
                <ul id="toggleReports" class="collapse unstyled">
                    <li><a href="user-logs.php"><i class="icon-list-alt"></i> System Logs</a></li>
                    <li><a href="analytics.php"><i class="icon-bar-chart"></i> Analytics</a></li>
                    <li><a href="reports.php"><i class="icon-file-text"></i> Generate Reports</a></li>
                    <li><a href="work-reports.php"><i class="icon-tasks"></i> Work Reports</a></li>
                    <li><a href="worker-performance.php"><i class="icon-star"></i> Worker Performance</a></li>
                </ul>
            </li>
        </ul>

        <!-- System Settings -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#toggleSettings" style="background: #6f42c1; color: white;">
                    <i class="menu-icon icon-cog"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    System Settings
                </a>
                <ul id="toggleSettings" class="collapse unstyled">
                    <li><a href="settings.php"><i class="icon-cog"></i> General Settings</a></li>
                    <li><a href="email-settings.php"><i class="icon-envelope"></i> Email Settings</a></li>
                    <li><a href="sms-settings.php"><i class="icon-phone"></i> SMS Settings</a></li>
                    <li><a href="work-settings.php"><i class="icon-tasks"></i> Work Settings</a></li>
                    <li><a href="backup.php"><i class="icon-hdd"></i> Backup & Restore</a></li>
                </ul>
            </li>
        </ul>

        <!-- Account -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a class="collapsed" data-toggle="collapse" href="#toggleAccount" style="background: #6c757d; color: white;">
                    <i class="menu-icon icon-user"></i>
                    <i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
                    My Account
                </a>
                <ul id="toggleAccount" class="collapse unstyled">
                    <li><a href="change-password.php"><i class="icon-lock"></i> Change Password</a></li>
                    <li><a href="profile.php"><i class="icon-user"></i> Edit Profile</a></li>
                </ul>
            </li>
        </ul>

        <!-- Logout -->
        <ul class="widget widget-menu unstyled">
            <li>
                <a href="logout.php" style="background: #dc3545; color: white;">
                    <i class="menu-icon icon-signout"></i>
                    Logout
                </a>
            </li>
        </ul>

    </div>
</div>

<!-- JavaScript for toggle functionality -->
<script>
$(document).ready(function() {
    // Initialize all collapse elements
    $('.collapse').collapse({
        toggle: false
    });
    
    // Add click handlers for proper toggle behavior
    $('a[data-toggle="collapse"]').on('click', function() {
        var target = $(this).attr('href');
        $(target).collapse('toggle');
        
        // Toggle chevron icons
        var $iconDown = $(this).find('.icon-chevron-down');
        var $iconUp = $(this).find('.icon-chevron-up');
        
        if ($(target).hasClass('in')) {
            $iconDown.hide();
            $iconUp.show();
        } else {
            $iconDown.show();
            $iconUp.hide();
        }
    });
    
    // Initialize icon states
    $('.collapse').each(function() {
        var $parentLink = $('a[href="#' + $(this).attr('id') + '"]');
        var $iconDown = $parentLink.find('.icon-chevron-down');
        var $iconUp = $parentLink.find('.icon-chevron-up');
        
        if ($(this).hasClass('in')) {
            $iconDown.hide();
            $iconUp.show();
        } else {
            $iconDown.show();
            $iconUp.hide();
        }
    });
});
</script>

<!-- Enhanced CSS for better visual appearance -->
<style>
.widget-menu li a {
    padding: 12px 15px;
    display: block;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
    position: relative;
}

.widget-menu li a:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.widget-menu .collapse li a {
    padding: 10px 15px 10px 30px;
    background: #f8f9fa;
    color: #495057;
    margin-bottom: 2px;
    border-left: 3px solid transparent;
}

.widget-menu .collapse li a:hover {
    background: #e9ecef;
    color: #007bff;
    border-left-color: #007bff;
}

.label {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.label-warning { background: #ffc107; color: #212529; }
.label-info { background: #17a2b8; color: white; }
.label-success { background: #28a745; color: white; }
.label-primary { background: #007bff; color: white; }
.label-danger { background: #dc3545; color: white; }
.label-orange { background: #fd7e14; color: white; }
.label-default { background: #6c757d; color: white; }

.icon-chevron-up {
    display: none;
}

/* Work Management specific styles */
#toggleWorkManagement + .collapse li a i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .widget-menu li a {
        padding: 10px 12px;
        font-size: 14px;
    }
    
    .widget-menu .collapse li a {
        padding: 8px 12px 8px 25px;
    }
}
</style>