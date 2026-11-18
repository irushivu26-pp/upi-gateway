<?php
require_once __DIR__ . '/../config.php';
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";
include "../partner/config.php";
date_default_timezone_set('Asia/Kolkata');

function RandomNumber($length) {
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= mt_rand(0, 9);
    }
    return $str;
}

function GenRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateUniqueToken() {
    $token = time() . bin2hex(random_bytes(16)) . rand(1, 50);
    return hash('sha256', $token);
}

function send_api_response($is_success, $message, $order_id = '', $payment_url = '', $upi_intent = '') {
    header('Content-Type: application/json');
    if (!$is_success) {
        echo json_encode(array(
            "status" => false,
            "success" => false,
            "message" => $message,
            "msg" => $message,
            "error" => $message
        ));
        exit;
    }

    $clean_upi = !empty($upi_intent) ? $upi_intent : $payment_url;
    echo json_encode(array(
        "status" => true,
        "success" => true,
        "message" => $message,
        "data" => array(
            "order_id" => $order_id,
            "client_txn_id" => $order_id,
            "payment_url" => $payment_url,
            "upi_url" => $clean_upi,
            "upi_intent" => array(
                "bhim_link" => $clean_upi,
                "phonepe_link" => $clean_upi,
                "paytm_link" => $clean_upi,
                "gpay_link" => $clean_upi
            )
        ),
        "result" => array(
            "orderId" => $order_id,
            "payment_url" => $payment_url,
            "upi_intent" => $clean_upi
        )
    ));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: application/json');
    echo json_encode(array("status" => false, "msg" => "Unauthorized Access"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    // Read payload from JSON body or standard Form POST
    $raw_input = file_get_contents('php://input');
    $json_input = json_decode($raw_input, true);
    if (!is_array($json_input)) {
        $json_input = array();
    }

    $customer_mobile = $json_input['customer_mobile'] ?? ($_POST['customer_mobile'] ?? '');
    $user_token = $json_input['key'] ?? ($json_input['user_token'] ?? ($_POST['key'] ?? ($_POST['user_token'] ?? '')));
    $amount = $json_input['amount'] ?? ($_POST['amount'] ?? '');
    $order_id = $json_input['client_txn_id'] ?? ($json_input['order_id'] ?? ($_POST['client_txn_id'] ?? ($_POST['order_id'] ?? '')));
    $redirect_url = $json_input['redirect_url'] ?? ($_POST['redirect_url'] ?? '');
    $remark1 = $json_input['p_info'] ?? ($json_input['remark1'] ?? ($_POST['p_info'] ?? ($_POST['remark1'] ?? '')));
    $remark2 = $json_input['udf1'] ?? ($json_input['remark2'] ?? ($_POST['udf1'] ?? ($_POST['remark2'] ?? '')));

    $byteorderid = rand(11111, 99999) . time();

    if (empty($amount)) {
        send_api_response(false, "Please Enter Amount Data");
    }
    if (empty($order_id)) {
        send_api_response(false, "Please Enter Order_id Data");
    }
    if (empty($user_token)) {
        send_api_response(false, "Please Enter User_token Data");
    }

    $user_token = mysqli_real_escape_string($conn, $user_token);
    $order_id = mysqli_real_escape_string($conn, $order_id);
    $amount = number_format((float)$amount, 2, '.', '');
    $customer_mobile = mysqli_real_escape_string($conn, $customer_mobile);
    $redirect_url = mysqli_real_escape_string($conn, $redirect_url);
    $remark1 = mysqli_real_escape_string($conn, $remark1);
    $remark2 = mysqli_real_escape_string($conn, $remark2);

    // Check if order_id already exists
    $check_order_id_query = "SELECT id, status FROM orders WHERE order_id='$order_id' AND user_token='$user_token'";
    $existing_order_result = getXbyY($check_order_id_query);

    if (!empty($existing_order_result)) {
        send_api_response(false, "Order ID already exists for this user");
    }

    $slq_pbbyt = "SELECT * FROM users where user_token='$user_token'";
    $res_pslq_pbbyt = getXbyY($slq_pbbyt);

    if (empty($res_pslq_pbbyt)) {
        send_api_response(false, "Invalid user token");
    }

    $bydb_unq_user_id = $res_pslq_pbbyt[0]['id'];
    $merchant_name = !empty($res_pslq_pbbyt[0]['company']) ? $res_pslq_pbbyt[0]['company'] : (!empty($res_pslq_pbbyt[0]['name']) ? $res_pslq_pbbyt[0]['name'] : 'Merchant');
    $bydb_order_hdfc_conn = $res_pslq_pbbyt[0]['hdfc_connected'];
    $bydb_order_phonepe_conn = $res_pslq_pbbyt[0]['phonepe_connected'];
    $bydb_order_paytm_conn = $res_pslq_pbbyt[0]['paytm_connected'];
    $bydb_order_bharatpe_conn = $res_pslq_pbbyt[0]['bharatpe_connected'];

    $today = date('Y-m-d');
    $expire_date = $res_pslq_pbbyt[0]['expiry'];
    if (!empty($expire_date) && $expire_date < $today) {
        send_api_response(false, "Your Plan Expired Please Renew");
    }

    // 1. PhonePe Business
    if ($bydb_order_phonepe_conn == "Yes") {
        $link_token = generateUniqueToken();
        $cxrtoday = date("Y-m-d H:i:s");
        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
        setXbyY($sql_insert_link);

        $payment_link = "$murl/payment2/instant-pay/" . $link_token;
        $gateway_txn = uniqid();

        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id)
                VALUES ('$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'PhonePe', '$byteorderid', '$cxrtoday', '$remark1', '$remark2', '$bydb_unq_user_id')";
        setXbyY($sql);

        $phn_upi = $res_pslq_pbbyt[0]['upi_id'] ?? '';
        $native_upi = !empty($phn_upi) ? "upi://pay?pa=$phn_upi&am=$amount&pn=" . urlencode($merchant_name) . "&tn=" . urlencode($remark1 ?: $order_id) . "&tr=$byteorderid" : $payment_link;
        send_order_success($order_id, $payment_link, $native_upi);
    }

    // 2. Paytm Business
    elseif ($bydb_order_paytm_conn == "Yes") {
        $link_token = generateUniqueToken();
        $cxrtoday = date("Y-m-d H:i:s");
        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
        setXbyY($sql_insert_link);

        $payment_link = "$murl/payment3/instant-pay/" . $link_token;
        $gateway_txn = uniqid();
        $bytetxn_ref_id = GenRandomString(10) . time();

        $sql = "INSERT INTO orders (paytm_txn_ref, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id)
                VALUES ('$bytetxn_ref_id', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Paytm', '$byteorderid', '$cxrtoday', '$remark1', '$remark2', '$bydb_unq_user_id')";
        setXbyY($sql);

        $res_ptm = getXbyY("SELECT Upiid FROM paytm_tokens WHERE user_token='$user_token'");
        $paytm_upi = $res_ptm[0]['Upiid'] ?? '';
        $native_upi = !empty($paytm_upi) ? "upi://pay?pa=$paytm_upi&am=$amount&pn=" . urlencode($merchant_name) . "&tn=" . urlencode($remark1 ?: $order_id) . "&tr=$bytetxn_ref_id" : $payment_link;
        send_order_success($order_id, $payment_link, $native_upi);
    }

    // 3. BharatPe QR
    elseif ($bydb_order_bharatpe_conn == "Yes") {
        $link_token = generateUniqueToken();
        $cxrtoday = date("Y-m-d H:i:s");
        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
        setXbyY($sql_insert_link);

        $payment_link = "$murl/payment4/instant-pay/" . $link_token;
        $gateway_txn = uniqid();

        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id)
                VALUES ('$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Bharatpe', '$byteorderid', '$cxrtoday', '$remark1', '$remark2', '$bydb_unq_user_id')";
        setXbyY($sql);

        $res_bp = getXbyY("SELECT Upiid FROM bharatpe_tokens WHERE user_token='$user_token'");
        $bp_upi = $res_bp[0]['Upiid'] ?? '';
        $native_upi = !empty($bp_upi) ? "upi://pay?pa=$bp_upi&am=$amount&pn=" . urlencode($merchant_name) . "&tn=" . urlencode($remark1 ?: $order_id) . "&tr=$byteorderid" : $payment_link;
        send_order_success($order_id, $payment_link, $native_upi);
    }

    // 4. HDFC SmartHub
    elseif ($bydb_order_hdfc_conn == "Yes") {
        $slq_p = "SELECT * FROM hdfc where user_token='$user_token'";
        $res_p = getXbyY($slq_p);
        $db_seassion = $res_p[0]['seassion'] ?? '';
        $db_DEVICE_ID = $res_p[0]['device_id'] ?? '';
        $tidlist = $res_p[0]['tidlist'] ?? '';
        $hdfc_number = $res_p[0]["number"] ?? '';

        $url = "$murl/HDFCSoft/sesion.php?no=$hdfc_number&device=$db_DEVICE_ID";
        $responseSEASION = @file_get_contents($url);
        $json = json_decode($responseSEASION, true);
        $sessionId = $json["sessionId"] ?? '';

        $diss = RandomNumber(18);
        $sql = "UPDATE hdfc SET seassion='$sessionId' WHERE number='$hdfc_number'";
        setXbyY($sql);

        $txn_data = @file_get_contents("$murl/HDFCSoft/payrequest.php?sessionid=$db_seassion&cnumber=$customer_mobile&amount=$amount&no=$customer_mobile&tidList=$tidlist&dis=$diss");
        $exampl = 'sessionid=' . $db_seassion . '&tidList=' . $tidlist;
        $json = json_decode($txn_data, true);
        $mTxnid = $json["mTxnid"] ?? '';
        $upiLink = $json["upiLink"] ?? '';

        if (empty($mTxnid)) {
            send_api_response(false, "HDFC Merchant Not Linked");
        }

        $order_id2 = base64_encode($order_id);
        $gateway_txn1 = rand(1000000000, 9999999999);
        $disst = RandomNumber(8);
        $ne = base64_encode($exampl);
        $rool = md5($disst);
        $currentTimestamp = date('Y-m-d H:i:s');

        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, method, HDFC_TXNID, upiLink, description, create_date, remark1, remark2, user_id)
                VALUES ('$gateway_txn1', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'HDFC', '$mTxnid', '$upiLink', '$diss', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id')";
        setXbyY($sql);

        $payment_link = $murl . "/payment/pay.php?data=" . $order_id2 . "&" . $rool . "&" . $ne;
        send_order_success($order_id, $payment_link, $upiLink);
    } else {
        send_api_response(false, "Merchant Not Linked. Please link PhonePe, Paytm, BharatPe, or HDFC in UPI Settings.");
    }
}
?>
