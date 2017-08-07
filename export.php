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

/* Verify if logged in */
$cookie = $_COOKIE["BattatAdmin"];
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
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/jquery.metadata.js"></script>
    <script src="js/addons/pager/jquery.tablesorter.pager.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="js/addons/pager/jquery.tablesorter.pager.css">
    <link rel="stylesheet" href="js/themes/blue/style.css">
    <link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet">
    </head>
  <body>
    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active"><a href="index.php" id="retour">Home</a></li>
            <?php
            if ($cookie == 1) {
            ?>
            <li role="presentation"><a href="export.php" id="ajouterForm">Export</a></li>
            <li role="presentation"><a href="logout.php">Logout</a></li>
            <?php } else { ?>
                <li role="presentation"><a href="admin.php">Admin</a></li>
            <?php } ?>
          </ul>
        </nav>
        <h3 class="text-muted">Maison Battat Inc.</h3>
      </div>
      <hr>
<?php

if (isset($_POST["brand"])) {

    $fp = fopen('data/export.csv','w+');
    fputcsv($fp, array('BattatNo','Description','BarCodeNo','CreateDate','UpdateDate','ProjectManager'));
    $brand = $_POST["brand"];
    $sql = "SELECT * FROM products WHERE UPC_Category = '".$brand."' AND status = 1";

      $stmt = $pdo->query($sql);
      while ($row = $stmt->fetch()) {
        $upc = $row["UPC_full"];
        $des = addslashes($row["t_UPC_description"]);
        $bar = $row["BarCodeNo"];
        $cre = $row["d_UPC_createdate"];
        $upd = $row["d_UPC_moddate"];
        $prm = $row["t_UPCProductManager"];
        fputcsv($fp, array($upc,$des,$bar,$cre,$upd,$prm));
      }

      echo "<div class=\"alert alert-success\" role=\"alert\">Your file is saved. <a href=\"data/export.csv\">Download it</a>.</div>";

} else {
?>
<h2 style="margin: 50px 0 0 0;">Export</h2>
<h3>By brands</h3>
<form action="export.php" method="post">
<div class="row">
  <div class="col-lg-2">
    <div class="input-group input-group-lg">
      <select name="brand" class="form-control input-lg">
        <option value="AB">All Aboard</option>
        <option value="BX">B. Brand</option>
        <option value="BT">Battat</option>
        <option value="BG">Baby Sweetheart</option>
        <option value="BB">Bristle Blocks</option>
        <option value="CF">Craftabelle</option>
        <option value="WH">Driven</option>
        <option value="EN">Enchanted</option>
        <option value="GG">Glitter Girls</option>
        <option value="LO">Lori</option>
        <option value="BD">OG</option>
        <option value="OG">OG - Me & You</option>
        <option value="ST">Pucci</option>
        <option value="AN">Terra</option>
        <option value="VO">Volta</option>
        <option value="AN">Terra</option>
      </select>
    </div>
  </div>
  <div class="col-lg-2">
    <div class="input-group input-group-lg">
        <button type="submit" class="btn-lg btn-primary">Download</button>
    </div>
  </div>
</div>
</form>
<?php } ?>
</div>
</body>
</html>