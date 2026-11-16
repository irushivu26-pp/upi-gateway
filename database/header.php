<?php
session_start();
require_once ('../database/config.php');
$usql = $conn->prepare("select * from loginusers WHERE username = ?");
$usql->execute([$_SESSION['username']]);
$userdata = $usql->fetch();
$dsql = $conn->prepare("select * from loginusers WHERE username = ?");
$dsql->execute([$userdata['createby']]);
$disdata = $dsql->fetch();
$ssql = $conn->prepare("select * from loginusers WHERE username = ?");
$ssql->execute([$disdata['createby']]);
$supdata = $ssql->fetch();
$wsql = $conn->prepare("select * from loginusers WHERE username = ?");
$wsql->execute([$supdata['createby']]);
$wldata = $wsql->fetch();



if ($userdata['balance'] == "") {
    $balance = 0;
} else {
    $balance = $userdata['balance'];
}
if (!isset($_SESSION['login_key']) || !isset($_SESSION['username'])) {
    header("Location:login.php");
    echo '<script>
window.location = "login.php"
</script>';
}
if ($userdata['usertype'] == "wluser") {
    if ($userdata['id'] == $webdata['users']) {
    } else {
        echo '<script>
window.location = "logout.php"
</script>';
    }
}
if ($_SESSION['login_key'] != $userdata['login_key']) {
    session_destroy();
    header('Location: index.php');
}

function Get_Operator($operator_id) {
switch ($operator_id) {
        case "A":
        $operatorName= "airtel";
        break;
        case "V":
        $operatorName= "idea";
        break;
        case "V":
        $operatorName= "vodafone";
        break;
        case "RC":
        $operatorName= "jio";
        break;
        case "RC":
        $operatorName= "jio";
        break;
        case "BT":
        $operatorName= "bsnl_topup";
        break;
        case "BT":
        $operatorName= "bsnl_special";
        break;
        case "ABC":
        $operatorName= "airtel_postpaid";
        break;
        case "ABC":
        $operatorName= "idea_postpaid";
        break;
        case "ABC":
        $operatorName= "vodafone_postpaid";
        break;
        case "ABC":
        $operatorName= "jio_postpaid";
        break;
        case "ABC":
        $operatorName= "bsnl_postpaid";
        break;
        case "ATV":
        $operatorName= "airtel_dth";
        break;
        case "DTV":
        $operatorName= "dish_dth";
        break;
        case "TTV":
        $operatorName= "tatasky_dth";
        break;
        case "VTV":
        $operatorName= "videocon_dth";
        break;
        case "STV":
        $operatorName= "sun_dth";
        break;
        case "bbps":
        $operatorName= "bbps";
        break;
        
        
       default:
       $operatorName= "";
}
return $operatorName;
}

function curl_get($url)
{

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL =>$url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);  

return $response;	
}


$msql = $conn->prepare("select * from settings WHERE id = ?");
$msql->execute(['1']);
$mw_data=$msql->fetch();
$pg_obj = json_decode($mw_data['pgapi']); 
	
$gateway_api = json_decode($mw_data['gateway_api']); 
$datetime =date('d-M-Y h:i:s A', time());
   $client_ip = $_SERVER['REMOTE_ADDR'];	
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $webdata['webname']; ?> - Dashboard</title>

  <!-- Custom fonts for this template-->
  <link href="../bootstrap/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.14/dist/sweetalert2.all.min.js"></script>
    <!-- Fav  -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>
  <link href="../bootstrap/css/sb-admin-2.min.css" rel="stylesheet">
  <link href="../bootstrap/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<?php
$color_obj = json_decode($webdata['color_json']);
?>
<style>
.bg-dark {
    background-color: <?php echo $color_obj->{'main_color'};?>!important;
}

.bg-primary {
    background-color: <?php echo $color_obj->{'main_color'};?>!important;
}

.btn-primary {
    background-color: <?php echo $color_obj->{'main_color'};?>;
    border-color: <?php echo $color_obj->{'supportive_color'};?>;
}

.text-primary {
    color: <?php echo $color_obj->{'main_color'};?>!important;
}
.btn-dark {
    color: #fff;
    background-color: <?php echo $color_obj->{'supportive_color'};?>;
    border-color: <?php echo $color_obj->{'supportive_color'};?>;
}
</style>

