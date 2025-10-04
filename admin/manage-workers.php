<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('Y-m-d H:i:s');

// Handle form actions
if(isset($_POST['add_worker'])) {
    $name = mysqli_real_escape_string($bd, $_POST['name']);
    $email = mysqli_real_escape_string($bd, $_POST['email']);
    $phone = mysqli_real_escape_string($bd, $_POST['phone']);
    $department = mysqli_real_escape_string($bd, $_POST['department']);
    $address = mysqli_real_escape_string($bd, $_POST['address']);
    $salary = mysqli_real_escape_string($bd, $_POST['salary']);
    
    // Check if email already exists
    $checkEmail = mysqli_query($bd, "SELECT id FROM workers WHERE email='$email'");
    if(mysqli_num_rows($checkEmail) > 0) {
        $_SESSION['errmsg'] = "Email already exists!";
    } else {
        $sql = mysqli_query($bd, "INSERT INTO workers (name, email, phone, department, address, salary, status, created_at) 
                                 VALUES ('$name', '$email', '$phone', '$department', '$address', '$salary', 'active', '$currentTime')");
        if($sql) {
            $_SESSION['msg'] = "Worker added successfully!";
        } else {
            $_SESSION['errmsg'] = "Failed to add worker: " . mysqli_error($bd);
        }
    }
}

// Handle worker deletion
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $delete = mysqli_query($bd, "DELETE FROM workers WHERE id='$id'");
    if($delete) {
        $_SESSION['msg'] = "Worker deleted successfully!";
    } else {
        $_SESSION['errmsg'] = "Failed to delete worker!";
    }
    header("location:manage-workers.php");
    exit;
}

// Handle status toggle
if(isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $worker = mysqli_fetch_array(mysqli_query($bd, "SELECT status FROM workers WHERE id='$id'"));
    $newStatus = $worker['status'] == 'active' ? 'inactive' : 'active';
    $update = mysqli_query($bd, "UPDATE workers SET status='$newStatus' WHERE id='$id'");
    if($update) {
        $_SESSION['msg'] = "Worker status updated successfully!";
    } else {
        $_SESSION['errmsg'] = "Failed to update worker status!";
    }
    header("location:manage-workers.php");
    exit;
}

// Handle worker update
if(isset($_POST['update_worker'])) {
    $id = intval($_POST['worker_id']);
    $name = mysqli_real_escape_string($bd, $_POST['name']);
    $email = mysqli_real_escape_string($bd, $_POST['email']);
    $phone = mysqli_real_escape_string($bd, $_POST['phone']);
    $department = mysqli_real_escape_string($bd, $_POST['department']);
    $address = mysqli_real_escape_string($bd, $_POST['address']);
    $salary = mysqli_real_escape_string($bd, $_POST['salary']);
    $status = mysqli_real_escape_string($bd, $_POST['status']);
    
    // Check if email exists for other workers
    $checkEmail = mysqli_query($bd, "SELECT id FROM workers WHERE email='$email' AND id != '$id'");
    if(mysqli_num_rows($checkEmail) > 0) {
        $_SESSION['errmsg'] = "Email already exists for another worker!";
    } else {
        $sql = mysqli_query($bd, "UPDATE workers SET name='$name', email='$email', phone='$phone', 
                                 department='$department', address='$address', salary='$salary', status='$status',
                                 updated_at='$currentTime' WHERE id='$id'");
        if($sql) {
            $_SESSION['msg'] = "Worker updated successfully!";
        } else {
            $_SESSION['errmsg'] = "Failed to update worker: " . mysqli_error($bd);
        }
    }
    header("location:manage-workers.php");
    exit;
}

// Fetch worker data for editing
$editWorker = null;
if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $editWorker = mysqli_fetch_array(mysqli_query($bd, "SELECT * FROM workers WHERE id='$id'"));
}

// Fetch statistics
$totalWorkers = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM workers"))['total'];
$activeWorkers = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM workers WHERE status='active'"))['total'];
$inactiveWorkers = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM workers WHERE status='inactive'"))['total'];
$totalSalary = mysqli_fetch_array(mysqli_query($bd, "SELECT SUM(salary) as total FROM workers WHERE status='active'"))['total'];

