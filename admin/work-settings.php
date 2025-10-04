<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}
?>
<?php include('include/header.php');?>
<div class="wrapper">
    <div class="container">
        <div class="row">
            
<?php include('include/sidebar.php');?>

<div class="span9">
    <div class="content">
        <div class="module">
            <div class="module-head">
                <h3>Work Management Settings</h3>
            </div>
            <div class="module-body">
                <form class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Default Work Priority</label>
                        <div class="controls">
                            <select class="span4">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Auto SMS Notification</label>
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" checked> Send SMS when work is assigned
                            </label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Default Deadline Days</label>
                        <div class="controls">
                            <input type="number" class="span2" value="7" min="1" max="30">
                            <span class="help-inline">days from assignment</span>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-success">Save Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
            </div><!--/.span3-->
        </div><!--/.row-->

<?php include('include/footer.php');?>