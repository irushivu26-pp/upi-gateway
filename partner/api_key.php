<?php
include "header.php";
?>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-table"></i> APi Key</h1>
        </div>
      </div>
     <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">️APi Key</h3>
              <table class="table table-bordered">
				<thead>
				<tr>
				<th>APi Key </th>
				<th>Data</th>
				</tr>
			   </thead>
			   <tbody>
<?php
if($userdata['role'] == 'User'){
$token = $userdata['user_token'];
    
echo "<tr>";
echo "<td>" . htmlspecialchars("APi Key", ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . "</td>";
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</main>
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>
