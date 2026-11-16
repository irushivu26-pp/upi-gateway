<?php
include "config.php";
session_start();

if (isset($_SESSION['username'])) {
    $mobile = $_SESSION['username'];
    $user = "SELECT * FROM users WHERE mobile = '$mobile'";
    $uu = mysqli_query($conn, $user);
    $userdata = mysqli_fetch_array($uu);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Fastest & Secured UPi Payment Gateway Instant Settlement.">

    <title>UPI Gateway - Fastest & Secured Payment Gateway ❤️</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
 

  <body class="app sidebar-mini">
    <!-- Navbar-->
<header class="app-header">
<a class="app-header__logo" href="dashboard.php">
        <img src="/partner/l.png" style="width: 210px;"  alt="Logo">
    </a>

      <!-- Sidebar
      <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <!-- Navbar Right Menu-->
      <ul class="app-nav">

        </li>

        </li>
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu"><i class="bi bi-person fs-4"></i></a>
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
              
            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2 fs-5"></i> Profile</a></li>
          
          <li><a class="dropdown-item" href="password.php"><i class="bi bi-gear me-2 fs-5"></i> Change Password</a></li>
          
            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2 fs-5"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </header>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
      <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="https://randomuser.me/api/portraits/men/1.jpg" alt="User Image">
        <div>
          <p class="app-sidebar__user-name"><?php echo $userdata['name']; ?></p>
          <p class="app-sidebar__user-designation"><?php echo $userdata['role']; ?></p>
        </div>
      </div>
      
      
      <ul class="app-menu">
        <li><a class="app-menu__item active" href="dashboard.php"><i class="app-menu__icon bi bi-speedometer"></i><span class="app-menu__label">Dashboard</span></a></li>
        <?php
			if($userdata['role'] == 'User'){
			    ?> 
			     <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon bi bi-laptop"></i><span class="app-menu__label">Merchant Settings</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="upisettings.php"><i class="icon bi bi-circle-fill"></i> UPI Settings</a></li>
            <li><a class="treeview-item" href="api_key.php"><i class="icon bi bi-circle-fill"></i> Api Key</a></li>
          </ul>
        </li>
        
        
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon bi bi-ui-checks"></i><span class="app-menu__label">Transaction</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
          <ul class="treeview-menu">
                          <li><a class="treeview-item" href="recent.php"><i class="icon bi bi-circle-fill"></i>Recent Transaction</a></li>
            <li><a class="treeview-item" href="transactions.php"><i class="icon bi bi-circle-fill"></i>Payment Report</a></li>
          </ul>
        </li>
        
        
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon bi bi-table"></i><span class="app-menu__label">Payment Link</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="payment_link.php"><i class="icon bi bi-circle-fill"></i>Create New</a></li>
          </ul>
        </li>
        
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon bi bi-file-earmark"></i><span class="app-menu__label">Subscription</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="plan.php"><i class="icon bi bi-circle-fill"></i>Purchase Plan</a></li>

          </ul>
        </li>
        <li><a class="app-menu__item" href="docs.php"><i class="app-menu__icon bi bi-code-square"></i><span class="app-menu__label">Docs</span></a></li>
        
        <li><a class="app-menu__item" href="logout.php"><i class="app-menu__icon bi bi-box-arrow-right"></i><span class="app-menu__label">Logout</span></a></li>
      </ul>
    </aside>
        <?php }?>
        <?php
			if($userdata['role'] == 'Admin'){
			    ?>
			    <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon bi bi-laptop"></i><span class="app-menu__label">User Management</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="total_user.php"><i class="icon bi bi-circle-fill"></i> Total Users</a></li>
            <li><a class="treeview-item" href="adduser.php"><i class="icon bi bi-circle-fill"></i> Add User</a></li>
          </ul>
        </li>


         
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon bi bi-ui-checks"></i><span class="app-menu__label">Transaction</span><i class="treeview-indicator bi bi-chevron-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="total_payment.php"><i class="icon bi bi-circle-fill"></i>Payment Report</a></li>
          </ul>
       
        
        </li>
        <li><a class="app-menu__item" href="docs.php"><i class="app-menu__icon bi bi-code-square"></i><span class="app-menu__label">Docs</span></a></li>
        
        <li><a class="app-menu__item" href="logout.php"><i class="app-menu__icon bi bi-box-arrow-right"></i><span class="app-menu__label">Logout</span></a></li>
      </ul>
    </aside>
			    <?php } ?>
       
    <?php
} else {

   header("location:login.php");
}
?>
