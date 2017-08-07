<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL|E_STRICT);
    include 'lib/UPCA.php';
    $code = $_GET['upc']; 
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.$code.'.svg"');    
  //  header("Content-type: image/svg+xml");
    echo draw($code);
 ?>