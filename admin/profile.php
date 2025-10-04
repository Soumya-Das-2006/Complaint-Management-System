<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

$msg = '';
$msg_type = '';

// Fetch admin data
$sql = "SELECT * FROM admin WHERE id = 1"; // Assuming only one admin exists
$result = mysqli_query($bd, $sql);
$admin_data = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($bd, $_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate required fields
    if (empty($username)) {
        $msg = "Username is required!";
        $msg_type = "error";
    } else {
        // Check if username already exists (excluding current admin)
        $check_sql = "SELECT id FROM admin WHERE username = '$username' AND id != 1";
        $check_result = mysqli_query($bd, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $msg = "Username already exists!";
            $msg_type = "error";
        } else {
            // If password change is requested
            if (!empty($new_password)) {
                // Verify current password
                $current_password_md5 = md5($current_password);
                if ($current_password_md5 !== $admin_data['password']) {
                    $msg = "Current password is incorrect!";
                    $msg_type = "error";
                } elseif ($new_password !== $confirm_password) {
                    $msg = "New passwords do not match!";
                    $msg_type = "error";
                } elseif (strlen($new_password) < 6) {
                    $msg = "New password must be at least 6 characters long!";
                    $msg_type = "error";
                } else {
                    // Update with new password
                    $new_password_md5 = md5($new_password);
                    $current_date = date('d-m-Y H:i:s');
                    $update_sql = "UPDATE admin SET username = '$username', password = '$new_password_md5', updationDate = '$current_date' WHERE id = 1";
                    
                    if (mysqli_query($bd, $update_sql)) {
                        $msg = "Profile updated successfully!";
                        $msg_type = "success";
                        // Refresh admin data
                        $admin_data['username'] = $username;
                        $admin_data['updationDate'] = $current_date;
                        
                        // Clear password fields
                        $current_password = $new_password = $confirm_password = '';
                    } else {
                        $msg = "Error updating profile: " . mysqli_error($bd);
                        $msg_type = "error";
                    }
                }
            } else {
                // Update without changing password
                $current_date = date('d-m-Y H:i:s');
                $update_sql = "UPDATE admin SET username = '$username', updationDate = '$current_date' WHERE id = 1";
                
                if (mysqli_query($bd, $update_sql)) {
                    $msg = "Profile updated successfully!";
                    $msg_type = "success";
                    // Refresh admin data
                    $admin_data['username'] = $username;
                    $admin_data['updationDate'] = $current_date;
                } else {
                    $msg = "Error updating profile: " . mysqli_error($bd);
                    $msg_type = "error";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Profile</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .profile-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        .profile-sidebar {
            flex: 0 0 280px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            text-align: center;
            height: fit-content;
        }
        
        .profile-content {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            margin: 0 auto 20px;
            border: 4px solid #e8f4fc;
        }
        
        .profile-name {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .profile-role {
            color: #3498db;
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .profile-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #3498db;
            display: block;
        }
        
        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        
        .profile-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .btn-custom {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .btn-outline-custom {
            background-color: transparent;
            border: 1px solid #3498db;
            color: #3498db;
        }
        
        .btn-outline-custom:hover {
            background-color: #3498db;
            color: white;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .form-control {
            border-radius: 5px;
        }
        
        .form-text {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        
        .password-strength {
            height: 3px;
            background: #eee;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }
        
        .strength-weak { background: #e74c3c; width: 33%; }
        .strength-medium { background: #f39c12; width: 66%; }
        .strength-strong { background: #27ae60; width: 100%; }
        
        .alert-custom {
            border-radius: 5px;
            border-left: 4px solid;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #dff0d8;
            border-color: #27ae60;
            color: #27ae60;
        }
        
        .alert-error {
            background-color: #f2dede;
            border-color: #e74c3c;
            color: #e74c3c;
        }
        
        .save-indicator {
            display: none;
            padding: 10px 15px;
            background: #d4edda;
            color: #155724;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #27ae60;
        }
        
        .save-indicator.show {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .btn-save {
            position: relative;
            overflow: hidden;
        }
        
        .btn-save .spinner {
            display: none;
            margin-right: 8px;
        }
        
        .btn-save.saving .spinner {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }
            
            .profile-sidebar {
                flex: none;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <?php include('include/header.php'); ?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php'); ?>                
                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-user"></i> Admin Profile</h3>
                            </div>
                            <div class="module-body">
                                <!-- Success/Error Messages -->
                                <?php if (!empty($msg)): ?>
                                    <div class="alert-custom alert-<?php echo $msg_type == 'success' ? 'success' : 'error'; ?>">
                                        <i class="icon-<?php echo $msg_type == 'success' ? 'ok' : 'warning-sign'; ?>"></i>
                                        <?php echo htmlspecialchars($msg); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Save Indicator -->
                                <div id="saveIndicator" class="save-indicator">
                                    <i class="icon-ok"></i> Changes saved successfully!
                                </div>
                                
                                <div class="profile-container">
                                    <!-- Left Sidebar - Profile Info -->
                                    <div class="profile-sidebar">
                                        <div class="profile-avatar">
                                            <?php echo strtoupper(substr($admin_data['username'], 0, 1)); ?>
                                        </div>
                                        
                                        <div class="profile-info">
                                            <div class="profile-name"><?php echo htmlspecialchars($admin_data['username']); ?></div>
                                            <div class="profile-role">System Administrator</div>
                                            <div class="profile-status">
                                                <span style="color: #27ae60;">
                                                    <i class="icon-ok"></i> Active
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="profile-stats">
                                            <div class="stat-item">
                                                <span class="stat-value">1</span>
                                                <span class="stat-label">Admin</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-value"><?php echo date('M Y'); ?></span>
                                                <span class="stat-label">Since</span>
                                            </div>
                                        </div>
                                        
                                        <div class="profile-actions">
                                            <button type="button" class="btn-custom btn-outline-custom">
                                                <i class="icon-camera"></i> Change Photo
                                            </button>
                                            <button type="button" class="btn-custom btn-outline-custom">
                                                <i class="icon-time"></i> Activity Log
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Content - Profile Form -->
                                    <div class="profile-content">
                                        <h4 class="section-title">
                                            <i class="icon-cog"></i> Profile Settings
                                        </h4>
                                        
                                        <form method="POST" action="admin-profile.php" id="profileForm">
                                            <div class="form-group">
                                                <label for="username">Username *</label>
                                                <input type="text" id="username" name="username" class="form-control" 
                                                    value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>
                                                <div class="form-text">Your unique username for system access</div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label for="admin_id">Admin ID</label>
                                                    <input type="text" id="admin_id" class="form-control" 
                                                        value="<?php echo htmlspecialchars($admin_data['id']); ?>" readonly>
                                                    <div class="form-text">System generated identifier</div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="updation_date">Last Updated</label>
                                                    <input type="text" id="updation_date" class="form-control" 
                                                        value="<?php echo htmlspecialchars($admin_data['updationDate']); ?>" readonly>
                                                    <div class="form-text">Last profile modification</div>
                                                </div>
                                            </div>
                                            
                                            <h5 class="section-title" style="margin-top: 25px;">
                                                <i class="icon-lock"></i> Change Password
                                            </h5>
                                            
                                            <div class="form-group">
                                                <label for="current_password">Current Password</label>
                                                <input type="password" id="current_password" name="current_password" class="form-control" 
                                                    value="<?php echo isset($current_password) ? htmlspecialchars($current_password) : ''; ?>">
                                                <div class="form-text">Required only when changing password</div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label for="new_password">New Password</label>
                                                    <input type="password" id="new_password" name="new_password" class="form-control" 
                                                        value="<?php echo isset($new_password) ? htmlspecialchars($new_password) : ''; ?>">
                                                    <div class="form-text">Minimum 6 characters</div>
                                                    <div class="password-strength">
                                                        <div class="strength-bar" id="passwordStrength"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="confirm_password">Confirm New Password</label>
                                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                                        value="<?php echo isset($confirm_password) ? htmlspecialchars($confirm_password) : ''; ?>">
                                                    <div class="form-text">Re-enter your new password</div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-actions" style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                                                <button type="submit" class="btn btn-primary btn-save" id="saveButton">
                                                    <i class="icon-spinner spinner"></i>
                                                    <i class="icon-save"></i> Save Changes
                                                </button>
                                                <button type="reset" class="btn" id="resetButton">
                                                    <i class="icon-refresh"></i> Reset Form
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--/.span9-->
            </div><!--/.row-->
        </div><!--/.container-->
    </div><!--/.wrapper-->

    <?php include('include/footer.php'); ?>
    
    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            const currentPassword = document.getElementById('current_password');
            const passwordStrength = document.getElementById('passwordStrength');
            const saveButton = document.getElementById('saveButton');
            const resetButton = document.getElementById('resetButton');
            const saveIndicator = document.getElementById('saveIndicator');

            // Check if we have a success message from PHP
            <?php if ($msg_type == 'success'): ?>
                showSaveIndicator();
            <?php endif; ?>

            function showSaveIndicator() {
                saveIndicator.classList.add('show');
                setTimeout(() => {
                    saveIndicator.classList.remove('show');
                }, 5000);
            }

            // Password strength indicator
            function checkPasswordStrength(password) {
                let strength = 0;
                
                if (password.length >= 6) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                
                return strength;
            }
            
            function updatePasswordStrength() {
                const password = newPassword.value;
                const strength = checkPasswordStrength(password);
                
                passwordStrength.className = 'strength-bar';
                
                if (password.length > 0) {
                    if (strength <= 2) {
                        passwordStrength.classList.add('strength-weak');
                    } else if (strength <= 4) {
                        passwordStrength.classList.add('strength-medium');
                    } else {
                        passwordStrength.classList.add('strength-strong');
                    }
                }
            }
            
            // Real-time password validation
            function validatePasswords() {
                if (newPassword.value && confirmPassword.value) {
                    if (newPassword.value !== confirmPassword.value) {
                        confirmPassword.style.borderColor = '#e74c3c';
                    } else {
                        confirmPassword.style.borderColor = '#27ae60';
                    }
                } else {
                    confirmPassword.style.borderColor = '';
                }
                
                updatePasswordStrength();
            }
            
            newPassword.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
            
            // Form submission
            form.addEventListener('submit', function(e) {
                // Basic validation
                if (newPassword.value && !currentPassword.value) {
                    e.preventDefault();
                    alert('Please enter your current password to change it.');
                    currentPassword.focus();
                    return;
                }
                
                if (newPassword.value && newPassword.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('New passwords do not match.');
                    newPassword.focus();
                    return;
                }
                
                if (newPassword.value && newPassword.value.length < 6) {
                    e.preventDefault();
                    alert('New password must be at least 6 characters long.');
                    newPassword.focus();
                    return;
                }
                
                // Show saving state
                saveButton.classList.add('saving');
                saveButton.disabled = true;
                
                // Simulate save delay for better UX
                setTimeout(() => {
                    saveButton.classList.remove('saving');
                    saveButton.disabled = false;
                }, 1000);
            });
            
            // Reset button functionality
            resetButton.addEventListener('click', function() {
                // Clear password fields
                currentPassword.value = '';
                newPassword.value = '';
                confirmPassword.value = '';
                passwordStrength.className = 'strength-bar';
                confirmPassword.style.borderColor = '';
            });
        });
    </script>
</body>
</html>