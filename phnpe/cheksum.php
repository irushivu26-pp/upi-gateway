<?php
/**
 * Local PhonePe SDK Checksum Generator
 */

function calculate_phonepe_checksum_internal($data) {
    $keys = array_merge(range('9', '0'), range('a', 'f'));
    $advikey = '';
    for ($i = 0; $i < 8; $i++) { $advikey .= $keys[array_rand($keys)]; }
    $advikey .= '-';
    for ($i = 0; $i < 4; $i++) { $advikey .= $keys[array_rand($keys)]; }
    $advikey .= '-';
    for ($i = 0; $i < 4; $i++) { $advikey .= $keys[array_rand($keys)]; }
    $advikey .= '-';
    for ($i = 0; $i < 4; $i++) { $advikey .= $keys[array_rand($keys)]; }
    $advikey .= '-';
    for ($i = 0; $i < 12; $i++) { $advikey .= $keys[array_rand($keys)]; }

    $ket = "1lgVNAAtWyq06UfYjM/UBnJ5ZSA=";
    $str = "";
    for ($i = 0; $i < 16; $i++) {
        $str .= substr($ket, $i, 1) . substr($advikey, $i, 1);
    }

    $aeskey = hash('sha1', $str, true);
    $datahash = base64_encode(hash('sha256', $data, true));
    date_default_timezone_set("Asia/Calcutta");
    $milliseconds = floor(microtime(true) * 1000);
    $payload = "$milliseconds###$datahash";

    $encryptedd = openssl_encrypt($payload, "AES-128-ECB", $aeskey, OPENSSL_RAW_DATA);
    $encc = base64_encode($encryptedd);

    $aa1 = substr($encc, 0, 4);
    $a1 = substr($encc, 4, 4);
    $a2 = substr($encc, 8, 4);
    $a3 = substr($encc, 12);

    $aa = substr($advikey, 0, 4);
    $ab = substr($advikey, 4, 4);
    $ac = substr($advikey, 8, 4);
    $ad = substr($advikey, 12, 4);

    return base64_encode("$aa$aa1$ab$a1$ac$a2$ad$a3");
}

function checksum($data) {
    return calculate_phonepe_checksum_internal($data);
}

function checksum_2($data) {
    return calculate_phonepe_checksum_internal($data);
}
?>
