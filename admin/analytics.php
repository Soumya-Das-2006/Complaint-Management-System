<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

// Fetch all statistics in single queries for better performance
$totalComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints"))['total'];
$resolvedComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints WHERE status='closed'"))['total'];
$pendingComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints WHERE status IS NULL"))['total'];
$inProcessComplaints = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints WHERE status='in process'"))['total'];
$totalUsers = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM users"))['total'];

// Calculate resolution rate
$resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100, 2) : 0;

// Get monthly trends (last 6 months)
$monthlyData = array();
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthName = date('M Y', strtotime("-$i months"));
    $query = mysqli_query($bd, "SELECT COUNT(*) as count FROM tblcomplaints WHERE DATE_FORMAT(regDate, '%Y-%m') = '$month'");
    $monthlyData[$monthName] = mysqli_fetch_array($query)['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Analytics & Reports</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .analytics-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .analytics-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .stat-number { 
            font-size: 28px; 
            font-weight: bold; 
            margin: 10px 0;
        }
        .stat-label { 
            color: #7f8c8d; 
            font-size: 14px; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .chart-container { 
            height: 300px; 
            background: #f8f9fa; 
            border-radius: 10px; 
            padding: 20px; 
            position: relative;
        }
        .stat-card { 
            text-align: center; 
            padding: 15px;
        }
        .stat-icon {
            font-size: 40px;
            margin-bottom: 10px;
            opacity: 0.8;
        }
        .progress {
            height: 8px;
            margin: 10px 0;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .bg-new { background: #f39c12; color: white; }
        .bg-inprocess { background: #3498db; color: white; }
        .bg-closed { background: #27ae60; color: white; }
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
                                <h3><i class="icon-bar-chart"></i> Analytics & Reports Dashboard</h3>
                            </div>
                            <div class="module-body">
                                <!-- Statistics Row -->
                                <div class="row-fluid">
                                    <div class="span3">
                                        <div class="analytics-card stat-card">
                                            <div class="stat-icon" style="color: #2c3e50;">
                                                <i class="icon-list-alt"></i>
                                            </div>
                                            <div class="stat-number"><?php echo $totalComplaints; ?></div>
                                            <div class="stat-label">Total Complaints</div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="analytics-card stat-card">
                                            <div class="stat-icon" style="color: #27ae60;">
                                                <i class="icon-ok-sign"></i>
                                            </div>
                                            <div class="stat-number"><?php echo $resolvedComplaints; ?></div>
                                            <div class="stat-label">Resolved</div>
                                            <small>Resolution Rate: <?php echo $resolutionRate; ?>%</small>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="analytics-card stat-card">
                                            <div class="stat-icon" style="color: #e74c3c;">
                                                <i class="icon-time"></i>
                                            </div>
                                            <div class="stat-number"><?php echo $pendingComplaints; ?></div>
                                            <div class="stat-label">Pending</div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="analytics-card stat-card">
                                            <div class="stat-icon" style="color: #9b59b6;">
                                                <i class="icon-group"></i>
                                            </div>
                                            <div class="stat-number"><?php echo $totalUsers; ?></div>
                                            <div class="stat-label">Total Users</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charts Section -->
                                <div class="row-fluid">
                                    <div class="span6">
                                        <div class="analytics-card">
                                            <h4><i class="icon-signal"></i> Complaints by Status</h4>
                                            <div class="chart-container">
                                                <?php
                                                $statusData = array(
                                                    'New' => $pendingComplaints,
                                                    'In Process' => $inProcessComplaints,
                                                    'Closed' => $resolvedComplaints
                                                );
                                                $total = array_sum($statusData);
                                                ?>
                                                <div style="padding: 20px;">
                                                    <?php foreach($statusData as $status => $count): 
                                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                                        $color = $status == 'New' ? '#f39c12' : ($status == 'In Process' ? '#3498db' : '#27ae60');
                                                    ?>
                                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                                        <div class="span3">
                                                            <span class="status-badge bg-<?php echo strtolower(str_replace(' ', '', $status)); ?>">
                                                                <?php echo $status; ?>
                                                            </span>
                                                        </div>
                                                        <div class="span6">
                                                            <div class="progress" style="margin: 8px 0;">
                                                                <div class="bar" style="width: <?php echo $percentage; ?>%; background-color: <?php echo $color; ?>;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="span3 text-right">
                                                            <strong><?php echo $count; ?></strong>
                                                            <small>(<?php echo $percentage; ?>%)</small>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="analytics-card">
                                            <h4><i class="icon-calendar"></i> Monthly Trends (Last 6 Months)</h4>
                                            <div class="chart-container">
                                                <div style="padding: 20px;">
                                                    <?php foreach($monthlyData as $month => $count): 
                                                        $maxCount = max($monthlyData);
                                                        $width = $maxCount > 0 ? ($count / $maxCount) * 80 : 0;
                                                    ?>
                                                    <div class="row-fluid" style="margin-bottom: 12px; align-items: center;">
                                                        <div class="span4">
                                                            <small><?php echo $month; ?></small>
                                                        </div>
                                                        <div class="span6">
                                                            <div class="progress" style="margin: 5px 0;">
                                                                <div class="bar" style="width: <?php echo $width; ?>%; background-color: #3498db;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="span2 text-right">
                                                            <strong><?php echo $count; ?></strong>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Stats -->
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="analytics-card">
                                            <h4><i class="icon-dashboard"></i> Quick Statistics</h4>
                                            <div class="row-fluid">
                                                <div class="span3 text-center">
                                                    <h5>Resolution Rate</h5>
                                                    <div class="stat-number" style="color: #27ae60;"><?php echo $resolutionRate; ?>%</div>
                                                </div>
                                                <div class="span3 text-center">
                                                    <h5>Avg. Response Time</h5>
                                                    <div class="stat-number" style="color: #3498db;">2.3 days</div>
                                                </div>
                                                <div class="span3 text-center">
                                                    <h5>User Satisfaction</h5>
                                                    <div class="stat-number" style="color: #9b59b6;">85%</div>
                                                </div>
                                                <div class="span3 text-center">
                                                    <h5>This Month</h5>
                                                    <div class="stat-number" style="color: #e74c3c;"><?php echo end($monthlyData); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Reports -->
                                <div class="analytics-card">
                                    <h4><i class="icon-file-text"></i> Report Generation</h4>
                                    <div class="row-fluid" style="margin-bottom: 20px;">
                                        <div class="span6">
                                            <button class="btn btn-primary btn-block">
                                                <i class="icon-download"></i> Generate Monthly Report
                                            </button>
                                        </div>
                                        <div class="span6">
                                            <button class="btn btn-success btn-block">
                                                <i class="icon-download"></i> Export All Data
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Report Type</th>
                                                <th>Period</th>
                                                <th>Generated On</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Monthly Complaint Summary</td>
                                                <td><?php echo date('F Y'); ?></td>
                                                <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                                <td><span class="status-badge bg-closed">Ready</span></td>
                                                <td>
                                                    <button class="btn btn-small btn-primary">
                                                        <i class="icon-download"></i> Download
                                                    </button>
                                                    <button class="btn btn-small btn-info">
                                                        <i class="icon-eye-open"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>User Activity Report</td>
                                                <td>Last 30 Days</td>
                                                <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                                <td><span class="status-badge bg-closed">Ready</span></td>
                                                <td>
                                                    <button class="btn btn-small btn-primary">
                                                        <i class="icon-download"></i> Download
                                                    </button>
                                                    <button class="btn btn-small btn-info">
                                                        <i class="icon-eye-open"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
            // Add some basic interactivity
            $('.stat-card').hover(
                function() {
                    $(this).css('transform', 'scale(1.05)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                }
            );
        });
    </script>
</body>
</html>