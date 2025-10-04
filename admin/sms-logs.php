<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

// Fetch SMS logs
$smsLogs = mysqli_query($bd, "SELECT sl.*, wk.name as worker_name, w.work_title 
                             FROM sms_logs sl 
                             LEFT JOIN workers wk ON sl.worker_id = wk.id 
                             LEFT JOIN works w ON sl.work_id = w.id 
                             ORDER BY sl.sent_at DESC");
?>
<?php include('include/header.php');?>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                
<?php include('include/sidebar.php');?>
                
<div class="span9">
    <div class="content">
        <div class="module">
            <div class="module-head">
                <h3>SMS Logs</h3>
            </div>
            <div class="module-body">
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Worker</th>
                            <th>Work</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($log = mysqli_fetch_array($smsLogs)): ?>
                        <tr>
                            <td><?php echo htmlentities($log['recipient_phone']); ?></td>
                            <td><?php echo htmlentities($log['worker_name']); ?></td>
                            <td><?php echo htmlentities($log['work_title']); ?></td>
                            <td><?php echo substr(htmlentities($log['message']), 0, 50) . '...'; ?></td>
                            <td>
                                <span class="label label-<?php 
                                echo $log['status'] == 'sent' ? 'success' : 
                                     ($log['status'] == 'failed' ? 'important' : 'warning');
                                ?>">
                                    <?php echo ucfirst($log['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($log['sent_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
                </div><!--/.span3-->
            </div><!--/.row-->
        </div><!--/.container-->
    </div><!--/.wrapper-->

<?php include('include/footer.php');?>