// Department-wise statistics
$deptStats = mysqli_query($bd, "SELECT department, COUNT(*) as count FROM workers GROUP BY department");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Workers</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .worker-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .worker-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .stats-card { 
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stats-active { background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%); }
        .stats-inactive { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); }
        .stats-salary { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .status-badge { 
            padding: 5px 10px; 
            border-radius: 15px; 
            font-size: 12px; 
            font-weight: bold;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .department-tag { 
            background: #e9ecef; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 11px;
            color: #495057;
        }
        .action-buttons { margin-top: 10px; }
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
                                <h3><i class="icon-dashboard"></i> Workers Dashboard</h3>
                            </div>
                            <div class="module-body">
                                <div class="row-fluid">
                                    <div class="span3">
                                        <div class="stats-card stats-total">
                                            <h4>Total Workers</h4>
                                            <h2><?php echo $totalWorkers; ?></h2>
                                            <small>All Time</small>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-active">
                                            <h4>Active Workers</h4>
                                            <h2><?php echo $activeWorkers; ?></h2>
                                            <small>Currently Working</small>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-inactive">
                                            <h4>Inactive Workers</h4>
                                            <h2><?php echo $inactiveWorkers; ?></h2>
                                            <small>Not Active</small>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="stats-card stats-salary">
                                            <h4>Monthly Salary</h4>
                                            <h2>₹<?php echo number_format($totalSalary, 2); ?></h2>
                                            <small>Total Payout</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Department Statistics -->
                                <div class="analytics-card" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                                    <h4><i class="icon-bar-chart"></i> Department-wise Distribution</h4>
                                    <div class="row-fluid">
                                        <?php while($dept = mysqli_fetch_array($deptStats)): ?>
                                        <div class="span3 text-center" style="margin-bottom: 15px;">
                                            <div class="department-tag" style="font-size: 14px; padding: 8px 12px;">
                                                <strong><?php echo $dept['department']; ?></strong>
                                                <br>
                                                <span style="color: #667eea; font-weight: bold;"><?php echo $dept['count']; ?> workers</span>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add/Edit Worker Form -->
                        <div class="module">
                            <div class="module-head">
                                <h3>
                                    <i class="icon-user"></i> 
                                    <?php echo $editWorker ? 'Edit Worker' : 'Add New Worker'; ?>
                                </h3>
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

                                <form class="form-horizontal" method="post">
                                    <?php if($editWorker): ?>
                                    <input type="hidden" name="worker_id" value="<?php echo $editWorker['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Full Name</label>
                                        <div class="controls">
                                            <input type="text" style="width: 500px" name="name" class="span8" required 
                                                   value="<?php echo $editWorker ? htmlentities($editWorker['name']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Email</label>
                                        <div class="controls">
                                            <input type="email" style="width: 500px" name="email" class="span8" required
                                                   value="<?php echo $editWorker ? htmlentities($editWorker['email']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Phone</label>
                                        <div class="controls">
                                            <input type="text" style="width: 500px" name="phone" class="span8" required
                                                   value="<?php echo $editWorker ? htmlentities($editWorker['phone']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Department</label>
                                        <div class="controls">
                                            <select name="department" class="span8" style="width: 500px" required>
                                                <option value="">Select Department</option>
                                                <option value="Technical" <?php echo ($editWorker && $editWorker['department']=='Technical')?'selected':''; ?>>Technical</option>
                                                <option value="Customer Service" <?php echo ($editWorker && $editWorker['department']=='Customer Service')?'selected':''; ?>>Customer Service</option>
                                                <option value="Billing" <?php echo ($editWorker && $editWorker['department']=='Billing')?'selected':''; ?>>Billing</option>
                                                <option value="Maintenance" <?php echo ($editWorker && $editWorker['department']=='Maintenance')?'selected':''; ?>>Maintenance</option>
                                                <option value="HR" <?php echo ($editWorker && $editWorker['department']=='HR')?'selected':''; ?>>HR</option>
                                                <option value="Management" <?php echo ($editWorker && $editWorker['department']=='Management')?'selected':''; ?>>Management</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Address</label>
                                        <div class="controls">
                                            <textarea name="address" class="span8" style="width: 500px" rows="3"><?php echo $editWorker ? htmlentities($editWorker['address']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Monthly Salary (₹)</label>
                                        <div class="controls">
                                            <input type="number" name="salary" class="span8" style="width: 500px" step="0.01" required
                                                   value="<?php echo $editWorker ? htmlentities($editWorker['salary']) : '0'; ?>">
                                        </div>
                                    </div>
                                    <?php if($editWorker): ?>
                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="status" class="span8" style="width: 500px" required>
                                                <option value="active" <?php echo $editWorker['status']=='active'?'selected':''; ?>>Active</option>
                                                <option value="inactive" <?php echo $editWorker['status']=='inactive'?'selected':''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="control-group">
                                        <div class="controls">
                                            <?php if($editWorker): ?>
                                            <button type="submit" name="update_worker" class="btn btn-success">
                                                <i class="icon-ok"></i> Update Worker
                                            </button>
                                            <a href="manage-workers.php" class="btn btn-danger">
                                                <i class="icon-remove"></i> Cancel
                                            </a>
                                            <?php else: ?>
                                            <button type="submit" name="add_worker" class="btn btn-primary">
                                                <i class="icon-plus"></i> Add Worker
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Workers List -->
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-group"></i> All Workers (<?php echo $totalWorkers; ?>)</h3>
                            </div>
                            <div class="module-body">
                                <?php
                                $workers = mysqli_query($bd, "SELECT * FROM workers ORDER BY 
                                    CASE status 
                                        WHEN 'active' THEN 1 
                                        ELSE 2 
                                    END, 
                                    created_at DESC");
                                
                                if(mysqli_num_rows($workers) > 0) {
                                    while($worker = mysqli_fetch_array($workers)) { 
                                ?>
                                <div class="worker-card">
                                    <div class="row-fluid">
                                        <div class="span8">
                                            <h5>
                                                <strong><?php echo htmlentities($worker['name']); ?></strong>
                                                <span class="status-badge <?php echo $worker['status']=='active'?'status-active':'status-inactive'; ?>">
                                                    <?php echo ucfirst($worker['status']); ?>
                                                </span>
                                            </h5>
                                            <p><i class="icon-envelope"></i> <?php echo htmlentities($worker['email']); ?></p>
                                            <p><i class="icon-phone"></i> <?php echo htmlentities($worker['phone']); ?></p>
                                            <p>
                                                <i class="icon-briefcase"></i> 
                                                <span class="department-tag"><?php echo htmlentities($worker['department']); ?></span>
                                                | 
                                                <i class="icon-money"></i> ₹<?php echo number_format($worker['salary'], 2); ?>
                                            </p>
                                            <?php if(!empty($worker['address'])): ?>
                                            <p><i class="icon-home"></i> <?php echo htmlentities(substr($worker['address'], 0, 100)); ?>...</p>
                                            <?php endif; ?>
                                            <p><i class="icon-time"></i> Joined: <?php echo date('M d, Y', strtotime($worker['created_at'])); ?></p>
                                        </div>
                                        <div class="span4 text-right">
                                            <div class="action-buttons">
                                                <a href="manage-workers.php?action=edit&id=<?php echo $worker['id']; ?>" 
                                                   class="btn btn-small btn-info">
                                                    <i class="icon-edit"></i> Edit
                                                </a>
                                                <a href="manage-workers.php?action=toggle_status&id=<?php echo $worker['id']; ?>" 
                                                   class="btn btn-small btn-warning" 
                                                   onclick="return confirm('Are you sure you want to change status?')">
                                                    <i class="icon-refresh"></i> Status
                                                </a>
                                                <a href="manage-workers.php?action=delete&id=<?php echo $worker['id']; ?>" 
                                                   class="btn btn-small btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this worker?')">
                                                    <i class="icon-trash"></i> Delete
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
                                    <i class="icon-info-sign"></i> No workers found. Add your first worker above.
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
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Form validation
            $('form').submit(function() {
                var phone = $('input[name="phone"]').val();
                var salary = $('input[name="salary"]').val();
                
                // Phone validation
                if(!/^\d{10}$/.test(phone)) {
                    alert('Please enter a valid 10-digit phone number');
                    return false;
                }
                
                // Salary validation
                if(salary < 0) {
                    alert('Salary cannot be negative');
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>