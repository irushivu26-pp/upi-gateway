<?php

if($_SERVER["SCRIPT_NAME"]=='/portallogin/index.php'){
if($userdata['usertype']=="mainadmin"){
    
$sCql = $conn->prepare("select count(*) from couponreq WHERE status = ?");
$sCql->execute(['pending']);
$resultT = $sCql->fetchColumn();
if($resultT){
echo '<script>
function CpFunction() {
var x = document.getElementById("CouponReq"); 
x.play(); 
}
</script>';    
}


$sqlp = $conn->prepare("select count(*) from paymentreq WHERE status=? AND type = ? AND web_url=?");
$sqlp->execute(['pending','payment',$_SERVER['SERVER_NAME']]);
$resultp = $sqlp->fetchColumn();
if($resultp){
echo '<script>
function PrFunction() {
var x = document.getElementById("topupreq"); 
x.play(); 
}
</script>';    
}
?>
<script>
  setTimeout(function(){ 
      
     window.location.href = "index.php";
      
  }, 30000);
  
setTimeout(function(){ 
CpFunction();
PrFunction();
      
}, 100);

</script>

<?php
}
}
?>
      
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Bootstrap core JavaScript-->
  <script src="../bootstrap/vendor/jquery/jquery.min.js"></script>
  <script src="../bootstrap/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="../bootstrap/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="../bootstrap/js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="../bootstrap/vendor/chart.js/Chart.min.js"></script>
  <script src="../bootstrap/vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../bootstrap/vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="../bootstrap/js/demo/datatables-demo.js"></script>

  <!-- Page level custom scripts -->
  <script src="../bootstrap/js/demo/chart-area-demo.js"></script>
  <script src="../bootstrap/js/demo/chart-pie-demo.js"></script>

</body>

</html>
