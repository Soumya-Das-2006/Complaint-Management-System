<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | All Complaints</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .filter-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .status-badge { padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .status-new { background: #ffc107; color: #000; }
        .status-process { background: #17a2b8; color: white; }
        .status-closed { background: #28a745; color: white; }
        .complaint-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
        }
        .action-buttons .btn { margin: 2px; }
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
                                <h3><i class="icon-list-alt"></i> All Complaints</h3>
                            </div>
                            <div class="module-body">
                                <!-- Filter Section -->
                                <div class="filter-section">
                                    <form method="get" class="form-horizontal">
                                        <div class="row-fluid">
                                            <div class="span3">
                                                <select name="status" class="span12">
                                                    <option value="">All Status</option>
                                                    <option value="new" <?php echo (isset($_GET['status']) && $_GET['status']=='new')?'selected':''; ?>>New</option>
                                                    <option value="in process" <?php echo (isset($_GET['status']) && $_GET['status']=='in process')?'selected':''; ?>>In Process</option>
                                                    <option value="closed" <?php echo (isset($_GET['status']) && $_GET['status']=='closed')?'selected':''; ?>>Closed</option>
                                                </select>
                                            </div>
                                            <div class="span3">
                                                <input type="text" name="search" class="span12" placeholder="Search by complaint number or user..." value="<?php echo isset($_GET['search'])?htmlentities($_GET['search']):''; ?>">
                                            </div>
                                            <div class="span3">
                                                <input type="date" name="date" class="span12" value="<?php echo isset($_GET['date'])?htmlentities($_GET['date']):''; ?>">
                                            </div>
                                            <div class="span3">
                                                <button type="submit" class="btn btn-primary span12"><i class="icon-search"></i> Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Complaints List -->
                                <div class="complaints-list">
                                    <?php
                                    $where = "WHERE 1=1";
                                    if(isset($_GET['status']) && $_GET['status'] != '') {
                                        if($_GET['status'] == 'new') {
                                            $where .= " AND c.status IS NULL";
                                        } else {
                                            $where .= " AND c.status='".mysqli_real_escape_string($bd, $_GET['status'])."'";
                                        }
                                    }
                                    if(isset($_GET['search']) && $_GET['search'] != '') {
                                        $search = mysqli_real_escape_string($bd, $_GET['search']);
                                        $where .= " AND (c.complaintNumber LIKE '%$search%' OR u.fullName LIKE '%$search%' OR u.userEmail LIKE '%$search%')";
                                    }
                                    if(isset($_GET['date']) && $_GET['date'] != '') {
                                        $date = mysqli_real_escape_string($bd, $_GET['date']);
                                        $where .= " AND DATE(c.regDate) = '$date'";
                                    }

                                    $query = mysqli_query($bd, "SELECT c.*, u.fullName, u.userEmail, cat.categoryName 
                                                              FROM tblcomplaints c 
                                                              JOIN users u ON c.userId = u.id 
                                                              JOIN category cat ON c.category = cat.id 
                                                              $where 
                                                              ORDER BY c.regDate DESC");
                                    $total = mysqli_num_rows($query);
                                    ?>

                                    <div class="alert alert-info">
                                        <strong>Total Complaints:</strong> <?php echo $total; ?>
                                    </div>

                                    <?php while($row = mysqli_fetch_array($query)) { 
                                        $statusClass = '';
                                        $statusText = 'New';
                                        if($row['status'] === null) {
                                            $statusClass = 'status-new';
                                        } elseif($row['status'] == 'in process') {
                                            $statusClass = 'status-process';
                                            $statusText = 'In Process';
                                        } elseif($row['status'] == 'closed') {
                                            $statusClass = 'status-closed';
                                            $statusText = 'Closed';
                                        }
                                    ?>
                                    <div class="complaint-card">
                                        <div class="row-fluid">
                                            <div class="span8">
                                                <h5><strong>Complaint #<?php echo htmlentities($row['complaintNumber']); ?></strong></h5>
                                                <p><strong>User:</strong> <?php echo htmlentities($row['fullName']); ?> (<?php echo htmlentities($row['userEmail']); ?>)</p>
                                                <p><strong>Category:</strong> <?php echo htmlentities($row['categoryName']); ?></p>
                                                <p><strong>Details:</strong> <?php echo substr(htmlentities($row['complaintDetails']), 0, 100); ?>...</p>
                                                <p><strong>Registered:</strong> <?php echo htmlentities($row['regDate']); ?></p>
                                            </div>
                                            <div class="span4 text-right">
                                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                <div class="action-buttons" style="margin-top: 10px;">
                                                    <a href="complaint-details.php?cid=<?php echo htmlentities($row['complaintNumber']); ?>" class="btn btn-info btn-small">
                                                        <i class="icon-eye-open"></i> View
                                                    </a>
                                                    <?php if($row['status'] != 'closed') { ?>
                                                    <a href="javascript:void(0);" onClick="popUpWindow('updatecomplaint.php?cid=<?php echo htmlentities($row['complaintNumber']); ?>');" class="btn btn-warning btn-small">
                                                        <i class="icon-edit"></i> Action
                                                    </a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <?php if($total == 0) { ?>
                                    <div class="alert alert-warning text-center">
                                        <i class="icon-info-sign"></i> No complaints found matching your criteria.
                                    </div>
                                    <?php } ?>
                                </div>
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
    <script language="javascript" type="text/javascript">
    var popUpWin=0;
    function popUpWindow(URLStr, left, top, width, height) {
        if(popUpWin) {
            if(!popUpWin.closed) popUpWin.close();
        }
        popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width=600,height=600,left=100, top=50,screenX=100,screenY=50');
    }
    </script>
</body>
</html>
<?php } ?>