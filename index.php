<?php
include('users/includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Consumer Complaint Tracking System - Track and monitor your electricity complaints in real-time">
    <meta name="author" content="GUVNL Complaint System">

    <title>GUVNL Complaint System | Track Your Electricity Issues</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <?php if(isset($title)) { ?>
        <title><?php echo htmlentities($title); ?> | GUVNL Complaint System</title>
    <?php } ?>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
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
            color: #333;
            background-color: #f8f9fa;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .hero-section {
            background: linear-gradient(rgba(26, 82, 118, 0.85), rgba(26, 82, 118, 0.9)), url('https://images.unsplash.com/photo-1509391366360-2e959784a276?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2072&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0 80px;
            text-align: center;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-section p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .btn-primary {
            background-color: var(--accent);
            border-color: var(--accent);
            padding: 10px 25px;
            font-weight: 500;
            border-radius: 30px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #d35400;
            border-color: #d35400;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-outline-light {
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-outline-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--secondary);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .stats-section {
            background-color: var(--primary);
            color: white;
            padding: 80px 0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .process-step {
            text-align: center;
            padding: 20px;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 20px;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--primary);
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-links h5 {
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-links h5:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background: var(--secondary);
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links ul li {
            margin-bottom: 10px;
        }
        
        .footer-links ul li a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links ul li a:hover {
            color: white;
        }
        
        .copyright {
            border-top: 1px solid #34495e;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: #bdc3c7;
            font-size: 0.9rem;
        }
        
        .navbar {
            padding: 15px 0;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar.scrolled {
            padding: 10px 0;
            background-color: var(--primary) !important;
        }
        
        .complaint-btn {
            background-color: var(--accent);
            border-color: var(--accent);
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: 500;
            margin-left: 10px;
        }
        
        .complaint-btn:hover {
            background-color: #d35400;
            border-color: #d35400;
        }
        
        .status-tracker {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .status-step {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ecf0f1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #7f8c8d;
        }
        
        .status-step.active .status-icon {
            background: var(--secondary);
            color: white;
        }
        
        .status-step.completed .status-icon {
            background: var(--success);
            color: white;
        }
        
        .login-section {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-top: 30px;
        }
        
        .complaint-types {
            background-color: #f8f9fa;
            padding: 60px 0;
        }
        
        .type-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
            border-top: 4px solid var(--secondary);
        }
        
        .type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .type-icon {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.2rem;
            }
            
            .hero-section {
                padding: 100px 0 60px;
            }
            
            .login-section {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: rgba(26, 82, 118, 0.9);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-bolt me-2"></i>GUVNL Complaint System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#news">News & Updates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#complaint-types">Complaint Types</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Login
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="users/index.php">Consumer Login</a></li>
                            <li><a class="dropdown-item" href="admin/index.php">Staff Login</a></li>
                            <li><a class="dropdown-item" href="worker/index.php">Worker Login</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users/registration.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn complaint-btn" href="users/register-complaint.php">File Complaint</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1>Electricity Complaint Tracking System</h1>
                    <p>Report electricity issues and track their resolution status in real-time. Our platform ensures efficient complaint management for GUVNL consumers.</p>
                    <div class="mt-4">
                        <a href="users/register-complaint.php" class="btn btn-primary me-3"><i class="fas fa-plus-circle me-2"></i>File Complaint</a>
                        <a href="users/track_complaint.php" class="btn btn-outline-light"><i class="fas fa-search me-2"></i>Track Complaint</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Access Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="login-section text-center">
                        <h3 class="mb-4">Quick Access</h3>
                        <div class="row" style="justify-content: center; text-align: center; flex-wrap: wrap;">
                            <div class="col-md-4 mb-3">
                                <a href="users/index.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-user me-2"></i>Consumer Login
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="admin/index.php" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-cog me-2"></i>Staff Login
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="worker/index.php" class="btn btn-outline-primary btn-lg w-100" style="background-color: #6c757d; border-color: #6c757d; color: white; border-radius: 50px;">
                                    <i class="fas fa-user me-2"></i>Worker Login
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3" style="margin: 0 auto; text-align: center;">
                                <a href="users/registration.php" class="btn btn-success" style="width: 100%; height: 40px; font-size: 1.1rem; ">
                                    <i class="fas fa-user-plus me-2"></i>New Registration User
                                </a>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="users/track_complaint.php" class="btn btn-warning">
                                <i class="fas fa-search me-2"></i>Track Complaint Without Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- All News Section -->
    <section id="news">
        <div class="card mt-4 shadow-sm border-0" style="border-radius: 14px; overflow: hidden; width: 100%; max-width: 860px; margin: 40px auto;">
            <div class="card-header" style="background: linear-gradient(90deg, #68dff0 0%, #38b6ff 100%); color: #fff; border-radius: 0;">
                <h4 class="mb-0" style="font-weight:700; letter-spacing: 0.5px;">
                    <i class="fa fa-newspaper-o me-2"></i> Latest News & Updates
                </h4>
            </div>
            <div class="card-body p-0" style="background: #fafdff;">
                <?php
                    // Fetch all news from tblnews
                    $news_query = mysqli_query($bd, "SELECT * FROM news ORDER BY created_at DESC");
                    if(!$news_query) {
                        echo '<div class="alert alert-danger m-4">Error fetching news: ' . mysqli_error($bd) . '</div>';
                    } elseif(mysqli_num_rows($news_query) > 0) {
                        while($news = mysqli_fetch_array($news_query)) {
                ?>
                <div class="news-item">
                    <div class="news-block d-flex flex-wrap align-items-start border-bottom px-4 py-3" style="background: #fff; transition: background 0.2s;">

                <!-- LEFT: News text section -->
                <div class="news-text flex-grow-1" style="flex: 1; min-width: 60%;">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge rounded-pill" style="background: #68dff0; color: #fff; font-size: 12px; margin-right: 10px;">
                            <i class="fa fa-bullhorn"></i>
                        </span>
                        <span class="news-title" style="font-weight: 600; color: #1a2233; font-size: 16px;">
                            <?php echo htmlentities($news['title']); ?>
                        </span>
                    </div>

                    <div class="news-date text-muted mb-2" style="font-size: 12px;">
                        <i class="fa fa-calendar me-1"></i>
                        <?php echo date('d M Y', strtotime($news['created_at'])); ?>
                    </div>

                    <div class="news-content" style="font-size: 14px; line-height: 1.7; color: #444;">
                        <?php echo nl2br(htmlentities($news['content'])); ?>
                    </div>
                </div>
                <?php if(!empty($news['image'])): ?>
                    <div class="news-image text-center" style="flex: 0 0 35%; padding: 15px 20px; background: white; border-left: 1px solid #e0e0e0;">
                        <?php 
                        $ss = "http://localhost/Complaint%20Management%20System/admin/news_images/";
                        ?>
                        <img src="<?php echo $ss . htmlentities($news['image']); ?>" alt="News Image" class="img-fluid" style="max-height: 120px; border-radius: 8px; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>

                </div>
                <?php
                        }
                    } else {
                ?>
                    <div class="text-center py-5">
                        <i class="fa fa-info-circle text-muted" style="font-size: 32px;"></i>
                        <div class="text-muted mt-3" style="font-size: 15px;">No news updates available at the moment.</div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="section-title">Why Use Our System</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h4>Real-time Tracking</h4>
                        <p>Monitor your complaint status in real-time, just like tracking a food delivery or cab.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile Friendly</h4>
                        <p>Access the system from any device - desktop, tablet, or smartphone.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Notification Alerts</h4>
                        <p>Receive updates via SMS or email when there's progress on your complaint.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h4>Complaint History</h4>
                        <p>Access your complete complaint history for future reference.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Performance Analytics</h4>
                        <p>GUVNLs can monitor workforce efficiency and complaint resolution metrics.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Location-based Assignment</h4>
                        <p>Complaints automatically assigned to nearest available workforce.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Complaint Types Section -->
    <section id="complaint-types" class="complaint-types">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="section-title">Common Complaint Types</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-plug"></i>
                        </div>
                        <h5>Power Outage</h5>
                        <p>Complete loss of electricity in your area</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <h5>Meter Issues</h5>
                        <p>Faulty meter, reading discrepancies</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h5>Voltage Fluctuation</h5>
                        <p>High/Low voltage issues damaging appliances</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-pole"></i>
                        </div>
                        <h5>Line Faults</h5>
                        <p>Damaged poles, hanging wires, safety hazards</p>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h5>Billing Issues</h5>
                        <p>Incorrect bills, payment problems</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h5>Connection Issues</h5>
                        <p>New connection, transfer, disconnection</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-tree"></i>
                        </div>
                        <h5>Tree Trimming</h5>
                        <p>Trees interfering with power lines</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="type-card">
                        <div class="type-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h5>Other Issues</h5>
                        <p>Any other electricity-related problems</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">42,856</div>
                        <div>Complaints Resolved</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">94%</div>
                        <div>Satisfaction Rate</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">2.1h</div>
                        <div>Avg. Resolution Time</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">15 min</div>
                        <div>Avg. Initial Response</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="section-title">How It Works</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <h4>File Complaint</h4>
                        <p>Register your electricity issue through our simple online form or mobile app.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <h4>Receive Complaint ID</h4>
                        <p>Get an immediate confirmation with your unique tracking ID for reference.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <h4>Track Progress</h4>
                        <p>Monitor the status of your complaint in real-time through our tracking system.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <h4>Resolution & Feedback</h4>
                        <p>Receive notification when resolved and provide feedback on the service.</p>
                    </div>
                </div>
            </div>
            
            <!-- Status Tracker Demo -->
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto">
                    <div class="status-tracker">
                        <h4 class="text-center mb-4">Track Your Complaint Status</h4>
                        <div class="status-step completed">
                            <div class="status-icon"><i class="fas fa-check"></i></div>
                            <div>
                                <h5 class="mb-1">Complaint Registered</h5>
                                <p class="mb-0">Your complaint has been received (Today, 10:15 AM)</p>
                            </div>
                        </div>
                        <div class="status-step completed">
                            <div class="status-icon"><i class="fas fa-user-cog"></i></div>
                            <div>
                                <h5 class="mb-1">Technician Assigned</h5>
                                <p class="mb-0">Rajesh Kumar is assigned to your complaint (Today, 10:30 AM)</p>
                            </div>
                        </div>
                        <div class="status-step active">
                            <div class="status-icon"><i class="fas fa-tools"></i></div>
                            <div>
                                <h5 class="mb-1">Work In Progress</h5>
                                <p class="mb-0">Technician is on the way to your location (ETA: 25 min)</p>
                            </div>
                        </div>
                        <div class="status-step">
                            <div class="status-icon"><i class="fas fa-flag-checkered"></i></div>
                            <div>
                                <h5 class="mb-1">Resolved</h5>
                                <p class="mb-0">Issue will be marked resolved once fixed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-bolt me-2"></i>GUVNL Complaint System</h5>
                    <p>A modern platform for tracking electricity complaints and ensuring timely resolution for consumers.</p>
                    <div class="mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#complaint-types">Complaint Types</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>System Access</h5>
                    <ul>
                        <li><a href="users/index.php">Consumer Login</a></li>
                        <li><a href="admin/index.php">Staff Login</a></li>
                        <li><a href="users/registration.php">New Registration</a></li>
                        <li><a href="users/complaint-form.php">File Complaint</a></li>
                        <li><a href="users/track-complaint.php">Track Complaint</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Contact Us</h5>
                    <ul>
                        <li><i class="fas fa-map-marker-alt me-2"></i> GUVNL Office, City</li>
                        <li><i class="fas fa-phone me-2"></i> 1912 (Toll-free)</li>
                        <li><i class="fas fa-envelope me-2"></i> support@guvnlcomplaints.gov</li>
                        <li><i class="fas fa-clock me-2"></i> 24x7 Helpline</li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="copyright">
                        <p>&copy; 2023 GUVNL Complaint Tracking System. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 70,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>