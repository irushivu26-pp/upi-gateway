<?php
include "config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is already logged in
if (isset($_SESSION['username']) && isset($_SESSION['csrf_token'])) {
    header("Location: dashboard.php");
    exit;
}

$alert_script = '';

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';

    $query = "SELECT * FROM users WHERE mobile = '$username'";
    $run = mysqli_query($conn, $query);

    if ($run && mysqli_num_rows($run) > 0) {
        $row = mysqli_fetch_array($run);
        $hashFromDatabase = $row['password'];
        $acc_lock = (int)($row['acc_lock'] ?? 0);
        $acc_ban = $row['acc_ban'] ?? 'off';
        $byteuserid = $row['id'];

        if ($acc_ban == 'on') {
            $alert_script = '
            Swal.fire({
                title: "Account Locked!",
                text: "Please contact the administrator.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "login.php";
                }
            });';
        } else if (password_verify($password, $hashFromDatabase)) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $byteuserid;
            $csrf_token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrf_token;

            // Reset the acc_lock value on successful login
            $query = "UPDATE users SET acc_lock = 0 WHERE mobile = '$username'";
            mysqli_query($conn, $query);

            $alert_script = '
            Swal.fire({
                title: "Login Successfully",
                text: "Redirecting To The Dashboard",
                icon: "success",
                confirmButtonText: "Ok"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "dashboard.php";
                }
            });';
        } else {
            // Wrong password, increment acc_lock value
            $acc_lock++;
            $query = "UPDATE users SET acc_lock = $acc_lock WHERE mobile = '$username'";
            mysqli_query($conn, $query);

            if ($acc_lock >= 3) {
                $alert_script = '
                Swal.fire({
                    title: "Account Locked!",
                    text: "Too many failed login attempts. Please contact the administrator.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "login.php";
                    }
                });';
            } else {
                $alert_script = '
                Swal.fire({
                    title: "Invalid Password!",
                    text: "Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "login.php";
                    }
                });';
            }
        }
    } else {
        $alert_script = '
        Swal.fire({
            title: "Username Does not Exist!",
            text: "Please try again.",
            icon: "error",
            confirmButtonText: "Ok"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "login.php";
            }
        });';
    }
} else if (isset($_POST['forgot'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $pass = rand(100000, 999999);
    $password = password_hash((string)$pass, PASSWORD_BCRYPT);

    $fetch = "SELECT * FROM users WHERE mobile='$username'";
    $res = mysqli_query($conn, $fetch);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_array($res);
        if ($username == $row['mobile']) {
            $update = "UPDATE users SET password='$password' WHERE mobile='$username'";
            $quer = mysqli_query($conn, $update);

            if ($quer) {
                $msg = "Dear Partner," . $row['name'] . " Your New Password Below\nYour Password = *" . $pass . "*\nThanks & Regards\n*UPB GROUP*";
                $encodedMsg = urlencode($msg);
                $email = $row['email'];  
                $subject = "Password Forgot Successfully";
                $curl = curl_init();

                $from = "UPB GROUP <help@aisensy.in>";
                $data = array(
                    'to' => array($email),
                    'from' => $from,
                    'subject' => $subject,
                    'content' => array(
                        array(
                            'type' => 'text/html',
                            'value' => $msg
                        )
                    )
                );

                $data_string = json_encode($data);

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.mailazy.com/v1/mail/send",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 5,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data_string,
                    CURLOPT_HTTPHEADER => array(
                        "X-Api-Key: cc5cf7qouhjuaf8tSBPkLdj",
                        "X-Api-Secret: WwmkTKBcoRLYkGyjRDHpBixlMKE.ucyw7ecJz9XvnV6bBHa",
                        "Content-Type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                @file_get_contents("https://wa.aisensy.in/send-message?api_key=LItZugFj2OVmhYxjGvxMQdbJ6&sender=8210142545&number=91$username&message=$encodedMsg");

                $alert_script = '
                Swal.fire({
                    title: "Congratulations! Your New Password Sent to Your WhatsApp!!",
                    confirmButtonText: "Ok",
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "login.php";
                    }
                });';
            } else {
                $alert_script = '
                Swal.fire({
                    title: "Opps! Something went wrong Please try again Later!!",
                    text: "Please Click Ok Button!!",
                    confirmButtonText: "Ok",
                    icon: "error"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "login.php";
                    }
                });';
            }
        } else {
            $alert_script = '
            Swal.fire({
                title: "Provided Pan Does Not Match Or Exist!!",
                text: "Please Click Ok Button!!",
                confirmButtonText: "Ok",
                icon: "error"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "login.php";
                }
            });';
        }
    } else {
        $alert_script = '
        Swal.fire({
            title: "Opps! Sorry Your Mobile Number Does Not Exist In Our Record!!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "login.php";
            }
        });';
    }
}
?>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>UPI Payment Gateway Login - UPB GROUP</title>
  </head>
  <body>
    <section class="material-half-bg">
      <div class="cover"></div>
    </section>
    <section class="login-content">
        <img src="/partner/l.png" style="width: 210px;" alt="Logo">
        <br>
      <div class="login-box">
        <form class="login-form" method="POST" action="login.php">
          <h3 class="login-head"><i class="bi bi-person me-2"></i>SIGN IN</h3>
          <div class="mb-3">
            <label class="form-label">Mobile Number</label>
            <input class="form-control" id="username" name="username" type="text" placeholder="Enter Mobile Number" autofocus required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" placeholder="Password" required>
          </div>
          <div class="mb-3">
            <div class="utility">
              <div class="form-check">
                <label class="form-check-label">
                  <p class="semibold-text mb-2"><a href="register.php">Create Account</a></p>
                </label>
              </div>
              <p class="semibold-text mb-2"><a href="#" data-toggle="flip">Forgot Password ?</a></p>
            </div>
          </div>
          <div class="mb-3 btn-container d-grid">
            <button class="btn btn-primary btn-block" name="submit"><i class="bi bi-box-arrow-in-right me-2 fs-5"></i>SIGN IN</button>
          </div>
        </form>

        <form class="forget-form" method="post" action="login.php">
          <h3 class="login-head"><i class="bi bi-person-lock me-2"></i>Forgot Password ?</h3>
          <div class="mb-3">
            <label class="form-label">Mobile Number</label>
            <input class="form-control" type="text" name="username" id="forgot_username" placeholder="Enter Mobile Number" required>
          </div>
          <div class="mb-3 btn-container d-grid">
            <button class="btn btn-primary btn-block" name="forgot"><i class="bi bi-unlock me-2 fs-5"></i>Submit</button>
          </div>
          <div class="mb-3 mt-3">
            <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="bi bi-chevron-left me-1"></i> Back to Login</a></p>
          </div>
        </form>
      </div>
    </section>

    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script type="text/javascript">
      // Login Page Flipbox control
      $('.login-content [data-toggle="flip"]').click(function() {
        $('.login-box').toggleClass('flipped');
        return false;
      });
    </script>
    <?php if (!empty($alert_script)): ?>
    <script>
      <?php echo $alert_script; ?>
    </script>
    <?php endif; ?>
  </body>
</html>
