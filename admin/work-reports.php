<?php ?>
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

// Generate work reports
$totalWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works"))['total'];
$completedWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='completed'"))['total'];
$pendingWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='pending'"))['total'];
$inProgressWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='in_progress'"))['total'];

// Department-wise distribution
$deptWorks = mysqli_query($bd, "SELECT wk.department, COUNT(*) as count 
                               FROM works w 
                               LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                               GROUP BY wk.department");
?>
<?php include('include/header.php');?>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="row">
               
<?php include('include/sidebar.php');?>
                

<div class="span9">
    <div class="content">
        <div class="module">
            <div class="module-head">
                <h3>Work Reports & Analytics</h3>
            </div>
            <div class="module-body">
                <div class="row-fluid">
                    <div class="span6">
                        <div class="alert alert-info">
                            <h4>Work Statistics</h4>
                            <p>Total Works: <strong><?php echo $totalWorks; ?></strong></p>
                            <p>Completed: <strong><?php echo $completedWorks; ?></strong></p>
                            <p>Pending: <strong><?php echo $pendingWorks; ?></strong></p>
                            <p>In Progress: <strong><?php echo $inProgressWorks; ?></strong></p>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="alert alert-success">
                            <h4>Department-wise Distribution</h4>
                            <?php while($dept = mysqli_fetch_array($deptWorks)): ?>
                            <p><?php echo $dept['department']; ?>: <strong><?php echo $dept['count']; ?></strong></p>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            </div><!--/.row-->
        </div><!--/.container-->
    </div><!--/.wrapper-->
<?php include('include/footer.php');?>