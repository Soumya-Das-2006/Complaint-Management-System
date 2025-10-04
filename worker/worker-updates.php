<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['wlogin'])==0) {    
    header('location:index.php');
    exit;
}

$worker_id = $_SESSION['wid'];

// Fetch all updates for worker's works
$updates = mysqli_query($bd, "SELECT wu.*, w.work_title, w.work_title,
                             CASE 
                                 WHEN wu.created_by = '$worker_id' THEN 'You'
                                 ELSE 'Admin'
                             END as creator
                             FROM work_updates wu 
                             JOIN works w ON wu.work_id = w.id 
                             WHERE w.assigned_worker_id = '$worker_id' 
                             ORDER BY wu.created_at DESC 
                             LIMIT 50");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Updates - Worker Panel</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .update-timeline {
            position: relative;
            padding-left: 30px;
        }
        .update-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #3498db;
        }
        .update-item {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            border-left: 4px solid #3498db;
        }
        .update-item::before {
            content: '';
            position: absolute;
            left: -27px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #3498db;
            border: 3px solid white;
        }
        .update-item.worker { border-left-color: #27ae60; }
        .update-item.admin { border-left-color: #e67e22; }
        .work-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .update-meta {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 10px;
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
                                <h3><i class="icon-time"></i> Recent Work Updates</h3>
                            </div>
                            <div class="module-body">
                                <?php if(mysqli_num_rows($updates) > 0): ?>
                                <div class="update-timeline">
                                    <?php while($update = mysqli_fetch_array($updates)): 
                                        $item_class = $update['creator'] == 'You' ? 'worker' : 'admin';
                                    ?>
                                    <div class="update-item <?php echo $item_class; ?>">
                                        <div class="work-title">
                                            <i class="icon-tasks"></i> <?php echo htmlentities($update['work_title']); ?>
                                        </div>
                                        <div class="update-meta">
                                            <i class="icon-user"></i> <?php echo $update['creator']; ?> | 
                                            <i class="icon-time"></i> <?php echo date('M d, Y h:i A', strtotime($update['created_at'])); ?>
                                        </div>
                                        <div class="update-text">
                                            <?php echo htmlentities($update['update_text']); ?>
                                        </div>
                                        <?php if($update['creator'] == 'You'): ?>
                                        <div style="margin-top: 10px;">
                                            <a href="worker-work-details.php?id=<?php echo $update['work_id']; ?>" class="btn btn-small btn-primary">
                                                View Work
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="icon-info-sign"></i> No work updates found. 
                                    Start working on your assigned tasks to see updates here.
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