<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

if(isset($_POST['save_settings'])) {
    // Handle settings save here
    $_SESSION['msg'] = "Settings updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | System Settings</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
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
                                <h3><i class="icon-cog"></i> System Settings</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php } ?>

                                <form class="form-horizontal" method="post">
                                    <div class="control-group">
                                        <label class="control-label">System Name</label>
                                        <div class="controls">
                                            <input style="background: #f8f9fa; border: 1px solid #ced4da; width: 350px;" type="text" class="span8" value="Complaint Management System" readonly>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Admin Email</label>
                                        <div class="controls">
                                            <input style="background: #f8f9fa; border: 1px solid #ced4da; width: 350px;" type="email" class="span8" value="admin@cms.com">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Auto-assign Complaints</label>
                                        <div class="controls">
                                            <label class="checkbox">
                                                <input type="checkbox" checked> Enable automatic complaint assignment
                                            </label>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Notification Email</label>
                                        <div class="controls">
                                            <label class="checkbox">
                                                <input type="checkbox" checked> Send email notifications for new complaints
                                            </label>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">SLA Hours</label>
                                        <div class="controls">
                                            <input type="number" class="span2" value="48" min="1"> hours to resolve complaints
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="save_settings" class="btn btn-primary">Save Settings</button>
                                            <button type="reset" class="btn">Reset</button>
                                        </div>
                                    </div>
                                </form>
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
</body>
</html>
<?php } ?>