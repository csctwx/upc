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

/*****************************************
 * Create UPC and Battat Number
 *
 * Database Description :
 *
 * 1. pack_info
 *    [id] : int auto increment (unique)
 *    [code] : alphabetical code
 *    [description] : Textual description
 *
 * 2. private_label
 *    [id] : int auto increment (unique)
 *    [label] : alphabetical code
 *    [labelName] : Textual description
 *
 * 3. products
 *    [id] : int auto increment (unique)
 *    [UPC_full] : Battat Number
 *    [UPC_Category] : alphabetical code of the brand
 *    [UPC_privatelabel] : Extra information (PrivateLabel)
 *    [t_UPC_description] : Product's Textual description
 *    [UPC_packinfo] : Extar information (PackInfo)
 *    [BarCodeOrig] : Import from old system - to test BarCodeOrig
 *    [BarCodeNo] : Actual UPC code for the product
 *    [d_UPC_createdate] : Creation date
 *    [d_UPC_moddate] : Last modification date
 *    [t_UPCProductManager]: Project Manager alphanumerical code (ProductManagers)
 *    [status]: 1 = published, 0 = deleted
 *
 * 4. product_lines
 *    [id] : int auto increment (unique)
 *    [catName] : Brands name
 *    [catPrefix] : Alphabetical code
 *    [lastUsedNumber] : Last number used to describe battat's number
 *
 * 5. product_manager
 *    [id] : int auto increment (unique)
 *    [code] : Alphanumerical code, identifies Project manager
 *    [name] : Project manager's name
 *
 *
 *  INSERT Description
 *
 * 1. Battat Number
 *    [catPrefix]+([lastUsedNumber]+1)+[pack_info:code]+[private_label:label]
 *
 * 2. UPC
 *    SELECT random UPC in products and add 1. If != exists, OK
 *
 *****************************************/
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
    <script>
      $(function() {

        $("table")
        .tablesorter({widthFixed: true,delayInit: true, widgets: ['zebra'],theme:'metro-dark',headers:{6:{sorter:false},7:{sorter:false},8:{sorter:false}} })
        .tablesorterPager({container: $("#pager"),size:10});

      $("#ajouterForm").click(function() {
        $("#ajouter").show("slow");
        $("#upcTable").hide();
        $("#rechercheForm").hide();
        $("#titleLatest").hide();
        $("#pager").hide();
      });

      $("#retour").click(function() {
        $("#ajouter").hide("slow");
        $("#upcTable").show();
        $("#rechercheForm").show();
        $("#titleLatest").show();
        $("#pager").show();
      });

     });
    </script>
  </head>
  <body>
    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation" class="active"><a href="#" id="retour">Home</a></li>
            <?php
            if ($cookie == 1) {
            ?>
            <li role="presentation"><a href="#" id="ajouterForm">Add a new product</a></li>
            <li role="presentation"><a href="manage.php">Manage Categories</a></li>
            <li role="presentation"><a href="export.php">Export</a></li>
            <li role="presentation"><a href="logout.php">Logout</a></li>
            <?php } else { ?>
                <li role="presentation"><a href="admin.php">Admin</a></li>
            <?php } ?>
          </ul>
        </nav>
        <h3 class="text-muted">Maison Battat Inc.</h3>
      </div>
      <hr>
  <div id="ajouter">
      <form action="ajouter.php" method="post">
        <div class="row">
          <div class="col-md-3 form-group">
            <label for="pm"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;Project Manager</label>
            <select id="pm" name="pm" class="form-control">
              <option value="0">- Select -</option>
              <?php
              $stmt = $pdo->query("SELECT * FROM product_managers ORDER BY name");
              while ($row = $stmt->fetch()) {
                echo "<option value=\"".$row["code"]."\">".$row["name"]." (".$row["code"].")</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-3 form-group">
            <label for="brand"><span class="glyphicon glyphicon-registration-mark" aria-hidden="true"></span>&nbsp;&nbsp;Brand</label>
               <select id="brand" name="brand" class="form-control">
                <option value="0">- Select -</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM product_lines ORDER BY catName");
                while ($row = $stmt->fetch()) {
                  echo "<option value=\"".$row["id"]."\">".$row["catName"]."</option>";
                }
                ?>
               </select>
          </div>
          <div class="col-md-3 form-group">
            <label for="pi"><span class="glyphicon glyphicon-gift" aria-hidden="true"></span>&nbsp;&nbsp;Pack Information</label>
               <select id="pi" name="pi" class="form-control">
                <option value="na">- Select -</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM pack_info ORDER BY code");
                while ($row = $stmt->fetch()) {
                  echo "<option value=\"".$row["code"]."\">".$row["code"]."  -  ".$row["description"]."</option>";
                }
                ?>
               </select>
          </div>
          <div class="col-md-3 form-group">
            <label for="pl"><span class="glyphicon glyphicon-sunglasses" aria-hidden="true"></span>&nbsp;&nbsp;Private Label</label>
               <select id="pl" name="pl" class="form-control">
                <option value="na">- Select -</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM private_label ORDER BY label");
                while ($row = $stmt->fetch()) {
                  echo "<option value=\"".$row["label"]."\">".$row["label"]."  -  ".$row["labelName"]."</option>";
                }
                ?>
               </select>
          </div>
      </div>
     <label for="description">Product Description</label>
     <textarea class="form-control" rows="3" name="description" id="description"></textarea>
     <button class="btn btn-primary" id="submit" style="display:block; width:25%; height: 80px; margin: 25px auto 0 auto;font-size: 125%;"><span class="glyphicon glyphicon-barcode" aria-hidden="true"></span>&nbsp;&nbsp;Create a new product</button>
     </form>
     <hr>
</div>
<form action="search.php" method="post" class="form-horizontal" id="rechercheForm">
<div class="form-group form-group-lg">
  <div class="col-sm-7">
    <input class="form-control" type="text" id="formGroupInputLarge" name="formGroupInputLarge" placeholder="Search a product" data-column="all">
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
<button class="btn btn-primary input-lg" id="submitSearch" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;&nbsp;Find</button>
</div>
</div>
</form>
<h2 style="margin-top: 50px;" id="titleLatest">Our Latest Products</h2>
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
$stmt = $pdo->query("SELECT UPC_full,t_UPC_description,BarCodeNo,d_UPC_createdate,d_UPC_moddate,t_UPCProductManager,id FROM products WHERE status = 1 ORDER BY d_UPC_createdate DESC LIMIT 0,1000");
while ($row = $stmt->fetch()) {
  echo "<tr><td>".$row["UPC_full"]."</td>";
  echo "<td>".$row["t_UPC_description"]."</td>";
  $upcFinal = str_pad($row["BarCodeNo"],12,"0",STR_PAD_LEFT);
  echo "<td><a href=\"popupcode.php?upc=".$upcFinal."\">".$upcFinal."</a></td>";
  echo "<td style=\"white-space:nowrap;\">".$row["d_UPC_createdate"]."</td>";
  echo "<td style=\"white-space:nowrap;\">".$row["d_UPC_moddate"]."</td>";
  /* PROJECT MANAGER */
  $param = $row["t_UPCProductManager"];
  $sql = "SELECT name FROM product_managers WHERE code LIKE '%".$param."%'";
  $st = $pdo->query($sql);
  $rang = $st->fetchObject();
  echo "<td style=\"white-space:nowrap;\">".$rang->name."</td>";

  if ($cookie == 1) {
    echo "<td><a href=\"edit.php?product=".$row["id"]."\"><span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\"></span></a></td>";
    echo "<td><a href=\"duplicate.php?product=".$row["id"]."\"><span class=\"glyphicon glyphicon-copy\" aria-hidden=\"true\"></span></a></td>";
    echo "<td><a href=\"delete.php?product=".$row["id"]."\"><span class=\"glyphicon glyphicon-floppy-remove\" aria-hidden=\"true\"></span></a></td>";
  }
  echo "</tr>";
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
<hr style="clear:both;">
  </body>
</html>