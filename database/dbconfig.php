<?php
$servername = "localhost";
$username = "u136883682_PRINT";
$password = "u136883682_PRIN";
$dbname = "u136883682_PRINT";
$driver = "mysql";
$socket = "https://";


function get_safe($value){
	
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a", "-","+","=");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z", "","","");

        return strip_tags(str_replace($search, $replace, $value));
}

$datetime =date('d-M-Y h:i:s A', time());

?>
