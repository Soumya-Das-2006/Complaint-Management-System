<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

// Fetch complaint statistics
$totalComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints"))['total'];
$newComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints WHERE status IS NULL"))['total'];
$inProcessComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints WHERE status='in process'"))['total'];
$closedComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints WHERE status='closed'"))['total'];
$totalUsers = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM users"))['total'];

// Fetch work statistics
$totalWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works"))['total'];
$pendingWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='pending'"))['total'];
$assignedWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='assigned'"))['total'];
$inProgressWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='in_progress'"))['total'];
$completedWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='completed'"))['total'];
$urgentWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE priority='urgent' AND status NOT IN ('completed', 'cancelled')"))['total'];
$totalWorkers = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM workers WHERE status='active'"))['total'];

// Recent activities
$recentComplaints = mysqli_query($bd, "SELECT c.*, u.fullName 
                                      FROM tblcomplaints c 
                                      JOIN users u ON c.userId = u.id 
                                      ORDER BY c.regDate DESC 
                                      LIMIT 5");

$recentWorks = mysqli_query($bd, "SELECT w.*, wk.name as worker_name 
                                 FROM works w 
                                 LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                                 ORDER BY w.created_at DESC 
                                 LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .dashboard-stats { margin-bottom: 30px; }
        .stat-card { 
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card i { font-size: 40px; margin-bottom: 15px; opacity: 0.9; }
        .stat-number { font-size: 32px; font-weight: bold; margin: 10px 0; }
        .stat-title { font-size: 16px; opacity: 0.9; font-weight: 500; }
        
        /* Complaint Stats Colors */
        .stat-new { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); }
        .stat-process { background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%); }
        .stat-closed { background: linear-gradient(135deg, #27ae60 0%, #219653 100%); }
        .stat-users { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        
        /* Work Stats Colors */
        .stat-works { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-pending { background: linear-gradient(135deg, #ffd93d 0%, #ff9a3d 100%); color: #333; }
        .stat-assigned { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }
        .stat-completed { background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); }
        .stat-workers { background: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%); }
        
        .quick-actions { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .action-btn { 
            display: block; 
            padding: 15px; 
            margin: 10px 0; 
            text-align: center; 
            background: #f8f9fa; 
            border: 2px solid #e9ecef;
            border-radius: 8px;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .action-btn:hover { 
            background: #007bff; 
            color: white; 
            border-color: #007bff;
            text-decoration: none;
            transform: translateX(5px);
        }
        .action-btn i { margin-right: 8px; }
        
        .recent-activity { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .activity-item { 
            padding: 12px 0; 
            border-bottom: 1px solid #eee; 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-item:last-child { border-bottom: none; }
        
        .status-badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 11px; 
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-new { background: #ffc107; color: #000; }
        .status-process { background: #17a2b8; color: white; }
        .status-closed { background: #28a745; color: white; }
        .status-pending { background: #ffc107; color: #000; }
        .status-assigned { background: #17a2b8; color: white; }
        .status-inprogress { background: #fd7e14; color: white; }
        .status-completed { background: #28a745; color: white; }
        .status-urgent { background: #dc3545; color: white; }
        
        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 5px;
        }
        .priority-urgent { background: #dc3545; color: white; }
        .priority-high { background: #fd7e14; color: white; }
        .priority-medium { background: #ffc107; color: #000; }
        .priority-low { background: #28a745; color: white; }
        
        .section-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .activity-content {
            flex-grow: 1;
            margin-right: 15px;
        }
        
        .activity-meta {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
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
                                <h3><i class="icon-dashboard"></i> Dashboard Overview</h3>
                            </div>
                            <div class="module-body">
                                <!-- Complaint Statistics -->
                                <h4 class="section-title"><i class="icon-warning-sign"></i> Complaint Statistics</h4>
                                <div class="row-fluid dashboard-stats">
                                    <div class="span3">
                                        <div class="stat-card stat-new">
                                            <i class="icon-warning-sign"></i>
                                            <div class="stat-number"><?php echo $newComplaints; ?></div>
                                            <div class="stat-title">New Complaints</div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stat-card stat-process">
                                            <i class="icon-refresh"></i>
                                            <div class="stat-number"><?php echo $inProcessComplaints; ?></div>
                                            <div class="stat-title">In Process</div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stat-card stat-closed">
                                            <i class="icon-ok-sign"></i>
                                            <div class="stat-number"><?php echo $closedComplaints; ?></div>
                                            <div class="stat-title">Closed Complaints</div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stat-card stat-users">
                                            <i class="icon-group"></i>
                                            <div class="stat-number"><?php echo $totalUsers; ?></div>
                                            <div class="stat-title">Total Users</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Work Management Statistics -->
                                <h4 class="section-title"><i class="icon-tasks"></i> Work Management Statistics</h4>
                                <div class="row-fluid dashboard-stats">
                                    <div class="span2">
                                        <div class="stat-card stat-works">
                                            <i class="icon-list-alt"></i>
                                            <div class="stat-number"><?php echo $totalWorks; ?></div>
                                            <div class="stat-title">Total Works</div>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="stat-card stat-pending">
                                            <i class="icon-time"></i>
                                            <div class="stat-number"><?php echo $pendingWorks; ?></div>
                                            <div class="stat-title">Pending</div>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="stat-card stat-assigned">
                                            <i class="icon-user"></i>
                                            <div class="stat-number"><?php echo $assignedWorks; ?></div>
                                            <div class="stat-title">Assigned</div>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="stat-card" style="background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%); color: white;">
                                            <i class="icon-refresh"></i>
                                            <div class="stat-number"><?php echo $inProgressWorks; ?></div>
                                            <div class="stat-title">In Progress</div>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="stat-card stat-completed">
                                            <i class="icon-ok-sign"></i>
                                            <div class="stat-number"><?php echo $completedWorks; ?></div>
                                            <div class="stat-title">Completed</div>
                                        </div>
                                    </div>
                                    <div class="span2">
                                        <div class="stat-card stat-workers">
                                            <i class="icon-briefcase"></i>
                                            <div class="stat-number"><?php echo $totalWorkers; ?></div>
                                            <div class="stat-title">Active Workers</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-fluid">
                                    <!-- Quick Actions -->
                                    <div class="span6">
                                        <div class="quick-actions">
                                            <h4><i class="icon-bolt"></i> Quick Actions</h4>
                                            <a href="notprocess-complaint.php" class="action-btn">
                                                <i class="icon-tasks"></i> Manage New Complaints
                                                <small class="pull-right" style="color: #6c757d;"><?php echo $newComplaints; ?> pending</small>
                                            </a>
                                            <a href="assign-work.php" class="action-btn">
                                                <i class="icon-plus-sign"></i> Assign New Work
                                                <small class="pull-right" style="color: #6c757d;"><?php echo $pendingWorks; ?> pending</small>
                                            </a>
                                            <a href="manage-users.php" class="action-btn">
                                                <i class="icon-group"></i> User Management
                                                <small class="pull-right" style="color: #6c757d;"><?php echo $totalUsers; ?> users</small>
                                            </a>
                                            <a href="manage-workers.php" class="action-btn">
                                                <i class="icon-briefcase"></i> Manage Workers
                                                <small class="pull-right" style="color: #6c757d;"><?php echo $totalWorkers; ?> active</small>
                                            </a>
                                            <a href="category.php" class="action-btn">
                                                <i class="icon-tags"></i> Category Setup
                                            </a>
                                            <a href="user-logs.php" class="action-btn">
                                                <i class="icon-list-alt"></i> View Activity Logs
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Recent Activities -->
                                    <div class="span6">
                                        <div class="recent-activity">
                                            <h4><i class="icon-time"></i> Recent Complaints</h4>
                                            <?php while($row = mysqli_fetch_array($recentComplaints)) {
                                                $statusClass = '';
                                                $statusText = '';
                                                if($row['status'] === null) {
                                                    $statusClass = 'status-new';
                                                    $statusText = 'New';
                                                } elseif($row['status'] == 'in process') {
                                                    $statusClass = 'status-process';
                                                    $statusText = 'In Process';
                                                } elseif($row['status'] == 'closed') {
                                                    $statusClass = 'status-closed';
                                                    $statusText = 'Closed';
                                                }
                                            ?>
                                            <div class="activity-item">
                                                <div class="activity-content">
                                                    <strong><?php echo htmlentities($row['fullName']); ?></strong>
                                                    <span class="activity-meta">
                                                        #<?php echo htmlentities($row['complaintNumber']); ?> | 
                                                        <?php echo htmlentities($row['regDate']); ?>
                                                    </span>
                                                </div>
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <div class="recent-activity">
                                            <h4><i class="icon-tasks"></i> Recent Work Assignments</h4>
                                            <?php while($work = mysqli_fetch_array($recentWorks)) {
                                                $statusClass = 'status-' . str_replace('_', '', $work['status']);
                                                $priorityClass = 'priority-' . $work['priority'];
                                            ?>
                                            <div class="activity-item">
                                                <div class="activity-content">
                                                    <strong><?php echo htmlentities($work['work_title']); ?></strong>
                                                    <span class="activity-meta">
                                                        <?php echo htmlentities($work['worker_name']); ?> | 
                                                        <?php echo date('M d, Y', strtotime($work['created_at'])); ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="priority-badge <?php echo $priorityClass; ?>">
                                                        <?php echo ucfirst($work['priority']); ?>
                                                    </span>
                                                    <span class="status-badge <?php echo $statusClass; ?>">
                                                        <?php echo str_replace('_', ' ', ucfirst($work['status'])); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Urgent Works Alert -->
                                <?php if($urgentWorks > 0): ?>
                                <div class="alert alert-danger">
                                    <h4><i class="icon-exclamation-sign"></i> Urgent Attention Required!</h4>
                                    <p>You have <strong><?php echo $urgentWorks; ?> urgent work<?php echo $urgentWorks > 1 ? 's' : ''; ?></strong> that need immediate attention.</p>
                                    <a href="urgent-works.php" class="btn btn-danger">View Urgent Works</a>
                                </div>
                                <?php endif; ?>
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
            // Auto refresh stats every 30 seconds
            setInterval(function() {
                location.reload();
            }, 30000);
            
            // Add smooth animations
            $('.stat-card').hover(
                function() {
                    $(this).css('transform', 'translateY(-5px)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                }
            );
        });
    </script>
</body>
</html>
<?php } ?>