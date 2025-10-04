<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0) { 
    header('location:users/index.php');
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
    // Sanitize and validate inputs
    $fname = mysqli_real_escape_string($bd, $_POST['fullname']);
    $contactno = mysqli_real_escape_string($bd, $_POST['contactno']);
    $address = mysqli_real_escape_string($bd, $_POST['address']);
    $state = mysqli_real_escape_string($bd, $_POST['state']);
    $country = mysqli_real_escape_string($bd, $_POST['country']);
    $pincode = mysqli_real_escape_string($bd, $_POST['pincode']);
    
    // Validation
    if(empty($fname) || empty($contactno) || empty($address) || empty($state) || empty($country) || empty($pincode)) {
        $errormsg = "All fields are required.";
    } elseif(!preg_match('/^[0-9]{10}$/', $contactno)) {
        $errormsg = "Please enter a valid 10-digit contact number.";
    } elseif(!preg_match('/^[0-9]{6}$/', $pincode)) {
        $errormsg = "Please enter a valid 6-digit pincode.";
    } else {
        // Update profile using prepared statement
        $stmt = mysqli_prepare($bd, "UPDATE users SET fullName=?, contactNo=?, address=?, State=?, country=?, pincode=?, updationDate=? WHERE userEmail=?");
        mysqli_stmt_bind_param($stmt, "ssssssss", $fname, $contactno, $address, $state, $country, $pincode, $currentTime, $_SESSION['login']);
        
        if(mysqli_stmt_execute($stmt)) {
            $successmsg = "Profile updated successfully!";
            
            // Log the activity (you should create a logging function)
            // logActivity($_SESSION['id'], "Profile updated", $currentTime);
        } else {
            $errormsg = "Error updating profile. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Get user data
$user_query = mysqli_query($bd, "SELECT * FROM users WHERE userEmail='".$_SESSION['login']."'");
$user_data = mysqli_fetch_array($user_query);
?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 250px; margin-top: 50px; padding: 20px; min-height: 100vh; background-color: #f8f9fa;">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title"><i class="fas fa-user me-2"></i>Profile Information</h1>
            <p class="dashboard-subtitle">Manage your GUVNL account details</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Profile Update Card -->
                <div class="welcome-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-1"><?php echo htmlentities($user_data['fullName']); ?></h3>
                            <p class="text-muted mb-0">GUVNL Consumer</p>
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>Last updated: <?php echo htmlentities($user_data['updationDate'] ?: 'Never'); ?></small>
                        </div>
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

                    <form method="post" name="profile" id="profileForm" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-user me-2"></i>Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="fullname" class="form-control" 
                                           value="<?php echo htmlentities($user_data['fullName']); ?>" 
                                           required placeholder="Enter your full name">
                                    <div class="invalid-feedback">Please enter your full name.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" name="useremail" class="form-control" 
                                           value="<?php echo htmlentities($user_data['userEmail']); ?>" 
                                           readonly style="background-color: #f8f9fa;">
                                    <small class="form-text text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-phone me-2"></i>Contact Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="contactno" class="form-control" 
                                           value="<?php echo htmlentities($user_data['contactNo']); ?>" 
                                           required pattern="[0-9]{10}" placeholder="10-digit mobile number">
                                    <div class="invalid-feedback">Please enter a valid 10-digit contact number.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-map-pin me-2"></i>Pincode <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="pincode" class="form-control" 
                                           value="<?php echo htmlentities($user_data['pincode']); ?>" 
                                           required pattern="[0-9]{6}" maxlength="6" placeholder="6-digit pincode">
                                    <div class="invalid-feedback">Please enter a valid 6-digit pincode.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-home me-2"></i>Address <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="address" class="form-control" rows="3" 
                                              required placeholder="Enter your complete address"><?php echo htmlentities($user_data['address']); ?></textarea>
                                    <div class="invalid-feedback">Please enter your address.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-map-marked me-2"></i>State <span class="text-danger">*</span>
                                    </label>
                                    <select name="state" class="form-select" required>
                                        <option value="">Select State</option>
                                        <?php 
                                        $state_query = mysqli_query($bd, "SELECT stateName FROM state ORDER BY stateName");
                                        $current_state = $user_data['State'];
                                        while ($state = mysqli_fetch_array($state_query)): 
                                        ?>
                                            <option value="<?php echo htmlentities($state['stateName']); ?>" 
                                                <?php echo ($state['stateName'] == $current_state) ? 'selected' : ''; ?>>
                                                <?php echo htmlentities($state['stateName']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select your state.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-globe me-2"></i>Country <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="country" class="form-control" 
                                           value="<?php echo htmlentities($user_data['country']); ?>" 
                                           required placeholder="Enter your country">
                                    <div class="invalid-feedback">Please enter your country.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-calendar me-2"></i>Registration Date
                                    </label>
                                    <input type="text" name="regdate" class="form-control" 
                                           value="<?php echo htmlentities($user_data['regDate']); ?>" 
                                           readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-history me-2"></i>Last Updated
                                    </label>
                                    <input type="text" class="form-control" 
                                           value="<?php echo htmlentities($user_data['updationDate'] ?: 'Never updated'); ?>" 
                                           readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-redo me-2"></i>Reset Changes
                            </button>
                            <button type="submit" name="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Account Summary -->
                <div class="quick-actions">
                    <h4 class="mb-4"><i class="fas fa-chart-bar me-2 text-primary"></i>Account Summary</h4>
                    
                    <?php
                    // Get user statistics
                    $user_id = $user_data['id'];
                    $total_complaints = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='$user_id'"));
                    $pending_complaints = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='$user_id' AND status IS NULL"));
                    $resolved_complaints = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='$user_id' AND status='closed'"));
                    ?>
                    
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="stat-mini">
                                <div class="stat-number text-primary"><?php echo $total_complaints; ?></div>
                                <div class="stat-label">Total Complaints</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini">
                                <div class="stat-number text-warning"><?php echo $pending_complaints; ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini">
                                <div class="stat-number text-success"><?php echo $resolved_complaints; ?></div>
                                <div class="stat-label">Resolved</div>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" style="width: <?php echo $total_complaints > 0 ? ($resolved_complaints/$total_complaints)*100 : 0; ?>%"></div>
                        <div class="progress-bar bg-warning" style="width: <?php echo $total_complaints > 0 ? ($pending_complaints/$total_complaints)*100 : 0; ?>%"></div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions mt-4">
                    <h4 class="mb-3"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h4>
                    <div class="d-grid gap-2">
                        <a href="register-complaint.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus-circle me-2"></i>Submit New Complaint
                        </a>
                        <a href="complaint-history.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history me-2"></i>View Complaint History
                        </a>
                        <a href="change-password.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-key me-2"></i>Change Password
                        </a>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="quick-actions mt-4">
                    <h4 class="mb-3"><i class="fas fa-headset me-2 text-info"></i>GUVNL Support</h4>
                    <p class="text-muted">For assistance, contact our support team:</p>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0">
                            <i class="fas fa-phone text-success me-2"></i>
                            <small><strong>Emergency:</strong> 1800-233-3333</small>
                        </div>
                        <div class="list-group-item px-0">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <small><strong>Email:</strong> support@guvnl.com</small>
                        </div>
                        <div class="list-group-item px-0">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <small><strong>Hours:</strong> 24x7 Support</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced form functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('profileForm');
    
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        }
        
        form.classList.add('was-validated');
    });

    // Real-time validation for contact number
    const contactInput = document.querySelector('input[name="contactno"]');
    contactInput.addEventListener('input', function() {
        const value = this.value.replace(/\D/g, ''); // Remove non-digits
        this.value = value.slice(0, 10); // Limit to 10 digits
        
        if (value.length === 10) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (value.length > 0) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            this.classList.remove('is-invalid', 'is-valid');
        }
    });

    // Real-time validation for pincode
    const pincodeInput = document.querySelector('input[name="pincode"]');
    pincodeInput.addEventListener('input', function() {
        const value = this.value.replace(/\D/g, ''); // Remove non-digits
        this.value = value.slice(0, 6); // Limit to 6 digits
        
        if (value.length === 6) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (value.length > 0) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else {
            this.classList.remove('is-invalid', 'is-valid');
        }
    });

    // Reset form validation on reset
    form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        document.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
    });
});
</script>

<style>
.profile-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--guvnl-primary), var(--guvnl-secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
}

.stat-mini {
    padding: 10px;
}

.stat-mini .stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-mini .stat-label {
    font-size: 0.8rem;
    color: #6c757d;
}

.list-group-item {
    border: none;
    padding: 10px 0;
}

.is-valid {
    border-color: #198754 !important;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
}

.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}
</style>

<?php
// Include footer
include('includes/footer.php');
?>