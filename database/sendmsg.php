<?php 

$api_url = "https://wa.aisensy.in/send-message";
$api_key = "LItZugFj2cuT9aOVmhYxjGvxMQdbJ6";

    
    $url ="https://wa.aisensy.in/send-message?api_key={$api_key}&sender=8102971884&number=91{$sms_number}&message=$msg";

    
    
$response = file_get_contents($url);


?>