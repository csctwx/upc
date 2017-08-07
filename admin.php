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

  if ($_POST) {
    $user = $_POST["username"];
    $pass = $_POST["password"];

    $sql = "SELECT status FROM admin WHERE user LIKE '".$user."' AND pass LIKE '".$pass."'";
    $st = $pdo->query($sql);
    $rang = $st->fetchObject();

      if ($rang->status) {
        setcookie("BattatAdmin","1",time()+288000); /* Set cookie for 8 hours */
        echo "<div class=\"alert alert-success\" role=\"alert\">You are now logged in. <a href=\"index.php\">Continue</a></div>";
      }
      else {
        echo "<div class=\"alert alert-danger\" role=\"alert\">Password and Username does not match.  <a href=\"admin.php\">Try again</div>";
      }

  //echo "<td>".$rang->name."</td>";
  } else {
?>
<form action="admin.php" method="post">
<div class="row">
  <div class="col-lg-2">
    <div class="input-group input-group-lg">
        <input type="text" class="form-control" placeholder="Username" name="username">
    </div>
  </div>
  <div class="col-lg-2">
    <div class="input-group input-group-lg">
        <input type="password" class="form-control" placeholder="Password" name="password">
    </div>
  </div>
  <div class="col-lg-2">
    <div class="input-group input-group-lg">
        <button type="submit" class="btn-lg btn-primary">Login</button>
    </div>
  </div>
</div>
</form>
<?php } ?>
   </div>
  </body>
</html>