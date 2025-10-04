<?php
// Prevent direct access
if (!isset($_SESSION['wlogin']) || strlen($_SESSION['wlogin']) == 0) {
    header('location:../index.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="span3">
    <div class="sidebar">
        <ul class="widget widget-menu unstyled">
            <li class="<?php echo $current_page == 'worker-dashboard.php' ? 'active' : ''; ?>">
                <a href="worker-dashboard.php">
                    <i class="menu-icon icon-dashboard"></i>Dashboard
                </a>
            </li>
            <li class="<?php echo $current_page == 'worker-my-works.php' ? 'active' : ''; ?>">
                <a href="worker-my-works.php">
                    <i class="menu-icon icon-tasks"></i>My Works
                </a>
            </li>
            <li class="<?php echo $current_page == 'worker-completed-works.php' ? 'active' : ''; ?>">
                <a href="worker-completed-works.php">
                    <i class="menu-icon icon-ok"></i>Completed Works
                </a>
            </li>
            <li class="<?php echo $current_page == 'worker-updates.php' ? 'active' : ''; ?>">
                <a href="worker-updates.php">
                    <i class="menu-icon icon-comment"></i>Work Updates
                </a>
            </li>
        </ul>

        <ul class="widget widget-menu unstyled">
            <li class="<?php echo $current_page == 'worker-profile.php' ? 'active' : ''; ?>">
                <a href="worker-profile.php">
                    <i class="menu-icon icon-user"></i>My Profile
                </a>
            </li>
            <li class="<?php echo $current_page == 'worker-change-password.php' ? 'active' : ''; ?>">
                <a href="worker-change-password.php">
                    <i class="menu-icon icon-lock"></i>Change Password
                </a>
            </li>
            <li>
                <a href="worker-logout.php">
                    <i class="menu-icon icon-signout"></i>Logout
                </a>
            </li>
        </ul>
        
        <div class="widget">
            <div class="widget-header">
                <i class="icon-info-sign"></i>
                <h3>Quick Stats</h3>
            </div>
            <div class="widget-content">
                <?php
                $worker_id = $_SESSION['wid'];
                $pending = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND status IN ('assigned', 'in_progress')"))['total'];
                $urgent = mysqli_fetch_array(mysqli_query($bd, "SELECT COUNT(*) as total FROM works WHERE assigned_worker_id='$worker_id' AND priority='urgent' AND status != 'completed'"))['total'];
                ?>
                <p>Pending Works: <strong><?php echo $pending; ?></strong></p>
                <p>Urgent Works: <strong><?php echo $urgent; ?></strong></p>
            </div>
        </div>
    </div>
</div>