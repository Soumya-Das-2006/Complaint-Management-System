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
$complaint_number = $_GET['cid'];
$query = mysqli_query($bd, "SELECT tblcomplaints.*, category.categoryName as catname 
                           FROM tblcomplaints 
                           JOIN category ON category.id = tblcomplaints.category 
                           WHERE userId = '".$_SESSION['id']."' 
                           AND complaintNumber = '$complaint_number'");
$complaint_exists = mysqli_num_rows($query) > 0;
?>

<!-- Main Content -->
<div class="main-content" style="margin-top: 50px;">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title">
                <i class="fas fa-file-alt me-2"></i>Complaint Details
            </h1>
            <p class="dashboard-subtitle">Complete information about your complaint #<?php echo htmlentities($complaint_number); ?></p>
        </div>
    </div>

    <div class="container-fluid">
        <?php if(!$complaint_exists): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Complaint not found or you don't have permission to view this complaint.
            </div>
        <?php else: ?>
            <?php while($row = mysqli_fetch_array($query)): ?>
                <!-- Complaint Summary Card -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="welcome-card">
                            <div class="row align-items-center mb-4">
                                <div class="col-md-8">
                                    <h3 class="mb-2">
                                        <i class="fas fa-hashtag me-2 text-primary"></i>
                                        Complaint #GUVNL-<?php echo htmlentities($row['complaintNumber']); ?>
                                    </h3>
                                    <h5 class="text-muted"><?php echo htmlentities($row['noc']); ?></h5>
                                </div>
                                <div class="col-md-4 text-end">
                                    <?php
                                    $status = $row['status'];
                                    if($status == "" || $status == "NULL" || $status === null) {
                                        echo '<span class="badge bg-warning fs-6"><i class="fas fa-clock me-1"></i>Pending</span>';
                                    } elseif($status == "in Process") {
                                        echo '<span class="badge bg-primary fs-6"><i class="fas fa-cog me-1"></i>In Process</span>';
                                    } elseif($status == "closed") {
                                        echo '<span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i>Resolved</span>';
                                    } else {
                                        echo '<span class="badge bg-info fs-6">' . htmlentities($status) . '</span>';
                                    }
                                    ?>
                                    <p class="text-muted mb-0 mt-2">
                                        <small>Registered: <?php echo htmlentities($row['regDate']); ?></small>
                                    </p>
                                </div>
                            </div>

                            <!-- Complaint Information Grid -->
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h5 class="info-card-title">
                                            <i class="fas fa-info-circle me-2"></i>Basic Information
                                        </h5>
                                        <div class="info-list">
                                            <div class="info-item">
                                                <span class="info-label">Complaint Number:</span>
                                                <span class="info-value">GUVNL-<?php echo htmlentities($row['complaintNumber']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Registration Date:</span>
                                                <span class="info-value"><?php echo htmlentities($row['regDate']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Category:</span>
                                                <span class="info-value"><?php echo htmlentities($row['catname']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Sub Category:</span>
                                                <span class="info-value"><?php echo htmlentities($row['subcategory']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Complaint Details -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h5 class="info-card-title">
                                            <i class="fas fa-clipboard-list me-2"></i>Complaint Details
                                        </h5>
                                        <div class="info-list">
                                            <div class="info-item">
                                                <span class="info-label">Complaint Type:</span>
                                                <span class="info-value"><?php echo htmlentities($row['complaintType']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">State:</span>
                                                <span class="info-value"><?php echo htmlentities($row['state']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Nature of Complaint:</span>
                                                <span class="info-value"><?php echo htmlentities($row['noc']); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Attachment:</span>
                                                <span class="info-value">
                                                    <?php 
                                                    $cfile = $row['complaintFile'];
                                                    if($cfile == "" || $cfile == "NULL") {
                                                        echo '<span class="text-muted">No file attached</span>';
                                                    } else { 
                                                    ?>
                                                        <a href="complaintdocs/<?php echo htmlentities($row['complaintFile']); ?>" 
                                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="fas fa-download me-1"></i>Download File
                                                        </a>
                                                    <?php } ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Complaint Description -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="info-card">
                                        <h5 class="info-card-title">
                                            <i class="fas fa-file-text me-2"></i>Complaint Description
                                        </h5>
                                        <div class="complaint-description">
                                            <p><?php echo nl2br(htmlentities($row['complaintDetails'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </div>
                             <?php if($row['status'] == 'closed'): ?>
    <?php
    // Check if feedback already given
    $feedback_check = mysqli_query($bd, "SELECT id FROM tblfeedback WHERE complaintId='".$row['complaintNumber']."' AND userId='".$_SESSION['id']."'");
    if(mysqli_num_rows($feedback_check) == 0):
    ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-success">
                    <h6><i class="fas fa-star me-2"></i>We value your feedback!</h6>
                    <p>This complaint has been resolved. Please share your experience to help us improve.</p>
                    <a href="feedback.php?complaintId=<?php echo $row['complaintNumber']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Submit Feedback
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-check-circle me-2"></i>Thank you for your feedback!
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
                    </div>

                    <!-- Sidebar - Status & Remarks -->
                    <div class="col-lg-4">
                        <!-- Status Timeline -->
                        <div class="quick-actions">
                            <h4 class="mb-4">
                                <i class="fas fa-history me-2"></i>Status Timeline
                            </h4>
                            <div class="status-timeline">
                                <?php
                                // Get remarks and status history
                                $ret = mysqli_query($bd, "SELECT complaintremark.remark as remark, 
                                                         complaintremark.status as sstatus, 
                                                         complaintremark.remarkDate as rdate 
                                                         FROM complaintremark 
                                                         JOIN tblcomplaints ON tblcomplaints.complaintNumber = complaintremark.complaintNumber 
                                                         WHERE complaintremark.complaintNumber = '$complaint_number' 
                                                         ORDER BY complaintremark.remarkDate DESC");
                                
                                if(mysqli_num_rows($ret) > 0) {
                                    while($rw = mysqli_fetch_array($ret)) {
                                ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Status Update</h6>
                                                <p class="timeline-text"><?php echo htmlentities($rw['remark']); ?></p>
                                                <div class="timeline-meta">
                                                    <span class="badge bg-<?php 
                                                        if($rw['sstatus'] == 'closed') echo 'success';
                                                        elseif($rw['sstatus'] == 'in Process') echo 'primary';
                                                        else echo 'warning';
                                                    ?>">
                                                        <?php echo htmlentities($rw['sstatus']); ?>
                                                    </span>
                                                    <span class="timeline-date">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php echo htmlentities($rw['rdate']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                <?php 
                                    }
                                } else {
                                    echo '<div class="text-center py-3">';
                                    echo '<i class="fas fa-inbox fa-2x text-muted mb-2"></i>';
                                    echo '<p class="text-muted">No remarks yet</p>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Final Status -->
                        <div class="quick-actions mt-4">
                            <h4 class="mb-3">
                                <i class="fas fa-flag me-2"></i>Final Status
                            </h4>
                            <div class="final-status">
                                <?php
                                $final_status = $row['status'];
                                if($final_status == "" || $final_status == "NULL" || $final_status === null) {
                                    echo '<div class="alert alert-warning">';
                                    echo '<i class="fas fa-clock me-2"></i>';
                                    echo '<strong>Not Processed Yet</strong>';
                                    echo '<p class="mb-0 mt-1 small">Your complaint is in queue and will be processed soon.</p>';
                                    echo '</div>';
                                } else {
                                    $alert_class = '';
                                    $icon = '';
                                    if($final_status == 'closed') {
                                        $alert_class = 'alert-success';
                                        $icon = 'fa-check-circle';
                                    } elseif($final_status == 'in Process') {
                                        $alert_class = 'alert-primary';
                                        $icon = 'fa-cog';
                                    } else {
                                        $alert_class = 'alert-info';
                                        $icon = 'fa-info-circle';
                                    }
                                    echo '<div class="alert ' . $alert_class . '">';
                                    echo '<i class="fas ' . $icon . ' me-2"></i>';
                                    echo '<strong>' . htmlentities(ucfirst($final_status)) . '</strong>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="quick-actions mt-4">
                            <h4 class="mb-3">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </h4>
                            <div class="d-grid gap-2">
                                <a href="complaint-history.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to History
                                </a>
                                <button onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="fas fa-print me-2"></i>Print Details
                                </button>
                                <a href="register-complaint.php" class="btn btn-warning">
                                    <i class="fas fa-plus-circle me-2"></i>New Complaint
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.info-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border-left: 4px solid var(--guvnl-primary);
}

.info-card-title {
    color: var(--guvnl-primary);
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-label {
    font-weight: 600;
    color: #555;
    min-width: 160px;
}

.info-value {
    color: #333;
    text-align: right;
    flex: 1;
}

.complaint-description {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid var(--guvnl-secondary);
}

.complaint-description p {
    margin-bottom: 0;
    line-height: 1.6;
    color: #555;
}

.status-timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
    padding-left: 25px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--guvnl-primary);
    border: 3px solid white;
    box-shadow: 0 0 0 2px var(--guvnl-primary);
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid var(--guvnl-primary);
}

.timeline-title {
    font-weight: 600;
    color: var(--guvnl-primary);
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.timeline-text {
    color: #666;
    margin-bottom: 10px;
    font-size: 0.9rem;
    line-height: 1.5;
}

.timeline-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
}

.timeline-date {
    color: #888;
}

.final-status .alert {
    border: none;
    border-radius: 8px;
    padding: 15px;
}

.badge {
    font-size: 0.75rem;
    padding: 6px 12px;
}

@media (max-width: 768px) {
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .info-label, .info-value {
        text-align: left;
    }
    
    .info-value {
        width: 100%;
    }
    
    .timeline-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to timeline items
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            item.style.transition = 'opacity 0.5s, transform 0.5s';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, index * 200);
    });

    // Print functionality
    window.addEventListener('beforeprint', function() {
        document.querySelector('.sidebar-custom').style.display = 'none';
        document.querySelector('.navbar-custom').style.display = 'none';
    });
    
    window.addEventListener('afterprint', function() {
        document.querySelector('.sidebar-custom').style.display = '';
        document.querySelector('.navbar-custom').style.display = '';
    });
});
</script>

<?php

// Include footer
include('includes/footer.php');
?>