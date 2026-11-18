<?php


// Function to sanitize user input
function sanitizeInput($input) {
    if (is_string($input)) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    } else {
        // Handle non-string input here (e.g., arrays, objects, etc.) if needed.
        return $input;
    }
}


function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


function curlGet($url) {
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url); // Set the URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL certificate verification (for testing)
    
    // Execute the cURL session and store the result in $response
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if(curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    }
    
    // Close the cURL session
    curl_close($ch);
    
    // Return the response
    return $response;
}




function getXbyY($query) {
    
    $con = connect_database();
    $result = $con->query($query) or die($query . " " . mysqli_error($con));

    for ($set = array(); 
    $row = $result->fetch_assoc(); 
    $set[] = $row);

    $con->close();
    return $set;
}

function setXbyY($query) {
    $con = connect_database();
    $result = $con->query($query) or die($query . " " . mysqli_error($con));
    $con->close();
    return $result;
}

###################################################################
//Start of function to get todays date
##################################################################

function todaysDate() {
    $tdate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("y")));
    $tdate = $tdate . " " . date('H:i:s');
    return $tdate;
}

###################################################################
//End of function to get todays date
##################################################################
###################################################################
//Start of function to convert date format to dd-mm-yyyy
##################################################################

function format_date($date) {
    $dd = explode(" ", $date);
    $dd1 = explode("-", $dd[0]);

    $new_date = $dd1[2] . "-" . $dd1[1] . "-" . $dd1[0] . "<br />" . $dd[1];
    return $new_date;
}

###################################################################
//End of function to convert date format to dd-mm-yyyy
##################################################################
###################################################################
//Start of function to convert date format to dd-mm-yyyy
##################################################################

function format_date1($date) {
    $dd = explode(" ", $date);
    $dd1 = explode("-", $dd[0]);

    $new_date = $dd1[2] . "-" . $dd1[1] . "-" . $dd1[0] . " " . $dd[1];
    return $new_date;
}

###################################################################
//End of function to convert date format to dd-mm-yyyy
##################################################################
####################################################################
//Start of function to convert date format to dd-mm-yyyy
##################################################################

function format_date_without_time($date) {
    $dd = explode(" ", $date);
    return $dd[0];
}

###################################################################
//End of function to convert date format to dd-mm-yyyy
##################################################################
###################################################################
//Start of function to convert date format to dd-mm-yyyy
##################################################################

function format_date_without_br($date) {
    $dd = explode(" ", $date);
    $dd1 = explode("-", $dd[0]);

    $new_date = $dd1[2] . "-" . $dd1[1] . "-" . $dd1[0] . " " . $dd[1];
    return $new_date;
}

###################################################################
//End of function to convert date format to dd-mm-yyyy
##################################################################
###################################################################
//Start of function to get todays date
##################################################################

function today_date_limit($tdate1) {
    if ($tdate == "") {
        $tdate1 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("y")));
    }
    $tdate['start_time'] = $tdate1 . " 00:00:00";
    $tdate['end_time'] = $tdate1 . " 23:59:59";
    return $tdate;
}

###################################################################
//End of function to get todays date
##################################################################
#

function getDates($ldate) {
    //$ldate2 = explode(" ", $ldate);
    $ldate1 = explode("/", $ldate);
    $id = $ldate1[1];

    switch ($id) {
        case 2:
            $monthNAME = "Feb";
            return $api_id;
        case 3:
            $monthNAME = "Mar";
            return $api_id;
        case 4:
            $monthNAME = "Apr";
            return $api_id;
        case 5:
            $monthNAME = "May";
            return $api_id;
        case 6:
            $monthNAME = "Jun";
            return $api_id;
        case 7:
            $monthNAME = "Jul";
            return $api_id;
        case 8:
            $monthNAME = "Aug";
            return $api_id;
        case 9:
            $monthNAME = "Sept";
            return $api_id;
        case 10:
            $monthNAME = "Oct";
            return $api_id;
        case 11:
            $monthNAME = "Nov";
            return $api_id;
        case 12:
            $monthNAME = "Dec";
            return $api_id;
        default:
            $monthNAME = "Jan";
            return $api_id;
    }

    // $rdate = $ldate1[2] . "-" . $ldate1[1] . "-" . $ldate1[0] . " " . $ldate2[1];
    $rdate = $ldate1[2] . "-" . $monthNAME . "-" . $ldate1[0];
    return $rdate;
}

###################################################################
//End of getdate function
##################################################################
###################################################################
//Start of function to get todays date
##################################################################

function todaysDate_only() {
    $tdate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("y")));
    return $tdate;
}

###################################################################
//End of function to get todays date
##################################################################
###################################################################
//Start of function to get date only leaving time back
##################################################################

