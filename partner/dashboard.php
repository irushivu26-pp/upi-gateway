<?php
include "header.php";
$to = $userdata['user_token'];
date_default_timezone_set('Asia/Kolkata');
$startDate = date("Y-m-d");
$lastday = date("Y-m-d", strtotime("-1 day"));


///Today Collaction Fetch Amount
$sql = "SELECT SUM(amount) AS total_amount FROM orders WHERE status = 'success' AND user_token = '$to' AND create_date BETWEEN '$startDate 00:00:00' AND '$startDate 23:59:59'";

$result = $conn->query($sql);
$row = $result->fetch_assoc();
    
    $totalAmount = $row['total_amount'];
//Last Day Collaction Fetch Amount
$sl = "SELECT SUM(amount) AS total_amount FROM orders WHERE status = 'success' AND user_token = '$to' AND create_date BETWEEN '$lastday 00:00:00' AND '$lastday 23:59:59'";

$result = $conn->query($sl);
$row = $result->fetch_assoc();
$total_last = $row['total_amount'];
?>
<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-speedometer"></i> Dashboard</h1>
            <p>Accept payments online hassle-free with our QR code service.</p>
        </div>
      </div>
    <?php 	if($userdata['role'] == 'User'){?>
      <div class="row">
        <div class="col-md-6 col-lg-3">
          <div class="widget-small primary coloured-icon"><i class="icon bi bi-wallet fs-1"></i>
            <div class="info">
               <h4>Today Collection</h4>
              <p><b>₹<?php echo $totalAmount?></b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small info coloured-icon"><i class="icon bi bi-wallet fs-1"></i>
            <div class="info">
            <h4>Last Day Collection</h4>
              <p><b>₹<?php echo $total_last?></b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small warning coloured-icon"><i class="icon bi bi-calendar-date fs-1"></i>
            <div class="info">
              <h4>Plan Expire Date</h4>
              <p><?php echo htmlspecialchars($userdata['expiry'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small primary coloured-icon"><i class="icon bi bi-activity fs-1"></i>
            <div class="info">
             <h4>Status</h4>
              <p><b  id="status"></b></p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
      <?php } ?>
      <?php
      //admin dashboard start here
			if($userdata['role'] == 'Admin'){
			    ?>
			    
			    <div class="row">
        <div class="col-md-6 col-lg-3">
          <div class="widget-small primary coloured-icon"><i class="icon bi bi-people fs-1"></i>
            <div class="info">
               <h4>Total Users</h4>
              <p><b><?php
    $sql = "SELECT COUNT(*) AS total_users FROM users";
    
    // Execute the SQL query
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo $row['total_users'];
    }
              
              ?></b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small info coloured-icon"><i class="icon bi bi-heart fs-1"></i>
            <div class="info">
            <h4>Gateway Health</h4>
              <p><b>GOOD</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small warning coloured-icon"><i class="icon bi bi-folder2 fs-1"></i>
            <div class="info">
              <h4>Plan Expire Date</h4>
              <p><?php echo htmlspecialchars($userdata['expiry'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small danger coloured-icon"><i class="icon bi bi-star fs-1"></i>
            <div class="info">
             <h4>Status</h4>
              <p><b  id="status"></b></p>
            </div>
          </div>
        </div>
      </div>
     <div class="row">
      <?php }?>
      
        <div class="col-md-6">
        <div class="tile">
        <div class="col-lg-6">
        <p><b>UPB Gateway provides Dynamic QR Generating service. UPB Gateway does not offer payment gateway service, nor does it provide UPI ID and UPI Merchant account.</b></p>
      <a type="submit" target="_blank" class="btn btn-danger btn-sm" href="https://wa.me/919506912898">Chat With Support Team</a>
            </div>
          </div>
        </div>
      <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Quick Link:-</h3>
              <div>
                <a type="submit" class="btn btn-success" href="transactions.php">Payment Reports</a>
                <a type="submit" class="btn btn-dark" href="payment_link.php">Create Link</a>
                <a type="submit" class="btn btn-warning" href="upisettings.php">Add Merchant</a>
              </div>
                   <p>Make payment acceptance a breeze by integrating dynamic QR codes into your website, e-commerce platform, and mobile apps.</p>
                  
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
 <script>
// Function to update status dynamically
function updateStatus() {
    // Construct the URL with the dynamic parameter
    const url = '/phnpe/status.php?no=<?php echo urlencode($to) ?>';

    // Make a fetch request to get the status
    fetch(url)
    .then(response => {
        // Check if response is successful
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        // Parse response as JSON
        return response.json();
    })
    .then(data => {
        // Update status based on response
        document.getElementById('status').innerText = data.message;
    })
    .catch(error => {
        console.error('Error:', error);
        // Handle error if needed
        document.getElementById('status').innerText = 'Error: Unable to fetch status';
    });
}

// Call updateStatus function when the page is reloaded
window.addEventListener('load', updateStatus);
</script>
<style>
    /* CSS to make the text bold and color it red */
    #status {
        font-weight: bold;
        color: green;
    }
</style>
  <script>
    fetch('https://<?php echo $server ?>/crons/cron.php')
        .then(response => {
            // Handle response if needed
            console.log('Request sent successfully.');
        })
        .catch(error => {
            console.error('Error sending request:', error);
        });
</script>
  </body>
</html>
