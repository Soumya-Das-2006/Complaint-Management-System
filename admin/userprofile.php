<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
    exit;
}

// Fetch user data
$user_id = $_GET['uid'];
$ret1 = mysqli_query($bd, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_array($ret1);

// Check if user exists
if(!$user) {
    echo "User not found";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .profile-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .profile-content {
            padding: 30px;
        }
        
        .profile-field {
            display: flex;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        
        .profile-field:last-child {
            border-bottom: none;
        }
        
        .field-label {
            flex: 0 0 200px;
            font-weight: 600;
            color: #555;
        }
        
        .field-value {
            flex: 1;
            color: #333;
        }
        
        .status-active {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-blocked {
            color: #dc3545;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-close {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-print {
            background-color: #007bff;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        @media print {
            .action-buttons {
                display: none;
            }
            
            body {
                background: white;
                padding: 0;
            }
            
            .profile-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1><?php echo htmlentities($user['fullName']); ?>'s Profile</h1>
            <p>User ID: <?php echo htmlentities($user_id); ?></p>
        </div>
        
        <div class="profile-content">
            <div class="profile-field">
                <div class="field-label">Registration Date:</div>
                <div class="field-value"><?php echo htmlentities($user['regDate'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">Email Address:</div>
                <div class="field-value"><?php echo htmlentities($user['userEmail'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">Contact Number:</div>
                <div class="field-value"><?php echo htmlentities($user['contactNo'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">Address:</div>
                <div class="field-value"><?php echo htmlentities($user['address'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">State:</div>
                <div class="field-value"><?php echo htmlentities($user['State'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">Country:</div>
                <div class="field-value"><?php echo htmlentities($user['country'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">Pincode:</div>
                <div class="field-value"><?php echo htmlentities($user['pincode'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">Last Updated:</div>
                <div class="field-value"><?php echo htmlentities($user['updationDate'] ?? 'Not available'); ?></div>
            </div>
            
            <div class="profile-field">
                <div class="field-label">Account Status:</div>
                <div class="field-value">
                    <?php 
                    if(($user['status'] ?? 0) == 1) {
                        echo '<span class="status-active">Active</span>';
                    } else {
                        echo '<span class="status-blocked">Blocked</span>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn btn-close" onclick="window.close();">Close Window</button>
                <button class="btn btn-print" onclick="window.print();">Print Profile</button>
            </div>
        </div>
    </div>

    <script>
        // Handle print functionality
        function handlePrint() {
            window.print();
        }
        
        // Handle window close with confirmation
        function handleClose() {
            if(confirm("Are you sure you want to close this window?")) {
                window.close();
            }
        }
        
        // Update button event listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.btn-close').addEventListener('click', handleClose);
            document.querySelector('.btn-print').addEventListener('click', handlePrint);
        });
    </script>
</body>
</html>