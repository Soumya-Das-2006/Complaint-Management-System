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
            <div class="span3">
<?php include('include/sidebar.php');?>
            </div>

<div class="span9">
    <div class="content">
        <div class="module">
            <div class="module-head">
                <h3>SMS Templates for Work Assignment</h3>
            </div>
            <div class="module-body">
                <div class="alert alert-info">
                    <h4>Default SMS Template</h4>
                    <pre>NEW WORK ASSIGNED
Title: {work_title}
Place: {place_address}
Priority: {priority}
Deadline: {deadline}
Details: {work_description}
Please check your dashboard for complete details.</pre>
                </div>
                
                <form class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label" style="font-weight: bold; width: 200px;">Custom Template:</label>
                        <div class="controls">
                            <textarea class="span8" rows="8" placeholder="Enter your custom SMS template here..." style="width:97%;">
NEW WORK ASSIGNED
Title: {work_title}
Place: {place_address}
Priority: {priority}
Deadline: {deadline}
Details: {work_description}
Please check your dashboard for complete details.</textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-success">Save Template</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
                </div><!--/.span3-->
            </div><!--/.row-->
        </div><!--/.container-->
    </div><!--/.wrapper-->  
<?php include('include/footer.php');?>