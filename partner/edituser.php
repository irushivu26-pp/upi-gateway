<?php 
include "header.php"; 
include "config.php"; 

$mobileno = mysqli_real_escape_string($conn, $_REQUEST['mobileno']);
$qyt = "SELECT * FROM users WHERE mobile='$mobileno'";
$act = mysqli_query($conn, $qyt);
$day = mysqli_fetch_array($act);

if (isset($_REQUEST['update'])) {
    
    // Verify CSRF token
    if (!isset($_REQUEST['csrf_token']) || $_REQUEST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token verification failed!";
        exit(); // Stop processing the request
    }
    
    
    $mobilex = mysqli_real_escape_string($conn, $_REQUEST['mobile']);
    $email = mysqli_real_escape_string($conn, $_REQUEST['email']);
    $password = mysqli_real_escape_string($conn, $_REQUEST['password']);
    $name = mysqli_real_escape_string($conn, $_REQUEST['name']);
    $company = mysqli_real_escape_string($conn, $_REQUEST['company']);
    $pin = mysqli_real_escape_string($conn, $_REQUEST['pin']);
    $location = mysqli_real_escape_string($conn, $_REQUEST['location']);
    $exp = mysqli_real_escape_string($conn, $_REQUEST['date']);
    
    $key = md5(rand(00000000, 99999999));
    $pass = password_hash($password, PASSWORD_BCRYPT);

    $upgc = "UPDATE users SET name='$name', email='$email', company='$company', pin='$pin', pan='$pan', aadhaar='$aadhaar', location='$location', expiry='$exp' WHERE mobile='$mobilex'";
    $resvp = mysqli_query($conn, $upgc);

    $msg = "Dear $name thanks For Registering Us
    Your Username = $mobile
    Your Password = $password
    Thanks & Regards
    Khilaadixpro";
    $encodedMsg = urlencode($msg);

    if($resvp){
    
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
    Swal.fire({
        icon: "success",
        title: "User Update Successfull!!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "total_user.php"; // Redirect to "dashboard" when the user clicks the confirm button
        }
    });
</script>';

    exit;
    
        
    } else {
        // Show SweetAlert2 error message
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
    Swal.fire({
        icon: "error",
        title: "User Update Failed!!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "total_user.php"; // Redirect to "userlist" when the user clicks the confirm button
        }
    });
</script>';
exit;
    }
}
?>

<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-table"></i> Edit User</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">					
							<div class="col-md-12">

<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <div class="col-md-6 mb-2">
        <label>Mobile Number</label>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="number" name="mobile" value="<?php echo htmlspecialchars($day['mobile'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Mobile Number" class="form-control" onkeypress="if(this.value.length==10) return false;" required="" readonly />
    </div>
   
    <div class="col-md-6 mb-2">
        <label>Email Address</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($day['email'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Email Address" class="form-control" required="" />
    </div>
    
    <div class="col-md-6 mb-2">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($day['name'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Name" class="form-control" required="" />
    </div>
    
    <div class="col-md-6 mb-2">
        <label>Company</label>
        <input type="text" name="company" value="<?php echo htmlspecialchars($day['company'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Company" class="form-control" required="" />
    </div>
    
    <div class="col-md-6 mb-2">
        <label>Area Pin</label>
        <input type="text" name="pin" value="<?php echo htmlspecialchars($day['pin'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Area Pin" class="form-control" required="" />
    </div>
    <div class="col-md-3 mb-2">
        <label>Expiry Date</label>
        <input type="date" id="from_date" name="date" value="<?php echo htmlspecialchars($day['date'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="DD-MM-YYYY" class="form-control datepicker">
    </div>
    
    <div class="col-md-12 mb-2">
        <label>Location</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($day['location'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Location" class="form-control" required="" />
    </div>
    
    <div class="col-md-12 mb-2 mt-2">
        <button type="submit" name="update" class="btn btn-primary btn-sm">Update Now</button>
    </div>
</form>



              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</div>
</body>
</html>			
