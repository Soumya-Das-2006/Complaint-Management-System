<?php 
// Public header - no session check required
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GUVNL Complaint Tracking System - Track your electricity complaint status online">
    <meta name="author" content="Gujarat Urja Vikas Nigam Limited">
    <meta name="keyword" content="GUVNL, Complaint, Tracking, Electricity, Status, Gujarat">

    <title>Track Your Complaint | GUVNL Electricity Complaint System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a5f7a;
            --secondary: #2c7873;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --info: #1abc9c;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .public-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .tracking-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 30px 0;
            border: none;
        }
        
        .status-timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .status-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 50px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #6c757d;
            border: 4px solid white;
            box-shadow: 0 0 0 3px #e9ecef;
        }
        
        .timeline-item.active::before {
            background: var(--success);
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
        }
        
        .timeline-item.completed::before {
            background: var(--success);
        }
        
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .badge-pending { background: rgba(243, 156, 18, 0.1); color: var(--warning); }
        .badge-process { background: rgba(52, 152, 219, 0.1); color: var(--secondary); }
        .badge-closed { background: rgba(46, 204, 113, 0.1); color: var(--success); }
        
        .complaint-details-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .btn-guvnl {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-guvnl:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 95, 122, 0.3);
            color: white;
        }
        
        .complaint-number-input {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-align: center;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }
        
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
            z-index: 2;
        }
        
        .step.active {
            background: var(--primary);
            color: white;
        }
        
        .step.completed {
            background: var(--success);
            color: white;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: #e9ecef;
            transform: translateY(-50%);
            z-index: 1;
        }
        
        .emergency-contact {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        
        .status-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 10px 0;
            border-left: 4px solid var(--primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        @media (max-width: 768px) {
            .tracking-card, .search-box {
                padding: 20px;
                margin: 15px 0;
            }
            
            .complaint-number-input {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Public Header -->
    <header class="public-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>GUVNL Complaint Tracker
                    </h1>
                    <p class="mb-0">Electricity Complaint Tracking System</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php if(isset($_SESSION['login'])): ?>
                        <a href="dashboard.php" class="btn btn-light me-2">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                        <a href="logout.php" class="btn btn-outline-light">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    <?php else: ?>
                        <a href="../index.php" class="btn btn-light me-2">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                        <a href="index.php" class="btn btn-light me-2">
                            <i class="fas fa-sign-in-alt me-1"></i>User Login
                        </a>
                        <a href="../admin/index.php" class="btn btn-outline-light">
                            <i class="fas fa-user-shield me-1"></i>Admin Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>