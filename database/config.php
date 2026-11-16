<?php
//header("Access-Control-Allow-Origin: *");
error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set("Asia/Kolkata"); //India time (GMT+5:30)
require_once ('dbconfig.php');
try {
    $conn = new PDO("$driver:host=$servername;dbname=" . $dbname, $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
    $wsql = $conn->prepare("select * from settings WHERE weburl = ?");
    $wsql->execute([$_SERVER['SERVER_NAME']]);
    $webdata = $wsql->fetch();
    
    $stmt = $conn->prepare("SELECT * FROM charge");
    $stmt->execute();
    $charge = $stmt->fetch();
    
    
    $sms_obj = json_decode($webdata['smsapi']);
    $msql = $conn->prepare("select * from settings WHERE id = ?");
    $msql->execute(['1']);
    $mw_data = $msql->fetch();
    $pg_obj = json_decode($mw_data['pgapi']);
    $site_short = rtrim(preg_replace('#([^ ])[^ ]* *#i', '$1', $webdata['webname']), " ");
    $order_id = rand(10000000, 999999999);
    $upbpan = "URT" . rand(100000000, 9999999999);
    $pin_id = rand(10000, 999999);
    $pay_id = "PY" . rand(1000, 9999);
    $date_time = date('Y-m-d', time());
    $date = date('m/d/Y', time());
    $mtime= date('d/m/Y h:i:s A');
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
if (substr($_SERVER['SERVER_NAME'], 0, 4) === 'www.') {
    header('Location: ' . $socket . '' . substr($_SERVER['SERVER_NAME'], 4));
    exit();
}
?>