function format_date_only($date) {
    $dd = explode(" ", $date);
    $dd1 = explode("-", $dd[0]);
    $tdate = $dd1[2] . "/" . $dd1[1] . "/" . $dd1[0];
    return $tdate;
}

###################################################################
//End of function to get date only leaving time back
##################################################################
###################################################################
//Start of function to get todays date
##################################################################

function yesterday_date() {
    $tdate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("y")));
    return $tdate;
}

###################################################################
//End of function to get todays date
##################################################################
###################################################################
//Start of function to get start of month
##################################################################

function month_start() {
    $tdate = date("Y") . "-" . date("m") . "-01 00:00:00";
    return $tdate;
}

###################################################################
//End of function to get start of month
##################################################################
#######################################################################################
//To print the values for testing
#######################################################################################

function pt($val) {
    echo "<pre>";
    print_R($val);
    echo "</pre>";
}

#######################################################################################
//End of function To send mail attachments
#######################################################################################
###############################################################
//Start of function to get unique reference number
###############################################################

function reference_number() {
    $rand = rand(0, 9);
    $ref_number = abs(crc32(time()));
    $ref_number = $ref_number . "" . $rand;
    return $ref_number;
}

###############################################################
//End of function to get unique reference number
###############################################################

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {

    $file = $path . "/" . $filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));

    $uid = md5(uniqid(time()));

    $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
    $header .= "Reply-To: " . $replyto . "\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--" . $uid . "\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message . "\r\n\r\n";
    $header .= "--" . $uid . "\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n";
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
    $header .= $content . "\r\n\r\n";
    $header .= "--" . $uid . "--";

    // Messages for testing only, nobody will see them unless this script URL is visited manually
    if (mail($mailto, $subject, "", $header)) {
        //echo "Message sent!";
    } else {
        //echo "ERROR sending message.";
    }
}

###################################################################
//Start of function to for sendign attachments
##################################################################
###################################################################

##################################################################
###############################################################
//function to upload files with thumbnails
###############################################################

function uploadimage($tmpfile, $source, $destination, $thumbnail, $newsize, $watermark) {
//echo "<pre>";print_r($_SERVER);
    $destinationname = $source . "/" . $destination;
    $target_filename = up_load_file($tmpfile, $destinationname);

    if ($thumbnail == 1) {
        $thumbnailfile = $source . "/thumbs/" . $destination;
        list($width, $height, $type, $attr) = getimagesize($destinationname);
        if ($width > $height) {
            $denominator = $width / $newsize;
        } else {
            $denominator = $height / $newsize;
        }

        $newWIDTH = floor(($width / $denominator));
        $newHEIGHT = floor(($height / $denominator));

        image_resize($destinationname, $thumbnailfile, $newWIDTH, $newHEIGHT);
    }
}

###################################################################
//End of upload function
##################################################################
###############################################################
//function to upload files
###############################################################

function up_load_file($source_file, $target_file) {
    $file_ext = substr(basename($target_file), strrpos(basename($target_file), "."));
    $dir_path = dirname($target_file);
    $base_name = basename($source_file);
    if (file_exists($target_file)) {
        $target_file = $dir_path . "/" . substr($base_name, 0, -4) . $file_ext;
    }

    if (!file_exists($target_file)) {
        copy($source_file, $target_file);
    } else {
        return false;
    }
    return basename($target_file);
}

###################################################################
//End of upload function
##################################################################
###################################################################
//Start of function to  create thumbnail
##################################################################

function image_resize($src, $dst, $width, $height) {

    if (!list($w, $h) = getimagesize($src)) {
        return "Unsupported picture type!";
    }

    $type = strtolower(substr(strrchr($src, "."), 1));
    if ($type == 'jpeg') {
        $type = 'jpg';
    }

    switch ($type) {
        case 'bmp':$img = imagecreatefromwbmp($src);
            return $api_id;
        case 'gif':$img = imagecreatefromgif($src);
            return $api_id;
        case 'jpg':$img = imagecreatefromjpeg($src);
            return $api_id;
        case 'png':$img = imagecreatefrompng($src);
            return $api_id;
        default:return "Unsupported picture type!";
    }

    $new = imagecreatetruecolor($width, $height);

    // preserve transparency
    if ($type == "gif" or $type == "png") {
        imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
        imagealphablending($new, false);
        imagesavealpha($new, true);
    }

    imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

    switch ($type) {
        case 'bmp':imagewbmp($new, $dst);
            return $api_id;
        case 'gif':imagegif($new, $dst);
            return $api_id;
        case 'jpg':imagejpeg($new, $dst);
            return $api_id;
        case 'png':imagepng($new, $dst);
            return $api_id;
    }
    return true;
}

###################################################################
//end of function to create thumbnail
##################################################################
##############################################################
//function to upload files with thumbnails
###############################################################

