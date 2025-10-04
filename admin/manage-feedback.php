<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Feedback</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .rating-stars { color: #ffc107; }
        .feedback-type { padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .type-complaint { background: #dc3545; color: white; }
        .type-suggestion { background: #28a745; color: white; }
        .type-appreciation { background: #007bff; color: white; }
        .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; }
        .status-read { background: #28a745; color: white; }
        .status-unread { background: #dc3545; color: white; }
        .table-actions { white-space: nowrap; }
        .table-responsive { overflow-x: auto; }
        .feedback-row-unread { background-color: #f8f9fa; font-weight: 500; }
    </style>
</head>
<body>
    <?php include('include/header.php');?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php');?>                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-comments"></i> Customer Feedback</h3>
                            </div>
                            <div class="module-body">
                                <?php
                                // Handle actions
                                if(isset($_GET['action'])) {
                                    $action = $_GET['action'];
                                    $id = $_GET['id'];
                                    
                                    if($action == 'markread' && !empty($id)) {
                                        mysqli_query($bd, "UPDATE tblfeedback SET isRead = 1 WHERE id = '$id'");
                                        echo '<div class="alert alert-success">Feedback marked as read.</div>';
                                    }
                                    
                                    if($action == 'markunread' && !empty($id)) {
                                        mysqli_query($bd, "UPDATE tblfeedback SET isRead = 0 WHERE id = '$id'");
                                        echo '<div class="alert alert-success">Feedback marked as unread.</div>';
                                    }
                                    
                                    if($action == 'delete' && !empty($id)) {
                                        mysqli_query($bd, "DELETE FROM tblfeedback WHERE id = '$id'");
                                        echo '<div class="alert alert-success">Feedback deleted successfully.</div>';
                                    }
                                }
                                
                                // Handle bulk actions
                                if(isset($_POST['bulk_action'])) {
                                    $bulk_action = $_POST['bulk_action'];
                                    $feedback_ids = isset($_POST['feedback_ids']) ? $_POST['feedback_ids'] : array();
                                    
                                    if(!empty($feedback_ids)) {
                                        $ids = implode(',', $feedback_ids);
                                        
                                        if($bulk_action == 'markread') {
                                            mysqli_query($bd, "UPDATE tblfeedback SET isRead = 1 WHERE id IN ($ids)");
                                            echo '<div class="alert alert-success">Selected feedback marked as read.</div>';
                                        }
                                        
                                        if($bulk_action == 'markunread') {
                                            mysqli_query($bd, "UPDATE tblfeedback SET isRead = 0 WHERE id IN ($ids)");
                                            echo '<div class="alert alert-success">Selected feedback marked as unread.</div>';
                                        }
                                        
                                        if($bulk_action == 'delete') {
                                            mysqli_query($bd, "DELETE FROM tblfeedback WHERE id IN ($ids)");
                                            echo '<div class="alert alert-success">Selected feedback deleted successfully.</div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-warning">Please select at least one feedback to perform this action.</div>';
                                    }
                                }
                                
                                $query = mysqli_query($bd, "SELECT f.*, u.fullName, u.userEmail, c.complaintNumber 
                                                          FROM tblfeedback f 
                                                          JOIN users u ON f.userId = u.id 
                                                          JOIN tblcomplaints c ON f.complaintId = c.complaintNumber 
                                                          ORDER BY f.isRead ASC, f.submissionDate DESC");
                                $total = mysqli_num_rows($query);
                                
                                $unreadQuery = mysqli_query($bd, "SELECT COUNT(*) as unreadCount FROM tblfeedback WHERE isRead = 0");
                                $unreadRow = mysqli_fetch_assoc($unreadQuery);
                                $unreadCount = $unreadRow['unreadCount'];
                                ?>

                                <div class="alert alert-info">
                                    <strong>Total Feedback:</strong> <?php echo $total; ?> | 
                                    <strong>Unread:</strong> <?php echo $unreadCount; ?>
                                </div>

                                <?php if($total > 0) { ?>
                                <form method="post" action="">
                                    <div class="bulk-actions mb-3">
                                        <select name="bulk_action" class="form-control d-inline-block" style="width: auto;">
                                            <option value="">Bulk Actions</option>
                                            <option value="markread">Mark as Read</option>
                                            <option value="markunread">Mark as Unread</option>
                                            <option value="delete">Delete</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to perform this action?')">Apply</button>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="30"><input type="checkbox" id="selectAll"></th>
                                                    <th>Status</th>
                                                    <th>Complaint #</th>
                                                    <th>User</th>
                                                    <th>Rating</th>
                                                    <th>Type</th>
                                                    <th>Comments</th>
                                                    <th>Submitted</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($row = mysqli_fetch_array($query)) { 
                                                    $typeClass = '';
                                                    if($row['feedbackType'] == 'Complaint') $typeClass = 'type-complaint';
                                                    elseif($row['feedbackType'] == 'Suggestion') $typeClass = 'type-suggestion';
                                                    elseif($row['feedbackType'] == 'Appreciation') $typeClass = 'type-appreciation';
                                                    
                                                    $statusClass = $row['isRead'] ? 'status-read' : 'status-unread';
                                                    $statusText = $row['isRead'] ? 'Read' : 'Unread';
                                                    $rowClass = $row['isRead'] ? '' : 'feedback-row-unread';
                                                ?>
                                                <tr class="<?php echo $rowClass; ?>">
                                                    <td><input type="checkbox" name="feedback_ids[]" value="<?php echo $row['id']; ?>"></td>
                                                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                                                    <td><?php echo htmlentities($row['complaintNumber']); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlentities($row['fullName']); ?></strong><br>
                                                        <small><?php echo htmlentities($row['userEmail']); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="rating-stars">
                                                            <?php 
                                                            for($i=1; $i<=5; $i++) {
                                                                if($i <= $row['rating']) {
                                                                    echo '<i class="icon-star"></i>';
                                                                } else {
                                                                    echo '<i class="icon-star-empty"></i>';
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                        (<?php echo $row['rating']; ?>/5)
                                                    </td>
                                                    <td><span class="feedback-type <?php echo $typeClass; ?>"><?php echo htmlentities($row['feedbackType']); ?></span></td>
                                                    <td><?php echo htmlentities($row['comments']); ?></td>
                                                    <td><?php echo htmlentities($row['submissionDate']); ?></td>
                                                    <td class="table-actions">
                                                        <?php if($row['isRead']) { ?>
                                                            <a href="?action=markunread&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Mark as Unread">
                                                                <i class="icon-envelope"></i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?action=markread&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" title="Mark as Read">
                                                                <i class="icon-ok"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this feedback?')" title="Delete">
                                                            <i class="icon-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                                <?php } else { ?>
                                <div class="alert alert-warning text-center">
                                    <i class="icon-info-sign"></i> No feedback submitted yet.
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('include/footer.php');?>
    
    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script>
        // Select all checkboxes
        document.getElementById('selectAll').addEventListener('click', function() {
            var checkboxes = document.getElementsByName('feedback_ids[]');
            for(var i=0; i<checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
    </script>
</body>
</html>
<?php } ?>