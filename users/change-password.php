<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0) { 
    header('location:index.php');
    exit;
}

// Include header
include('includes/header.php');

// Include sidebar
include('includes/sidebar.php');
?>

<?php
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());
$successmsg = "";
$errormsg = "";

if(isset($_POST['submit'])) {
    // Validate CSRF token (you'll need to implement this)
    // if(!validateCsrfToken($_POST['csrf_token'])) {
    //     $errormsg = "Security token invalid. Please try again.";
    // } else {
    
    $oldPassword = $_POST['password'];
    $newPassword = $_POST['newpassword'];
    $confirmPassword = $_POST['confirmpassword'];
    
    // Basic validation
    if(empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errormsg = "All password fields are required.";
    } elseif($newPassword !== $confirmPassword) {
        $errormsg = "New password and confirm password do not match.";
    } elseif(strlen($newPassword) < 8) {
        $errormsg = "New password must be at least 8 characters long.";
    } else {
        // Check if old password matches
        $stmt = mysqli_prepare($bd, "SELECT password FROM users WHERE password = ? AND userEmail = ?");
        $hashedOldPassword = md5($oldPassword);
        mysqli_stmt_bind_param($stmt, "ss", $hashedOldPassword, $_SESSION['login']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0) {
            // Update password
            $updateStmt = mysqli_prepare($bd, "UPDATE users SET password = ?, updationDate = ? WHERE userEmail = ?");
            $hashedNewPassword = md5($newPassword);
            mysqli_stmt_bind_param($updateStmt, "sss", $hashedNewPassword, $currentTime, $_SESSION['login']);
            
            if(mysqli_stmt_execute($updateStmt)) {
                $successmsg = "Password changed successfully!";
                
                // Log the password change (you should create a logging function)
                // logActivity($_SESSION['id'], "Password changed", $currentTime);
                
                // Clear form
                $_POST = array();
            } else {
                $errormsg = "Error updating password. Please try again.";
            }
            mysqli_stmt_close($updateStmt);
        } else {
            $errormsg = "Current password is incorrect.";
        }
        mysqli_stmt_close($stmt);
    }
    // }
}

