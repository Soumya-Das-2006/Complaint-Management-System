<?php
// Prevent direct access
if (!isset($_SESSION['wlogin']) || strlen($_SESSION['wlogin']) == 0) {
    header('location:../index.php');
    exit;
}
?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                <i class="icon-reorder shaded"></i>
            </a>
            <a class="brand" href="worker-dashboard.php">
                <i class="icon-cog"></i> Worker Panel - CMS
            </a>
            <div class="nav-collapse collapse navbar-inverse-collapse">
                <ul class="nav pull-right">
                    <li class="nav-user dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-user"></i> <?php echo htmlentities($_SESSION['wname']); ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="worker-profile.php"><i class="icon-user"></i> My Profile</a></li>
                            <li><a href="worker-change-password.php"><i class="icon-lock"></i> Change Password</a></li>
                            <li class="divider"></li>
                            <li><a href="worker-logout.php"><i class="icon-signout"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>