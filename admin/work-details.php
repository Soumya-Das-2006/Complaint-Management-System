<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

if(!isset($_GET['id'])) {
    header('location:assign-work.php');
    exit;
}

$work_id = intval($_GET['id']);

// Fetch work details
$work = mysqli_fetch_array(mysqli_query($bd, "SELECT w.*, wk.name as worker_name, wk.phone as worker_phone, 
                                             wk.email as worker_email, wk.department as worker_department 
                                             FROM works w 
                                             LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                                             WHERE w.id='$work_id'"));

if(!$work) {
    $_SESSION['errmsg'] = "Work not found!";
    header('location:assign-work.php');
    exit;
}

// Fetch work updates
$updates = mysqli_query($bd, "SELECT * FROM work_updates WHERE work_id='$work_id' ORDER BY created_at DESC");

// Handle status update
if(isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($bd, $_POST['status']);
    $update_notes = mysqli_real_escape_string($bd, $_POST['update_notes']);
    
    $update = mysqli_query($bd, "UPDATE works SET status='$new_status' WHERE id='$work_id'");
    
    if($update) {
        // Log the status update
        mysqli_query($bd, "INSERT INTO work_updates (work_id, update_text, update_type, created_by) 
                          VALUES ('$work_id', 'Status changed to: $new_status. Notes: $update_notes', 'progress', '{$_SESSION['id']}')");
        
        $_SESSION['msg'] = "Work status updated successfully!";
    } else {
        $_SESSION['errmsg'] = "Failed to update work status!";
    }
    header("location:work-details.php?id=$work_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Work Details</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .work-detail-card { 
            background: white; 
            border-radius: 10px; 
            padding: 25px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .priority-badge, .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-urgent { background: #e74c3c; color: white; }
        .priority-high { background: #e67e22; color: white; }
        .priority-medium { background: #f39c12; color: white; }
        .priority-low { background: #27ae60; color: white; }
        .status-pending { background: #f8d7da; color: #721c24; }
        .status-assigned { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #cce7ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f5f5f5; color: #6c757d; }
        .update-item {
            padding: 15px;
            border-left: 4px solid #007bff;
            background: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 0 5px 5px 0;
        }
        .update-item.assignment { border-left-color: #28a745; }
        .update-item.completion { border-left-color: #ffc107; }
        .update-item.issue { border-left-color: #dc3545; }
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
                                    <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php } ?>
                                
                                <!-- Work Details -->
                                <div class="work-detail-card">
                                    <div class="row-fluid">
                                        <div class="span8">
                                            <h4><?php echo htmlentities($work['work_title']); ?></h4>
                                            <div class="row-fluid" style="margin-bottom: 15px;">
                                                <div class="span6">
                                                    <p><strong>Status:</strong> 
                                                        <span class="status-badge status-<?php echo $work['status']; ?>">
                                                            <?php echo str_replace('_', ' ', ucfirst($work['status'])); ?>
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="span6">
                                                    <p><strong>Priority:</strong> 
                                                        <span class="priority-badge priority-<?php echo $work['priority']; ?>">
                                                            <?php echo strtoupper($work['priority']); ?>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <p><strong>Assigned To:</strong> 
                                                <?php echo htmlentities($work['worker_name']); ?> 
                                                (<?php echo $work['worker_department']; ?>)
                                            </p>
                                            <p><strong>Worker Contact:</strong> 
                                                <?php echo $work['worker_phone']; ?> | 
                                                <?php echo $work['worker_email']; ?>
                                            </p>
                                            <p><strong>Place/Address:</strong><br>
                                                <?php echo nl2br(htmlentities($work['place_address'])); ?>
                                            </p>
                                            <p><strong>Work Description:</strong><br>
                                                <?php echo nl2br(htmlentities($work['work_description'])); ?>
                                            </p>
                                            <p><strong>Deadline:</strong> 
                                                <?php echo date('M d, Y', strtotime($work['deadline'])); ?>
                                            </p>
                                            <?php if(!empty($work['admin_notes'])): ?>
                                            <p><strong>Admin Notes:</strong><br>
                                                <?php echo nl2br(htmlentities($work['admin_notes'])); ?>
                                            </p>
                                            <?php endif; ?>
                                            <p><strong>Assigned On:</strong> 
                                                <?php echo date('M d, Y h:i A', strtotime($work['assigned_date'])); ?>
                                            </p>
                                        </div>
                                        <div class="span4">
                                            <!-- Status Update Form -->
                                            <form method="post" class="well">
                                                <h5>Update Status</h5>
                                                <div class="control-group">
                                                    <label>New Status:</label>
                                                    <select name="status" class="span12" required>
                                                        <option value="pending" <?php echo $work['status']=='pending'?'selected':''; ?>>Pending</option>
                                                        <option value="assigned" <?php echo $work['status']=='assigned'?'selected':''; ?>>Assigned</option>
                                                        <option value="in_progress" <?php echo $work['status']=='in_progress'?'selected':''; ?>>In Progress</option>
                                                        <option value="completed" <?php echo $work['status']=='completed'?'selected':''; ?>>Completed</option>
                                                        <option value="cancelled" <?php echo $work['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                                                    </select>
                                                </div>
                                                <div class="control-group">
                                                    <label>Update Notes:</label>
                                                    <textarea name="update_notes" class="span12" rows="3" 
                                                              placeholder="Add update notes..."></textarea>
                                                </div>
                                                <button type="submit" name="update_status" class="btn btn-primary btn-block">
                                                    Update Status
                                                </button>
                                            </form>
                                            
                                            <!-- Quick Actions -->
                                            <div class="well">
                                                <h5>Quick Actions</h5>
                                                <a href="assign-work.php?action=resend_sms&work_id=<?php echo $work['id']; ?>" 
                                                   class="btn btn-warning btn-block">
                                                    <i class="icon-refresh"></i> Resend SMS
                                                </a>
                                                <a href="manage-works.php" class="btn btn-info btn-block">
                                                    <i class="icon-list"></i> Back to Works
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Work Updates Timeline -->
                                <div class="work-detail-card">
                                    <h4><i class="icon-time"></i> Work Updates Timeline</h4>
                                    <?php if(mysqli_num_rows($updates) > 0): ?>
                                        <?php while($update = mysqli_fetch_array($updates)): 
                                            $updateClass = 'update-item ' . $update['update_type'];
                                        ?>
                                        <div class="<?php echo $updateClass; ?>">
                                            <strong><?php echo date('M d, Y h:i A', strtotime($update['created_at'])); ?></strong><br>
                                            <?php echo htmlentities($update['update_text']); ?>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="alert alert-warning">No updates yet.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('include/footer.php');?>
</body>
</html>
