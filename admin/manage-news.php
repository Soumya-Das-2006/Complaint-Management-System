<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

// Add new news
if(isset($_POST['add_news'])) {
    $title = mysqli_real_escape_string($bd, $_POST['title']);
    $content = mysqli_real_escape_string($bd, $_POST['content']);
    $status = $_POST['status'];
    $admin_id = $_SESSION['id'];
    
    // Handle image upload
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "news_images/";
        if(!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    
    $sql = mysqli_query($bd, "INSERT INTO news (title, content, image, status, created_by) 
                             VALUES ('$title', '$content', '$image', '$status', '$admin_id')");
    if($sql) {
        $_SESSION['msg'] = "News added successfully!";
    } else {
        $_SESSION['errmsg'] = "Failed to add news!";
    }
}

// Delete news
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($bd, "DELETE FROM news WHERE id='$id'");
    $_SESSION['msg'] = "News deleted successfully!";
}

// Toggle status
if(isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $current = mysqli_fetch_array(mysqli_query($bd, "SELECT status FROM news WHERE id='$id'"));
    $new_status = $current['status'] == 'published' ? 'draft' : 'published';
    mysqli_query($bd, "UPDATE news SET status='$new_status' WHERE id='$id'");
    $_SESSION['msg'] = "News status updated!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage News</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .news-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .news-image { 
            max-width: 200px; 
            max-height: 150px; 
            border-radius: 5px; 
            margin-right: 15px;
        }
        .status-published { color: #28a745; }
        .status-draft { color: #6c757d; }
        .news-content { 
            max-height: 100px; 
            overflow: hidden; 
            position: relative;
        }
        .news-content:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(transparent, white);
        }
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
                        <!-- Add News Form -->
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-plus"></i> Add New News</h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($_SESSION['msg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']); $_SESSION['msg']=""; ?>
                                </div>
                                <?php } ?>
                                <?php if(isset($_SESSION['errmsg'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['errmsg']); $_SESSION['errmsg']=""; ?>
                                </div>
                                <?php } ?>

                                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                    <div class="control-group">
                                        <label class="control-label">News Title</label>
                                        <div class="controls">
                                            <input type="text" style="width: 500px" name="title" class="span8" placeholder="Enter news title" required>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Content</label>
                                        <div class="controls">
                                            <textarea name="content" style="width: 500px" class="span8" rows="6" placeholder="Enter news content" required></textarea>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Featured Image</label>
                                        <div class="controls">
                                            <input type="file" style="width: 500px" name="image" accept="image/*">
                                            <small class="help-block">Optional: Upload a featured image for the news</small>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Status</label>
                                        <div class="controls">
                                            <select name="status" class="span4" style="width: 200px" required>
                                                <option value="published">Publish Immediately</option>
                                                <option value="draft">Save as Draft</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="add_news" class="btn btn-primary">
                                                <i class="icon-save"></i> Publish News
                                            </button>
                                            <button type="reset" class="btn">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- News List -->
                        <div class="module">
                            <div class="module-head">
                                <h3><i class="icon-list"></i> All News</h3>
                            </div>
                            <div class="module-body">
                                <?php
                                $news = mysqli_query($bd, "SELECT n.*, a.username as author 
                                                         FROM news n 
                                                         JOIN admin a ON n.created_by = a.id 
                                                         ORDER BY n.created_at DESC");
                                $totalNews = mysqli_num_rows($news);
                                $publishedNews = mysqli_num_rows(mysqli_query($bd, "SELECT * FROM news WHERE status='published'"));
                                ?>

                                <div class="alert alert-info">
                                    <strong>Total News:</strong> <?php echo $totalNews; ?> | 
                                    <strong>Published:</strong> <?php echo $publishedNews; ?> | 
                                    <strong>Drafts:</strong> <?php echo $totalNews - $publishedNews; ?>
                                </div>

                                <?php while($item = mysqli_fetch_array($news)) { ?>
                                <div class="news-card">
                                    <div class="row-fluid">
                                        <?php if($item['image']) { ?>
                                        <div class="span2">
                                            <img src="news_images/<?php echo htmlentities($item['image']); ?>" class="news-image">
                                        </div>
                                        <div class="span8">
                                        <?php } else { ?>
                                        <div class="span10">
                                        <?php } ?>
                                            <h4><?php echo htmlentities($item['title']); ?></h4>
                                            <div class="news-content">
                                                <?php echo nl2br(htmlentities($item['content'])); ?>
                                            </div>
                                            <div class="news-meta">
                                                <small class="text-muted">
                                                    <i class="icon-user"></i> By <?php echo htmlentities($item['author']); ?> | 
                                                    <i class="icon-time"></i> <?php echo htmlentities($item['created_at']); ?> | 
                                                    <span class="status-<?php echo $item['status']; ?>">
                                                        <i class="icon-circle"></i> <?php echo ucfirst($item['status']); ?>
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="span2 text-right">
                                            <div class="btn-group-vertical">
                                                <a href="edit-news.php?id=<?php echo $item['id']; ?>" class="btn btn-small btn-info">
                                                    <i class="icon-edit"></i> Edit
                                                </a>
                                                <a href="manage-news.php?toggle_status=<?php echo $item['id']; ?>" class="btn btn-small btn-warning">
                                                    <i class="icon-refresh"></i> <?php echo $item['status']=='published'?'Unpublish':'Publish'; ?>
                                                </a>
                                                <a href="manage-news.php?delete=<?php echo $item['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this news?')">
                                                    <i class="icon-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>

                                <?php if($totalNews == 0) { ?>
                                <div class="alert alert-info text-center">
                                    <i class="icon-info-sign"></i> No news articles yet. Start by adding your first news above.
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
</body>
</html>
<?php } ?>