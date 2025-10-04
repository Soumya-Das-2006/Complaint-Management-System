<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('Y-m-d H:i:s');

// SMS Configuration
define('SMS_API_KEY', 'your_fast2sms_api_key_here');
define('SMS_SENDER_ID', 'FSTSMS');

// Function to send SMS
function sendSMS($phone, $message, $worker_id = null, $work_id = null) {
    global $bd;
    
    // Clean phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Log SMS (for demo - replace with actual SMS gateway)
    $log_sql = mysqli_query($bd, "INSERT INTO sms_logs (recipient_phone, message, worker_id, work_id, status) 
                                 VALUES ('$phone', '".mysqli_real_escape_string($bd, $message)."', 
                                         '$worker_id', '$work_id', 'sent')");
    
    // Simulate SMS sending
    $api_success = true;
    
    if($api_success) {
        return true;
    } else {
        mysqli_query($bd, "UPDATE sms_logs SET status='failed' WHERE id='".mysqli_insert_id($bd)."'");
        return false;
    }
}

// Handle work assignment
if(isset($_POST['assign_work'])) {
    $work_title = mysqli_real_escape_string($bd, $_POST['work_title']);
    $work_description = mysqli_real_escape_string($bd, $_POST['work_description']);
    $place_address = mysqli_real_escape_string($bd, $_POST['place_address']);
    $assigned_worker_id = intval($_POST['assigned_worker_id']);
    $priority = mysqli_real_escape_string($bd, $_POST['priority']);
    $deadline = mysqli_real_escape_string($bd, $_POST['deadline']);
    $admin_notes = mysqli_real_escape_string($bd, $_POST['admin_notes']);
    
    // Insert work assignment
    $sql = mysqli_query($bd, "INSERT INTO works (work_title, work_description, place_address, assigned_worker_id, 
                               priority, status, deadline, admin_notes) 
                             VALUES ('$work_title', '$work_description', '$place_address', '$assigned_worker_id',
                               '$priority', 'assigned', '$deadline', '$admin_notes')");
    
    if($sql) {
        $work_id = mysqli_insert_id($bd);
        
        // Get worker details for SMS
        $worker = mysqli_fetch_array(mysqli_query($bd, "SELECT name, phone FROM workers WHERE id='$assigned_worker_id'"));
        
        // Create SMS message
        $sms_message = "NEW WORK ASSIGNED\n";
        $sms_message .= "Title: $work_title\n";
        $sms_message .= "Place: $place_address\n";
        $sms_message .= "Priority: " . strtoupper($priority) . "\n";
        $sms_message .= "Deadline: $deadline\n";
        $sms_message .= "Details: " . substr($work_description, 0, 100) . "...\n";
        $sms_message .= "Please check your dashboard for complete details.";
        
        // Send SMS to worker
        $sms_sent = sendSMS($worker['phone'], $sms_message, $assigned_worker_id, $work_id);
        
        // Log work assignment
        mysqli_query($bd, "INSERT INTO work_updates (work_id, update_text, update_type, created_by) 
                          VALUES ('$work_id', 'Work assigned to {$worker['name']}', 'assignment', '{$_SESSION['id']}')");
        
        if($sms_sent) {
            $_SESSION['msg'] = "Work assigned successfully and SMS sent to worker!";
        } else {
            $_SESSION['msg'] = "Work assigned but SMS failed to send!";
        }
    } else {
        $_SESSION['errmsg'] = "Failed to assign work: " . mysqli_error($bd);
    }
    header("location:assign-work.php");
    exit;
}

// Handle resend SMS
if(isset($_GET['action']) && $_GET['action'] == 'resend_sms' && isset($_GET['work_id'])) {
    $work_id = intval($_GET['work_id']);
    $work = mysqli_fetch_array(mysqli_query($bd, "SELECT w.*, wk.name, wk.phone FROM works w LEFT JOIN workers wk ON w.assigned_worker_id = wk.id WHERE w.id='$work_id'"));
    
    if($work) {
        $sms_message = "WORK REMINDER\n";
        $sms_message .= "Title: {$work['work_title']}\n";
        $sms_message .= "Place: {$work['place_address']}\n";
        $sms_message .= "Priority: " . strtoupper($work['priority']) . "\n";
        $sms_message .= "Deadline: {$work['deadline']}\n";
        $sms_message .= "Please complete this work as soon as possible.";
        
        $sms_sent = sendSMS($work['phone'], $sms_message, $work['assigned_worker_id'], $work_id);
        
        if($sms_sent) {
            $_SESSION['msg'] = "SMS resent successfully!";
        } else {
            $_SESSION['errmsg'] = "Failed to resend SMS!";
        }
    }
    header("location:assign-work.php");
    exit;
}

// Fetch active workers
$workers = mysqli_query($bd, "SELECT * FROM workers WHERE status='active' ORDER BY name");

// Fetch statistics
$totalWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works"))['total'];
$pendingWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='pending'"))['total'];
$assignedWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='assigned'"))['total'];
$completedWorks = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE status='completed'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Assign Work</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .work-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }
        .work-card.urgent { border-left-color: #e74c3c; }
        .work-card.high { border-left-color: #e67e22; }
        .work-card.medium { border-left-color: #f39c12; }
        .work-card.low { border-left-color: #27ae60; }
        .stats-card { 
            color: white; 
            padding: 15px; 
            border-radius: 10px; 
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stats-pending { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); }
        .stats-assigned { background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%); }
        .stats-completed { background: linear-gradient(135deg, #27ae60 0%, #219653 100%); }
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
        .status-pending { background: #f8d7da; color: #721c24; }
        .status-assigned { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #cce7ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .sms-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            font-family: monospace;
            white-space: pre-wrap;
            display: none;
        }
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
                        <!-- Statistics Cards -->
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-tasks"></i> Work Assignment Dashboard</h3>
                            </div>
                            <div class="module-body">
                                <div class="row-fluid">
                                    <div class="span3">
                                        <div class="stats-card stats-total">
                                            <h4>Total Works</h4>
                                            <h2><?php echo $totalWorks; ?></h2>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-pending">
                                            <h4>Pending</h4>
                                            <h2><?php echo $pendingWorks; ?></h2>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-assigned">
                                            <h4>Assigned</h4>
                                            <h2><?php echo $assignedWorks; ?></h2>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-completed">
                                            <h4>Completed</h4>
                                            <h2><?php echo $completedWorks; ?></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assign Work Form -->
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-plus-sign"></i> Assign New Work</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php } ?>
                                <?php if(isset($_SESSION['errmsg'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['errmsg']); $_SESSION['errmsg']=""; ?>
                                </div>
                                <?php } ?>

                                <form class="form-horizontal" method="post" id="assignWorkForm">
                                    <div class="control-group">
                                        <label class="control-label">Work Title</label>
                                        <div class="controls">
                                            <input type="text" style="width: 500px" name="work_title" class="span8" required 
                                                   placeholder="Enter work title">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Work Description</label>
                                        <div class="controls">
                                            <textarea name="work_description" class="span8" style="width: 500px" rows="4" required 
                                                      placeholder="Detailed description of the work"></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Place/Address</label>
                                        <div class="controls">
                                            <textarea name="place_address" class="span8" style="width: 500px" rows="3" required 
                                                      placeholder="Full address where work needs to be done"></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Assign to Worker</label>
                                        <div class="controls">
                                            <select name="assigned_worker_id" class="span8" style="width: 500px" required id="workerSelect">
                                                <option value="">Select Worker</option>
                                                <?php while($worker = mysqli_fetch_array($workers)) { ?>
                                                <option value="<?php echo $worker['id']; ?>" 
                                                        data-phone="<?php echo $worker['phone']; ?>"
                                                        data-department="<?php echo $worker['department']; ?>">
                                                    <?php echo htmlentities($worker['name']); ?> 
                                                    (<?php echo $worker['department']; ?>)
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Priority</label>
                                        <div class="controls">
                                            <select name="priority" class="span8" style="width: 500px" required id="prioritySelect">
                                                <option value="low">Low</option>
                                                <option value="medium" selected>Medium</option>
                                                <option value="high">High</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Deadline</label>
                                        <div class="controls">
                                            <input type="date" name="deadline" class="span8" style="width: 500px" required 
                                                   min="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Admin Notes</label>
                                        <div class="controls">
                                            <textarea name="admin_notes" class="span8" style="width: 500px" rows="2" 
                                                      placeholder="Any special instructions or notes"></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">SMS Preview</label>
                                        <div class="controls">
                                            <button type="button" class="btn btn-info btn-small" onclick="previewSMS()">
                                                <i class="icon-eye-open"></i> Preview SMS
                                            </button>
                                            <div id="smsPreview" class="sms-preview"></div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="assign_work" class="btn btn-success btn-large">
                                                <i class="icon-ok"></i> Assign Work & Send SMS
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Recent Work Assignments -->
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-list-alt"></i> Recent Work Assignments</h3>
                            </div>
                            <div class="module-body">
                                <?php
                                $recentWorks = mysqli_query($bd, "SELECT w.*, wk.name as worker_name, wk.phone as worker_phone 
                                                                FROM works w 
                                                                LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                                                                ORDER BY w.assigned_date DESC 
                                                                LIMIT 10");
                                
                                if(mysqli_num_rows($recentWorks) > 0) {
                                    while($work = mysqli_fetch_array($recentWorks)) {
                                        $priorityClass = $work['priority'];
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
                                               (<?php echo htmlentities($work['worker_phone']); ?>)</p>
                                            <p><strong>Place:</strong> <?php echo htmlentities($work['place_address']); ?></p>
                                            <p><strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($work['deadline'])); ?></p>
                                            <p><strong>Description:</strong> <?php echo htmlentities(substr($work['work_description'], 0, 150)); ?>...</p>
                                            <?php if(!empty($work['admin_notes'])): ?>
                                            <p><strong>Notes:</strong> <?php echo htmlentities($work['admin_notes']); ?></p>
                                            <?php endif; ?>
                                            <p><small class="text-muted">Assigned on: <?php echo date('M d, Y h:i A', strtotime($work['assigned_date'])); ?></small></p>
                                        </div>
                                        <div class="span3 text-right">
                                            <div class="action-buttons">
                                                <a href="work-details.php?id=<?php echo $work['id']; ?>" 
                                                   class="btn btn-small btn-info">
                                                    <i class="icon-eye-open"></i> View
                                                </a>
                                                <a href="assign-work.php?action=resend_sms&work_id=<?php echo $work['id']; ?>" 
                                                   class="btn btn-small btn-warning"
                                                   onclick="return confirm('Resend SMS to worker?')">
                                                    <i class="icon-refresh"></i> Resend SMS
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    }
                                } else { 
                                ?>
                                <div class="alert alert-info text-center">
                                    <i class="icon-info-sign"></i> No work assignments yet. Start by assigning a new work above.
                                </div>
                                <?php } ?>
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
        function previewSMS() {
            var workTitle = $('input[name="work_title"]').val();
            var workDesc = $('textarea[name="work_description"]').val();
            var place = $('textarea[name="place_address"]').val();
            var priority = $('#prioritySelect').val();
            var deadline = $('input[name="deadline"]').val();
            var workerOption = $('#workerSelect option:selected');
            var workerName = workerOption.text();
            
            if(!workTitle || !workerName) {
                alert('Please fill work title and select a worker first');
                return;
            }
            
            var smsContent = "NEW WORK ASSIGNED\\n";
            smsContent += "Title: " + workTitle + "\\n";
            smsContent += "Place: " + place + "\\n";
            smsContent += "Priority: " + priority.toUpperCase() + "\\n";
            smsContent += "Deadline: " + deadline + "\\n";
            smsContent += "Details: " + workDesc.substring(0, 100) + "...\\n";
            smsContent += "Please check your dashboard for complete details.";
            
            $('#smsPreview').text(smsContent).show();
        }
        
        $(document).ready(function() {
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Form validation
            $('#assignWorkForm').submit(function() {
                var deadline = $('input[name="deadline"]').val();
                var today = new Date().toISOString().split('T')[0];
                
                if(deadline < today) {
                    alert('Deadline cannot be in the past!');
                    return false;
                }
                
                return confirm('Are you sure you want to assign this work and send SMS?');
            });
        });
    </script>
</body>
</html>