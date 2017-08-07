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
<script>
  $(function() {
        $("table")
        .tablesorter({widthFixed: true,delayInit: true, widgets: ['zebra'],theme:'metro-dark',headers:{6:{sorter:false},7:{sorter:false}} })
        .tablesorterPager({container: $("#pager"),size:10});
 });
</script>
  </head>
  <body>
    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active"><a href="index.php">Home</a></li>
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
$rechercher = $_POST["where"];
$trouver = $_POST["formGroupInputLarge"];
/*
  0:all
  1:battat#
  2:Description
  3:UPC#
*/
  $requete = "SELECT * FROM products WHERE status = 1 AND ";
  if ($rechercher == 1) {
    $requete .= "UPC_full LIKE '%".$trouver."%'";
  }
  else if ($rechercher == 2) {
    $requete .= "t_UPC_description LIKE '%".$trouver."%'";
  }
  else if ($rechercher == 3) {
    $requete .= "BarCodeNo LIKE '%".$trouver."%'";
  }
  else {
    $requete .= "(UPC_full LIKE '%".$trouver."%') OR (t_UPC_description LIKE '%".$trouver."%') OR (BarCodeNo LIKE '%".$trouver."%')";
  }
  $requete .= " ORDER BY d_UPC_createdate DESC";
?>
<form action="search.php" method="post" class="form-horizontal">
<div class="form-group form-group-lg">
  <div class="col-sm-7">
    <input class="form-control" type="text" id="formGroupInputLarge" name="formGroupInputLarge" placeholder="Search a product">
  </div>
  <div class="col-sm-3">
  <select id="where" name="where" class="form-control input-lg">
    <option value="0"> - Choose - </option>
    <option value="1">Battat #</option>
    <option value="2">Description</option>
    <option value="3">UPC Number</option>
  </select>
  </div>
  <div class="col-sm-2">
    <button class="btn btn-primary input-lg" id="submitSearch"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;&nbsp;Find</button>
  </div>
</div>
</form>
<?php
$stmt = $pdo->query($requete);
$nombre = $stmt->rowCount();
?>
<h2 style="margin-top: 50px;"><?php echo $nombre;?> Product(s)</h2>
<table id="upcTable" class="tablesorter">
 <thead>
  <tr>
    <th>Battat #</th>
    <th>Description</th>
    <th>BarCodeNo</th>
    <th>Created</th>
    <th>Modified</th>
    <th style="white-space:nowrap;">Product Manager</th>
    <?php
    if ($cookie == 1) {
    ?>
    <th>Edit</th>
    <th>Duplicate</th>    
    <th>Delete</th>
    <?php } ?>
  </tr>
 </thead>
<tbody>
<?php
while ($row = $stmt->fetch()) {
  echo "<tr><td>".$row["UPC_full"]."</td>";
  echo "<td>".$row["t_UPC_description"]."</td>";
  $upcFinal = str_pad($row["BarCodeNo"],12,"0",STR_PAD_LEFT);
  echo "<td><a href=\"popupcode.php?upc=".$upcFinal."\" onclick=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=0,menubar=0,width=350,height=200');return false;\">".$upcFinal."</a></td>";
  echo "<td style=\"white-space:nowrap;\">".$row["d_UPC_createdate"]."</td>";
  echo "<td style=\"white-space:nowrap;\">".$row["d_UPC_moddate"]."</td>";
  /* PROJECT MANAGER */
  $param = $row["t_UPCProductManager"];
  $sql = "SELECT name FROM product_managers WHERE code LIKE '%".$param."%'";
  $st = $pdo->query($sql);
  $rang = $st->fetchObject();
  echo "<td>".$rang->name."</td>";

     if ($cookie == 1) {

  echo "<td><a href=\"edit.php?product=".$row["id"]."\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\"></span></a></td>";
    echo "<td><a href=\"duplicate.php?product=".$row["id"]."\"><span class=\"glyphicon glyphicon-copy\" aria-hidden=\"true\"></span></a></td>";
  
  echo "<td><a href=\"delete.php?product=".$row["id"]."\"><span class=\"glyphicon glyphicon-floppy-remove\" aria-hidden=\"true\"></span></a></td>";
  echo "</tr>";
    }
}
?>
</tbody>
</table>
    <div id="pager" class="pager">
      <form>
        <img src="js/addons/pager/first.png" class="first"/>
        <img src="js/addons/pager/prev.png" class="prev"/>
        <input type="text" class="pagedisplay"/>
        <img src="js/addons/pager/next.png" class="next"/>
        <img src="js/addons/pager/last.png" class="last"/>
        <select class="pagesize">
          <option selected="selected" value="10">10</option>
          <option value="20">20</option>
          <option value="30">30</option>
          <option value="40">40</option>
        </select>
      </form>
    </div>
   </div>
  </div>
  </body>
</html>