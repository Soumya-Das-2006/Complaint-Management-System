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
$user_id = $_SESSION['id'];
$successmsg = "";
$errormsg = "";

// Handle feedback submission
if(isset($_POST['submit_feedback'])) {
    $complaint_id = intval($_POST['complaint_id']);
    $rating = intval($_POST['rating']);
    $feedback_type = mysqli_real_escape_string($bd, $_POST['feedback_type']);
    $comments = mysqli_real_escape_string($bd, $_POST['comments']);
    
    // Validation
    if(empty($complaint_id) || empty($rating) || empty($feedback_type)) {
        $errormsg = "Please fill all required fields.";
    } elseif($rating < 1 || $rating > 5) {
        $errormsg = "Please select a valid rating.";
    } else {
        // Check if complaint belongs to user and is closed
        $check_stmt = mysqli_prepare($bd, "SELECT complaintNumber FROM tblcomplaints 
                                         WHERE complaintNumber = ? AND userId = ? AND status = 'closed'");
        mysqli_stmt_bind_param($check_stmt, "ii", $complaint_id, $user_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if(mysqli_stmt_num_rows($check_stmt) > 0) {
            // Check if feedback already exists
            $existing_stmt = mysqli_prepare($bd, "SELECT id FROM tblfeedback WHERE complaintId = ? AND userId = ?");
            mysqli_stmt_bind_param($existing_stmt, "ii", $complaint_id, $user_id);
            mysqli_stmt_execute($existing_stmt);
            mysqli_stmt_store_result($existing_stmt);
            
            if(mysqli_stmt_num_rows($existing_stmt) > 0) {
                $errormsg = "You have already submitted feedback for this complaint.";
            } else {
                // Insert feedback
                $insert_stmt = mysqli_prepare($bd, "INSERT INTO tblfeedback (complaintId, userId, rating, feedbackType, comments, submissionDate) 
                                                  VALUES (?, ?, ?, ?, ?, ?)");
                $current_date = date('Y-m-d H:i:s');
                mysqli_stmt_bind_param($insert_stmt, "iiisss", $complaint_id, $user_id, $rating, $feedback_type, $comments, $current_date);
                
                if(mysqli_stmt_execute($insert_stmt)) {
                    $successmsg = "Thank you! Your feedback has been submitted successfully.";
                } else {
                    $errormsg = "Error submitting feedback. Please try again.";
                }
                mysqli_stmt_close($insert_stmt);
            }
            mysqli_stmt_close($existing_stmt);
        } else {
            $errormsg = "Invalid complaint selection or complaint is not resolved.";
        }
        mysqli_stmt_close($check_stmt);
    }
}

// Fetch user's resolved complaints available for feedback
$complaints_query = mysqli_query($bd, "SELECT c.complaintNumber, c.complaintType, c.noc, c.regDate 
                                      FROM tblcomplaints c 
                                      WHERE c.userId = '$user_id' 
                                      AND c.status = 'closed' 
                                      AND c.complaintNumber NOT IN (
                                          SELECT complaintId FROM tblfeedback WHERE userId = '$user_id'
                                      )
                                      ORDER BY c.regDate DESC");

// Fetch user's previous feedback (limited to 5 for sidebar)
$previous_feedback_sidebar = mysqli_query($bd, "SELECT f.*, c.complaintNumber, c.complaintType, c.noc 
                                               FROM tblfeedback f 
                                               JOIN tblcomplaints c ON f.complaintId = c.complaintNumber 
                                               WHERE f.userId = '$user_id' 
                                               ORDER BY f.submissionDate DESC 
                                               LIMIT 5");

// Fetch all feedback for main section
$all_previous_feedback = mysqli_query($bd, "SELECT f.*, c.complaintNumber, c.complaintType, c.noc 
                                           FROM tblfeedback f 
                                           JOIN tblcomplaints c ON f.complaintId = c.complaintNumber 
                                           WHERE f.userId = '$user_id' 
                                           ORDER BY f.submissionDate DESC");
?>

<!-- Main Content -->
<div class="main-content" style="margin-top: 50px;">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1 class="dashboard-title"><i class="fas fa-comments me-2"></i>Submit Feedback</h1>
            <p class="dashboard-subtitle">Share your experience with GUVNL complaint resolution</p>
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
                <!-- Available Complaints for Feedback -->
                <div class="welcome-card">
                    <h4 class="mb-4">
                        <i class="fas fa-star me-2 text-warning"></i>Rate Your Resolved Complaints
                    </h4>
                    
                    <?php if(mysqli_num_rows($complaints_query) > 0): ?>
                        <div class="row">
                            <?php while($complaint = mysqli_fetch_array($complaints_query)): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="complaint-card">
                                        <div class="complaint-header">
                                            <h6>Complaint #GUVNL-<?php echo htmlentities($complaint['complaintNumber']); ?></h6>
                                            <span class="badge bg-success">Resolved</span>
                                        </div>
                                        <div class="complaint-body">
                                            <p class="complaint-type">
                                                <i class="fas fa-tag me-2"></i>
                                                <?php echo htmlentities($complaint['complaintType']); ?>
                                            </p>
                                            <p class="complaint-nature">
                                                <strong>Nature:</strong> <?php echo htmlentities($complaint['noc']); ?>
                                            </p>
                                            <p class="complaint-date">
                                                <i class="fas fa-calendar me-2"></i>
                                                <?php echo htmlentities($complaint['regDate']); ?>
                                            </p>
                                        </div>
                                        <div class="complaint-footer">
                                            <button type="button" class="btn btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#feedbackModal"
                                                    data-complaint-id="<?php echo $complaint['complaintNumber']; ?>"
                                                    data-complaint-details="Type: <?php echo htmlentities($complaint['complaintType']); ?> | Nature: <?php echo htmlentities($complaint['noc']); ?>">
                                                <i class="fas fa-edit me-1"></i>Give Feedback
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Complaints Available for Feedback</h4>
                            <p class="text-muted mb-4">You have either provided feedback for all resolved complaints or no complaints have been resolved yet.</p>
                            <a href="complaint-history.php" class="btn btn-primary">
                                <i class="fas fa-history me-2"></i>View Complaint History
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
<!-- Add this after the "Available Complaints for Feedback" section -->

<!-- Direct Feedback Form Section -->
<div class="welcome-card mt-4">
    <h4 class="mb-4">
        <i class="fas fa-edit me-2 text-primary"></i>Submit Feedback Directly
    </h4>
    
    <form method="post" id="directFeedbackForm">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Complaint <span class="text-danger">*</span></label>
                    <select name="complaint_id" class="form-select" required>
                        <option value="">-- Select Complaint --</option>
                        <?php 
                        $available_complaints = mysqli_query($bd, "SELECT complaintNumber, complaintType, noc, regDate 
                                                                FROM tblcomplaints 
                                                                WHERE userId = '$user_id' 
                                                                AND status = 'closed' 
                                                                AND complaintNumber NOT IN (
                                                                    SELECT complaintId FROM tblfeedback WHERE userId = '$user_id'
                                                                )
                                                                ORDER BY regDate DESC");
                        while($comp = mysqli_fetch_array($available_complaints)): 
                        ?>
                            <option value="<?php echo $comp['complaintNumber']; ?>">
                                GUVNL-<?php echo htmlentities($comp['complaintNumber']); ?> - 
                                <?php echo htmlentities($comp['complaintType']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
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
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
            <div class="rating-input">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" id="directStar<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" class="rating-radio" required>
                    <label for="directStar<?php echo $i; ?>" class="rating-label">
                        <i class="fas fa-star"></i>
                    </label>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="directComments" class="form-label fw-bold">Comments</label>
            <textarea class="form-control" id="directComments" name="comments" rows="3" 
                      placeholder="Share your experience..."></textarea>
        </div>
        
        <div class="text-end">
            <button type="submit" name="submit_feedback" class="btn btn-warning">
                <i class="fas fa-paper-plane me-2"></i>Submit Feedback
            </button>
        </div>
    </form>
</div>
                <!-- All Previous Feedback Section -->
                <div class="welcome-card mt-4">
                    <h4 class="mb-4">
                        <i class="fas fa-history me-2 text-info"></i>Your Feedback History
                    </h4>
                    
                    <?php if(mysqli_num_rows($all_previous_feedback) > 0): ?>
                        <div class="feedback-history">
                            <?php while($feedback = mysqli_fetch_array($all_previous_feedback)): ?>
                                <div class="feedback-item">
                                    <div class="feedback-header">
                                        <div class="feedback-complaint-info">
                                            <h6>Complaint #GUVNL-<?php echo htmlentities($feedback['complaintNumber']); ?></h6>
                                            <span class="feedback-type-badge <?php echo getFeedbackTypeClass($feedback['feedbackType']); ?>">
                                                <?php echo htmlentities($feedback['feedbackType']); ?>
                                            </span>
                                        </div>
                                        <div class="feedback-date">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d M Y, h:i A', strtotime($feedback['submissionDate'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="feedback-rating">
                                        <div class="rating-display">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $feedback['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                            <span class="rating-text">(<?php echo $feedback['rating']; ?>/5)</span>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($feedback['comments'])): ?>
                                        <div class="feedback-comments">
                                            <p class="mb-0"><?php echo nl2br(htmlentities($feedback['comments'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="feedback-complaint-details">
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i><?php echo htmlentities($feedback['complaintType']); ?> | 
                                            <i class="fas fa-info-circle me-1"></i><?php echo htmlentities($feedback['noc']); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Feedback History</h5>
                            <p class="text-muted">You haven't submitted any feedback yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Feedback Guidelines -->
                <div class="quick-actions">
                    <h4 class="mb-4">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Feedback Guidelines
                    </h4>
                    
                    <div class="guideline-list">
                        <div class="guideline-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Be honest and specific about your experience</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Focus on facts and constructive suggestions</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Your feedback helps us improve our services</span>
                        </div>
                        <div class="guideline-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>All feedback is reviewed and taken seriously</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6><i class="fas fa-star me-2 text-warning"></i>Rating Guide</h6>
                        <div class="rating-guide">
                            <div class="rating-level">
                                <span class="stars">★☆☆☆☆</span>
                                <span>1 - Very Poor</span>
                            </div>
                            <div class="rating-level">
                                <span class="stars">★★☆☆☆</span>
                                <span>2 - Poor</span>
                            </div>
                            <div class="rating-level">
                                <span class="stars">★★★☆☆</span>
                                <span>3 - Average</span>
                            </div>
                            <div class="rating-level">
                                <span class="stars">★★★★☆</span>
                                <span>4 - Good</span>
                            </div>
                            <div class="rating-level">
                                <span class="stars">★★★★★</span>
                                <span>5 - Excellent</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Feedback Sidebar -->
                <div class="quick-actions mt-4">
                    <h4 class="mb-3">
                        <i class="fas fa-clock me-2 text-success"></i>Recent Feedback
                    </h4>
                    
                    <?php if(mysqli_num_rows($previous_feedback_sidebar) > 0): ?>
                        <div class="recent-feedback-sidebar">
                            <?php while($feedback = mysqli_fetch_array($previous_feedback_sidebar)): ?>
                                <div class="recent-feedback-item">
                                    <div class="recent-feedback-header">
                                        <strong>GUVNL-<?php echo htmlentities($feedback['complaintNumber']); ?></strong>
                                        <div class="recent-rating">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $feedback['rating'] ? 'text-warning' : 'text-muted'; ?>" style="font-size: 0.8rem;"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="recent-feedback-type">
                                        <span class="badge <?php echo getFeedbackTypeClass($feedback['feedbackType']); ?> sm">
                                            <?php echo htmlentities($feedback['feedbackType']); ?>
                                        </span>
                                    </div>
                                    <?php if(!empty($feedback['comments'])): ?>
                                        <p class="recent-comment-preview">
                                            <?php 
                                            $comment = htmlentities($feedback['comments']);
                                            echo strlen($comment) > 80 ? substr($comment, 0, 80) . '...' : $comment;
                                            ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="recent-feedback-date">
                                        <small class="text-muted">
                                            <?php echo date('d M Y', strtotime($feedback['submissionDate'])); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            
                            <?php 
                            $total_feedback = mysqli_num_rows($all_previous_feedback);
                            if($total_feedback > 5): 
                            ?>
                                <div class="text-center mt-3">
                                    <a href="#feedback-history" class="btn btn-outline-primary btn-sm" onclick="scrollToFeedbackHistory()">
                                        View All (<?php echo $total_feedback; ?>) Feedback
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-comment-slash fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">No feedback submitted yet</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Feedback Statistics -->
                <div class="quick-actions mt-4">
                    <h4 class="mb-3">
                        <i class="fas fa-chart-bar me-2 text-info"></i>Feedback Summary
                    </h4>
                    
                    <?php
                    // Calculate feedback statistics
                    $total_feedback = mysqli_num_rows($all_previous_feedback);
                    if($total_feedback > 0) {
                        mysqli_data_seek($all_previous_feedback, 0);
                        
                        $rating_sum = 0;
                        $rating_count = 0;
                        $type_counts = [];
                        
                        while($feedback = mysqli_fetch_array($all_previous_feedback)) {
                            $rating_sum += $feedback['rating'];
                            $rating_count++;
                            
                            $type = $feedback['feedbackType'];
                            if(!isset($type_counts[$type])) {
                                $type_counts[$type] = 0;
                            }
                            $type_counts[$type]++;
                        }
                        
                        $average_rating = $rating_count > 0 ? round($rating_sum / $rating_count, 1) : 0;
                    ?>
                        <div class="feedback-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $total_feedback; ?></div>
                                <div class="stat-label">Total Feedback</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $average_rating; ?>/5</div>
                                <div class="stat-label">Average Rating</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-value">
                                    <?php 
                                    $latest_feedback = mysqli_fetch_array(mysqli_query($bd, 
                                        "SELECT submissionDate FROM tblfeedback WHERE userId = '$user_id' ORDER BY submissionDate DESC LIMIT 1"));
                                    echo $latest_feedback ? date('d M', strtotime($latest_feedback['submissionDate'])) : 'N/A';
                                    ?>
                                </div>
                                <div class="stat-label">Last Feedback</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Feedback Types:</h6>
                            <div class="feedback-types-list">
                                <?php foreach($type_counts as $type => $count): ?>
                                    <div class="feedback-type-item">
                                        <span class="feedback-type-name"><?php echo htmlentities($type); ?></span>
                                        <span class="feedback-type-count"><?php echo $count; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="text-center py-3">
                            <p class="text-muted small">No feedback statistics available</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function for feedback type classes
function getFeedbackTypeClass($type) {
    switch($type) {
        case 'Complaint Resolution': return 'bg-primary';
        case 'Staff Behavior': return 'bg-info';
        case 'Response Time': return 'bg-warning';
        case 'Service Quality': return 'bg-success';
        case 'Communication': return 'bg-purple';
        case 'Technical Support': return 'bg-teal';
        case 'Other': return 'bg-secondary';
        default: return 'bg-dark';
    }
}
?>

<!-- Feedback Modal (Same as before) -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" id="feedbackForm">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="feedbackModalLabel">
                        <i class="fas fa-edit me-2"></i>Submit Feedback
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="complaint_id" id="modalComplaintId">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Complaint Details</label>
                        <div class="complaint-details-preview p-3 bg-light rounded">
                            <p class="mb-1"><strong>Complaint #:</strong> <span id="modalComplaintNumber"></span></p>
                            <p class="mb-0"><strong>Details:</strong> <span id="modalComplaintDetails"></span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" class="rating-radio">
                                        <label for="star<?php echo $i; ?>" class="rating-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                                <small class="text-muted">Click on stars to rate (1 = Poor, 5 = Excellent)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Feedback Type <span class="text-danger">*</span></label>
                                <select name="feedback_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="Complaint Resolution">Complaint Resolution</option>
                                    <option value="Staff Behavior">Staff Behavior</option>
                                    <option value="Response Time">Response Time</option>
                                    <option value="Service Quality">Service Quality</option>
                                    <option value="Communication">Communication</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comments" class="form-label fw-bold">Additional Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="4" 
                                  placeholder="Share your detailed experience, suggestions for improvement, or any other comments..."></textarea>
                        <div class="form-text">Your feedback helps us improve our services.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_feedback" class="btn btn-warning">
                        <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Existing styles... */

/* New styles for feedback display */
.feedback-history {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feedback-item {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    transition: all 0.3s ease;
}

.feedback-item:hover {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.feedback-complaint-info h6 {
    margin-bottom: 5px;
    color: #333;
}

.feedback-type-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.feedback-rating {
    margin-bottom: 15px;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 5px;
}

.rating-text {
    font-weight: 600;
    color: #666;
    margin-left: 8px;
}

.feedback-comments {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    border-left: 3px solid var(--guvnl-primary);
}

.feedback-comments p {
    margin-bottom: 0;
    line-height: 1.5;
}

.feedback-complaint-details {
    border-top: 1px solid #e9ecef;
    padding-top: 10px;
}

/* Recent Feedback Sidebar */
.recent-feedback-sidebar {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.recent-feedback-item {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.2s ease;
}

.recent-feedback-item:hover {
    background: #f8f9fa;
}

.recent-feedback-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.recent-rating {
    display: flex;
    gap: 2px;
}

.recent-feedback-type {
    margin-bottom: 8px;
}

.recent-comment-preview {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 8px;
    line-height: 1.4;
}

.recent-feedback-date {
    text-align: right;
}

/* Feedback Statistics */
.feedback-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--guvnl-primary);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
}

.feedback-types-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.feedback-type-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: white;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.feedback-type-name {
    font-size: 0.85rem;
    color: #333;
}

.feedback-type-count {
    background: var(--guvnl-primary);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Additional badge colors */
.bg-purple { background-color: #6f42c1; }
.bg-teal { background-color: #20c997; }

.badge.sm {
    font-size: 0.7rem;
    padding: 3px 6px;
}

@media (max-width: 768px) {
    .feedback-header {
        flex-direction: column;
        gap: 10px;
    }
    
    .feedback-stats {
        grid-template-columns: 1fr;
    }
    
    .recent-feedback-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Existing modal functionality...
    const feedbackModal = document.getElementById('feedbackModal');
    feedbackModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const complaintId = button.getAttribute('data-complaint-id');
        const complaintDetails = button.getAttribute('data-complaint-details');
        
        document.getElementById('modalComplaintId').value = complaintId;
        document.getElementById('modalComplaintNumber').textContent = 'GUVNL-' + complaintId;
        document.getElementById('modalComplaintDetails').textContent = complaintDetails;
        
        // Reset form
        document.getElementById('feedbackForm').reset();
        
        // Reset stars
        document.querySelectorAll('.rating-radio').forEach(radio => {
            radio.checked = false;
        });
    });

    // Form validation
    document.getElementById('feedbackForm').addEventListener('submit', function(e) {
        const rating = document.querySelector('input[name="rating"]:checked');
        const feedbackType = document.querySelector('select[name="feedback_type"]');
        
        if(!rating) {
            e.preventDefault();
            alert('Please select a rating.');
            return false;
        }
        
        if(!feedbackType.value) {
            e.preventDefault();
            alert('Please select a feedback type.');
            return false;
        }
    });
});

function scrollToFeedbackHistory() {
    const element = document.querySelector('.feedback-history');
    if(element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>
<?php
// Include footer
include('includes/footer.php');
?>