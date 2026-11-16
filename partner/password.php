<?php

include "header.php";

if (isset($_REQUEST['update'])) {
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token verification failed!";
        exit(); // Stop processing the request
    }

    // Assuming $mobile is already defined in header.php
    $sanitizedMobile = mysqli_real_escape_string($conn, $mobile);
    
    // Sanitize input using mysqli_real_escape_string
    $current_password = mysqli_real_escape_string($conn, $_REQUEST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_REQUEST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_REQUEST['confirm_password']);

    // Retrieve the hashed password from the database
    $query = "SELECT `password` FROM `users` WHERE `mobile` = '$sanitizedMobile'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $hashedPasswordFromDB = $row['password'];
        
        // Check if the current password matches the stored hashed password
        if (password_verify($current_password, $hashedPasswordFromDB)) {
            if ($new_password === $confirm_password) {
                // Hash the new password using bcrypt
                $newpass = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update the password in the database
                $passwor = "UPDATE `users` SET `password` = '$newpass' WHERE `mobile` = '$sanitizedMobile'";
                $up = mysqli_query($conn, $passwor);
                
                if ($up) {
                    // Password changed successfully
                    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>';
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Password Changed Successfully",
                            text: "Your password has been updated.",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "dashboard.php";
                            }
                        });
                    </script>';
                    exit;
                } else {
                    // Password update failed, handle the error
                    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>';
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Password Update Failed",
                            text: "Please try again later.",
                            showConfirmButton: true,
                            confirmButtonText: "Try Again",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "password.php";
                            }
                        });
                    </script>';
                    exit;
                }
            } else {
                // New Password and Confirm Password do not match
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>';
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "New Password and Confirm Password Do Not Match",
                        showConfirmButton: true,
                        confirmButtonText: "Try Again",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "password.php";
                        }
                    });
                </script>';
                exit;
            }
        } else {
            // Current Password does not match
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>';
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Current Password Does Not Match",
                    showConfirmButton: true,
                    confirmButtonText: "Try Again",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "password.php";
                    }
                });
            </script>';
            exit;
        }
    } else {
        // Database query error
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>';
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Please try again later.",
                text: "Please try again later.",
                showConfirmButton: true,
                confirmButtonText: "Try Again",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "password.php";
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
          <h1><i class="bi bi-ui-checks"></i> Change Password</h1>
          
        </div>
      </div>
      <div class="row">

        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">Change Password </h3>
            <div class="tile-body">
                <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                     <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="col-md-4 mb-3">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Current Password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="New Password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <button type="submit" name="update" class="btn btn-primary btn-block">Change Password</button>
                </div>
</form>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <!-- Page specific javascripts-->
    <!-- Google analytics script-->
    <script type="text/javascript">
  </body>
</html>
