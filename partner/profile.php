<?php
include "header.php";


?>
<<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-speedometer"></i> Profile</h1>
        </div>
      </div>
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">								<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="col-md-4 mb-3">
        <label>Username</label>
        <input type="text" placeholder="Username" value="<?php echo htmlspecialchars($userdata['mobile'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
    </div>
    <div class="col-md-4 mb-3">
        <label>Mobile Number</label>
        <input type="text" name="mobile" placeholder="Mobile Number" value="<?php echo htmlspecialchars($userdata['mobile'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control input-solid" required readonly>
    </div>
    <div class="col-md-4 mb-3">
        <label>Email Address</label>
        <input type="text" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($userdata['email'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control input-solid" required readonly>
    </div>
    <div class="col-md-4 mb-3">
        <label>Name</label>
        <input type="text" placeholder="Name" value="<?php echo htmlspecialchars($userdata['name'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
    </div>
    <div class="col-md-4 mb-3">
        <label>Company Name</label>
        <input type="text" placeholder="Company Name" value="<?php echo htmlspecialchars($userdata['company'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
    </div>
    <div class="col-md-4 mb-3">
        <label>PAN Number</label>
        <input type="text" placeholder="PAN Number" value="<?php echo htmlspecialchars($userdata['pan'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
    </div>
    <div class="col-md-4 mb-3">
        <label>Aadhaar Number</label>
        <input type="text" placeholder="Aadhaar Number" value="<?php echo htmlspecialchars($userdata['aadhaar'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
    </div>
    <div class="col-md-8 mb-3">
        <label>Location</label>
        <input type="text" placeholder="Location" value="<?php echo htmlspecialchars($userdata['location'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
    </div>
</form>
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
    <!-- Page specific javascripts-->
    <!-- Google analytics script-->

  </body>
</html>
