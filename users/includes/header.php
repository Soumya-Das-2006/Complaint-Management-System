<?php 
session_start();
error_reporting(0);
include('config.php');
if(strlen($_SESSION['login'])==0) { 
    header('location:user_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GUVNL Complaint Management System">
    <meta name="author" content="Gujarat Urja Vikas Nigam Limited">
    <meta name="keyword" content="GUVNL, Complaint, Management, System">

    <title>GUVNL Complaint Management System | Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --info: #1abc9c;
            --guvnl-primary: #1a5f7a;
            --guvnl-secondary: #2c7873;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--guvnl-primary), var(--guvnl-secondary));
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        
        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-left: 4px solid var(--guvnl-primary);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            transition: transform 0.3s;
            height: 100%;
            border-top: 3px solid transparent;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .stat-pending .stat-icon {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }
        
        .stat-process .stat-icon {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary);
        }
        
        .stat-closed .stat-icon {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }
        
        .stat-total .stat-icon {
            background-color: rgba(26, 95, 122, 0.1);
            color: var(--guvnl-primary);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-left: 4px solid var(--guvnl-secondary);
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #f8f9fa;
            color: var(--guvnl-primary);
            text-decoration: none;
            transition: all 0.3s;
            margin-bottom: 10px;
            width: 100%;
            text-align: left;
        }
        
        .action-btn:hover {
            background: var(--guvnl-primary);
            color: white;
            transform: translateX(5px);
        }
        
        .action-btn i {
            font-size: 1.5rem;
            margin-right: 15px;
            width: 30px;
        }
        
        .recent-complaints {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-left: 4px solid var(--info);
        }
        
        .complaint-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }
        
        .complaint-item:hover {
            background: #f8f9fa;
        }
        
        .complaint-item:last-child {
            border-bottom: none;
        }
        
        .complaint-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 15px;
        }
        
        .status-pending {
            background-color: var(--warning);
        }
        
        .status-process {
            background-color: var(--secondary);
        }
        
        .status-closed {
            background-color: var(--success);
        }
        
        .progress-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-left: 4px solid var(--accent);
        }
        
        .progress-bar-custom {
            height: 10px;
            border-radius: 5px;
            background: #ecf0f1;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 5px;
        }
        
        .navbar-custom {
            background: var(--guvnl-primary) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-custom {
            background: var(--guvnl-primary);
            color: white;
            min-height: 100vh;
        }
        
        .sidebar-custom .nav-link {
            color: #bdc3c7;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar-custom .nav-link:hover, 
        .sidebar-custom .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 3px solid var(--guvnl-secondary);
        }
        
        .sidebar-custom .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 0;
            padding: 20px;
        }
        
        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }
        }
        
        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .dashboard-subtitle {
            color: #bdc3c7;
            margin-bottom: 0;
        }
        
        .guvnl-logo {
            height: 40px;
            margin-right: 10px;
        }
        /* Additional styles for complaint history page */
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

.table thead {
    background: linear-gradient(135deg, var(--guvnl-primary), var(--guvnl-secondary));
    color: white;
}

.table th {
    border: none;
    padding: 15px 12px;
    font-weight: 600;
}

.table td {
    padding: 12px;
    vertical-align: middle;
    border-color: #f1f1f1;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    transition: all 0.2s;
}

.btn-group .btn {
    border-radius: 5px;
    margin-right: 5px;
}

.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
}

/* Status badges */
.bg-warning { background-color: var(--warning) !important; }
.bg-primary { background-color: var(--secondary) !important; }
.bg-success { background-color: var(--success) !important; }

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        margin-bottom: 5px;
    }
}
/* Complaint Details Specific Styles */
.info-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.complaint-description {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.timeline-content {
    transition: all 0.3s ease;
}

.timeline-content:hover {
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Print Styles */
@media print {
    .sidebar-custom,
    .navbar-custom,
    .quick-actions,
    .btn {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
    }
    
    .info-card {
        break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-bolt me-2"></i>GUVNL Complaint System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlentities($_SESSION['login']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="change-password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>