</head>
<body id="page-top" >
   
  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-dark sidebar sidebar-dark accordion " id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
       <div class="sidebar-brand-icon">
          <img src="https://upbpay.tech/user.png" class="img-circle"  height="60">
          
          
          
        
        </div>
        <!---end here ---->
        <div></div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading text-white">
        <?php echo strtoupper($userdata['usertype']); ?> - <?php echo $userdata['username']; ?>
      </div>
<?php if ($userdata['usertype'] == "mainadmin") { ?>
      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-user text-white"></i>
          <span class="text-white">Partner Management</span>  
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Partner Management:</h6><?php if ($userdata['usertype'] == "mainadmin") { ?>
            
            <a class="collapse-item text-dark" href="bal.php">Transfer Balance</a>
            
                        <a class="collapse-item text-dark" href="createusers.php">Create Users</a>
            
                        <a class="collapse-item text-dark" href="listofusers.php">List of Users</a>
                        
                                                  <a class="collapse-item text-dark" href="report.php">Today Report</a>
            <?php
    } ?>
		
			<?php if ($userdata['usertype'] == "mainadmin") { ?>
      
			<?php
    } ?>
          </div>
        </div>
</li>
<?php
} ?>
   
         <!-- Nav Item - Pages Collapse Menu -->
      <!----li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages2" aria-expanded="true" aria-controls="collapsePages2">
          <i class="fas fa-fw fa-qrcode text-white"></i>
          <span class="text-white">UTIITSL Management</span>
        </a>
        <div id="collapsePages2" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
   <?php if ($userdata['usertype'] == "mainadmin") { ?>
            <a class="collapse-item text-dark" href="pendingpcoupon.php">Pending P-Coupon [<?php
    $sql = $conn->prepare("select count(*) from couponreq WHERE status = ? and type=? ");
    $sql->execute(['pending', 'p-coupon']);
    $result = $sql->fetchColumn();
    echo $result; ?>]</a>
            <a class="collapse-item text-dark" href="pendingecoupon.php">Pending E-Coupon [<?php
    $sql = $conn->prepare("select count(*) from couponreq WHERE status = ? and type=? ");
    $sql->execute(['pending', 'e-coupon']);
    $result = $sql->fetchColumn();
    echo $result; ?>]</a>
            <a class="collapse-item text-dark" href="couponhistory.php">Coupon History</a>
   <?php
} ?>	

