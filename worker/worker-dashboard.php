<?php
session_start();
// Fix: Include config.php from same directory, not include/config.php
include('config.php');

// Check if worker is logged in
if(!isset($_SESSION['wlogin']) || strlen($_SESSION['wlogin']) == 0) {    
    header('location:worker-login.php');
    exit;
}

$worker_id = $_SESSION['wid'];

// Get worker stats
$total_works = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id'"))['total'];
$completed_works = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND status='completed'"))['total'];
$pending_works = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND status IN ('pending', 'assigned', 'in_progress')"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard - CMS</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">
    <link href="images/icons/css/font-awesome.css" rel="stylesheet">
</head>
<body>
    <?php include('include/worker-header.php');?>
    
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/worker-sidebar.php');?>
                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Worker Dashboard</h3>
                            </div>
                            <div class="module-body">
                                <div class="alert alert-success">
                                    <h4>Welcome, <?php echo htmlentities($_SESSION['wname']); ?>!</h4>
                                    <p>You have successfully logged in to the worker panel.</p>
                                </div>
                                
                                <div class="row-fluid">
                                    <div class="span4">
                                        <div class="well" style="text-align: center;">
                                            <i class="icon-tasks icon-3x"></i>
                                            <h3><?php echo $total_works; ?></h3>
                                            <p>Total Works</p>
                                            <a href="worker-my-works.php" class="btn btn-primary">View Works</a>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="well" style="text-align: center;">
                                            <i class="icon-ok icon-3x" style="color: green;"></i>
                                            <h3><?php echo $completed_works; ?></h3>
                                            <p>Completed Works</p>
                                            <a href="worker-completed-works.php" class="btn btn-success">View Completed</a>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="well" style="text-align: center;">
                                            <i class="icon-time icon-3x" style="color: orange;"></i>
                                            <h3><?php echo $pending_works; ?></h3>
                                            <p>Pending Works</p>
                                            <a href="worker-my-works.php" class="btn btn-warning">View Pending</a>
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

    <script src="scripts/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>