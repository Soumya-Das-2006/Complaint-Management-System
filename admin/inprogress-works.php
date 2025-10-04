<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

// Fetch in-progress works
$works = mysqli_query($bd, "SELECT w.*, wk.name as worker_name, wk.phone as worker_phone, wk.department as worker_department
                           FROM works w 
                           LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                           WHERE w.status='in_progress'
                           ORDER BY 
                            CASE w.priority 
                                WHEN 'urgent' THEN 1
                                WHEN 'high' THEN 2 
                                WHEN 'medium' THEN 3
                                WHEN 'low' THEN 4
                            END,
                            w.deadline ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | In Progress Works</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .stats-card { 
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-inprogress { background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%); }
        .work-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #fd7e14;
        }
        .work-card.urgent { border-left-color: #e74c3c; }
        .work-card.high { border-left-color: #e67e22; }
        .work-card.medium { border-left-color: #f39c12; }
        .work-card.low { border-left-color: #27ae60; }
        .priority-badge { 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 11px; 
            font-weight: bold;
            color: white;
        }
        .priority-urgent { background: #e74c3c; }
        .priority-high { background: #e67e22; }
        .priority-medium { background: #f39c12; }
        .priority-low { background: #27ae60; }
        .status-badge { 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 11px; 
            font-weight: bold;
        }
        .status-in_progress { background: #fff3cd; color: #856404; }
        .progress { margin: 10px 0; }
    </style>
</head>
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
                    <h3><i class="icon-refresh"></i> In Progress Works</h3>
                </div>
                <div class="module-body">
                    <?php
                    // Count in-progress works
                    $inprogressCount = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='in_progress'"))['total'];
                    ?>
                    
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="stats-card stats-inprogress">
                                <h4>In Progress Works</h4>
                                <h2><?php echo $inprogressCount; ?></h2>
                                <small>Currently being worked on</small>
                            </div>
                        </div>
                    </div>

                    <?php if(mysqli_num_rows($works) > 0): ?>
                        <?php while($work = mysqli_fetch_array($works)): 
                            $priorityClass = $work['priority'];
                            $daysLeft = floor((strtotime($work['deadline']) - time()) / (60 * 60 * 24));
                            // Simulate progress (you can replace this with actual progress tracking)
                            $progress = rand(20, 90);
                        ?>
                        <div class="work-card <?php echo $priorityClass; ?>">
                            <div class="row-fluid">
                                <div class="span9">
                                    <h5>
                                        <strong><?php echo htmlentities($work['work_title']); ?></strong>
                                        <span class="priority-badge priority-<?php echo $work['priority']; ?>">
                                            <?php echo strtoupper($work['priority']); ?>
                                        </span>
                                        <span class="status-badge status-<?php echo $work['status']; ?>">
                                            <?php echo str_replace('_', ' ', ucfirst($work['status'])); ?>
                                        </span>
                                    </h5>
                                    <p><strong>Assigned to:</strong> <?php echo htmlentities($work['worker_name']); ?> 
                                       (<?php echo htmlentities($work['worker_department']); ?>)</p>
                                    <p><strong>Contact:</strong> <?php echo htmlentities($work['worker_phone']); ?></p>
                                    <p><strong>Place:</strong> <?php echo htmlentities($work['place_address']); ?></p>
                                    <p><strong>Deadline:</strong> 
                                        <?php echo date('M d, Y', strtotime($work['deadline'])); ?>
                                        <?php if($daysLeft < 3 && $daysLeft >= 0): ?>
                                        <span class="label label-important">Only <?php echo $daysLeft; ?> days left!</span>
                                        <?php elseif($daysLeft < 0): ?>
                                        <span class="label label-important">Overdue by <?php echo abs($daysLeft); ?> days!</span>
                                        <?php endif; ?>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="bar" style="width: <?php echo $progress; ?>%;">
                                            <?php echo $progress; ?>% Complete
                                        </div>
                                    </div>
                                    <p><strong>Description:</strong> <?php echo htmlentities(substr($work['work_description'], 0, 150)); ?>...</p>
                                </div>
                                <div class="span3 text-right">
                                    <div class="action-buttons">
                                        <a href="work-details.php?id=<?php echo $work['id']; ?>" 
                                           class="btn btn-small btn-info btn-block">
                                            <i class="icon-eye-open"></i> View Details
                                        </a>
                                        <a href="work-details.php?id=<?php echo $work['id']; ?>&action=complete" 
                                           class="btn btn-small btn-success btn-block">
                                            <i class="icon-ok"></i> Mark Complete
                                        </a>
                                        <a href="assign-work.php?action=resend_sms&work_id=<?php echo $work['id']; ?>" 
                                           class="btn btn-small btn-primary btn-block"
                                           onclick="return confirm('Send progress update to worker?')">
                                            <i class="icon-phone"></i> Send Update
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="icon-info-sign"></i> No works in progress at the moment.
                        </div>
                    <?php endif; ?>
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