<?php if ($userdata['usertype'] != "mainadmin") { ?>			
            <a class="collapse-item text-dark" href="purchasecoupon.php">Purchase Coupon</a>
            </div>
        </div>
      </li>
   <?php
} ?>	      

 
	 
	<!-- Nav Item - Pages Collapse Menu>
      <!--li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#nsdl" aria-expanded="true" aria-controls="nsdl">
          <i class="fas fa-fw fa-id-card text-white"></i>
          <span class="text-white">NSDL Management</span>
        </a>
        <div id="nsdl" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
		<?php if($userdata['usertype']!="mainadmin") {?>	
            <a class="collapse-item text-primary" href="newpancard.php">New PAN Card</a>	
            <a class="collapse-item text-primary" href="csfpancard.php">CSF PAN Card</a>
            <a class="collapse-item text-primary" href="listofpancard.php">List of PAN Card</a>	
        <?php }?>	
		<?php if($userdata['usertype']=="mainadmin") {?>
            <a class="collapse-item text-primary" href="newpanreq.php">Pending NEW [<?php
$sql = $conn->prepare("select count(*) from nsdlpancard WHERE status = ? AND type='newpan' ");
$sql->execute(['process']);
$result = $sql->fetchColumn();
echo $result;
					  ?>]</a>	
            <a class="collapse-item text-primary" href="csfpanreq.php">Pending CSF [<?php
$sql = $conn->prepare("select count(*) from nsdlpancard WHERE status = ? AND type='csfpan' ");
$sql->execute(['process']);
$result = $sql->fetchColumn();
echo $result;
					  ?>]</a>	
            <a class="collapse-item text-primary" href="panhistory.php">PAN History</a>
					  
		<h6 class="collapse-header text-dark">Hold Application</h6>
		<a class="collapse-item text-primary" href="newholdreq.php">Hold NEW [<?php
$sql = $conn->prepare("select count(*) from nsdlpancard WHERE status = ? AND type='newpan' ");
$sql->execute(['holdprocess']);
$result = $sql->fetchColumn();
echo $result;
					  ?>]</a>
		<a class="collapse-item text-primary" href="csfholdreq.php">Hold CSF [<?php
$sql = $conn->prepare("select count(*) from nsdlpancard WHERE status = ? AND type='csfpan' ");
$sql->execute(['holdprocess']);
$result = $sql->fetchColumn();
echo $result;
					  ?>]</a>			  
		
		<?php }?>	
            </div>
        </div>
        
         <!-- Nav Item - Pages Collapse Menu -->
	   <?php if ($userdata['usertype'] == "supdistributor") { ?>
	  <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#recharge" aria-expanded="false" aria-controls="recharge">
          <i class="fas fa-fw fa-clipboard text-white"></i>
          <span class="text-white">Pan Data Update</span>
        </a>
        <div id="recharge" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar" style="">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item text-primary" href="vendor_pan.php">Pending Data</a>
            <a class="collapse-item text-primary" href="vendor_report.php">Today Report</a>
            <a class="collapse-item text-primary" href="panlost.php">List Of Data</a>
      </div>
      </div>
      <?php  } ?>
      </li>
        
        
      </li>
 <!-- Nav Item - Pages Collapse Menu -->
	   <?php if ($userdata['usertype'] == "retailer") { ?>
	  <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#recharge" aria-expanded="false" aria-controls="recharge">
          <i class="fas fa-fw fa-clipboard text-white"></i>
         
      </div>
      </div>
      <?php  } ?>
      </li>
      
      <!---start newtag here---->
 <!-- Nav Item - Pages Collapse Menu -->
	   <?php if ($userdata['usertype'] == "mainadmin") { ?>
	  <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#recharge" aria-expanded="false" aria-controls="recharge">
          <i class="fas fa-fw fa-clipboard text-white"></i>
          <span class="text-white">Data Update</span>
        </a>
        <div id="recharge" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar" style="">
        <div class="bg-white py-2 collapse-inner rounded">
          
            <a class="collapse-item text-primary" href="pendingdl.php">pdate</a>
            <a class="collapse-item text-primary" href="srno.php"> Rcord</a>
          
      </div>
      </div>
      <?php  } ?>
      </li>
             <!-- Nav Item - Pages Collapse Menu -->
	   
	  <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#pp" aria-expanded="false" aria-controls="recharge">
          <i class="fas fa-fw fa-check text-white"></i>
          <span class="text-white">Request List</span>
        </a>
        <div id="pp" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar" style="">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item text-primary" href="panlost.php">APi  List</a>
               
      </div>
      </div>
      


       <!-- Nav Item - Pages Collapse Menu -->
	  
      <!---start newtag here---->

	  <!---li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#softwares" aria-expanded="false" aria-controls="recharge">
          <i class="fas fa-fw fa-th-large text-white"></i>
          <span class="text-white">Sample Documents</span>
        </a>
        <div id="softwares" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar" style="">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item text-primary" href="softwares.php">New Pan Card</a>
          
      </div>
      </div>
      </li>
      
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#youtubes" aria-expanded="false" aria-controls="recharge">
          <i class="fas fa-fw fa-film text-white"></i>
          <span class="text-white">You Tube Videos</span>
        </a>
        <div id="youtubes" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar" style="">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item text-primary" href="youtubes.php">New Videos</a>
          
      </div>
      </div>
      </li>
      
      <!-- Nav Item - Pages Collapse Menu -->
      <!--li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages1" aria-expanded="true" aria-controls="collapsePages1">
          <i class="fas fa-fw fa-key text-white"></i>
          <span class="text-white"> PSA Password Reset</span>
        </a>
        <div id="collapsePages1" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
		  <?php if ($userdata['usertype'] != "mainadmin") { ?>
             <a class="collapse-item text-dark" href="resetpassreq.php"> Reset Request</a>
		  <?php
} ?>	 
		  <?php if ($userdata['usertype'] == "mainadmin") { ?>
             <a class="collapse-item text-dark" href="pendingpassreq.php"> Pending Request [<?php
    $sql = $conn->prepare("select count(*) from psapassreq WHERE status = ?");
    $sql->execute(['pending']);
    $result = $sql->fetchColumn();
    echo $result; ?>]</a>
         <?php
} ?>	 
             <a class="collapse-item text-dark" href="passreqrecord.php">Password Change Record</a>
            <div class="collapse-divider"></div>
          </div>
        </div>
      </li>
	  <!------end-------->
	   <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Wallet" aria-expanded="true" aria-controls="Wallet">
            <i class="fas fa-fw fa-wallet text-white"></i>
            <span class="text-white">Wallet Management</span>
          </a>
          <div id="Wallet" class="collapse" aria-labelledby="headingTwo" data-parent="#Wallet">
            <div class="bg-white py-2 collapse-inner rounded">
