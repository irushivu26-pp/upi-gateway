<!DOCTYPE html>

<html>
    <title>UPB GROUP - UPI Paymemt Gateway</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<head>
    <!-- Include the SweetAlert CDN link -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
include "config.php";
session_start();
session_destroy();
session_unset();

 echo '
    <script>
        Swal.fire({
            title: "Logout Successfull!!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "success" // Use "error" for the failure icon
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "login.php"; // Replace with your desired redirect URL
            }
        });
    </script>
';

?>
</body>
</html>
