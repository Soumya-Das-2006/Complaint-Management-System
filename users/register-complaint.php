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
$successmsg = "";
$errormsg = "";

if(isset($_POST['submit'])) {
    $uid = $_SESSION['id'];
    $category = $_POST['category'];
    $subcat = $_POST['subcategory'];
    $complaintype = $_POST['complaintype'];
    $state = $_POST['state'];
    $noc = mysqli_real_escape_string($bd, $_POST['noc']);
    $complaintdetails = mysqli_real_escape_string($bd, $_POST['complaindetails']);
    $compfile = $_FILES["compfile"]["name"];
    
    // File upload handling
    $uploadOk = 1;
    $target_dir = "complaintdocs/";
    $target_file = $target_dir . basename($_FILES["compfile"]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check file size (5MB maximum)
    if ($_FILES["compfile"]["size"] > 5000000) {
        $errormsg = "Sorry, your file is too large. Maximum size is 5MB.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx');
    if (!empty($compfile) && !in_array($fileType, $allowedTypes)) {
        $errormsg = "Sorry, only JPG, JPEG, PNG, GIF, PDF, DOC & DOCX files are allowed.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1) {
        if (!empty($compfile)) {
            move_uploaded_file($_FILES["compfile"]["tmp_name"], $target_file);
        }
        
        $query = mysqli_query($bd, "INSERT INTO tblcomplaints(userId, category, subcategory, complaintType, state, noc, complaintDetails, complaintFile) 
                                   VALUES('$uid', '$category', '$subcat', '$complaintype', '$state', '$noc', '$complaintdetails', '$compfile')");
        
        if($query) {
            $sql = mysqli_query($bd, "SELECT complaintNumber FROM tblcomplaints ORDER BY complaintNumber DESC LIMIT 1");
            if($row = mysqli_fetch_array($sql)) {
                $complainno = $row['complaintNumber'];
                $successmsg = "Your complaint has been successfully submitted. Your complaint number is <strong>GUVNL-$complainno</strong>";
                
                // Reset form values
                $_POST = array();
            }
        } else {
            $errormsg = "Error submitting complaint. Please try again.";
        }
    }
}
?>
<style>
:root {
    --guvnl-primary: #1A5F7A;
    --guvnl-secondary: #3498DB;
    --guvnl-accent: #E74C3C;
    --success: #2ECC71;
    --warning: #F39C12;
    --info: #2980B9;
    --light: #ECF0F1;
    --dark: #34495E;
}
/* Form Styles */
.form-label {
    font-weight: 600;
    color: var(--guvnl-primary);
    margin-bottom: 8px;
}

.form-control, .form-select {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s;
}

.form-control:focus, .form-select:focus {
    border-color: var(--guvnl-primary);
    box-shadow: 0 0 0 0.2rem rgba(26, 95, 122, 0.25);
}

/* Alert Styles */
.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
}

.alert-success {
    background-color: rgba(46, 204, 113, 0.1);
    color: #155724;
    border-left: 4px solid var(--success);
}

.alert-danger {
    background-color: rgba(231, 76, 60, 0.1);
    color: #721c24;
    border-left: 4px solid var(--accent);
}

.alert-info {
    background-color: rgba(52, 152, 219, 0.1);
    color: #0c5460;
    border-left: 4px solid var(--secondary);
}

/* Button Styles */
.btn {
    border-radius: 8px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border: none;
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
}

/* Progress Bar */
.progress {
    height: 12px;
    border-radius: 4px;
}

/* List Group */
.list-group-item {
    border: none;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.list-group-item:last-child {
    border-bottom: none;
}

/* Character Count */
#charCount {
    font-weight: 600;
    color: var(--guvnl-primary);
}

/* Responsive Design */
@media (max-width: 768px) {
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .btn-lg {
        padding: 10px 20px;
    }
}
</style>
<!-- Main Content -->
<div class="main-content" style="margin-left: 250px; margin-top: 50px; padding: 20px; min-height: 100vh; background-color: #f8f9fa;">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title"><i class="fas fa-plus-circle me-2"></i>Submit New Complaint</h1>
            <p class="dashboard-subtitle">Report electricity issues to GUVNL for quick resolution</p>
        </div>
    </div>

    <div class="container-fluid">
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

        <div class="row">
            <div class="col-lg-8">
                <!-- Complaint Form -->
                <div class="welcome-card">
                    <h4 class="mb-4"><i class="fas fa-bolt me-2 text-warning"></i>Complaint Information</h4>
                    
                    <form method="post" name="complaint" enctype="multipart/form-data" id="complaintForm">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-select" onChange="getCat(this.value);" required>
                                        <option value="">Select Category</option>
                                        <?php 
                                        $sql = mysqli_query($bd, "SELECT id, categoryName FROM category");
                                        while ($rw = mysqli_fetch_array($sql)): 
                                        ?>
                                            <option value="<?php echo htmlentities($rw['id']); ?>" 
                                                <?php echo (isset($_POST['category']) && $_POST['category'] == $rw['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlentities($rw['categoryName']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Sub Category</label>
                                    <select name="subcategory" id="subcategory" class="form-select">
                                        <option value="">Select Subcategory</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Complaint Type <span class="text-danger">*</span></label>
                                    <select name="complaintype" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="Complaint" <?php echo (isset($_POST['complaintype']) && $_POST['complaintype'] == 'Complaint') ? 'selected' : ''; ?>>Complaint</option>
                                        <option value="General Query" <?php echo (isset($_POST['complaintype']) && $_POST['complaintype'] == 'General Query') ? 'selected' : ''; ?>>General Query</option>
                                        <option value="Emergency" <?php echo (isset($_POST['complaintype']) && $_POST['complaintype'] == 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">State <span class="text-danger">*</span></label>
                                    <select name="state" class="form-select" required>
                                        <option value="">Select State</option>
                                        <?php 
                                        $sql = mysqli_query($bd, "SELECT stateName FROM state");
                                        while ($rw = mysqli_fetch_array($sql)): 
                                        ?>
                                            <option value="<?php echo htmlentities($rw['stateName']); ?>"
                                                <?php echo (isset($_POST['state']) && $_POST['state'] == $rw['stateName']) ? 'selected' : ''; ?>>
                                                <?php echo htmlentities($rw['stateName']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Nature of Complaint <span class="text-danger">*</span></label>
                                    <input type="text" name="noc" class="form-control" 
                                           value="<?php echo isset($_POST['noc']) ? htmlentities($_POST['noc']) : ''; ?>" 
                                           placeholder="Brief description of your complaint" required>
                                    <small class="form-text text-muted">e.g., Power outage, High bill, Meter not working</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Complaint Details <span class="text-danger">*</span></label>
                                    <textarea name="complaindetails" class="form-control" rows="6" 
                                              placeholder="Please provide detailed information about your complaint..." 
                                              maxlength="2000" required><?php echo isset($_POST['complaindetails']) ? htmlentities($_POST['complaindetails']) : ''; ?></textarea>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="form-text text-muted">Maximum 2000 characters</small>
                                        <small class="form-text text-muted"><span id="charCount">0</span>/2000</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Supporting Documents</label>
                                    <input type="file" name="compfile" class="form-control" 
                                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                    <small class="form-text text-muted">Supported formats: JPG, PNG, GIF, PDF, DOC, DOCX (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" name="submit" class="btn btn-warning btn-lg me-3">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Complaint
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-redo me-2"></i>Reset Form
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Help Section -->
                <div class="quick-actions">
                    <h4 class="mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>Quick Help</h4>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Before Submitting:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Provide accurate and detailed information</li>
                            <li>Include location details if possible</li>
                            <li>Upload relevant photos/documents</li>
                            <li>Keep your complaint number safe</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <h6><i class="fas fa-clock me-2"></i>Expected Response Time</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: 100%">Within 2 Hours</div>
                        </div>
                        <small class="text-muted">For emergency complaints</small>
                    </div>

                    <div class="mt-4">
                        <h6><i class="fas fa-phone me-2"></i>Emergency Contacts</h6>
                        <div class="d-grid gap-2">
                            <a href="tel:18002333333" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-phone me-2"></i>24x7 Emergency: 1800-233-3333
                            </a>
                            <a href="mailto:complaints@guvnl.com" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-envelope me-2"></i>Email: complaints@guvnl.com
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Complaint Categories Info -->
                <div class="quick-actions mt-4">
                    <h4><i class="fas fa-tags me-2"></i>Common Complaint Types</h4>
                    <p class="text-muted">Select a category to get started</p>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-bolt text-warning me-3"></i>
                            <div>
                                <small class="fw-bold">Power Outage</small><br>
                                <small class="text-muted">No electricity in your area</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-file-invoice-dollar text-primary me-3"></i>
                            <div>
                                <small class="fw-bold">Billing Issues</small><br>
                                <small class="text-muted">Bill disputes and corrections</small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-tachometer-alt text-info me-3"></i>
                            <div>
                                <small class="fw-bold">Meter Problems</small><br>
                                <small class="text-muted">Faulty meter readings</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Script for Subcategory -->
<script>
function getCat(val) {
    $.ajax({
        type: "POST",
        url: "getsubcat.php",
        data: 'catid=' + val,
        success: function(data) {
            $("#subcategory").html(data);
        }
    });
}

// Character count for complaint details
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.querySelector('textarea[name="complaindetails"]');
    const charCount = document.getElementById('charCount');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
    
    // Initialize character count
    charCount.textContent = textarea.value.length;

    // Form validation
    document.getElementById('complaintForm').addEventListener('submit', function(e) {
        const fileInput = document.querySelector('input[name="compfile"]');
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
            if (fileSize > 5) {
                e.preventDefault();
                alert('File size must be less than 5MB');
                return false;
            }
        }
        return true;
    });
});
</script>
<?php
// Include footer
include('includes/footer.php');
?>