<?php if ($userdata['usertype'] == "mainadmin" || $userdata['usertype'] == "wluser") { ?>
              
<?php
} ?>	
<?php if ($userdata['usertype'] != "mainadmin") { ?>
         <a class="collapse-item text-dark" href="addwallet.php" >Add Wallet Online</a>
                  <a class="collapse-item text-dark" href="transaction_info.php" >Get Transaction Details</a>
         <a class="collapse-item text-dark" href="walletrecord.php" >Wallet Topup Record</a>
<?php
} ?>	  
<?php if ($userdata['usertype'] == "mainadmin") { ?>

         <a class="collapse-item text-dark" href="transaction_info.php" >Get Transaction Details</a>
         <a class="collapse-item text-dark" href="wallettotalrecord.php" >Wallet Summary</a>
         <a class="collapse-item text-dark" href="wallettotaladdwallet.php" >Wallet Topup Record</a>
          <a class="collapse-item text-dark" href="wallettotalrefund.php" >Wallet Refund Record</a>
<?php
} ?>	  
<?php if ($userdata['usertype'] == "mainadmin" || $userdata['usertype'] == "wluser") { ?>

<?php
} ?>	

            </div>
          </div>
        </li>
		
		
	   <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsemsg" aria-expanded="true" aria-controls="collapsemsg">
          <i class="fas fa-fw fa-envelope  text-white"></i>
          <span class="text-white">Support Team</span>
        </a>
        <div id="collapsemsg" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header text-primary">Chat With Team:</h6>
             <?php if ($userdata['usertype'] == "mainadmin") { ?>
            <a class="collapse-item text-primary" href="pendingquery.php">Pending Query</a>
            <a class="collapse-item text-primary" href="listofquery.php">List of Query</a>
			 <?php
} ?>
			 <?php if ($userdata['usertype'] != "mainadmin") { ?>	
            <a class="collapse-item text-primary" href="raisequery.php">Raise Query</a>
            <a class="collapse-item text-primary" href="viewquery.php">View Reply</a> 
			 <?php
} ?>
           </div>
        </div>
      </li-->
