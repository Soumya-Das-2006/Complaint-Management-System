<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['wlogin'])==0) {    
    header('location:index.php');
    exit;
}

$worker_id = $_SESSION['wid'];

// Handle filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($bd, $_GET['status']) : '';
$priority_filter = isset($_GET['priority']) ? mysqli_real_escape_string($bd, $_GET['priority']) : '';

$where_conditions = ["assigned_worker_id='$worker_id'"];
if($status_filter && $status_filter != 'all') {
    $where_conditions[] = "status='$status_filter'";
}
if($priority_filter && $priority_filter != 'all') {
    $where_conditions[] = "priority='$priority_filter'";
}

$where_clause = implode(' AND ', $where_conditions);

// Fetch works with filters
$works = mysqli_query($bd, "SELECT * FROM works WHERE $where_clause ORDER BY 
                          CASE 
                            WHEN priority='urgent' THEN 1
                            WHEN priority='high' THEN 2
                            WHEN priority='medium' THEN 3
                            ELSE 4
                          END, assigned_date DESC");

// Count works by status
$counts = [
    'all' => mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id'"))['total'],
    'assigned' => mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND status='assigned'"))['total'],
    'in_progress' => mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND status='in_progress'"))['total'],
    'completed' => mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND status='completed'"))['total']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Works - Worker Panel</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../images/icons/css/font-awesome.css" rel="stylesheet">
    <style>
        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .count-badge {
            background: #3498db;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <?php include('../include/worker-header.php');?>
    
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('../include/worker-sidebar.php');?>
                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-tasks"></i> My Assigned Works</h3>
                            </div>
                            <div class="module-body">
                                <!-- Status Filters -->
                                <div class="filter-card">
                                    <div class="row-fluid">
                                        <div class="span6">
                                            <h5>Filter by Status:</h5>
                                            <div class="btn-group">
                                                <a href="?status=all&priority=<?php echo $priority_filter; ?>" 
                                                   class="btn btn-small <?php echo $status_filter == 'all' || $status_filter == '' ? 'btn-primary' : 'btn-default'; ?>">
                                                    All <span class="count-badge"><?php echo $counts['all']; ?></span>
                                                </a>
                                                <a href="?status=assigned&priority=<?php echo $priority_filter; ?>" 
                                                   class="btn btn-small <?php echo $status_filter == 'assigned' ? 'btn-warning' : 'btn-default'; ?>">
                                                    Assigned <span class="count-badge"><?php echo $counts['assigned']; ?></span>
                                                </a>
                                                <a href="?status=in_progress&priority=<?php echo $priority_filter; ?>" 
                                                   class="btn btn-small <?php echo $status_filter == 'in_progress' ? 'btn-info' : 'btn-default'; ?>">
                                                    In Progress <span class="count-badge"><?php echo $counts['in_progress']; ?></span>
                                                </a>
                                                <a href="?status=completed&priority=<?php echo $priority_filter; ?>" 
                                                   class="btn btn-small <?php echo $status_filter == 'completed' ? 'btn-success' : 'btn-default'; ?>">
                                                    Completed <span class="count-badge"><?php echo $counts['completed']; ?></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <h5>Filter by Priority:</h5>
                                            <div class="btn-group">
                                                <a href="?status=<?php echo $status_filter; ?>&priority=all" 
                                                   class="btn btn-small <?php echo $priority_filter == 'all' || $priority_filter == '' ? 'btn-primary' : 'btn-default'; ?>">
                                                    All Priorities
                                                </a>
                                                <a href="?status=<?php echo $status_filter; ?>&priority=urgent" 
                                                   class="btn btn-small btn-danger">
                                                    Urgent
                                                </a>
                                                <a href="?status=<?php echo $status_filter; ?>&priority=high" 
                                                   class="btn btn-small btn-warning">
                                                    High
                                                </a>
                                                <a href="?status=<?php echo $status_filter; ?>&priority=medium" 
                                                   class="btn btn-small btn-info">
                                                    Medium
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Works Table -->
                                <?php if(mysqli_num_rows($works) > 0): ?>
                                <table class="table table-striped table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Work Title</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Deadline</th>
                                            <th>Assigned Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($work = mysqli_fetch_array($works)): 
                                            $is_overdue = strtotime($work['deadline']) < time() && $work['status'] != 'completed';
                                        ?>
                                        <tr class="<?php echo $is_overdue ? 'error' : ''; ?>">
                                            <td>
                                                <strong><?php echo htmlentities($work['work_title']); ?></strong>
                                                <?php if($is_overdue): ?>
                                                <span class="label label-important">Overdue</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge" style="background: <?php 
                                                switch($work['priority']) {
                                                    case 'urgent': echo '#e74c3c'; break;
                                                    case 'high': echo '#e67e22'; break;
                                                    case 'medium': echo '#f39c12'; break;
                                                    default: echo '#27ae60';
                                                }
                                                ?>; color: white;">
                                                    <?php echo ucfirst($work['priority']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge" style="background: <?php 
                                                switch($work['status']) {
                                                    case 'completed': echo '#27ae60'; break;
                                                    case 'in_progress': echo '#3498db'; break;
                                                    case 'assigned': echo '#f39c12'; break;
                                                    default: echo '#95a5a6';
                                                }
                                                ?>; color: white;">
                                                    <?php echo str_replace('_', ' ', ucfirst($work['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($work['deadline'])); ?>
                                                <?php if($is_overdue): ?>
                                                <br><small class="text-error">Overdue by <?php echo floor((time() - strtotime($work['deadline'])) / (60*60*24)); ?> days</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($work['assigned_date'])); ?></td>
                                            <td>
                                                <a href="worker-work-details.php?id=<?php echo $work['id']; ?>" class="btn btn-small btn-primary">
                                                    <i class="icon-eye-open"></i> View
                                                </a>
                                                <?php if($work['status'] != 'completed'): ?>
                                                <a href="worker-work-details.php?id=<?php echo $work['id']; ?>" class="btn btn-small btn-success">
                                                    <i class="icon-edit"></i> Update
                                                </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <strong>No works found!</strong> 
                                    <?php if($status_filter || $priority_filter): ?>
                                        Try changing your filters.
                                    <?php else: ?>
                                        You don't have any works assigned to you yet.
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('../include/footer.php');?>
    
    <script src="../scripts/jquery-1.9.1.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>