// Generate CSRF token (you'll need to implement this function)
// $csrf_token = generateCsrfToken();
?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 250px; margin-top: 50px; padding: 20px; min-height: 100vh; background-color: #f8f9fa;">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title"><i class="fas fa-key me-2"></i>Change Password</h1>
            <p class="dashboard-subtitle">Secure your GUVNL account with a new password</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Password Change Card -->
                <div class="welcome-card">
                    <div class="text-center mb-4">
                        <div class="password-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>Update Your Password</h3>
                        <p class="text-muted">Choose a strong password to protect your account</p>
                    </div>

                    <!-- Alert Messages -->
                    <?php if($successmsg): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $successmsg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($errormsg): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $errormsg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" name="chngpwd" id="passwordForm" novalidate>
                        <!-- CSRF Token (implement this) -->
                        <!-- <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"> -->

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-lock me-2"></i>Current Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control" 
                                               id="currentPassword" required 
                                               placeholder="Enter your current password">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" 
                                                data-target="currentPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">Please enter your current password.</div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-key me-2"></i>New Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="newpassword" class="form-control" 
                                               id="newPassword" required minlength="8"
                                               placeholder="Enter your new password (min. 8 characters)">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" 
                                                data-target="newPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">Password must be at least 8 characters long.</div>
                                    <div class="password-strength mt-2">
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" id="passwordStrengthBar" style="width: 0%;"></div>
                                        </div>
                                        <small class="text-muted" id="passwordStrengthText">Password strength</small>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-key me-2"></i>Confirm New Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="confirmpassword" class="form-control" 
                                               id="confirmPassword" required minlength="8"
                                               placeholder="Confirm your new password">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" 
                                                data-target="confirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="confirmPasswordFeedback">
                                        Passwords do not match.
                                    </div>
                                </div>

                                <!-- Password Requirements -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Password Requirements</h6>
                                        <ul class="list-unstyled mb-0 small">
                                            <li id="req-length" class="text-muted">
                                                <i class="fas fa-circle me-1"></i>At least 8 characters long
                                            </li>
                                            <li id="req-uppercase" class="text-muted">
                                                <i class="fas fa-circle me-1"></i>Contains uppercase letters
                                            </li>
                                            <li id="req-lowercase" class="text-muted">
                                                <i class="fas fa-circle me-1"></i>Contains lowercase letters
                                            </li>
                                            <li id="req-number" class="text-muted">
                                                <i class="fas fa-circle me-1"></i>Contains numbers
                                            </li>
                                            <li id="req-special" class="text-muted">
                                                <i class="fas fa-circle me-1"></i>Contains special characters
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="reset" class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                    <button type="submit" name="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-2"></i>Update Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                <!-- Security Tips (Side Column) -->
            <div class="col-lg-4">
                <!-- Security Tips Card -->
                <div class="card shadow-sm mb-4 border-0" style="background: linear-gradient(135deg, #f7fafc 60%, #e3f0ff 100%); border-radius: 18px;">
                    <div class="card-body">
                        <h4 class="card-title mb-3 text-primary">
                            <i class="fas fa-shield-alt me-2"></i>Security Tips
                        </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0 ps-0 d-flex align-items-start bg-transparent">
                                <span class="badge bg-success rounded-circle me-3 mt-1 shadow-sm" style="width:28px;height:28px; font-size:1.1rem;">
                                    <i class="fas fa-check"></i>
                                </span>
                                <div>
                                    <strong>Use a Strong Password</strong>
                                    <br><small class="text-muted">Combine letters, numbers, and symbols</small>
                                </div>
                            </li>
                            <li class="list-group-item border-0 ps-0 d-flex align-items-start bg-transparent">
                                <span class="badge bg-info rounded-circle me-3 mt-1 shadow-sm" style="width:28px;height:28px; font-size:1.1rem;">
                                    <i class="fas fa-sync-alt"></i>
                                </span>
                                <div>
                                    <strong>Don't Reuse Passwords</strong>
                                    <br><small class="text-muted">Use unique passwords for different accounts</small>
                                </div>
                            </li>
                            <li class="list-group-item border-0 ps-0 d-flex align-items-start bg-transparent">
                                <span class="badge bg-warning rounded-circle me-3 mt-1 shadow-sm" style="width:28px;height:28px; font-size:1.1rem;">
                                    <i class="fas fa-redo"></i>
                                </span>
                                <div>
                                    <strong>Change Regularly</strong>
                                    <br><small class="text-muted">Update your password every 3-6 months</small>
                                </div>
                            </li>
                            <li class="list-group-item border-0 ps-0 d-flex align-items-start bg-transparent">
                                <span class="badge bg-secondary rounded-circle me-3 mt-1 shadow-sm" style="width:28px;height:28px; font-size:1.1rem;">
                                    <i class="fas fa-mobile-alt"></i>
                                </span>
                                <div>
                                    <strong>Enable 2FA</strong>
                                    <br><small class="text-muted">Use two-factor authentication when available</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Account Security Checklist Card -->
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #f8f9fa 60%, #f0f7ff 100%); border-radius: 18px;">
                    <div class="card-body px-4 py-4">
                        <h4 class="card-title mb-3 text-primary d-flex align-items-center">
                            <i class="fas fa-list-check me-2"></i>Account Security Checklist
                        </h4>
                        <ol class="ps-3 mb-0" style="list-style-type: decimal;">
                            <li class="mb-4 d-flex align-items-start">
                                <span class="me-3 mt-1" style="color:#0d6efd;">
                                    <i class="fas fa-user-secret fa-lg"></i>
                                </span>
                                <div>
                                    <span class="fw-bold">Keep Your Password Private</span>
                                    <br><small class="text-muted">Never share your password with anyone, including support staff.</small>
                                </div>
                            </li>
                            <li class="mb-4 d-flex align-items-start">
                                <span class="me-3 mt-1" style="color:#fd7e14;">
                                    <i class="fas fa-calendar-alt fa-lg"></i>
                                </span>
                                <div>
                                    <span class="fw-bold">Update Passwords Periodically</span>
                                    <br><small class="text-muted">Change your password every few months to reduce risk.</small>
                                </div>
                            </li>
                            <li class="mb-4 d-flex align-items-start">
                                <span class="me-3 mt-1" style="color:#20c997;">
                                    <i class="fas fa-lock fa-lg"></i>
                                </span>
                                <div>
                                    <span class="fw-bold">Enable Two-Factor Authentication</span>
                                    <br><small class="text-muted">Add an extra layer of security to your account if available.</small>
                                </div>
                            </li>
                            <li class="mb-4 d-flex align-items-start">
                                <span class="me-3 mt-1" style="color:#dc3545;">
                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                </span>
                                <div>
                                    <span class="fw-bold">Beware of Phishing</span>
                                    <br><small class="text-muted">Do not click suspicious links or provide credentials via email.</small>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="me-3 mt-1" style="color:#6610f2;">
                                    <i class="fas fa-key fa-lg"></i>
                                </span>
                                <div>
                                    <span class="fw-bold">Use Unique Passwords</span>
                                    <br><small class="text-muted">Avoid using the same password for multiple sites or services.</small>
                                </div>
                            </li>
                        </ol>
                    </div>
                </div>
                <style>
                /* Custom styles for the right partition */
                .card {
                    box-shadow: 0 4px 24px rgba(0,0,0,0.07), 0 1.5px 4px rgba(0,0,0,0.04);
                }
                .card-title i {
                    vertical-align: middle;
                }
                .list-group-item {
                    background: transparent !important;
                }
                .list-group-item .badge {
                    min-width: 28px;
                    min-height: 28px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.1rem;
                }
                .card-body ol li span.fw-bold {
                    font-size: 1.05rem;
                }
                .card-body ol li small {
                    font-size: 0.93rem;
                }
                </style>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced password functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password strength checker
    const newPasswordInput = document.getElementById('newPassword');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    
    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        
        strengthBar.style.width = strength.percentage + '%';
        strengthBar.className = 'progress-bar ' + strength.class;
        strengthText.textContent = strength.text;
        strengthText.className = 'text-muted ' + strength.textClass;
        
        // Update requirement indicators
        updateRequirementIndicators(password);
    });

    // Password confirmation validation
    const confirmPasswordInput = document.getElementById('confirmPassword');
    confirmPasswordInput.addEventListener('input', function() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && newPassword !== confirmPassword) {
            this.classList.add('is-invalid');
            document.getElementById('confirmPasswordFeedback').style.display = 'block';
        } else {
            this.classList.remove('is-invalid');
            document.getElementById('confirmPasswordFeedback').style.display = 'none';
        }
    });

    // Form validation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            confirmPasswordInput.classList.add('is-invalid');
            document.getElementById('confirmPasswordFeedback').style.display = 'block';
            confirmPasswordInput.focus();
            return false;
        }
        
        if (newPassword.length < 8) {
            e.preventDefault();
            newPasswordInput.classList.add('is-invalid');
            newPasswordInput.focus();
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    });

    // Password strength calculation
    function checkPasswordStrength(password) {
        let score = 0;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };

        // Calculate score
        Object.values(requirements).forEach(req => {
            if (req) score++;
        });

        const percentage = (score / 5) * 100;
        
        // Return strength info
        if (password.length === 0) {
            return { percentage: 0, class: 'bg-secondary', text: 'Password strength', textClass: 'text-muted' };
        } else if (percentage < 40) {
            return { percentage, class: 'bg-danger', text: 'Weak password', textClass: 'text-danger' };
        } else if (percentage < 80) {
            return { percentage, class: 'bg-warning', text: 'Moderate password', textClass: 'text-warning' };
        } else {
            return { percentage, class: 'bg-success', text: 'Strong password', textClass: 'text-success' };
        }
    }

    // Update requirement indicators
    function updateRequirementIndicators(password) {
        const requirements = {
            'length': password.length >= 8,
            'uppercase': /[A-Z]/.test(password),
            'lowercase': /[a-z]/.test(password),
            'number': /[0-9]/.test(password),
            'special': /[^A-Za-z0-9]/.test(password)
        };

        Object.keys(requirements).forEach(req => {
            const element = document.getElementById('req-' + req);
            const icon = element.querySelector('i');
            
            if (requirements[req]) {
                element.classList.remove('text-muted');
                element.classList.add('text-success');
                icon.classList.replace('fa-circle', 'fa-check-circle');
            } else {
                element.classList.remove('text-success');
                element.classList.add('text-muted');
                icon.classList.replace('fa-check-circle', 'fa-circle');
            }
        });
    }
});
</script>

<style>
.password-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--guvnl-primary), var(--guvnl-secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 2rem;
}

.input-group .btn {
    border-left: none;
}

.input-group .form-control:focus {
    border-right: none;
}

.progress-bar {
    transition: width 0.3s ease;
}

.list-unstyled li {
    margin-bottom: 5px;
}
</style>
<?php

// Include footer
include('includes/footer.php');
?>