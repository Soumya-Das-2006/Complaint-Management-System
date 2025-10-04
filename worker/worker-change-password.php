<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['wlogin'])==0) {    
    header('location:index.php');
    exit;
}

$worker_id = $_SESSION['wid'];
$error = '';
$success = '';

if(isset($_POST['change_password'])) {
    $current_password = md5($_POST['current_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check current password
    $check = mysqli_query($bd, "SELECT password FROM workers WHERE id='$worker_id' AND password='$current_password'");
    
    if(mysqli_num_rows($check) == 0) {
        $error = "Current password is incorrect!";
    } elseif($new_password != $confirm_password) {
        $error = "New password and confirm password do not match!";
    } elseif(strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        $new_password_hashed = md5($new_password);
        $update = mysqli_query($bd, "UPDATE workers SET password='$new_password_hashed', updated_at=NOW() WHERE id='$worker_id'");
        
        if($update) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Worker Panel</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .password-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
        .strength-weak { background: #e74c3c; width: 25%; }
        .strength-medium { background: #f39c12; width: 50%; }
        .strength-strong { background: #27ae60; width: 100%; }
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
                                <h3><i class="icon-lock"></i> Change Password</h3>
                            </div>
                            <div class="module-body">
                                <div class="password-card">
                                    <?php if($success): ?>
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <i class="icon-ok"></i> <?php echo htmlentities($success); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($error): ?>
                                    <div class="alert alert-error">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <i class="icon-warning-sign"></i> <?php echo htmlentities($error); ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form class="form-horizontal" method="post" id="passwordForm">
                                        <div class="control-group">
                                            <label class="control-label">Current Password</label>
                                            <div class="controls">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="icon-lock"></i></span>
                                                    <input type="password" name="current_password" class="span8" required placeholder="Enter your current password">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="control-group">
                                            <label class="control-label">New Password</label>
                                            <div class="controls">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="icon-key"></i></span>
                                                    <input type="password" name="new_password" id="new_password" class="span8" required placeholder="Enter new password (min 6 characters)">
                                                </div>
                                                <div class="password-strength" id="passwordStrength"></div>
                                                <span class="help-inline">Password must be at least 6 characters long</span>
                                            </div>
                                        </div>
                                        
                                        <div class="control-group">
                                            <label class="control-label">Confirm New Password</label>
                                            <div class="controls">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="icon-key"></i></span>
                                                    <input type="password" name="confirm_password" id="confirm_password" class="span8" required placeholder="Confirm your new password">
                                                </div>
                                                <span class="help-inline" id="passwordMatch"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="control-group">
                                            <div class="controls">
                                                <button type="submit" name="change_password" class="btn btn-success">
                                                    <i class="icon-ok"></i> Change Password
                                                </button>
                                                <a href="worker-profile.php" class="btn btn-default">
                                                    <i class="icon-arrow-left"></i> Back to Profile
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <div class="alert alert-info" style="margin-top: 20px;">
                                        <h5><i class="icon-info-sign"></i> Password Security Tips:</h5>
                                        <ul>
                                            <li>Use at least 8 characters</li>
                                            <li>Include numbers and special characters</li>
                                            <li>Don't use common words or personal information</li>
                                            <li>Consider using a passphrase</li>
                                        </ul>
                                    </div>
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
        $(document).ready(function() {
            // Password strength indicator
            $('#new_password').on('keyup', function() {
                var password = $(this).val();
                var strength = 0;
                
                if (password.length >= 6) strength += 1;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
                if (password.match(/\d/)) strength += 1;
                if (password.match(/[^a-zA-Z\d]/)) strength += 1;
                
                var strengthBar = $('#passwordStrength');
                strengthBar.removeClass('strength-weak strength-medium strength-strong');
                
                if (password.length === 0) {
                    strengthBar.css('width', '0');
                } else if (strength <= 1) {
                    strengthBar.addClass('strength-weak');
                } else if (strength <= 2) {
                    strengthBar.addClass('strength-medium');
                } else {
                    strengthBar.addClass('strength-strong');
                }
            });
            
            // Password match indicator
            $('#confirm_password').on('keyup', function() {
                var newPassword = $('#new_password').val();
                var confirmPassword = $(this).val();
                var matchIndicator = $('#passwordMatch');
                
                if (confirmPassword.length === 0) {
                    matchIndicator.text('').removeClass('text-error text-success');
                } else if (newPassword === confirmPassword) {
                    matchIndicator.text('✓ Passwords match').removeClass('text-error').addClass('text-success');
                } else {
                    matchIndicator.text('✗ Passwords do not match').removeClass('text-success').addClass('text-error');
                }
            });
        });
    </script>
</body>
</html>