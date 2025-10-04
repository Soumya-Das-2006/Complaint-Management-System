<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
    exit;
}

$complaintnumber = $_GET['cid'];
$success_message = '';

if(isset($_POST['update'])) {
    $status = $_POST['status'];
    $remark = $_POST['remark'];
    
    $query = mysqli_query($bd, "INSERT INTO complaintremark(complaintNumber, status, remark) VALUES('$complaintnumber', '$status', '$remark')");
    $sql = mysqli_query($bd, "UPDATE tblcomplaints SET status='$status' WHERE complaintNumber='$complaintnumber'");
    
    $success_message = "Complaint details updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Complaint - Popup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .popup-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            animation: popup-appear 0.3s ease-out;
        }
        
        @keyframes popup-appear {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .popup-header {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .popup-header h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }
        
        .complaint-number {
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .popup-body {
            padding: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: border 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        
        .button-group .btn {
            flex: 1;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .status-in-process {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-closed {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="popup-container">
        <div class="popup-header">
            <h2>Update Complaint Status</h2>
            <div class="complaint-number">Complaint #<?php echo htmlentities($complaintnumber); ?></div>
        </div>
        
        <div class="popup-body">
            <?php if(!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form name="updatecomplaint" method="post">
                <div class="form-group">
                    <label for="status">Status <span style="color: red;">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">Select Status</option>
                        <option value="in process">In Process</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="remark">Remark <span style="color: red;">*</span></label>
                    <textarea name="remark" id="remark" class="form-control" placeholder="Enter your remarks here..." required></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="update" class="btn btn-primary">Update Complaint</button>
                    <button type="button" class="btn btn-secondary" onclick="closeWindow()">Close Window</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function closeWindow() {
            if(confirm("Are you sure you want to close this window? Any unsaved changes will be lost.")) {
                window.close();
            }
        }
        
        // Add visual feedback for status selection
        document.getElementById('status').addEventListener('change', function() {
            const statusValue = this.value;
            const statusLabel = document.querySelector('label[for="status"]');
            
            // Remove existing status badge
            const existingBadge = statusLabel.querySelector('.status-badge');
            if(existingBadge) {
                statusLabel.removeChild(existingBadge);
            }
            
            // Add new status badge if a status is selected
            if(statusValue) {
                const badge = document.createElement('span');
                badge.className = `status-badge status-${statusValue.replace(' ', '-')}`;
                badge.textContent = statusValue.charAt(0).toUpperCase() + statusValue.slice(1);
                statusLabel.appendChild(badge);
            }
        });
        
        // Focus on the first form field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('status').focus();
        });
        
        // Prevent form resubmission on page refresh
        if(window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
