<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">


  <!-- Custom fonts for this template-->
  <link href="../bootstrap/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  
  <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>

  <link href="../bootstrap/css/sb-admin-2.min.css" rel="stylesheet">
  <link href="../bootstrap/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  </head>
</html>

<?php
 $order_id = $_POST['order_id'];
 $user_token = "922e353dfb6dfe49fe60fd92e0085d6c";


$url = "https://indcollect.in/api/check-order-status";

 $data = array(
    "order_id" => $order_id,
    "user_token" => $user_token,
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/json",
        'method'  => 'POST',
        'content' => json_encode($data),
    ),
);

$context  = stream_context_create($options);
 $response = file_get_contents($url, false, $context);


// {"status":"SUCCESS","message":"Transaction Successfully","txnStatus":"COMPLETED","resultInfo":"Transaction Success","orderId":"938458T1054","amount":500,"date":"2024-02-15 10:41:33","utr":"404619475376","remark":"U9713312211"}

 $data = json_decode($response,true);

$current_url = $_SERVER['REQUEST_URI'];

if (strpos($current_url, "results=payment%20cancel") !== false) {
       
      
  echo  "   <script>
      swal.fire({
  title: 'Payment Cancelled By User',
  icon: 'error',
  button: 'OK',
}).then(function() {
  window.location.href = 'https://web.whatsalerts.in/home';
});
      </script>"; 

 
} else {
    

    
//echo json_encode($array);
 $amount = $data['result']['amount'];
 $email = $data['result']['remark1'];

    
    // Database connection variables
$servername = "localhost";
$username = "whatsale_waapi";
$password = "whatsale_waapi";
$dbname = "whatsale_waapi";

// Establish the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




$currentDate = date('Y-m-d H:i:s');

if ($amount == 300) {
    $month = 1;
    $limit =1;
} elseif ($amount == 500) {
    $month = 1;
    $limit = 3;
} elseif ($amount == 3000) {
    $month = 12;
    $limit = 1;
} elseif ($amount == 5000) {
    $month = 12;
    $limit = 1;    
} else {
    // Set default values if the amount doesn't match any condition
    $month = 0;
    $limit = 0;
}

// Calculate the new expiration date based on the current date and months
$newExpirationDate = date('Y-m-d H:i:s', strtotime("+$month month", strtotime($currentDate)));

// ...

// SQL query to update the table
$sql = "UPDATE users SET  limit_device = '$limit' , active_subscription = 'active', subscription_expired = '$newExpirationDate' WHERE email = '$email'";

// Execute the query
if ($conn->query($sql) === TRUE) {
 //   echo "Table updated successfully.";
    echo "<script>
            Swal.fire({
              icon: 'success',
              title: 'Plan Purchase Successfully.',
            button: 'OK',
}).then(function() {
  window.location.href = 'https://web.whatsalerts.in/home';
});
      </script>"; 
    
    
    
    
    
    
    
} else {
    echo "Error updating table: " . $conn->error;
}

// Close the database connection
$conn->close();
    
}
?>
<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  
  <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>

 
  </head>
</html>