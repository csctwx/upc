<?php
  require_once('lib/utils.php');  
  require_once('lib/header.php');  
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>upc</title>
<link rel="stylesheet" href="upc.css">
<!-- JQUERY -->
<!-- Librairies -->
<script src="js/jquery-latest.js"></script>
<script src="js/jquery.metadata.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active"><a href="index.php">Home</a></li>
          </ul>
        </nav>
        <h3 class="text-muted">Maison Battat Inc.</h3>
      </div>
     <hr/>
<?php
$formObj = getFormInfo();      
/* Get the next Battat Number available for this category */
$rang = getProductLineById($formObj->brand, $pdo);

/* BUILD BATTAT NUMBER */
$battatNumber = createBattatNumber($rang, $formObj);

// UPDATER le lastUsedNumber pour le brand
$sqlUpdateLastNumber = "UPDATE product_lines SET lastUsedNumber = lastUsedNumber+1 WHERE id = ".$formObj->brand;
$go = $pdo->query($sqlUpdateLastNumber);

echo "<p>New Battat Number :  ".$battatNumber."</p>";
/* ECHO SVG IMAGE */








/* FIND NEXT UPC AVAILABLE */
$upc = getUpc($pdo);

echo "<hr /><p><a href=\"popupcode.php?upc=".$upc->upcCode."\" target=\"_blank\">Download Barcode</a>";
      
echo "<img src=\"svg.php?code=$upc->upcCode\" /></p><hr />";
      

/* INSERT in Products Table */
$dateNow = date("Y-m-d");
$sql = "INSERT INTO products (UPC_full, UPC_Category, UPC_privatelabel, t_UPC_description, UPC_packinfo, BarCodeOrig, BarCodeNo, d_UPC_createdate, d_UPC_moddate, t_UPCProductManager,status) ";
$sql .= "VALUES ";
$sql .= "('".$battatNumber."','".$rang->catPrefix."','".$formObj->privateLabel."','".addslashes($formObj->description)."','".$formObj->packInfo."','".$upc->upcOrig."','".$upc->upcCode."','".$dateNow."','".$dateNow."','".$formObj->projectManager."',1);";
$ok = $pdo->query($sql);
?>
    <div class="alert alert-success" role="alert">Saved! <a href="index.php">Back</a></div>
   </div>
  </body>
</html>
