<?php
session_start();
include('include/config.php');

// Enhanced session validation
if(!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {	
    header('location: index.php');
    exit;
}

// Error handling for database connection
if (!$bd) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all works with error handling
$works = mysqli_query($bd, "SELECT w.*, wk.name as worker_name, wk.phone as worker_phone 
                           FROM works w 
                           LEFT JOIN workers wk ON w.assigned_worker_id = wk.id 
                           ORDER BY w.created_at DESC");

if (!$works) {
    die("Query failed: " . mysqli_error($bd));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Works</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .table th { background: #f8f9fa; }
        .priority-badge, .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        .priority-urgent { background: #e74c3c; color: white; }
        .priority-high { background: #e67e22; color: white; }
        .priority-medium { background: #f39c12; color: white; }
        .priority-low { background: #27ae60; color: white; }
        .priority-default { background: #95a5a6; color: white; }
        .status-pending { background: #f8d7da; color: #721c24; }
        .status-assigned { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #cce7ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f5f5f5; color: #6c757d; }
        .status-default { background: #e9ecef; color: #495057; }
        .btn-action { margin: 2px; }
        .table-responsive { overflow-x: auto; }
        .no-data { text-align: center; padding: 20px; color: #6c757d; }
    </style>
</head>
<?php include('include/header.php'); ?>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php'); ?>
    <div class="span9">
        <div class="content">
            <div class="module">
                <div class="module-head">
                    <h3><i class="fas fa-tasks"></i> All Works</h3>
                </div>
                <div class="module-body">
                    <?php if(mysqli_num_rows($works) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Work Title</th>
                                    <th>Assigned To</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                    <th>Assigned Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($work = mysqli_fetch_assoc($works)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($work['work_title'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($work['worker_name'] ?? 'Unassigned'); ?></td>
                                    <td>
                                        <?php 
                                        $priority = $work['priority'] ?? 'default';
                                        $priorityClass = 'priority-' . $priority;
                                        ?>
                                        <span class="priority-badge <?php echo $priorityClass; ?>">
                                            <?php echo ucfirst($priority); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $status = $work['status'] ?? 'default';
                                        $statusClass = 'status-' . $status;
                                        $statusDisplay = str_replace('_', ' ', ucfirst($status));
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo $statusDisplay; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($work['deadline']) && $work['deadline'] != '0000-00-00') {
                                            echo date('M d, Y', strtotime($work['deadline']));
                                        } else {
                                            echo 'Not set';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($work['assigned_date']) && $work['assigned_date'] != '0000-00-00') {
                                            echo date('M d, Y', strtotime($work['assigned_date']));
                                        } else {
                                            echo 'Not assigned';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="work-details.php?id=<?php echo urlencode($work['id']); ?>" 
                                               class="btn btn-small btn-info btn-action" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if(!empty($work['worker_phone'])): ?>
                                            <a href="assign-work.php?action=resend_sms&work_id=<?php echo urlencode($work['id']); ?>" 
                                               class="btn btn-small btn-warning btn-action" 
                                               title="Resend SMS"
                                               onclick="return confirm('Are you sure you want to resend SMS?')">
                                                <i class="fas fa-sms"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No works found. <a href="assign-work.php">Assign a new work</a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
    
    <script>
        // Add confirmation for resend SMS action
        document.addEventListener('DOMContentLoaded', function() {
            const resendButtons = document.querySelectorAll('a[href*="resend_sms"]');
            resendButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to resend SMS notification?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>