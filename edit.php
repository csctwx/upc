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

     <hr>
    <?php
    if (isset($_POST["saveEdit"])) {
      $today = date("Y-m-d");
      $description = addslashes($_POST["description"]);
      $pm = $_POST["pm"];
      $produit = $_POST["product"];
      $sql = "UPDATE products SET t_UPC_description = '".$description."', t_UPCProductManager = '".$pm."', d_UPC_moddate = '".$today."' WHERE id = ".$produit;
      $update = $pdo->query($sql);
      echo "<div class=\"alert alert-success\" role=\"alert\">Saved. <a href=\"index.php\">Back</a></div>";
    }

   else if (isset($_GET["product"])) {
    $produit = $_GET["product"];
    ?>
  <form action="edit.php" method="post">
    <input type="hidden" name="saveEdit" value="1">
    <input type="hidden" name="product" value="<?php echo $produit; ?>">
    <div class="form-group">
    <?php
      $sql = "SELECT * FROM products WHERE id = ".$produit;
      $st = $pdo->query($sql);
      $prod = $st->fetchObject();
      echo "<h4># Battat</h4><input type=\"text\" value=\"".$prod->UPC_full."\" id=\"upc_full\" class=\"form-control\" disabled=\"disabled\">";
      echo "<h4>Description</h4><textarea name=\"description\" id=\"description\" class=\"form-control\" rows=\"5\">".$prod->t_UPC_description."</textarea>";

      $barcode = $prod->BarCodeNo;
      $barcode = str_pad($barcode,12,"0",STR_PAD_LEFT);

      echo "<hr><p><a href=\"popupcode.php?upc=".$barcode."\" target=\"_blank\">Download Barcode</a>";
      ?>
<img src="svg.php?code=<?php echo $barcode; ?>"></p>
<hr>
      <?php
      echo "<h4>Project Manager</h4>";

      $pm = $prod->t_UPCProductManager;
      $stmt = $pdo->query("SELECT * FROM product_managers ORDER BY name");

      while ($row = $stmt->fetch()) {

          $pmCode = $row["code"];

          if ($pm == $pmCode) {
            $select = "checked=\"checked\"";
          } else {
            $select = "";
          }

        echo "<input name=\"pm\" id=\"pm\" type=\"radio\" value=\"".$row["code"]."\" ".$select.">".$row["name"]." (".$row["code"].")<br/>";
      }
      ?>
      <input type="hidden" name="upDate" value="<?php echo date("Y-m-d"); ?>">
      <button class="btn btn-primary" id="submit" style="display:block; width:200px; height: 50px; margin: 25px auto 0 auto;font-size: 125%;">Save</button>
    </form>
<?php
}
?>
   </div>
  </body>
</html>