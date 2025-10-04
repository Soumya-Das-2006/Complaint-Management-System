<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['wlogin'])==0) {    
    header('location:index.php');
    exit;
}

$worker_id = $_SESSION['wid'];
$worker = mysqli_fetch_array(mysqli_query($bd, "SELECT * FROM workers WHERE id='$worker_id'"));

// Handle profile update
if(isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($bd, $_POST['name']);
    $phone = mysqli_real_escape_string($bd, $_POST['phone']);
    $address = mysqli_real_escape_string($bd, $_POST['address']);
    
    $update = mysqli_query($bd, "UPDATE workers SET name='$name', phone='$phone', address='$address', updated_at=NOW() WHERE id='$worker_id'");
    
    if($update) {
        $_SESSION['wname'] = $name;
        $_SESSION['msg'] = "Profile updated successfully!";
        header('location:worker-profile.php');
        exit;
    } else {
        $error = "Failed to update profile!";
    }
}

// Get worker statistics
$stats = mysqli_fetch_array(mysqli_query($bd, "SELECT 
    COUNT(*) as total_works,
    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed_works,
    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) as in_progress_works
    FROM works WHERE assigned_worker_id='$worker_id'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Worker Panel</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .profile-stats {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #3498db;
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
                        <!-- Profile Header -->
                        <div class="profile-header">
                            <div class="row-fluid">
                                <div class="span8">
                                    <h2><i class="icon-user"></i> My Profile</h2>
                                    <p class="lead">Manage your personal information and view your work statistics</p>
                                </div>
                                <div class="span4 text-right">
                                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px;">
                                        <h4>Worker ID: #<?php echo str_pad($worker_id, 4, '0', STR_PAD_LEFT); ?></h4>
                                        <span class="label label-success"><?php echo ucfirst($worker['status']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row-fluid">
                            <div class="span4">
                                <div class="profile-stats">
                                    <div class="stat-item">
                                        <i class="icon-tasks icon-3x"></i>
                                        <div class="stat-number"><?php echo $stats['total_works']; ?></div>
                                        <h4>Total Works</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="span4">
                                <div class="profile-stats">
                                    <div class="stat-item">
                                        <i class="icon-ok icon-3x" style="color: #27ae60;"></i>
                                        <div class="stat-number"><?php echo $stats['completed_works']; ?></div>
                                        <h4>Completed</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="span4">
                                <div class="profile-stats">
                                    <div class="stat-item">
                                        <i class="icon-refresh icon-3x" style="color: #f39c12;"></i>
                                        <div class="stat-number"><?php echo $stats['in_progress_works']; ?></div>
                                        <h4>In Progress</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Form -->
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-edit"></i> Edit Profile Information</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['msg'])): ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <i class="icon-ok"></i> <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if(isset($error)): ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <i class="icon-warning-sign"></i> <?php echo htmlentities($error); ?>
                                </div>
                                <?php endif; ?>
                                
                                <form class="form-horizontal row-fluid" method="post">
                                    <div class="control-group">
                                        <label class="control-label">Full Name</label>
                                        <div class="controls">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="icon-user"></i></span>
                                                <input type="text" name="name" class="span8" value="<?php echo htmlentities($worker['name']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Email Address</label>
                                        <div class="controls">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="icon-envelope"></i></span>
                                                <input type="email" class="span8" value="<?php echo htmlentities($worker['email']); ?>" readonly>
                                            </div>
                                            <span class="help-inline">Email cannot be changed</span>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Phone Number</label>
                                        <div class="controls">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="icon-phone"></i></span>
                                                <input type="text" name="phone" class="span8" value="<?php echo htmlentities($worker['phone']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Department</label>
                                        <div class="controls">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="icon-briefcase"></i></span>
                                                <input type="text" class="span8" value="<?php echo htmlentities($worker['department']); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Address</label>
                                        <div class="controls">
                                            <textarea name="address" class="span8" rows="3" placeholder="Enter your complete address"><?php echo htmlentities($worker['address']); ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Account Status</label>
                                        <div class="controls">
                                            <span class="label label-<?php echo $worker['status']=='active'?'success':'important'; ?>">
                                                <?php echo ucfirst($worker['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">Member Since</label>
                                        <div class="controls">
                                            <span class="help-inline">
                                                <?php echo date('F d, Y', strtotime($worker['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="update_profile" class="btn btn-primary">
                                                <i class="icon-ok"></i> Update Profile
                                            </button>
                                            <a href="worker-change-password.php" class="btn btn-warning">
                                                <i class="icon-lock"></i> Change Password
                                            </a>
                                        </div>
                                    </div>
                                </form>
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