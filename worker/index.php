<?php
session_start();
// Include config with proper path
include('includes/config.php');

// Check if user is already logged in
if(isset($_SESSION['wlogin']) && strlen($_SESSION['wlogin']) > 0) {
    header('location:worker-dashboard.php');
    exit;
}

$error = '';
if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($bd, $_POST['email']);
    $password = mysqli_real_escape_string($bd, md5($_POST['password']));
    
    $query = mysqli_query($bd, "SELECT * FROM workers WHERE email='$email' AND password='$password' AND status='active'");
    $num = mysqli_fetch_array($query);
    
    if($num > 0) {
        $_SESSION['wlogin'] = $email;
        $_SESSION['wid'] = $num['id'];
        $_SESSION['wname'] = $num['name'];
        $_SESSION['wdept'] = $num['department'];
        header('location:worker-dashboard.php');
        exit;
    } else {
        $error = "Invalid email or password, or your account is inactive";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Login - CMS</title>
    <!-- Correct paths for worker directory -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }
        .worker-login-header {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .login-logo {
            font-size: 3em;
            color: #3498db;
            margin-bottom: 20px;
        }
        .btn-worker {
            background: #3498db;
            color: white;
            padding: 12px;
            font-size: 16px;
        }
        .btn-worker:hover {
            background: #2980b9;
            color: white;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="container">
        <div class="login-container">
            <div class="worker-login-header">
                <div class="login-logo">
                    <i class="icon-cog"></i>
                </div>
                <h2>Worker Login</h2>
                <p class="text-muted">Access your work dashboard</p>
            </div>
            
            <?php if($error): ?>
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>Error!</strong> <?php echo htmlentities($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="control-group">
                    <label class="control-label">Email Address</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-envelope"></i></span>
                            <input type="email" name="email" class="span12" required placeholder="Enter your email">
                        </div>
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label">Password</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-lock"></i></span>
                            <input type="password" name="password" class="span12" required placeholder="Enter your password">
                        </div>
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" name="login" class="btn btn-worker btn-block">
                            <i class="icon-signin"></i> Login to Dashboard
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="../index.php" class="btn btn-link">
                    <i class="icon-home"></i> Back to Main Site
                </a>
            </div>
        </div>
    </div>
    
    <script src="scripts/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>