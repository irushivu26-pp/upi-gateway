<?php include "header.php"; ?>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-table"></i> UPI Settings</h1>
        </div>
      </div>
 <?php
if (isset($_POST['delete'])) {

    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token verification failed!";
        exit(); // Stop processing the request
    }

    $merchant_type = mysqli_real_escape_string($conn, $_POST['merchant_type']);
    $token = $userdata['user_token'];
  $phonepe_mobile = $_POST['phonepe_mobile'];
    // Initialize the delete and update queries
    $del = "";
    $update = "";

    // Construct the delete and update queries based on merchant type
    if ($merchant_type == 'hdfc') {
        $del = "DELETE FROM hdfc WHERE user_token = '$token'";
        $update = "UPDATE users SET hdfc_connected = 'No' WHERE user_token = '$token'";
    } elseif ($merchant_type == 'phonepe') {
        $del = "DELETE FROM phonepe_tokens WHERE phoneNumber = '$phonepe_mobile'";
        $update = "UPDATE users SET phonepe_connected = 'No' WHERE user_token = '$token'";
        
        // Add a query to delete from the store_id table as well
        $del_store_id = "DELETE FROM store_id WHERE user_token = '$token'";
        mysqli_query($conn, $del_store_id);
    } elseif ($merchant_type == 'paytm') {
        $del = "DELETE FROM paytm_tokens WHERE user_token = '$token'";
        $update = "UPDATE users SET paytm_connected = 'No' WHERE user_token = '$token'";
    } elseif ($merchant_type == 'bharatpe') {
        $del = "DELETE FROM bharatpe_tokens WHERE user_token = '$token'";
        $update = "UPDATE users SET bharatpe_connected = 'No' WHERE user_token = '$token'";
    }

    // Execute the delete query
    $res_del = mysqli_query($conn, $del);

    // Execute the update query
    $res_update = mysqli_query($conn, $update);

    if ($res_del && $res_update) {
        // Show SweetAlert2 success message
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
    Swal.fire({
        icon: "success",
        title: "Congratulations! Your Merchant Has been Deleted Successfully!",
        showConfirmButton: true,
        confirmButtonText: "Ok!",
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings";
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
        title: "Merchant Not Deleted! Contact Admin",
        showConfirmButton: true,
        confirmButtonText: "Ok!",
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings";
        }
    });
</script>';
        exit;
    }
}
?>






<?php

if(isset($_POST['addmerchant'])){
    
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token verification failed!";
        exit(); // Stop processing the request
    }
    $bbbytemerchant = mysqli_real_escape_string($conn, $_POST['merchant_name']);
    
    if ($bbbytemerchant=="hdfc"){
    $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
    $data = "INSERT INTO hdfc(id, number, seassion, device_id, user_token, pin, upi_hdfc, UPI, tidlist, status, mobile) VALUES ('','$no','','','" . $userdata['user_token'] . "','','','','', 'Deactive','$mobile')";
    $insert = mysqli_query($conn, $data);
    }
    
    elseif ($bbbytemerchant == "phonepe") {
    $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
    $bbbytetokken = $userdata['user_token'];
    
    // Check if mobile number already exists
    $check_query = "SELECT * FROM phonepe_tokens WHERE user_token = '$bbbytetokken'";
    $result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($result) > 0) {
        // Mobile number already exists in the database
       // echo "Mobile number already found in data.";
    } else {
        // Mobile number doesn't exist, proceed with insertion
        $data = "INSERT INTO phonepe_tokens (user_token, phoneNumber, userId, token, refreshToken, name, device_data)
                 VALUES ('$bbbytetokken', '$no', '', '', '', '', '')";
        $insert = mysqli_query($conn, $data);
        
        if ($insert) {
            echo "Record inserted successfully.";
        } else {
            echo "Error inserting record: " . mysqli_error($conn);
        }
    }
}


    elseif ($bbbytemerchant=="paytm"){
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $data = "INSERT INTO paytm_tokens (user_token, phoneNumber, MID, Upiid)
        VALUES ('$bbbytetokken', '$no', '','')";
        $insert = mysqli_query($conn, $data);


    }
    
    elseif ($bbbytemerchant=="bharatpe"){
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $data = "INSERT INTO bharatpe_tokens (user_token, phoneNumber, token, cookie, merchantId)
        VALUES ('$bbbytetokken', '$no', '', '', '')";
$insert = mysqli_query($conn, $data);

    }

    if($insert){
    // Show SweetAlert2 success message
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
    Swal.fire({
        icon: "success",
        title: "Congratulations! Your Merchant Has been Added Successfully!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings.php"; // Redirect to "upisettings" when the user clicks the confirm button
        }
    });
