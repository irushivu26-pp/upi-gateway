<?php
require_once __DIR__ . '/../config.php';

@file_get_contents("$murl/crons/bharatpecron.php");
@file_get_contents("$murl/crons/hdfccron.php");
@file_get_contents("$murl/crons/hdfcupi.php");
echo @file_get_contents("$murl/crons/phonepecron.php");
echo @file_get_contents("$murl/crons/pay_update.php");
?>
