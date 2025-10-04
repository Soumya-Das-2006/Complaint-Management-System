<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

// Fetch worker performance
$performance = mysqli_query($bd, "SELECT wk.name, wk.department, 
                                 COUNT(w.id) as total_works,
                                 SUM(CASE WHEN w.status='completed' THEN 1 ELSE 0 END) as completed_works
                                 FROM workers wk 
                                 LEFT JOIN works w ON wk.id = w.assigned_worker_id 
                                 WHERE wk.status='active'
                                 GROUP BY wk.id, wk.name, wk.department");
?>
<?php include('include/header.php');?>
    <div class="wrapper">
        <div class="container">
            <div class="row">
<?php include('include/sidebar.php');?>

<div class="span9">
    <div class="content">
        <div class="module">
            <div class="module-head">
                <h3>Worker Performance</h3>
            </div>
            <div class="module-body">
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Worker Name</th>
                            <th>Department</th>
                            <th>Total Works</th>
                            <th>Completed Works</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($worker = mysqli_fetch_array($performance)): 
                            $completionRate = $worker['total_works'] > 0 ? 
                                round(($worker['completed_works'] / $worker['total_works']) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlentities($worker['name']); ?></td>
                            <td><?php echo htmlentities($worker['department']); ?></td>
                            <td><?php echo $worker['total_works']; ?></td>
                            <td><?php echo $worker['completed_works']; ?></td>
                            <td>
                                <span class="label label-<?php 
                                if($completionRate >= 80) echo 'success';
                                elseif($completionRate >= 60) echo 'warning';
                                else echo 'important';
                                ?>">
                                    <?php echo $completionRate; ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php');?>