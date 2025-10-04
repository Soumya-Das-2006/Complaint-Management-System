<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['wlogin'])==0) {    
    header('location:index.php');
    exit;
}

$worker_id = $_SESSION['wid'];

// Fetch completed works
$completed_works = mysqli_query($bd, "SELECT * FROM works 
                                    WHERE assigned_worker_id='$worker_id' AND status='completed' 
                                    ORDER BY updated_at DESC");

// Statistics
$total_completed = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND status='completed'"))['total'];
$avg_completion_time = mysqli_fetch_array(mysqli_query($bd, "SELECT AVG(DATEDIFF(updated_at, assigned_date)) as avg_days FROM works WHERE assigned_worker_id='$worker_id' AND status='completed'"))['avg_days'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Works - Worker Panel</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin: 10px 0;
        }
        .completion-badge {
            background: #27ae60;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <?php include('../include/worker-header.php');?>
    
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('../include/worker-sidebar.php');?>
                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-ok" style="color: #27ae60;"></i> Completed Works History</h3>
                            </div>
                            <div class="module-body">
                                <!-- Statistics -->
                                <div class="row-fluid">
                                    <div class="span6">
                                        <div class="stats-card">
                                            <i class="icon-ok-circle icon-3x"></i>
                                            <div class="stat-number"><?php echo $total_completed; ?></div>
                                            <h4>Total Completed Works</h4>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="stats-card" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                                            <i class="icon-time icon-3x"></i>
                                            <div class="stat-number"><?php echo round($avg_completion_time, 1); ?></div>
                                            <h4>Average Completion Days</h4>
                                        </div>
                                    </div>
                                </div>

                                <!-- Completed Works Table -->
                                <?php if(mysqli_num_rows($completed_works) > 0): ?>
                                <table class="table table-striped table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Work Title</th>
                                            <th>Priority</th>
                                            <th>Assigned Date</th>
                                            <th>Completed Date</th>
                                            <th>Completion Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($work = mysqli_fetch_array($completed_works)): 
                                            $completion_days = floor((strtotime($work['updated_at']) - strtotime($work['assigned_date'])) / (60*60*24));
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlentities($work['work_title']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="label label-<?php 
                                                switch($work['priority']) {
                                                    case 'urgent': echo 'important'; break;
                                                    case 'high': echo 'warning'; break;
                                                    case 'medium': echo 'info'; break;
                                                    default: echo 'success';
                                                }
                                                ?>">
                                                    <?php echo ucfirst($work['priority']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($work['assigned_date'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($work['updated_at'])); ?></td>
                                            <td>
                                                <span class="completion-badge">
                                                    <?php echo $completion_days; ?> day<?php echo $completion_days != 1 ? 's' : ''; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="worker-work-details.php?id=<?php echo $work['id']; ?>" class="btn btn-small btn-primary">
                                                    <i class="icon-eye-open"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <strong>No completed works yet!</strong> 
                                    Complete your assigned works to see them here.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('../include/footer.php');?>
    
    <script src="../scripts/jquery-1.9.1.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>