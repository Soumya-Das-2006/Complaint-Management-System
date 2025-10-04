<?php 
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0) { 
    header('location:index.php');
    exit;
}

// Handle PDF Export
if(isset($_POST['export_pdf'])) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="complaint_history_'.date('Y-m-d').'.pdf"');
    
    $pdf_content = "GUVNL Complaint History\n";
    $pdf_content .= "Generated on: ".date('d-m-Y H:i:s')."\n\n";
    
    $query = mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='".$_SESSION['id']."' ORDER BY regDate DESC");
    while($row = mysqli_fetch_array($query)) {
        $pdf_content .= "Complaint #: ".$row['complaintNumber']."\n";
        $pdf_content .= "Date: ".$row['regDate']."\n";
        $pdf_content .= "Status: ".($row['status'] ?: 'Pending')."\n";
        $pdf_content .= "Category: ".$row['category']."\n";
        $pdf_content .= "----------------------------------------\n";
    }
    
    echo $pdf_content;
    exit;
}

// Handle Excel Export
if(isset($_POST['export_excel'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="complaint_history_'.date('Y-m-d').'.xls"');
    
    echo "Complaint Number\tRegistration Date\tLast Updated\tCategory\tStatus\tNature of Complaint\n";
    
    $query = mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='".$_SESSION['id']."' ORDER BY regDate DESC");
    while($row = mysqli_fetch_array($query)) {
        echo $row['complaintNumber']."\t";
        echo $row['regDate']."\t";
        echo ($row['lastUpdationDate'] ?: 'Not Updated')."\t";
        echo $row['category']."\t";
        echo ($row['status'] ?: 'Pending')."\t";
        echo $row['noc']."\n";
    }
    exit;
}

include('includes/header.php');
include('includes/sidebar.php');

$query = mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='".$_SESSION['id']."' ORDER BY regDate DESC");
$total_complaints = mysqli_num_rows($query);
?>

<div class="main-content" style="margin-left: 250px; margin-top: 50px; padding: 20px; background: #f4f6f9; min-height: 100vh;">
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title"><i class="fas fa-history me-2"></i>Complaint History</h1>
            <p class="dashboard-subtitle">Track all your electricity complaints with GUVNL</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-total">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-number"><?php echo $total_complaints; ?></div>
                    <div class="stat-label">Total Complaints</div>
                </div>
            </div>
            
            <?php
            $pending = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='".$_SESSION['id']."' AND status IS NULL"));
            $in_process = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='".$_SESSION['id']."' AND status='in Process'"));
            $closed = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM tblcomplaints WHERE userId='".$_SESSION['id']."' AND status='closed'"));
            ?>
            
            <div class="col-md-3">
                <div class="stat-card stat-pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?php echo $pending; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card stat-process">
                    <div class="stat-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="stat-number"><?php echo $in_process; ?></div>
                    <div class="stat-label">In Process</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card stat-closed">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number"><?php echo $closed; ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>
        </div>

        <div class="recent-complaints">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-list-ol me-2"></i>Your Complaint History</h4>
                <div>
                    <a href="register-complaint.php" class="btn btn-warning me-2">
                        <i class="fas fa-plus-circle me-1"></i>New Complaint
                    </a>
                    <a href="dashboard.php" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </div>
            </div>

            <?php if($total_complaints > 0): ?>
                <!-- Status Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filter by Status:</label>
                        <select class="form-select" id="statusFilter" onchange="filterComplaints()">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in process">In Process</option>
                            <option value="closed">Resolved</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="complaintTable">
                        <thead class="table-light" style="background: #007bff; color: black;">
                            <tr>
                                <th>Complaint No.</th>
                                <th>Registered Date</th>
                                <th>Last Updated</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="complaintTableBody">
                            <?php 
                            // Store complaints data for JavaScript
                            $complaints_data = [];
                            $counter = 0;
                            
                            mysqli_data_seek($query, 0);
                            while($row = mysqli_fetch_array($query)): 
                                $status = $row['status'];
                                $status_class = '';
                                if($status == "" || $status == "NULL") {
                                    $status_class = 'pending';
                                } elseif($status == "in Process") {
                                    $status_class = 'in process';
                                } elseif($status == "closed") {
                                    $status_class = 'closed';
                                }
                                
                                $complaints_data[] = [
                                    'id' => $counter,
                                    'status' => $status_class
                                ];
                                $counter++;
                            ?>
                                <tr id="complaintRow<?php echo $counter; ?>" data-status="<?php echo $status_class; ?>">
                                    <td>
                                        <strong>GUVNL-<?php echo htmlentities($row['complaintNumber']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlentities($row['noc']); ?></small>
                                    </td>
                                    <td><?php echo htmlentities($row['regDate']); ?></td>
                                    <td>
                                        <?php 
                                        $lastUpdate = !empty($row['lastUpdationDate']) ? htmlentities($row['lastUpdationDate']) : 'Not Updated';
                                        echo $lastUpdate;
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $category = htmlentities($row['category']);
                                        $category_icons = [
                                            'Power Outage' => 'fas fa-bolt',
                                            'Billing Issue' => 'fas fa-file-invoice-dollar',
                                            'Meter Problem' => 'fas fa-tachometer-alt',
                                            'Safety Concern' => 'fas fa-exclamation-triangle',
                                            'New Connection' => 'fas fa-plug',
                                            'Other' => 'fas fa-question-circle'
                                        ];
                                        $icon = isset($category_icons[$category]) ? $category_icons[$category] : 'fas fa-question-circle';
                                        ?>
                                        <i class="<?php echo $icon; ?> me-1"></i><?php echo $category; ?>
                                    </td>
                                    <td>
                                        <?php if($status == "" || $status == "NULL"): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif($status == "in Process"): ?>
                                            <span class="badge bg-primary">In Process</span>
                                        <?php elseif($status == "closed"): ?>
                                            <span class="badge bg-success">Resolved</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="complaint-details.php?cid=<?php echo htmlentities($row['complaintNumber']); ?>" 
                                               class="btn btn-sm btn-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($status != "closed"): ?>
                                                <a href="track-complaint.php?cid=<?php echo htmlentities($row['complaintNumber']); ?>" 
                                                   class="btn btn-sm btn-info" title="Track Status">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Export Options -->
                <div class="mt-4 p-3 bg-light rounded export-options">
                    <h6><i class="fas fa-download me-2"></i>Export Complaint History</h6>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                        <form method="post" style="display:inline;">
                            <button type="submit" name="export_pdf" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </button>
                        </form>
                        <form method="post" style="display:inline;">
                            <button type="submit" name="export_excel" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Excel
                            </button>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Complaints Found</h4>
                    <p class="text-muted mb-4">You haven't submitted any complaints yet.</p>
                    <a href="register-complaint.php" class="btn btn-warning btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Submit Your First Complaint
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="row mt-4">
            <div class="col-lg-8">
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

                <!-- Feedback Form -->
                <div class="welcome-card">
                    <h4 class="mb-4">
                        <i class="fas fa-star me-2 text-warning"></i>Feedback Form
                    </h4>

                    <form method="post" id="feedbackForm" novalidate>
                        <!-- Complaint Selection -->
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-file-alt me-2"></i>Select Complaint
                            </h5>
                            
                            <?php if($complaint_details): ?>
                                <!-- Pre-selected complaint -->
                                <div class="selected-complaint p-3 bg-light rounded mb-3">
                                    <h6>Selected Complaint:</h6>
                                    <p class="mb-1"><strong>Complaint #GUVNL-<?php echo htmlentities($complaint_details['complaintNumber']); ?></strong></p>
                                    <p class="mb-1"><strong>Type:</strong> <?php echo htmlentities($complaint_details['complaintType']); ?></p>
                                    <p class="mb-1"><strong>Nature:</strong> <?php echo htmlentities($complaint_details['noc']); ?></p>
                                    <p class="mb-0"><strong>Registered:</strong> <?php echo htmlentities($complaint_details['regDate']); ?></p>
                                    <input type="hidden" name="complaint_id" value="<?php echo $complaint_details['complaintNumber']; ?>">
                                </div>
                                
                                <?php if(mysqli_num_rows($other_complaints) > 0): ?>
                                    <div class="mt-3">
                                        <label class="form-label fw-bold">Or select another complaint:</label>
                                        <select name="complaint_id_alt" id="complaintSelect" class="form-select">
                                            <option value="">-- Select different complaint --</option>
                                            <?php while($complaint = mysqli_fetch_array($other_complaints)): ?>
                                                <option value="<?php echo $complaint['complaintNumber']; ?>">
                                                    GUVNL-<?php echo htmlentities($complaint['complaintNumber']); ?> - 
                                                    <?php echo htmlentities($complaint['complaintType']); ?> 
                                                    (<?php echo htmlentities($complaint['regDate']); ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <!-- No pre-selected complaint -->
                                <?php 
                                $all_complaints = mysqli_query($bd, "SELECT complaintNumber, complaintType, noc, regDate 
                                                                   FROM tblcomplaints 
                                                                   WHERE userId = '$user_id' 
                                                                   AND status = 'closed' 
                                                                   AND complaintNumber NOT IN (
                                                                       SELECT complaintId FROM tblfeedback WHERE userId = '$user_id'
                                                                   )
                                                                   ORDER BY regDate DESC");
                                ?>
                                
                                <?php if(mysqli_num_rows($all_complaints) > 0): ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Select Complaint <span class="text-danger">*</span></label>
                                        <select name="complaint_id" id="complaintSelect" class="form-select" required>
                                            <option value="">-- Select a resolved complaint --</option>
                                            <?php while($complaint = mysqli_fetch_array($all_complaints)): ?>
                                                <option value="<?php echo $complaint['complaintNumber']; ?>" 
                                                        <?php echo ($complaint_id == $complaint['complaintNumber']) ? 'selected' : ''; ?>>
                                                    GUVNL-<?php echo htmlentities($complaint['complaintNumber']); ?> - 
                                                    <?php echo htmlentities($complaint['complaintType']); ?> 
                                                    (<?php echo htmlentities($complaint['regDate']); ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <small class="form-text text-muted">Only resolved complaints without feedback are shown</small>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No complaints available for feedback. All your resolved complaints have been reviewed or no complaints are resolved yet.
                                        <div class="mt-2">
                                            <a href="complaint-history.php" class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-history me-1"></i>View Complaint History
                                            </a>
                                            <a href="feedback.php" class="btn btn-sm btn-warning">
                                                <i class="fas fa-arrow-left me-1"></i>Back to Feedback
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <?php if(($complaint_details || mysqli_num_rows($all_complaints) > 0) && (!isset($all_complaints) || mysqli_num_rows($all_complaints) > 0)): ?>
                        <!-- Rating Section -->
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-star me-2 text-warning"></i>Rating
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Overall Experience <span class="text-danger">*</span></label>
                                <div class="rating-container text-center">
                                    <div class="rating-input mb-3">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" class="rating-radio" required>
                                            <label for="star<?php echo $i; ?>" class="rating-label">
                                                <i class="fas fa-star"></i>
                                            </label>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="rating-labels">
                                        <small class="text-muted" id="ratingText">Select your rating (1 = Very Poor, 5 = Excellent)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Details -->
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-comment-dots me-2"></i>Feedback Details
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Feedback Type <span class="text-danger">*</span></label>
                                        <select name="feedback_type" class="form-select" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="Complaint Resolution">Complaint Resolution</option>
                                            <option value="Staff Behavior">Staff Behavior</option>
                                            <option value="Response Time">Response Time</option>
                                            <option value="Service Quality">Service Quality</option>
                                            <option value="Communication">Communication</option>
                                            <option value="Technical Support">Technical Support</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="comments" class="form-label fw-bold">Detailed Comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="5" 
                                          placeholder="Please share your detailed experience, suggestions for improvement, or any specific feedback..."></textarea>
                                <div class="form-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Your constructive feedback helps us serve you better.
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="form-section">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="feedback.php" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Feedback
                                </a>
                                <button type="submit" name="submit_feedback" class="btn btn-warning">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="info-card shadow-sm p-4 rounded mb-4 w-100" style="background: linear-gradient(135deg, #f8fafc 80%, #e3eafc 100%); border-left: 5px solid #007bff;">
                    <h5 class="info-card-title mb-3" style="color: #007bff; font-weight: 600;">
                        <i class="fas fa-headset me-2"></i>Need Help? Contact Support
                    </h5>
                    <p class="info-card-text mb-3" style="color: #495057;">
                        Our dedicated team is available 24/7 to resolve your queries and complaints quickly.
                    </p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-center">
                            <span class="badge bg-primary me-2" style="font-size:1em;">
                                <i class="fas fa-phone-alt"></i>
                            </span>
                            <span style="font-weight: 500;">Toll Free:</span>
                            <span class="ms-1" style="color:#007bff;">1800-233-3333</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <span class="badge bg-success me-2" style="font-size:1em;">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <span style="font-weight: 500;">Email:</span>
                            <a href="mailto:support@example.com" class="ms-1" style="color:#28a745; text-decoration:underline;">support@example.com</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        </div>

<script>
// Simple and reliable filter function
function filterComplaints() {
    const filterValue = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#complaintTableBody tr');
    
    console.log('Filtering by:', filterValue); // Debug log
    
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        console.log('Row status:', rowStatus); // Debug log
        
        if (filterValue === 'all' || rowStatus === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Initialize table rows with animation
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#complaintTableBody tr');
    rows.forEach((row, index) => {
        setTimeout(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 100);
    });
});

// Print functionality
window.addEventListener('beforeprint', function() {
    document.querySelector('.sidebar-custom').style.display = 'none';
    document.querySelector('.navbar-custom').style.display = 'none';
    document.querySelector('.dashboard-header').style.display = 'none';
    document.querySelector('.export-options').style.display = 'none';
});

window.addEventListener('afterprint', function() {
    document.querySelector('.sidebar-custom').style.display = '';
    document.querySelector('.navbar-custom').style.display = '';
    document.querySelector('.dashboard-header').style.display = '';
    document.querySelector('.export-options').style.display = '';
});
</script>

<style>
.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.stat-total .stat-icon { color: #6c757d; }
.stat-pending .stat-icon { color: #ffc107; }
.stat-process .stat-icon { color: #007bff; }
.stat-closed .stat-icon { color: #28a745; }

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-weight: 500;
}

#complaintTableBody tr {
    opacity: 0;
    transform: translateX(-20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
}

@media print {
    .sidebar-custom,
    .navbar-custom,
    .dashboard-header,
    .export-options,
    .btn-group {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        margin-top: 0 !important;
    }
}
</style>
</div>
<?php
include('includes/footer.php');
?>