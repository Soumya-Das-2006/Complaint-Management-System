<?php
include('includes/config.php');
error_reporting(0);
if(isset($_POST['submit']))
{
	$fullname=$_POST['fullname'];
	$email=$_POST['email'];
	$password=md5($_POST['password']);
	$contactno=$_POST['contactno'];
	$address=$_POST['address'];
	$consumerid=$_POST['consumerid'];
	$status=1;
	$query=mysqli_query($bd, "insert into users(fullName,userEmail,password,contactNo,address,consumerId,status) values('$fullname','$email','$password','$contactno','$address','$consumerid','$status')");
	$msg="Registration successful. Now you can login!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GUVNL Complaint System - Consumer Registration">
    <meta name="author" content="GUVNL Complaint System">
    <meta name="keyword" content="GUVNL, Electricity, Complaint, Registration">

    <title>GUVNL Complaint System | Consumer Registration</title>

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
        
        .registration-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .registration-left {
            flex: 1;
            background: linear-gradient(rgba(26, 82, 118, 0.85), rgba(26, 82, 118, 0.9)), url('https://images.unsplash.com/photo-1581094794329-c8112a89af24?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .registration-right {
            flex: 1.2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
            max-height: 90vh;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
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
            padding-left: 15px;
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
        
        .btn-register {
            background-color: var(--accent);
            border-color: var(--accent);
            color: white;
            height: 50px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-register:hover {
            background-color: #d35400;
            border-color: #d35400;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
        }
        
        .login-link a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
            color: #2980b9;
        }
        
        .alert {
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
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
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 3px;
        }
        
        .requirement i {
            font-size: 0.7rem;
            margin-right: 5px;
        }
        
        .requirement.met {
            color: var(--success);
        }
        
        .requirement.unmet {
            color: #e74c3c;
        }
        
        .emergency-contact {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: center;
            border-left: 4px solid var(--accent);
        }
        
        .emergency-contact h5 {
            color: white;
            margin-bottom: 5px;
        }
        
        .emergency-contact p {
            margin: 0;
            font-weight: 500;
            color: var(--accent);
        }
        
        @media (max-width: 768px) {
            .registration-container {
                flex-direction: column;
            }
            
            .registration-left {
                padding: 30px;
            }
            
            .registration-right {
                padding: 30px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function userAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data:'email='+$("#email").val(),
            type: "POST",
            success:function(data){
                $("#user-availability-status1").html(data);
                $("#loaderIcon").hide();
            },
            error:function (){}
        });
    }
    
    function validatePassword() {
        const password = document.getElementById('password').value;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
        };
        
        // Update requirement indicators
        document.getElementById('length').className = requirements.length ? 'requirement met' : 'requirement unmet';
        document.getElementById('uppercase').className = requirements.uppercase ? 'requirement met' : 'requirement unmet';
        document.getElementById('lowercase').className = requirements.lowercase ? 'requirement met' : 'requirement unmet';
        document.getElementById('number').className = requirements.number ? 'requirement met' : 'requirement unmet';
        document.getElementById('special').className = requirements.special ? 'requirement met' : 'requirement unmet';
        
        // Check if all requirements are met
        const allMet = Object.values(requirements).every(val => val === true);
        document.getElementById('submit').disabled = !allMet;
    }
    
    function validateContact() {
        const contactInput = document.getElementById('contactno');
        const contactValue = contactInput.value.replace(/\D/g, ''); // Remove non-digits
        contactInput.value = contactValue; // Update input with digits only
        
        if (contactValue.length > 10) {
            contactInput.value = contactValue.slice(0, 10); // Limit to 10 digits
        }
    }
    </script>
</head>

<body>
    <div class="registration-container">
        <div class="registration-left">
            <div class="welcome-content">
                <h2>Join GUVNL Complaint System</h2>
                <p>Register now to report electricity issues and track their resolution in real-time. Our platform ensures efficient complaint management for all consumers.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Real-time Complaint Tracking</li>
                    <li><i class="fas fa-check-circle"></i> SMS & Email Notifications</li>
                    <li><i class="fas fa-check-circle"></i> Complaint History Access</li>
                    <li><i class="fas fa-check-circle"></i> Mobile-Friendly Interface</li>
                    <li><i class="fas fa-check-circle"></i> 24/7 Support Availability</li>
                </ul>
                
                <div class="emergency-contact">
                    <h5><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                    <p>1912 (Toll-free) | 24x7 Helpline</p>
                </div>
            </div>
        </div>
        
        <div class="registration-right">
            <div class="logo">
                <i class="fas fa-bolt"></i>
                <h1>GUVNL Complaint System</h1>
            </div>
            
            <div class="welcome-text">
                <h2>Consumer Registration</h2>
                <p>Create your account to start reporting electricity issues</p>
            </div>
            
            <!-- Success Message -->
            <?php if(isset($msg)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlentities($msg); ?>
                </div>
            <?php endif; ?>
            
            <!-- Registration Form -->
            <form class="form-registration" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Full Name" name="fullname" required="required" autofocus>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Consumer ID" name="consumerid" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email Address" id="email" onBlur="userAvailability()" name="email" required="required">
                    <span id="user-availability-status1" style="font-size:12px; display:block; margin-top:5px;"></span>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" id="contactno" name="contactno" placeholder="Contact Number" maxlength="10" onInput="validateContact()" required="required">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Area/Locality" name="address" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="password" class="form-control" id="password" placeholder="Password" required="required" name="password" onKeyUp="validatePassword()">
                    <div class="password-requirements">
                        <div class="requirement" id="length"><i class="fas fa-circle"></i> At least 8 characters</div>
                        <div class="requirement" id="uppercase"><i class="fas fa-circle"></i> One uppercase letter</div>
                        <div class="requirement" id="lowercase"><i class="fas fa-circle"></i> One lowercase letter</div>
                        <div class="requirement" id="number"><i class="fas fa-circle"></i> One number</div>
                        <div class="requirement" id="special"><i class="fas fa-circle"></i> One special character</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Confirm Password" required="required" name="confirmpassword" id="confirmpassword">
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a></label>
                </div>
                
                <button class="btn btn-register" type="submit" name="submit" id="submit" disabled>
                    <i class="fas fa-user-plus me-2"></i> CREATE ACCOUNT
                </button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="index.php">Sign in here</a>
            </div>
            
            <div class="text-center mt-3">
                <a href="../index.html" class="text-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>