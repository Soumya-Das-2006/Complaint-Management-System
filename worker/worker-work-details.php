<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['wlogin'])==0) {    
    header('location:index.php');
    exit;
}

if(!isset($_GET['id'])) {
    header('location:worker-my-works.php');
    exit;
}

$work_id = intval($_GET['id']);
$worker_id = $_SESSION['wid'];

// Fetch work details
$work = mysqli_fetch_array(mysqli_query($bd, "SELECT w.*, wk.name as worker_name 
                                             FROM works w 
                                             LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                                             WHERE w.id='$work_id' AND w.assigned_worker_id='$worker_id'"));

if(!$work) {
    $_SESSION['errmsg'] = "Work not found or access denied!";
    header('location:worker-my-works.php');
    exit;
}

// Fetch work updates
$updates = mysqli_query($bd, "SELECT wu.*, 
                             CASE 
                                 WHEN wu.created_by = '$worker_id' THEN 'You'
                                 ELSE 'Admin'
                             END as creator
                             FROM work_updates wu 
                             WHERE wu.work_id='$work_id' 
                             ORDER BY wu.created_at DESC");

// Handle status update
if(isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($bd, $_POST['status']);
    $update_notes = mysqli_real_escape_string($bd, $_POST['update_notes']);
    
    $update = mysqli_query($bd, "UPDATE works SET status='$new_status', updated_at=NOW() WHERE id='$work_id'");
    
    if($update) {
        // Log the status update
        $update_text = "Status changed to: " . str_replace('_', ' ', $new_status);
        if(!empty($update_notes)) {
            $update_text .= ". Notes: " . $update_notes;
        }
        
        mysqli_query($bd, "INSERT INTO work_updates (work_id, update_text, update_type, created_by) 
                          VALUES ('$work_id', '$update_text', 'progress', '$worker_id')");
        
        $_SESSION['msg'] = "Work status updated successfully!";
        
        // Send notification to admin (you can implement this)
        // mysqli_query($bd, "INSERT INTO notifications (to_user, title, message, type) 
        //                   VALUES (1, 'Work Status Updated', 'Worker {$_SESSION['wname']} updated work status to {$new_status}', 'user_to_admin')");
        
    } else {
        $_SESSION['errmsg'] = "Failed to update work status!";
    }
    header("location:worker-work-details.php?id=$work_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Details - Worker Panel</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .work-detail-card { 
            background: white; 
            border-radius: 10px; 
            padding: 25px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .update-item {
            padding: 15px;
            border-left: 4px solid #3498db;
            background: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 0 8px 8px 0;
            position: relative;
        }
        .update-item.assignment { border-left-color: #27ae60; }
        .update-item.completion { border-left-color: #f39c12; }
        .update-item.issue { border-left-color: #e74c3c; }
        .update-item.worker { border-left-color: #3498db; }
        .update-meta {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        .priority-indicator {
            width: 8px;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 8px 0 0 8px;
        }
        .priority-urgent { background: #e74c3c; }
        .priority-high { background: #e67e22; }
        .priority-medium { background: #f39c12; }
        .priority-low { background: #27ae60; }
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
                                <h3>
                                    <i class="icon-tasks"></i> Work Details: 
                                    <?php echo htmlentities($work['work_title']); ?>
                                </h3>
                            </div>
                            <div class="module-body">
                                <!-- Display messages -->
                                <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <i class="icon-ok"></i> <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php } ?>
                                
                                <?php if(isset($_SESSION['errmsg'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <i class="icon-warning-sign"></i> <?php echo htmlentities($_SESSION['errmsg']); $_SESSION['errmsg']=""; ?>
                                </div>
                                <?php } ?>
                                
                                <!-- Work Details Card -->
                                <div class="work-detail-card">
                                    <div class="priority-indicator priority-<?php echo $work['priority']; ?>"></div>
                                    <div style="margin-left: 15px;">
                                        <div class="row-fluid">
                                            <div class="span8">
                                                <h4><?php echo htmlentities($work['work_title']); ?></h4>
                                                
                                                <div class="row-fluid" style="margin-bottom: 20px;">
                                                    <div class="span6">
                                                        <p><strong>Current Status:</strong> 
                                                            <span class="label label-<?php 
                                                            switch($work['status']) {
                                                                case 'completed': echo 'success'; break;
                                                                case 'in_progress': echo 'info'; break;
                                                                case 'assigned': echo 'warning'; break;
                                                                default: echo 'important';
                                                            }
                                                            ?>">
                                                                <?php echo str_replace('_', ' ', ucfirst($work['status'])); ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="span6">
                                                        <p><strong>Priority:</strong> 
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
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <p><strong>📍 Place/Address:</strong><br>
                                                    <?php echo nl2br(htmlentities($work['place_address'])); ?>
                                                </p>
                                                
                                                <p><strong>📝 Work Description:</strong><br>
                                                    <?php echo nl2br(htmlentities($work['work_description'])); ?>
                                                </p>
                                                
                                                <div class="row-fluid">
                                                    <div class="span6">
                                                        <p><strong>📅 Deadline:</strong><br>
                                                            <?php echo date('F d, Y', strtotime($work['deadline'])); ?>
                                                            <?php if(strtotime($work['deadline']) < time() && $work['status'] != 'completed'): ?>
                                                            <br><span class="label label-important">OVERDUE</span>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                    <div class="span6">
                                                        <p><strong>📋 Assigned On:</strong><br>
                                                            <?php echo date('F d, Y h:i A', strtotime($work['assigned_date'])); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <?php if(!empty($work['admin_notes'])): ?>
                                                <p><strong>💡 Admin Notes:</strong><br>
                                                    <em><?php echo nl2br(htmlentities($work['admin_notes'])); ?></em>
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="span4">
                                                <!-- Status Update Form -->
                                                <form method="post" class="well" style="background: #f8f9fa;">
                                                    <h5><i class="icon-edit"></i> Update Work Status</h5>
                                                    <div class="control-group">
                                                        <label><strong>New Status:</strong></label>
                                                        <select name="status" class="span12" required>
                                                            <option value="assigned" <?php echo $work['status']=='assigned'?'selected':''; ?>>Assigned</option>
                                                            <option value="in_progress" <?php echo $work['status']=='in_progress'?'selected':''; ?>>In Progress</option>
                                                            <option value="completed" <?php echo $work['status']=='completed'?'selected':''; ?>>Completed</option>
                                                        </select>
                                                    </div>
                                                    <div class="control-group">
                                                        <label><strong>Update Notes:</strong></label>
                                                        <textarea name="update_notes" class="span12" rows="4" required 
                                                                  placeholder="Describe your progress, completion details, or any issues faced..."></textarea>
                                                    </div>
                                                    <div class="control-group">
                                                        <button type="submit" name="update_status" class="btn btn-success btn-block">
                                                            <i class="icon-ok"></i> Update Status
                                                        </button>
                                                    </div>
                                                </form>
                                                
                                                <!-- Quick Actions -->
                                                <div class="well" style="background: #fff3cd;">
                                                    <h5><i class="icon-bolt"></i> Quick Actions</h5>
                                                    <a href="worker-my-works.php" class="btn btn-info btn-block">
                                                        <i class="icon-arrow-left"></i> Back to My Works
                                                    </a>
                                                    <?php if($work['status'] != 'completed'): ?>
                                                    <a href="#update-form" class="btn btn-warning btn-block">
                                                        <i class="icon-edit"></i> Jump to Update Form
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Work Updates Timeline -->
                                <div class="work-detail-card">
                                    <h4><i class="icon-time"></i> Work Updates Timeline</h4>
                                    <?php if(mysqli_num_rows($updates) > 0): ?>
                                        <?php while($update = mysqli_fetch_array($updates)): 
                                            $updateClass = 'update-item worker';
                                            if(strpos($update['update_text'], 'assigned') !== false) $updateClass .= ' assignment';
                                            if(strpos($update['update_text'], 'completed') !== false) $updateClass .= ' completion';
                                            if(strpos($update['update_text'], 'issue') !== false) $updateClass .= ' issue';
                                        ?>
                                        <div class="<?php echo $updateClass; ?>">
                                            <div class="update-meta">
                                                <i class="icon-user"></i> <?php echo $update['creator']; ?> | 
                                                <i class="icon-time"></i> <?php echo date('M d, Y h:i A', strtotime($update['created_at'])); ?>
                                            </div>
                                            <?php echo htmlentities($update['update_text']); ?>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="icon-info-sign"></i> No updates recorded yet. Start by updating the work status.
                                        </div>
                                    <?php endif; ?>
                                </div>
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
    <script>
        // Smooth scroll to update form
        $('a[href="#update-form"]').click(function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('form.well').offset().top - 20
            }, 500);
        });
    </script>
</body>
</html>