<?php if ($userdata['usertype'] == "mainadmin" or $userdata['usertype'] == "wluser") { ?>
<?php
} ?>	
<!--- Nav Item Page>
<?php if ($userdata['usertype'] == "mainadmin" or $userdata['usertype'] == "wluser") { ?>
        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Control" aria-expanded="true" aria-controls="Control">
            <i class="fas fa-fw fa-tools text-white"></i>
            <span class="text-white">System Management</span>
          </a>
          <div id="Control" class="collapse" aria-labelledby="headingTwo" data-parent="#Control">
            <div class="bg-white py-2 collapse-inner rounded">
              <a class="collapse-item text-dark" href="notifications.php" >Update Notifications</a>
              <a class="collapse-item text-dark" href="wlaction.php?type=update&siteid=1 <?php echo $webdata['id']; ?>" >Update Portal Info</a>
              <a class="collapse-item text-dark" href="updatelogo.php" >Update Logo</a>
              <a class="collapse-item text-dark" href="updatelink.php" >Update Link</a>
              
            </div>
          </div>
        </li>
<?php
} ?>		
         <!-- Nav Item - Pages Collapse Menu -->
        <!---li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapselinks" aria-expanded="true" aria-controls="collapselinks">
            <i class="fas fa-fw fa-arrow-right text-white"></i>
            <span class="text-white">Important Links</span>
          </a>
          <div id="collapselinks" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <a class="collapse-item text-dark" href="https://www.myutiitsl.com/PAN_ONLINE/ePANCard" target="_blank">e-PAN Download</a>
              <a class="collapse-item text-dark" href="https://www1.incometaxindiaefiling.gov.in/e-FilingGS/Services/LinkAadhaarHome.html?lang=eng" target="_blank">Link PAN-Aadhar</a>
              <a class="collapse-item text-dark" href="https://www1.incometaxindiaefiling.gov.in/e-FilingGS/Services/VerifyYourPanDeatils.html?lang=eng" target="_blank">Verify PAN Details</a> 
              <a class="collapse-item text-dark" href="photosigntool.php" target="_blank">Photo Sign Crop Tool</a>          
            </div>
          </div>
        </li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsefilemgmt" aria-expanded="true" aria-controls="collapsefilemgmt">
            <i class="fas fa-fw fa-file  text-white"></i>
            <span class="text-white">File Management</span>
          </a>
          <div id="collapsefilemgmt" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <a class="collapse-item text-dark" href="downloads.php">Downloads</a>
			    <?php if ($userdata['usertype'] == "mainadmin") { ?>
              <a class="collapse-item text-dark" href="uploadsfiles.php">Uploads Files</a>
				<?php
} ?>
             </div>
          </div>
        </li>

    <!----49+858--->
	<?php if ($userdata['usertype'] == "apipartner") { ?>
     <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Developers" aria-expanded="true" aria-controls="collapselogout">
          <i class="fas fa-fw fa-tools  text-white"></i>
          <span class="text-white">Developers API</span>
        </a>
        <div id="Developers" class="collapse" aria-labelledby="headingUtilities" data-parent="#Developers">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item text-dark" href="apikey.php">Api Key</a>
            <a class="collapse-item text-dark" href="a.php">Api Docs</a>
            
           </div>
        </div>
      </li>
	<?php
} ?>
	  <!--------end------->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapselogout" aria-expanded="true" aria-controls="collapselogout">
          <i class="fas fa-fw fa-lock  text-white"></i>
          <span class="text-white">Exit</span>
        </a>
        <div id="collapselogout" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header text-dark">Logout</h6>
            <a class="collapse-item text-dark" href="logout.php">Logout</a>
            
           </div>
        </div>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
		  <?php if ($userdata['usertype'] == "mainadmin") { ?>
		   <form class="form-inline my-md-0">
            <div class="input-group">
              <button type="button" class="btn btn-dark text-white">Users Balance: 
			  <?php
    $stmt = $conn->prepare("select sum(balance) from loginusers");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        echo $row['sum(balance)'] - $balance;
    }
?> 
			  </button>          
            </div>
          </form>
		  
		  <?php
} else { ?>	
          <form class="form-inline my-md-0">
            <div class="input-group">
              <button type="button" class="btn btn-dark text-white">Balance: <?php echo $balance; ?> </button>          
            </div>
          </form>
		  <?php
} ?>
                    <!-- Topbar Search -->
          <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
<!--marquee direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" style="color:red;">
<?php echo $webdata['webnote']; ?></marquee-->
			
          </form>

            <div class="topbar-divider d-none d-sm-block"></div>


            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow ml-3">
              <a class="dropdown-toggle" href="#" style="text-decoration: none;" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="d-lg-inline text-gray-800 mr-5 small">My Account</span>
                
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
			   
                <a class="dropdown-item" href="profile.php">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile Edit
                </a>
                <a class="dropdown-item" href="logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>

        </div>
		</li>

        </nav>
      
        <!-- End of Topbar -->

<script>
        function updateIndianTime() {
            const options = {
                timeZone: 'Asia/Kolkata',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
            };
            const indianTime = new Date().toLocaleString('en-US', options);
            document.getElementById('indian-time').textContent = indianTime;
        }

        // Update the time immediately and then every second
        updateIndianTime();
        setInterval(updateIndianTime, 1000);
    </script>
