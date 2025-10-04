<?php
session_start();
error_reporting(0);
include("includes/config.php");

if(isset($_POST['submit']))
{
    $ret=mysqli_query($bd, "SELECT * FROM users WHERE userEmail='".$_POST['username']."' and password='".md5($_POST['password'])."'");
    $num=mysqli_fetch_array($ret);
    if($num>0)
    {
        $extra="dashboard.php";
        $_SESSION['login']=$_POST['username'];
        $_SESSION['id']=$num['id'];
        $host=$_SERVER['HTTP_HOST'];
        $uip=$_SERVER['REMOTE_ADDR'];
        $status=1;
        $log=mysqli_query($bd, "insert into userlog(uid,username,userip,status) values('".$_SESSION['id']."','".$_SESSION['login']."','$uip','$status')");
        $uri=rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
        header("location:http://$host$uri/$extra");
        exit();
    }
    else
    {
        $_SESSION['login']=$_POST['username'];	
        $uip=$_SERVER['REMOTE_ADDR'];
        $status=0;
        mysqli_query($bd, "insert into userlog(username,userip,status) values('".$_SESSION['login']."','$uip','$status')");
        $errormsg="Invalid username or password";
        $extra="login.php";
    }
}

if(isset($_POST['change']))
{
    $email=$_POST['email'];
    $contact=$_POST['contact'];
    $password=md5($_POST['password']);
    $query=mysqli_query($bd, "SELECT * FROM users WHERE userEmail='$email' and contactNo='$contact'");
    $num=mysqli_fetch_array($query);
    if($num>0)
    {
        mysqli_query($bd, "update users set password='$password' WHERE userEmail='$email' and contactNo='$contact' ");
        $msg="Password Changed Successfully";
    }
    else
    {
        $errormsg="Invalid email id or Contact no";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GUVNL Complaint System - Consumer Login">
    <meta name="author" content="GUVNL Complaint System">
    <meta name="keyword" content="GUVNL, Electricity, Complaint, Login">

    <title>GUVNL Complaint System | Consumer Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a5276;
            --secondary: #3498db;
            --accent: #e67e22;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #0d2b3e, #1a5276);
            color: #333;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(rgba(26, 82, 118, 0.85), rgba(26, 82, 118, 0.9)), url('https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 2.5rem;
            margin-right: 15px;
            color: var(--accent);
        }
        
        .logo h1 {
            font-size: 1.8rem;
            margin: 0;
            color: var(--primary);
        }
        
        .welcome-text {
            margin-bottom: 30px;
        }
        
        .welcome-text h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .welcome-text p {
            color: #7f8c8d;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-control {
            height: 50px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding-left: 45px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 1.2rem;
        }
        
        .btn-login {
            background-color: var(--accent);
            border-color: var(--accent);
            color: white;
            height: 50px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #d35400;
            border-color: #d35400;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .forgot-password a {
            color: var(--secondary);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .forgot-password a:hover {
            color: #2980b9;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
        }
        
        .register-link a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .register-link a:hover {
            color: #2980b9;
        }
        
        .alert {
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin-top: 30px;
        }
        
        .features-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .features-list i {
            color: var(--success);
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background-color: var(--primary);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
            padding: 20px;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            border: none;
            padding: 20px;
        }
        
        .btn-reset {
            background-color: var(--accent);
            border-color: var(--accent);
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .btn-reset:hover {
            background-color: #d35400;
            border-color: #d35400;
            transform: translateY(-2px);
        }
        
        .emergency-contact {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
            border-left: 4px solid var(--accent);
        }
        
        .emergency-contact h5 {
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .emergency-contact p {
            margin: 0;
            font-weight: 500;
            color: var(--accent);
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-left {
                padding: 30px;
            }
            
            .login-right {
                padding: 30px;
            }
        }
    </style>
    
    <script type="text/javascript">
        function valid() {
            if(document.forgot.password.value != document.forgot.confirmpassword.value) {
                alert("Password and Confirm Password Field do not match!!");
                document.forgot.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="login-container">
        <div class="login-left">
            <div class="welcome-content">
                <h2>GUVNL Complaint Tracking System</h2>
                <p>Report electricity issues and track their resolution status in real-time. Our platform ensures efficient complaint management for GUVNL consumers.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Real-time Complaint Tracking</li>
                    <li><i class="fas fa-check-circle"></i> Mobile-Friendly Interface</li>
                    <li><i class="fas fa-check-circle"></i> SMS & Email Notifications</li>
                    <li><i class="fas fa-check-circle"></i> Complaint History Access</li>
                    <li><i class="fas fa-check-circle"></i> 24/7 Support Availability</li>
                </ul>
                
                <div class="emergency-contact">
                    <h5><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                    <p>1912 (Toll-free) | 24x7 Helpline</p>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="logo">
                <i class="fas fa-bolt"></i>
                <h1>GUVNL Complaint System</h1>
            </div>
            
            <div class="welcome-text">
                <h2>Consumer Login</h2>
                <p>Sign in to track your complaints and access your account</p>
            </div>
            
            <!-- Error/Success Messages -->
            <?php if($errormsg): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlentities($errormsg); ?>
                </div>
            <?php endif; ?>
            
            <?php if($msg): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlentities($msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form class="form-login" name="login" method="post">
                <div class="form-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="text" class="form-control" name="username" placeholder="Email Address or Consumer ID" required autofocus>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" name="password" required placeholder="Password">
                </div>
                
                <div class="forgot-password">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
                </div>
                
                <button class="btn btn-login btn-block w-100" name="submit" type="submit">
                    <i class="fas fa-lock me-2"></i> SIGN IN
                </button>
            </form>
            
            <div class="register-link">
                New consumer? <a href="registration.php">Create an account</a>
            </div>
            
            <div class="text-center mt-3">
                <a href="../index.html" class="text-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Home</a>
            </div>
        </div>
    </div>
    
    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="forgot" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">
                            <i class="fas fa-key me-2"></i>Reset Your Password
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Enter your registered details to reset your password.</p>
                        
                        <div class="form-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" placeholder="Registered Email Address" autocomplete="off" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" name="contact" placeholder="Registered Contact Number" autocomplete="off" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control" placeholder="New Password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control" placeholder="Confirm Password" id="confirmpassword" name="confirmpassword" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-reset" type="submit" name="change" onclick="return valid();">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>