<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Main CSS-->
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <!-- Font-icon css-->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>UPi Payment Gateway Registration</title>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center mb-6">Register Form</h2>
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">										
          <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="col-md-6 mb-2">
              <label>Mobile Number</label>
              <input type="text" name="mobile" placeholder="Enter Mobile Number" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required />
            </div>
            <div class="col-md-6 mb-2">
              <label>Password</label>
              <input type="text" name="password" placeholder="Enter Password" class="form-control" value="<?php echo rand(000000,999999)?>" required="" />
            </div>
            <div class="col-md-6 mb-2">
              <label>Email Address</label>
              <input type="email" name="email" placeholder="Enter Email Address" class="form-control" required="" />
            </div>
            <div class="col-md-6 mb-2">
              <label>Name</label>
              <input type="text" name="name" placeholder="Enter Name" class="form-control" required="" />
            </div>
            <div class="col-md-6 mb-2">
              <label>Company</label>
              <input type="text" name="company" placeholder="Enter Company" class="form-control" required="" />
            </div>
            <div class="col-md-6 mb-2">
              <label>Area Pin</label>
              <input type="text" name="pin" placeholder="Area Pin" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" required />
            </div>
            <div class="col-md-12 mb-2">
              <label>Location</label>
              <input type="text" name="location" placeholder="Enter Location" class="form-control" required="" />
            </div>
            <div class="col-md-12 mb-2 mt-2">
              <button type="submit" name="create" class="btn btn-primary btn-big">Register</button>
            </div>
          </form>
        <?php
include "config.php";
if (isset($_POST['create'])) {
    
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $checkMobileQuery = "SELECT * FROM `users` WHERE `mobile` = '$mobile'";
    $checkMobileResult = mysqli_query($conn, $checkMobileQuery);

    $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkMobileResult) > 0) {
        echo '
    <script>
        Swal.fire({
            title: "Opps! Sorry Mobile Number Already Exist. Please use a different number",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        })
    </script>
';
exit;
    } elseif (mysqli_num_rows($checkEmailResult) > 0) {
        // The email already exists, display an error message
       echo '
    <script>
        Swal.fire({
            title: "Opps! Sorry Mobile Number Already Exist. Please use a different email",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        })
    </script>
';
exit;
    } else {
        // Proceed with user registration
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $company = mysqli_real_escape_string($conn, $_POST['company']);
        $pin = mysqli_real_escape_string($conn, $_POST['pin']);

 
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $key = md5(rand(00000000, 99999999));
        $pass = password_hash($password, PASSWORD_BCRYPT);
        $today = date("Y-m-d", strtotime("+3 days"));

        $register = "INSERT INTO `users`(`name`, `mobile`, `role`, `password`, `email`, `company`, `pin`, `location`, `user_token`, `expiry`) VALUES ('$name','$mobile','User','$pass','$email','$company','$pin','$location','$key','$today')";
        $result = mysqli_query($conn, $register);

        $msg = "Dear $name thanks For Registering Us
        Your Username = $mobile
        Your Password = $password
        Thanks & Regards
        UPB TEAM";
        $encodedMsg = urlencode($msg);

        if ($result) {
            // Optional notification send (silenced to prevent blocking registration on failure)
            @file_get_contents("https://server.aisensy.in/api/send?number=91$mobile&type=text&message=$encodedMsg&instance_id=6555EB274DF7F&access_token=64de19ca0114c");
            echo '
    <script>
        Swal.fire({
            title: "Registration Successfull!!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "success"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "login.php"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
        } else {
             echo '
    <script>
        Swal.fire({
            title: "Rgistration Failed!!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        })
    </script>
';
exit;
        }
    }
}
?>  
          
          
          
          
        </div>
      </div>
    </div>
  </div>
  <!-- Essential javascripts for application to work-->
  <script src="js/jquery-3.7.0.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
  <!-- Page specific javascripts-->
  <!-- Google analytics script-->
</body>
</html>
