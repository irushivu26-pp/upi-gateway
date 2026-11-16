<?php include "header.php"; ?>
<?php
if (isset($_POST['delete'])) {
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "CSRF token verification failed!";
        exit(); // Stop processing the request
    }
    
    
    
    $mb = mysqli_real_escape_string($conn,$_POST['mobileno']);
    $del = "DELETE FROM `users` WHERE mobile='$mb'";
    $rpt = mysqli_query($conn, $del);

    if ($rpt) {
        
        
        // Show SweetAlert2 success message
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
    Swal.fire({
        icon: "success",
        title: "User Deleted Successfully!!",
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
       // $error = mysqli_error($conn); 
        
         // Show SweetAlert2 error message
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
    Swal.fire({
        icon: "error",
        title: "User Delete Failure!!",
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
    
  
    }
}






?>
 <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-table"></i> Total User</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered" id="sampleTable">
										<thead>
											<tr>
												<th>#</th>
												<th>User Name</th>
												<th>Email</th>
												<th>Shop Name</th>
												<th>Mobile No</th>
												<th>Address</th>
												<th>Expire Date</th>
												<th>Action</th>
												
											</tr>
										</thead>
										<tbody>
<?php
$token = $userdata['user_token'];


$query = "SELECT `id`, `name`, `mobile`, `role`, `password`, `email`, `company`, `pin`, `pan`, `aadhaar`, `location`, `user_token`, `expiry`, `callback_url` FROM `users` WHERE role='User'";
$query_run = mysqli_query($conn, $query);

if ($query_run) {
    while ($row = mysqli_fetch_assoc($query_run)) {
        
      
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['company'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['mobile'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['expiry'], ENT_QUOTES, 'UTF-8') . "</td>";

     ?>
     
     <td>        <div class="row">
    <div class="col">
        <form action="edituser.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="mobileno" value="<?php echo $row['mobile']; ?>">
            <button class="btn btn-success" name="edituser">Edit</button>
        </form>
    </div>

    
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="mobileno" value="<?php echo $row['mobile']; ?>">
            <button class="btn btn-danger" name="delete" onclick="return confirm('Are you sure Deleted?')">Delete</button>
        </form>
    
</div>

        </td>
     
     
     
     <?php
        echo "</tr>";
    }
} else {
    echo "Error in query: " . mysqli_error($conn); 
}
?>
											
									</tbody>
                </table>
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
    <!-- Page specific javascripts-->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css">
    <!-- Data table plugin-->
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
    
  </body>
</html>
