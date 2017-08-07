<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL|E_STRICT);
    include 'lib/UPCA.php';

	/* /CONNEXION */



/* Identifier le brand */


/* Chercher le last insert ID */


/* Last insert ID + 1 
   is Unique?
   Save!
*/




/*
    $brand = $_POST['brand'];
    $battatNo = '062243'; 
    $code = $battatNo.$brand.'666666';
    $barcode = draw($code);
    $fp = fopen('e:\\htdocs\\upc\\svg\\'.$code.'.svg','w+');
    fwrite($fp,$barcode);
    fclose($fp);

*/






?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>upc</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active"><a href="index.php">Home</a></li>
            <li role="presentation"><a href="search.php">Search a UPC</a></li>
          </ul>
        </nav>
        <h3 class="text-muted">Maison Battat Inc.</h3>
      </div>
      <div class="jumbotron">
        <h1>UPC-o-tron</h1>          
      </div>
      <div style="width:400px; margin: 0 auto 0 auto;">
		<img src="svg/<?php echo $code; ?>.svg">
	  </div>
  </body>
</html>