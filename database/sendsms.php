<?php 
$api_url = "https://wa.aisensy.in/send-message";
$api_key = "LItZugFj2cuT9aOVmhYxjGvxMQdbJ6";


    $params = array(
        "api_key" => $api_key,
        "sender" => '8102971884',
        "number" => '91'.$sms_number,
        "message" => $msg
    );
  $url = $api_url . '?' . http_build_query($params);
  "Sending request to URL: $url<br>";
$response = file_get_contents($url);

$qy = $conn->prepare("select * from loginusers WHERE mobile_no = ?");

$qy->execute([$sms_number]);

$results =$qy->fetch();

$qy = $conn->prepare("select * from loginusers WHERE mobile_no = ?");

$qy->execute([$sms_number]);

$results =$qy->fetch();

$SetFrom = "no-reply@".$_SERVER['SERVER_NAME'];

$emai= $results['email_id'];
$email = strtolower($emai);



$curl = curl_init();


$from = "UPB GROUP <help@aisensy.in>";
$data = array(
    'to' => array($email),
    'from' => $from,
    'subject' => $subject,
    'content' => array(
        array(
            'type' => 'text/plain',
            'value' => $msg
        )
    )
);

$data_string = json_encode($data);

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.mailazy.com/v1/mail/send",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $data_string,
    CURLOPT_HTTPHEADER => array(
        "X-Api-Key: cc5cf7qouhjuaf8t2degaSwSBPkLdj",
        "X-Api-Secret: afPQOXCdYGaVUJhCIwhvSOxryDs.z4keVgSpzYWRvfWcZftsWM",
        "Content-Type: application/json"
    ),
));

$response = curl_exec($curl);

curl_close($curl);
$response;
?>