function uploadimagepreview($tmpfile, $source, $destination, $thumbnail, $newsize, $watermark) {
    $destinationname = $source . "/" . $destination;

    if ($thumbnail == 1) {

        $thumbnailfile = $source . "/preview/" . $destination;

        list($width, $height, $type, $attr) = getimagesize($destinationname);
        if ($width > $height) {
            $denominator = $width / $newsize;
        } else {
            $denominator = $height / $newsize;
        }

        $newWIDTH = floor(($width / $denominator));
        $newHEIGHT = floor(($height / $denominator));

        image_resize($destinationname, $thumbnailfile, $newWIDTH, $newHEIGHT);
    }
}

###################################################################
//End of getdate function
##################################################################
#######################################################################################
//Function to check sql injections
#######################################################################################

function parameters_check() {
    //  pt($_POST);
    $test_arr = array();
    $posted_values = $_POST;
    $count_post = count($_POST);
    $con = connect_database();
    foreach ($posted_values as $key => $value) {
        $_POST[$key] = mysqli_real_escape_string($con, $_POST[$key]);

        $test_arr[] = strtolower($value);
    }

    for ($i = 0; $i < count($test_arr); $i++) {
        $i1 = strrpos($test_arr[$i], "Select");
        if (strpos($test_arr[$i], 'select') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }

        if (strpos($test_arr[$i], 'insert') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
        if (strpos($test_arr[$i], 'delete') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
        if (strpos($test_arr[$i], 'update') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
        if (strpos($test_arr[$i], 'show') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
    }

    $get_values = $_GET;
    $count_get = count($_GET);

    foreach ($get_values as $key => $value) {
        $_GET[$key] = mysqli_real_escape_string($con, $_GET[$key]);
        $test_arr[] = strtolower($value);
    }

    for ($i = 0; $i < count($test_arr); $i++) {
        $i1 = strrpos($test_arr[$i], "Select");
        if (strpos($test_arr[$i], 'select') !== false) {

            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }

        if (strpos($test_arr[$i], 'insert') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
        if (strpos($test_arr[$i], 'delete') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
        if (strpos($test_arr[$i], 'update') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
        if (strpos($test_arr[$i], 'show') !== false) {
            $result['error'] = "Unauthorized Access";
            echo json_encode($result);
            die();
        }
    }
}

#######################################################################################
//End of Function to check sql injections
#######################################################################################
#######################################################################################
//Start of function to create user defined password
#######################################################################################

function cpassword($password) {
    return $password;
}

/**
 * Universal Merchant Callback Dispatcher
 * Automatically triggers the merchant's redirect_url / callback_url via POST and GET
 * whenever an order is completed, ensuring instant fulfillment.
 */
function notify_merchant_callback($order_id, $status = 'SUCCESS', $utr = '') {
    if (!defined('DB_HOST')) {
        @require_once __DIR__ . '/../config.php';
    }
    if (!function_exists('connect_database')) {
        @require_once __DIR__ . '/dbInfo.php';
    }
    if (function_exists('connect_database')) {
        $con = connect_database();
    } else {
        $con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    }
    if (!$con || $con->connect_error) {
        return false;
    }
    $con->set_charset('utf8mb4');
    $clean_oid = mysqli_real_escape_string($con, $order_id);
    $sql = "SELECT o.redirect_url, o.gateway_txn, o.amount, o.remark1, o.user_token, u.callback_url 
            FROM orders o 
            LEFT JOIN users u ON o.user_token = u.user_token 
            WHERE o.order_id = '$clean_oid' LIMIT 1";
    $res = $con->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $con->close();
        $target_url = !empty($row['redirect_url']) ? $row['redirect_url'] : (!empty($row['callback_url']) ? $row['callback_url'] : 'https://payprints.in/api/payment/tajapi/callback');
        $gateway_txn = $row['gateway_txn'] ?? '';
        $amount = $row['amount'] ?? '';
        $remark1 = $row['remark1'] ?? '';

        $post_fields = [
            'order_id' => $order_id,
            'status' => $status,
            'utr' => $utr,
            'hash' => $gateway_txn,
            'gateway_txn' => $gateway_txn,
            'amount' => $amount,
            'remark1' => $remark1
        ];

        // 1. Send POST callback with cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);

        // 2. Also send GET callback as backup
        $sep = (strpos($target_url, '?') !== false) ? '&' : '?';
        $get_url = $target_url . $sep . http_build_query($post_fields);
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $get_url);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch2);
        curl_close($ch2);

        return true;
    }
    return false;
}

#######################################################################################
//End of function to create user defined password
#######################################################################################
#######################################################################################
//Start of function to create user defined create_username
#######################################################################################


?>
