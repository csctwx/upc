<?php
// User IP
$userIp = $_SERVER['SERVER_ADDR'];

if (in_array(substr($userIp, 0, strrpos($userIp, '.')), array('10.10.10', '192.168.0'))) {
  setcookie("BattatUPC", 1, time()+86400);
}
else {
  setcookie("BattatUPC", 0, time()+86400);
}

if ($_COOKIE["BattatUPC"] === 0) { echo "<h2>Maison Battat Inc.</h2>"; exit; }


  /* CONNEXION */
  $host = "127.0.0.1";
  $db = "upc";
  $user = "upc";
  $password = "upc";
  $charset = "utf8";
  $dsn = "mysql:host=".$host.";dbname=".$db.";charset=".$charset;
  $opt = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
  ];
  $pdo = new PDO($dsn, $user, $password, $opt);





function generate_upc_checkdigit($upc_code)
{
    $odd_total  = 0;
    $even_total = 0;
 
    for($i=0; $i<11; $i++)
    {
        if((($i+1)%2) == 0) {
            /* Sum even digits */
            $even_total += $upc_code[$i];
        } else {
            /* Sum odd digits */
            $odd_total += $upc_code[$i];
        }
    }
 
    $sum = (3 * $odd_total) + $even_total;
 
    /* Get the remainder MOD 10*/
    $check_digit = $sum % 10;
 
    /* If the result is not zero, subtract the result from ten. */
    return ($check_digit > 0) ? 10 - $check_digit : $check_digit;
}






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
$pm = $_POST["pm"];
$brand = $_POST["brand"];
$pack = $_POST["pi"];
$private = $_POST["pl"];
$description = $_POST["description"];
/*
 * Get the next Battat Number available for this category
 */
$sql = "SELECT lastUsedNumber,catPrefix,id FROM product_lines WHERE id = ".$brand;

$st = $pdo->query($sql);
$rang = $st->fetchObject();

/* BUILD BATTAT NUMBER */
$battatNumberPrefix = $rang->catPrefix;
$battatNumberLastNumber = $rang->lastUsedNumber;
$battatNumberId = $rang->id;

$battatNumber = $battatNumberPrefix;
$battatNumber .= $battatNumberLastNumber;

if ($pack !== "na") {
  $battatNumber .= $pack;
}


if ($private !== "na") {
  $battatNumber .= trim($private);
}


// UPDATER le lastUsedNumber pour le brand
$sqlUpdateLastNumber = "UPDATE product_lines SET lastUsedNumber = lastUsedNumber+1 WHERE id = ".$battatNumberId;
$go = $pdo->query($sqlUpdateLastNumber);

echo "<p>New Battat Number :  ".$battatNumber."</p>";
/* ECHO SVG IMAGE */








/* FIND NEXT UPC AVAILABLE */
/*
$sql = "SELECT BarCodeNo + 1 as newBC FROM products WHERE NOT EXISTS (SELECT 1 FROM products t2 WHERE t2.BarCodeNo = products.BarCodeNo + 1) AND BarCodeNo < 062243999000 LIMIT 0,1";
$stmt = $pdo->query($sql);
while ($row = $stmt->fetch()) {
  $upcFinal = str_pad($row["newBC"],12,"0",STR_PAD_LEFT);
}
*/

$sql = "SELECT BarCodeOrig + 1 as newBC, id FROM products WHERE NOT EXISTS (SELECT 1 FROM products t2 WHERE t2.BarCodeOrig = products.BarCodeOrig + 1) LIMIT 0,1";

$stmt = $pdo->query($sql);
while ($row = $stmt->fetch()) {
  $upc = $row["newBC"];
  $upc = str_pad($upc,5,"0",STR_PAD_LEFT);
  $upcOrig = $upc;
  $upc = "062243".$upc;
  $upcFinale = generate_upc_checkdigit($upc);
  $upc = $upc.$upcFinale;
  $upcFinal = $upc;
}
















/* INSERT in Products Table */
$dateNow = date("Y-m-d");
$sql = "INSERT INTO products (UPC_full, UPC_Category, UPC_privatelabel, t_UPC_description, UPC_packinfo, BarCodeOrig, BarCodeNo, d_UPC_createdate, d_UPC_moddate, t_UPCProductManager,status) ";
$sql .= "VALUES ";
$sql .= "('".$battatNumber."','".$battatNumberPrefix."','".$private."','".addslashes($description)."','".$pack."','".$upcOrig."','".$upcFinal."','".$dateNow."','".$dateNow."','".$pm."',1);";
$ok = $pdo->query($sql);
?>
    <div class="alert alert-success" role="alert">Saved! <a href="index.php">Back</a></div>
   </div>
  </body>
</html>