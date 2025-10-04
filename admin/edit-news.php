<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

$id = intval($_GET['id']);
if(isset($_POST['update_news'])) {
    $title = mysqli_real_escape_string($bd, $_POST['title']);
    $content = mysqli_real_escape_string($bd, $_POST['content']);
    $status = $_POST['status'];
    
    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "news_images/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_sql = ", image='$image'";
    } else {
        $image_sql = "";
    }
    
    $sql = mysqli_query($bd, "UPDATE news SET title='$title', content='$content', status='$status' $image_sql WHERE id='$id'");
    if($sql) {
        $_SESSION['msg'] = "News updated successfully!";
    } else {
        $_SESSION['errmsg'] = "Failed to update news!";
    }
}

$news = mysqli_fetch_array(mysqli_query($bd, "SELECT * FROM news WHERE id='$id'"));
if(!$news) {
    header('location:manage-news.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit News</title>
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
                                <h3><i class="icon-edit"></i> Edit News</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php } ?>

                                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                    <div class="control-group">
                                        <label class="control-label">News Title</label>
                                        <div class="controls">
                                            <input type="text" style="width: 500px" name="title" class="span8" value="<?php echo htmlentities($news['title']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Content</label>
                                        <div class="controls">
                                            <textarea name="content" style="width: 500px" class="span8" rows="6" required><?php echo htmlentities($news['content']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Current Image</label>
                                        <div class="controls">
                                            <?php if($news['image']) { ?>
                                            <img src="news_images/<?php echo htmlentities($news['image']); ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
                                            <?php } else { ?>
                                            <p>No image uploaded</p>
                                            <?php } ?>
                                            <input type="file" name="image" accept="image/*">
                                            <small class="help-block">Upload new image to replace current one</small>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="status" class="span4">
                                                <option value="published" <?php echo $news['status']=='published'?'selected':''; ?>>Published</option>
                                                <option value="draft" <?php echo $news['status']=='draft'?'selected':''; ?>>Draft</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="update_news" class="btn btn-primary">
                                                <i class="icon-save"></i> Update News
                                            </button>
                                            <a href="manage-news.php" class="btn">Cancel</a>
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