</script>';
 exit;
        
    
        
        
    }else{
        
        // Show SweetAlert2 error message
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
    Swal.fire({
        icon: "error",
        title: "Internal Server Error!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings.php"; // Redirect to "dashboard" when the user clicks the confirm button
        }
    });
</script>';
exit;

    }
}

?>
<?php
$token = $userdata['user_token'];
$fetchData = "
    SELECT 'hdfc' AS merchant_type, id, number, date, status FROM hdfc WHERE user_token = '$token' 
    UNION ALL 
    SELECT 'phonepe' AS merchant_type, sl AS id, phoneNumber AS number, date, status FROM phonepe_tokens WHERE user_token = '$token'
    UNION ALL
    SELECT 'paytm' AS merchant_type, id, phoneNumber AS number, date, status FROM paytm_tokens WHERE user_token = '$token'
    UNION ALL
    SELECT 'bharatpe' AS merchant_type, id, phoneNumber AS number, date, status FROM bharatpe_tokens WHERE user_token = '$token'";

$ssData = mysqli_query($conn, $fetchData);
//if (!$ssData) {
    //die("Error in query execution: " . mysqli_error($conn));
//}

                    ?>    
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">All Merchants</h3>
            <table class="table">
                  							<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-2">


            
								<div class="row" id="merchant">
								    <div class="col-md-4 mb-2">
    									<label>Merchant Name</label>
    									<select type="number" name="merchant_name" class="form-control" onchange="get_merchant(this.value,'#merchant')" required>
    									    <option value="phonepe">PhonePe For Business</option>
    									        <option value="paytm">Paytm Business</option>
    									         <option value="bharatpe">BharatPe Buisness</option>
    									   
    									</select>
    								</div>
    								<div class="col-md-4 mb-2"> 
        								<label>Cashier Mobile Number</label> 
        								<input type="number" name="c_mobile" placeholder="Enter Mobile Number" class="form-control" onkeypress="if(this.value.length==10) return false;" required=""> 
    								</div>
                                    <div class="col-md-5"> 
        								<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        								<button type="submit" name="addmerchant" class="btn btn-primary btn-block">Add Merchant</button> 
        							</div>
								</div>
								
							</form>	
                            <thead>
                                <tr>
                              
                                    <th>Merchant Type</th>
                                    <th>Username</th>                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($ssData) > 0) {
                                    while ($merchant = mysqli_fetch_array($ssData)) {
                                        $class = ($merchant['status'] == 'Active') ? 'btn btn-success' : 'btn btn-warning';
                                ?>
                                        <tr>
                                          
                                            <td><?php echo !empty($merchant['merchant_type']) ? htmlspecialchars($merchant['merchant_type'], ENT_QUOTES, 'UTF-8') : ''; ?></td>

                                            <td><?php echo !empty($merchant['number']) ? htmlspecialchars($merchant['number'], ENT_QUOTES, 'UTF-8') : ''; ?></td>

                                            <td>
                                                <button class="<?php echo htmlspecialchars($class, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo htmlspecialchars($merchant['status'], ENT_QUOTES, 'UTF-8'); ?>
                                                </button>
                                            </td>
                                            <td>
                            <?php
                            if ($merchant['merchant_type'] == 'hdfc') {
                                // HDFC specific actions
                                ?>
                                <form action="send_hdfcotp.php" method="post">
                                    <input type="hidden" name="hdfc_mobile" value="<?php echo $merchant['number']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="hdfc_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_type" value="hdfc">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'phonepe') {
                                // Phonepe specific actions
                                ?>
                                <form action="send_phonepeotp.php" method="post">
                                    <input type="hidden" name="phonepe_mobile" value="<?php echo $merchant['number']; ?>">
                                     <?php if ($merchant['status'] != 'Active') { ?>
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                    <?php }?>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="phonepe_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="merchant_type" value="phonepe">
                                   <button class="btn btn-danger btn-xs mb-2 mt-1" onclick="return confirm('Are you sure Deleted?')" name="delete">Delete</button>
                                </form>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'paytm') {
                                // Paytm specific actions
                                ?>
                                <form action="send_paytmotp.php" method="post">
                                    <input type="hidden" name="paytm_mobile" value="<?php echo $merchant['number']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="paytm_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="merchant_type" value="paytm">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'bharatpe') {
                                // Bharatpe specific actions
                                ?>
                                <form action="send_bharatpeotp.php" method="post">
                                    <input type="hidden" name="bharatpe_mobile" value="<?php echo $merchant['number']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="bharatpe_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="merchant_type" value="bharatpe">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
          </div>
        </div>

            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    
  </body>
</html>
