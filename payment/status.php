<?php
include "../partner/config.php";

$number = $_GET['no'];

$sql = "SELECT * FROM `hdfc` WHERE number = '$number' ORDER BY `seassion` ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($rows as $row) {
  $session = $row['seassion'];
   
$txn_data = file_get_contents('https://'.$server.'/HDFCSoft/userdtl.php?no='.$no.'&sessionid='.$session.''); 

$data = json_decode($txn_data, true);
$dynamicNumber = key($data['terminalInfo'][0]);

function todaysDate() {
    $tdate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("y")));
    return $tdate;
}
$date=todaysDate();

 $txn_data = file_get_contents('https://'.$server.'/HDFCSoft/miniStatement.php?&count=10&no='.$no.'&tidList='.$dynamicNumber.'&sessionid='.$session.'&startDate='.$date.'&endDate='.$date.''); 

$json = json_decode($txn_data,true);
//{"status":"Success","message":"Data Available"

if($json['status']=="Success" && $json['message']=="Data Available"){
    echo"HDFC Business Connected";
}else{
     echo"HDFC Business DisConnected";
}



}
    
}
?>