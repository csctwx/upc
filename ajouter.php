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
<script src="js/jquery.tablesorter.min.js"></script>
<script src="js/jquery.metadata.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link rel="stylesheet" href="js/themes/blue/style.css">
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
      $projectManager = $_POST["pm"];
      $brand = $_POST["brand"];
      $packInfo = $_POST["pi"];
      $privateLabel = $_POST["pl"];
      $description = $_POST["description"];
    ?>
    <dl>
      <dt>Project Manager</dt>
    <?php
      $param = $projectManager;
      $sql = "SELECT name FROM product_managers WHERE code LIKE '%".$param."%'";
      $st = $pdo->query($sql);
      $rang = $st->fetchObject();
    ?>
        <dd class="b"><?php echo $rang->name; ?></dd>
      <dt>Brand</dt>
    <?php
      $param = $brand;
      $sql = "SELECT catName FROM product_lines WHERE id = ".$param;
      $st = $pdo->query($sql);
      $rang = $st->fetchObject();
    ?>
        <dd class="b"><?php echo $rang->catName; ?></dd>

      <dt>Pack Info</dt>
    <?php
      $param = $packInfo;
      $sql = "SELECT description FROM pack_info WHERE code LIKE '%".$param."%'";
      $st = $pdo->query($sql);
      $rang = $st->fetchObject();
    ?>
      <dd class="b"><?php echo $packInfo; ?> (<?php echo $rang->description; ?>)</dd>
      
      <dt>Private Label</dt>
        <dd class="b"><?php echo $privateLabel; ?></dd>
      
      <dt>Description</dt>
        <dd class="b"><?php echo $description; ?></dd>
    
    </dl>

<table>
  <tr>
    <td>
      <button onclick="history.back();return false">Back</button>
    </td>
    <td>
  <form action="save.php" method="post">
    <input type="hidden" name="pm" value="<?php echo $projectManager; ?>">
    <input type="hidden" name="brand" value="<?php echo $brand; ?>">
    <input type="hidden" name="pi" value="<?php echo $packInfo; ?>">
    <input type="hidden" name="pl" value="<?php echo $privateLabel; ?>">
    <input type="hidden" name="description" value="<?php echo $description; ?>">
    <input type="submit" value="Save">
  </form>
  </td>
 </tr>
</table>


  </div>
  </body>
</html>