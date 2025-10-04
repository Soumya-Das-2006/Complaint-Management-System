<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

// Fetch pending works
$works = mysqli_query($bd, "SELECT w.*, wk.name as worker_name, wk.phone as worker_phone 
                           FROM works w 
                           LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                           WHERE w.status='pending'
                           ORDER BY w.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Pending Works</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
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
                    <h3>Pending Works</h3>
                </div>
                <div class="module-body">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Work Title</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($work = mysqli_fetch_array($works)): ?>
                            <tr>
                                <td><?php echo htmlentities($work['work_title']); ?></td>
                                <td><?php echo htmlentities($work['worker_name']); ?></td>
                                <td>
                                    <span class="label label-<?php 
                                    switch($work['priority']) {
                                        case 'urgent': echo 'important'; break;
                                        case 'high': echo 'warning'; break;
                                        case 'medium': echo 'info'; break;
                                        case 'low': echo 'success'; break;
                                    }
                                    ?>">
                                        <?php echo ucfirst($work['priority']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($work['deadline'])); ?></td>
                                <td>
                                    <a href="work-details.php?id=<?php echo $work['id']; ?>" class="btn btn-small btn-info">View</a>
                                    <a href="assign-work.php?action=resend_sms&work_id=<?php echo $work['id']; ?>" class="btn btn-small btn-warning">Assign</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
                </div><!--/.row-->
            </div><!--/.container-->
        </div><!--/.wrapper-->
    <?php include('include/footer.php');?>
</body>
</html>