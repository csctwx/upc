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
if (isset($_GET["product"])) {
?>
    <div class="container">
      <div class="clearfix">
        <nav>
          <ul class="nav nav-pills pull-left">
            <!--<li role="presentation" class="active"><a href="duplicate.php?id=<?php echo $_GET["product"]; ?>&action=battatno">Duplicate Battat Number</a></li>-->
            <li role="presentation" class="active"><a href="duplicate.php?id=<?php echo $_GET["product"]; ?>&action=upcno">Duplicate UPC</a></li>
          </ul>
        </nav>
</div>
<p style="margin-top: 25px;">OR</p>

<hr>
<h3 style="margin-top: 25px;">Duplicate Battat #</h3>
<form method="get" action="duplicate.php">
  <input type="hidden" name="action" value="battatno">
 <input type="hidden" name="id" value="<?php echo $_GET["product"]; ?>">
          
  <?php
          $sql = "SELECT UPC_full FROM products WHERE id = '".$_GET["product"]."'";

          $st = $pdo->query($sql);
          $rang = $st->fetchObject();
  ?>

<input type="text" name="battatno" value="<?php echo $rang->UPC_full; ?>">
<input type="submit" value="Proceed">

</form>



      </div>

<?php
}


  if (isset($_GET[action])) {

      $quoi = $_GET["action"];
      $produit = $_GET["id"];

      $sql = "SELECT * FROM products WHERE id = ".$produit;
      $st = $pdo->query($sql);
      $prod = $st->fetchObject();

      $barcode = $prod->BarCodeNo;
      $noBattat = $prod->UPC_full;
      $description = $prod->t_UPC_description; 
      $cat = $prod->UPC_Category;
      $pack = $prod->UPC_packinfo;
      $private = $prod->UPC_privatelabel;
      $create = $prod->d_UPC_createdate;
      $modif = $prod->d_UPC_moddate;
      $pm = $prod->t_UPCProductManager;
      $status = 1;
      $dateNow = date("Y-m-d");

        if ($quoi == "battatno") {
          /* Meme BattatNo / Different UPC */


          /* FIND NEXT UPC AVAILABLE */
          $sql = "SELECT BarCodeNo + 1 as newBC FROM products WHERE NOT EXISTS (SELECT 1 FROM products t2 WHERE t2.BarCodeNo = products.BarCodeNo + 1) AND BarCodeNo < 062243999000 LIMIT 0,1";
          $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
              $upcFinal = str_pad($row["newBC"],12,"0",STR_PAD_LEFT);
            }

             $noBattat = $_GET["battatno"];

        }

        else if ($quoi == "upcno") {
          /* Meme UPC / Different BattatNo */
          $upcFinal = $barcode;

          /*
           * Get the next Battat Number available for this category
           */
          $sql = "SELECT lastUsedNumber,catPrefix,id FROM product_lines WHERE catPrefix = '".$cat."'";

          $st = $pdo->query($sql);
          $rang = $st->fetchObject();

          /* BUILD BATTAT NUMBER */
          $battatNumberPrefix = $rang->catPrefix;
          $battatNumberLastNumber = $rang->lastUsedNumber;
          $battatNumberId = $rang->id;

          $battatNumber = $battatNumberPrefix;
          $battatNumber .= $battatNumberLastNumber;

          if ($pack != 0) {
            $battatNumber .= $pack;
          }

          if ($private != 0) {
            $battatNumber .= $private;
          }

          // UPDATER le lastUsedNumber pour le brand
          $sqlUpdateLastNumber = "UPDATE product_lines SET lastUsedNumber = lastUsedNumber+1 WHERE id = ".$battatNumberId;
          $go = $pdo->query($sqlUpdateLastNumber);

            $noBattat = $battatNumber;
        }


          $sql = "INSERT INTO products (UPC_full,UPC_Category,UPC_privatelabel,t_UPC_description,UPC_packinfo,BarCodeOrig,BarCodeNo,d_UPC_createdate,d_UPC_moddate,t_UPCProductManager,status) VALUES ";
          $sql .= "('".$noBattat."','".$cat."','".$private."','".addslashes($description)."','".$pack."','000000000000','".$upcFinal."','".$dateNow."','".$dateNow."','".$pm."',1);";
          $ok = $pdo->query($sql);
          ?>
          <div class="alert alert-success" role="alert">Saved! <a href="index.php">Back</a></div>
          <?php

  }
 ?>
   </div>
  </body>
</html>