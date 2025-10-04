<?php

session_start();
error_reporting(0);
include('includes/config.php');

// Include public header (without login requirements)
include('includes/header-public.php');

$complaint_data = null;
$error_message = '';
$success_message = '';


    $complaint_number = trim($_POST['complaint_number']);
    $mobile_number = trim($_POST['mobile_number']);
 
    // Validate inputs
    if(empty($complaint_number) || empty($mobile_number)) {
        $error_message = "Please enter both complaint number and mobile number.";
    } elseif(!preg_match('/^[0-9]+$/', $complaint_number)) {
        $error_message = "Please enter a valid complaint number.";
    } elseif(!preg_match('/^[0-9]{10}$/', $mobile_number)) {
        $error_message = "Please enter a valid 10-digit mobile number.";
    } else {
        // Fetch complaint details with mobile number verification
        $sql = "SELECT c.*, u.contactNo, u.fullName, u.address 
                FROM tblcomplaints c 
                JOIN users u ON c.userId = u.id 
                WHERE c.complaintNumber = ? AND u.contactNo = ?";
                
        $stmt = mysqli_prepare($bd, $sql);
        
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "is", $complaint_number, $mobile_number);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) > 0) {
                $complaint_data = mysqli_fetch_assoc($result);
                
                // Get status remarks/history
                $remark_sql = "SELECT * FROM complaintremark 
                              WHERE complaintNumber = ? 
                              ORDER BY remarkDate DESC";
                $remark_stmt = mysqli_prepare($bd, $remark_sql);
                
                if($remark_stmt) {
                    mysqli_stmt_bind_param($remark_stmt, "i", $complaint_number);
                    mysqli_stmt_execute($remark_stmt);
                    $status_history = mysqli_stmt_get_result($remark_stmt);
                }
            } else {
                $error_message = "No complaint found with the provided details. Please check your complaint number and mobile number.";
            }
        } else {
            $error_message = "Database error. Please try again later.";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Complaint - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .search-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .complaint-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .status-badge {
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 20px;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 25px;
            padding-left: 20px;
        }
        
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -10px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #007bff;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #007bff;
            z-index: 2;
        }
        
        .timeline-item:after {
            content: '';
            position: absolute;
            left: 0;
            top: 25px;
            bottom: -25px;
            width: 2px;
            background: #007bff;
            z-index: 1;
        }
        
        .timeline-item:last-child:after {
            display: none;
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        
        .info-box {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .progress {
            height: 25px;
            border-radius: 12px;
            background: #e9ecef;
        }
        
        .progress-bar {
            border-radius: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-bolt me-2"></i>
                Complaint Management System
            </a>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Search Section -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="search-box">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-search-location me-2"></i>Track Your Complaint
                    </h2>
                    
                    <?php if($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlentities($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="trackingForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-hashtag me-2"></i>Complaint Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="complaint_number" class="form-control" 
                                           placeholder="Enter complaint number" 
                                           value="<?php echo isset($_POST['complaint_number']) ? htmlentities($_POST['complaint_number']) : ''; ?>" 
                                           required>
                                    <small class="form-text text-muted">Enter your complaint number (numbers only)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-mobile-alt me-2"></i>Registered Mobile <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="mobile_number" class="form-control" 
                                           placeholder="Enter 10-digit mobile number" 
                                           value="<?php echo isset($_POST['mobile_number']) ? htmlentities($_POST['mobile_number']) : ''; ?>" 
                                           required pattern="[0-9]{10}" maxlength="10">
                                    <small class="form-text text-muted">Mobile number used during registration</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" name="track" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Track Complaint
                            </button>
                        </div>
                    </form>
                    
                    <!-- Quick Tips -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6><i class="fas fa-lightbulb me-2"></i>Quick Tips:</h6>
                        <ul class="mb-0 small">
                            <li>Enter the exact complaint number provided during submission</li>
                            <li>Use the mobile number registered with your account</li>
                            <li>Contact support if you've lost your complaint number</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Details Section -->
        <?php if($complaint_data): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-lg-10">
                <div class="card complaint-card">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-1">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Complaint #<?php echo htmlentities($complaint_data['complaintNumber']); ?>
                                </h3>
                                <p class="mb-0">
                                    <i class="fas fa-user me-1"></i>
                                    Registered by: <?php echo htmlentities($complaint_data['fullName']); ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <?php
                                $status = $complaint_data['status'];
                                $status_badge = '';
                                if($status == "" || $status == "NULL" || $status == null) {
                                    $status_badge = '<span class="badge bg-warning status-badge"><i class="fas fa-clock me-1"></i>Pending</span>';
                                } elseif(strtolower($status) == "in process") {
                                    $status_badge = '<span class="badge bg-info status-badge"><i class="fas fa-cog me-1"></i>In Process</span>';
                                } elseif(strtolower($status) == "closed") {
                                    $status_badge = '<span class="badge bg-success status-badge"><i class="fas fa-check-circle me-1"></i>Resolved</span>';
                                } else {
                                    $status_badge = '<span class="badge bg-secondary status-badge">' . htmlentities($status) . '</span>';
                                }
                                echo $status_badge;
                                ?>
                                <p class="mb-0 mt-2">
                                    <i class="fas fa-calendar me-1"></i>
                                    Registered: <?php echo date('d M Y, h:i A', strtotime($complaint_data['regDate'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Complaint Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <h5><i class="fas fa-info-circle me-2"></i>Complaint Information</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Complaint Type:</strong></td>
                                            <td><?php echo htmlentities($complaint_data['complaintType']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nature:</strong></td>
                                            <td><?php echo htmlentities($complaint_data['noc']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Details:</strong></td>
                                            <td><?php echo htmlentities($complaint_data['complaintDetails']); ?></td>
                                        </tr>
                                        <?php if(!empty($complaint_data['complaintFile'])): ?>
                                        <tr>
                                            <td><strong>Attachment:</strong></td>
                                            <td>
                                                <a href="complaint_files/<?php echo htmlentities($complaint_data['complaintFile']); ?>" 
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download me-1"></i>Download File
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <h5><i class="fas fa-user me-2"></i>User Information</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Name:</strong></td>
                                            <td><?php echo htmlentities($complaint_data['fullName']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mobile:</strong></td>
                                            <td><?php echo htmlentities($complaint_data['contactNo']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td><?php echo htmlentities($complaint_data['address']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>State:</strong></td>
                                            <td><?php echo htmlentities($complaint_data['state']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <h5><i class="fas fa-tasks me-2"></i>Complaint Progress</h5>
                            <?php
                            $progress = 0;
                            $status_text = '';
                            if($status == "" || $status == "NULL" || $status == null) {
                                $progress = 25;
                                $status_text = 'Registered - Waiting for review';
                            } elseif(strtolower($status) == "in process") {
                                $progress = 60;
                                $status_text = 'In Process - Being worked on';
                            } elseif(strtolower($status) == "closed") {
                                $progress = 100;
                                $status_text = 'Resolved - Complaint closed';
                            }
                            ?>
                            <div class="progress mb-2">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%" 
                                     aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo $progress; ?>%
                                </div>
                            </div>
                            <p class="text-center mb-0"><small><?php echo $status_text; ?></small></p>
                        </div>

                        <!-- Status History -->
                        <div class="mt-4">
                            <h5><i class="fas fa-history me-2"></i>Status History</h5>
                            <?php if(isset($status_history) && mysqli_num_rows($status_history) > 0): ?>
                                <div class="timeline">
                                    <?php while($remark = mysqli_fetch_assoc($status_history)): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-content">
                                                <h6 class="mb-1 text-primary"><?php echo htmlentities($remark['status']); ?></h6>
                                                <p class="mb-1"><?php echo htmlentities($remark['remark']); ?></p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('d M Y, h:i A', strtotime($remark['remarkDate'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No status updates available yet. Your complaint is under review.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-sync-alt me-1"></i>
                                Last Updated: <?php 
                                $lastUpdate = !empty($complaint_data['lastUpdationDate']) && $complaint_data['lastUpdationDate'] != '0000-00-00 00:00:00' 
                                    ? date('d M Y, h:i A', strtotime($complaint_data['lastUpdationDate'])) 
                                    : 'Not updated yet';
                                echo $lastUpdate;
                                ?>
                            </small>
                            <div>
                                <button onclick="window.print()" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-print me-1"></i>Print
                                </button>
                                <a href="register-complaint.php" class="btn btn-warning btn-sm">
                                    <i class="fas fa-plus me-1"></i>New Complaint
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Features Section -->
        <div class="row mt-5">
            <div class="col-md-4 mb-3">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                    <h5>24/7 Tracking</h5>
                    <p class="small text-muted">Track your complaint status anytime, anywhere</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <i class="fas fa-shield-alt fa-2x text-success mb-3"></i>
                    <h5>Secure</h5>
                    <p class="small text-muted">Your information is protected and confidential</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <i class="fas fa-headset fa-2x text-warning mb-3"></i>
                    <h5>Support</h5>
                    <p class="small text-muted">Need help? Contact our support team</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Complaint Management System</h5>
                    <p class="mb-0">Efficient complaint tracking and resolution system</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> CMS. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation and enhancement
        const form = document.getElementById('trackingForm');
        const complaintInput = form.querySelector('input[name="complaint_number"]');
        const mobileInput = form.querySelector('input[name="mobile_number"]');
        
        // Format inputs to accept numbers only
        complaintInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
        
        mobileInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
        
        // Auto-focus on complaint number field
        if(complaintInput.value === '') {
            complaintInput.focus();
        }
        
        // Loading state for form submission
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Tracking...';
        });
    });
    </script>
</body>
</html>