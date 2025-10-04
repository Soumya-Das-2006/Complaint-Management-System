<?php
session_start();
error_reporting(0);
include("include/config.php");
if(isset($_POST['submit']))
{
    $username=$_POST['username'];
    $password=md5($_POST['password']);
    $ret=mysqli_query($bd, "SELECT * FROM admin WHERE username='$username' and password='$password'");
    $num=mysqli_fetch_array($ret);
    if($num>0)
    {
        $extra="dashboard.php";
        $_SESSION['alogin']=$_POST['username'];
        $_SESSION['id']=$num['id'];
        $host=$_SERVER['HTTP_HOST'];
        $uri=rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
        header("location:http://$host$uri/$extra");
        exit();
    }
    else
    {
        $_SESSION['errmsg']="Invalid username or password";
        $extra="index.php";
        $host  = $_SERVER['HTTP_HOST'];
        $uri  = rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
        header("location:http://$host$uri/$extra");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS | Admin Login</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Open Sans', sans-serif;
            overflow-x: hidden;
        }
        
        .login-wrapper {
            display: flex;
            align-items: center;
            min-height: 100vh;
            width: 100%;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transform-style: preserve-3d;
            perspective: 1000px;
        }
        
        .login-left {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-right {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 600px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }
        
        .login-icon {
            font-size: 80px;
            margin-bottom: 20px;
            display: block;
            animation: float 3s ease-in-out infinite;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
        
        .feature-card:hover {
            transform: translateY(-10px) rotateX(5deg);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .feature-icon {
            font-size: 35px;
            margin-bottom: 15px;
            display: block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .feature-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: white;
        }
        
        .feature-desc {
            font-size: 11px;
            opacity: 0.8;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.4;
        }
        
        .form-control {
            border: none;
            border-bottom: 2px solid #e9ecef;
            border-radius: 0;
            padding: 15px 0;
            background: transparent;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-bottom-color: #3498db;
            box-shadow: none;
            background: transparent;
        }
        
        .input-group-addon {
            background: transparent;
            border: none;
            border-bottom: 2px solid #e9ecef;
            border-radius: 0;
            color: #6c757d;
            padding: 15px 0;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 25px;
            font-size: 16px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-login:hover:before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .system-stats {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 25px;
            margin-top: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-item {
            text-align: center;
            color: white;
            padding: 10px;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            display: block;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        
        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: floatRandom 15s infinite linear;
        }
        
        @keyframes floatRandom {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(50px, -30px) rotate(90deg) scale(1.1); }
            50% { transform: translate(20px, -60px) rotate(180deg) scale(0.9); }
            75% { transform: translate(-30px, -30px) rotate(270deg) scale(1.05); }
            100% { transform: translate(0, 0) rotate(360deg) scale(1); }
        }
        
        .portal-link {
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .portal-link:hover {
            color: #3498db;
            transform: translateX(5px);
            text-decoration: none;
        }
        
        .welcome-text {
            font-size: 16px;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .alert-animation {
            animation: slideInDown 0.5s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .login-left, .login-right {
                min-height: auto;
                padding: 30px 20px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .login-icon {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="container">
            <div class="row-fluid">
                <div class="span8 offset2">
                    <div class="login-container">
                        <div class="row-fluid">
                            <!-- Left Side - Features -->
                            <div class="span6 login-left">
                                <div class="floating-elements">
                                    <?php for($i=1; $i<=8; $i++): ?>
                                        <div class="floating-element" style="
                                            width: <?php echo rand(30, 80); ?>px;
                                            height: <?php echo rand(30, 80); ?>px;
                                            top: <?php echo rand(10, 90); ?>%;
                                            left: <?php echo rand(10, 90); ?>%;
                                            animation-delay: <?php echo $i * 2; ?>s;
                                        "></div>
                                    <?php endfor; ?>
                                </div>
                                
                                <div class="login-header">
                                    <i class="icon-bolt login-icon pulse"></i>
                                    <h2 style="color: white; margin-bottom: 10px;">CMS Admin</h2>
                                    <p class="welcome-text">Welcome to the Complaint Management System Admin Portal</p>
                                </div>
                                
                                <div class="features-grid">
                                    <div class="feature-card">
                                        <i class="icon-dashboard feature-icon"></i>
                                        <div class="feature-title">Real-time Dashboard</div>
                                        <div class="feature-desc">Live updates and comprehensive analytics</div>
                                    </div>
                                    
                                    <div class="feature-card">
                                        <i class="icon-tasks feature-icon"></i>
                                        <div class="feature-title">Workflow Management</div>
                                        <div class="feature-desc">Automated complaint routing & tracking</div>
                                    </div>
                                    
                                    <div class="feature-card">
                                        <i class="icon-group feature-icon"></i>
                                        <div class="feature-title">User Management</div>
                                        <div class="feature-desc">Multi-level access control system</div>
                                    </div>
                                    
                                    <div class="feature-card">
                                        <i class="icon-shield feature-icon"></i>
                                        <div class="feature-title">Secure System</div>
                                        <div class="feature-desc">Enterprise-grade security</div>
                                    </div>
                                </div>
                                
                                <!-- System Statistics -->
                                <div class="system-stats">
                                    <div class="row-fluid">
                                        <div class="span4">
                                            <div class="stat-item">
                                                <?php 
                                                $users = mysqli_query($bd, "SELECT COUNT(*) as total FROM users");
                                                $userCount = mysqli_fetch_array($users);
                                                ?>
                                                <span class="stat-number"><?php echo $userCount['total']; ?></span>
                                                <span class="stat-label">Users</span>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="stat-item">
                                                <?php 
                                                $complaints = mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints");
                                                $compCount = mysqli_fetch_array($complaints);
                                                ?>
                                                <span class="stat-number"><?php echo $compCount['total']; ?></span>
                                                <span class="stat-label">Complaints</span>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="stat-item">
                                                <?php 
                                                $resolved = mysqli_query($bd, "SELECT COUNT(*) as total FROM tblcomplaints WHERE status='closed'");
                                                $resCount = mysqli_fetch_array($resolved);
                                                ?>
                                                <span class="stat-number"><?php echo $resCount['total']; ?></span>
                                                <span class="stat-label">Resolved</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Side - Login Form -->
                            <div class="span6 login-right">
                                <div class="login-header" style="color: #2c3e50;">
                                    <h1 style="margin-bottom: 30px;">Admin Portal</h1>
                                </div>
                                <div class="login-header" style="text-align: center; margin-bottom: 20px;">
                                    <i class="icon-bolt login-icon pulse"></i>
                                    <h2 style="color: white; margin-bottom: 10px; color: black">CMS Admin</h2>
                                    <p style="color: black">Admin login portal</p>
                                </div>
                                <div class="login-header" style="text-align: center; margin-bottom: 20px;">
                                    
                                    <i class="icon-bolt login-icon pulse"></i>
                                </div>
                                <?php if(isset($_SESSION['errmsg'])) { ?>
                                <div class="alert alert-danger alert-animation">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong><i class="icon-warning-sign"></i> Access Denied!</strong> 
                                    <?php echo htmlentities($_SESSION['errmsg']); ?>
                                    <?php echo htmlentities($_SESSION['errmsg']=""); ?>
                                </div>
                                <?php } ?>
                                
                                <form class="form-vertical" method="post">
                                    <div class="control-group">
                                        <div class="controls">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="icon-user"></i><p style="color: black; display: inline;"> Username: </p></span>
                                                <input class="form-control" type="text" name="username" placeholder="Enter your username" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group" style="margin-top: 25px;">
                                        <div class="controls">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="icon-lock"></i><p style="color: black; display: inline;"> Password: </p></span>
                                                <input class="form-control" type="password" name="password" placeholder="Enter your password" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <div class="controls clearfix">
                                            <button type="submit" class="btn btn-login" name="submit">
                                                <i class="icon-signin"></i> Access Dashboard
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                
                                <div class="text-center">
                                    <a href="../" class="portal-link">
                                        <i class="icon-arrow-left"></i> Return to Main Portal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            // Add focus effects
            $('.form-control').focus(function() {
                $(this).parent().parent().addClass('focused');
                $(this).parent().find('.input-group-addon').css('color', '#3498db');
            }).blur(function() {
                $(this).parent().parent().removeClass('focused');
                $(this).parent().find('.input-group-addon').css('color', '#6c757d');
            });
            
            // Remove alert after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Add hover effects to feature cards
            $('.feature-card').hover(
                function() {
                    $(this).addClass('hover');
                },
                function() {
                    $(this).removeClass('hover');
                }
            );
            
            // Add typing effect to welcome text
            let welcomeText = "Welcome to the Complaint Management System Admin Portal";
            let i = 0;
            let speed = 50;
            
            function typeWriter() {
                if (i < welcomeText.length) {
                    $('.welcome-text').text(welcomeText.substring(0, i+1));
                    i++;
                    setTimeout(typeWriter, speed);
                }
            }
            
            // Start typing effect
            setTimeout(typeWriter, 1000);
        });
    </script>
</body>
</html>