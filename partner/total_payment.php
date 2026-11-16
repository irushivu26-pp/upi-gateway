<?php
include "header.php";
?>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-table"></i> Payments Report</h1>
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
												<th>Mobile Number</th>
												<th>Date Time</th>
												<th>Bank RRN</th>
												<th>Order ID</th>
												<th>Amount</th>
												<th>Status</th>
											</tr>
										</thead>
                <tbody>
                 <?php
if($userdata['role'] == 'Admin'){
    $token = $userdata['user_token'];
    $query = "SELECT `id`, `create_date`, `gateway_txn`, `customer_mobile`, `method`, `utr`, `byteTransactionId`, `order_id`, `amount`, `status` FROM `orders` ORDER BY create_date DESC";
    $query_run = mysqli_query($conn, $query);
    if ($query_run) {
        while ($row = mysqli_fetch_assoc($query_run)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['customer_mobile'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['create_date'], ENT_QUOTES, 'UTF-8') . "</td>";
            ?>
            <?php
            echo "<td>" . htmlspecialchars($row['utr'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>₹" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
            if($row['status']=="SUCCESS"){
                 $sts = 'Success';
                $cls = "btn btn-success";
            }elseif($row['status']=="PENDING"){
                $sts = 'Pending';
                $cls = "btn btn-primary";
            }elseif($row['status']=="FAILED"){
                $sts = 'FAILED';
                $cls = "btn btn-secondary";
            }
            echo "<td><button class='$cls'>" . $sts . "</button></td>";
            echo "</tr>";
        }
    } else {
        echo "Error in query: ";
        //echo "Error in query: " . mysqli_error($conn); 
    }
} else {
    $query = "SELECT `id`, `transactionId`, `date`, `mobile`, `UTR`, `user_name`, `order_id`, `amount`, `status` FROM `reports` ";
    $query_run = mysqli_query($conn, $query);
    if ($query_run) {
        while ($row = mysqli_fetch_assoc($query_run)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['transactionId'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . "</td>";
            ?>
            <td class="badge badge-dark" style="width:70px;">UPI</td>
            <?php
            echo "<td>" . htmlspecialchars($row['transactionId'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>₹" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8') . "</td>";
            if($row['status']=="SUCCESS"){
                $sts = 'Success';
                $cls = "badge badge-success";
            }else{
                $sts = 'Pending';
                $cls = "badge badge-warning";
            }
            echo "<td><button class='$cls'>" . $sts . "</button></td>";
            echo "</tr>";
        }
    } else {
        echo "Error in query: ";
        //echo "Error in query: " . mysqli_error($conn); 
    }
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
