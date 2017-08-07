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
<!--<script src="js/jquery-latest.js"></script>-->
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>    
<script src="js/jquery.metadata.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
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
  if (isset($_GET["quoi"])) {

    $quoi = $_GET["quoi"];
    $faire = $_GET["faire"];

    if ($quoi == "brand") {

      /* BRAND */
      if ($faire == "edit") {

        $sql = "SELECT * FROM product_lines WHERE id = ".$_GET["item"];
        $st = $pdo->query($sql);
        $rang = $st->fetchObject();
        ?>
        <form action="manage.php" method="get">
        <input type="hidden" name="quoi" value="brand">
        <input type="hidden" name="faire" value="save">
        <input type="hidden" name="item" value="<?php echo $rang->id; ?>">
        <p>Name<br>
          <input type="text" name="catname" value="<?php echo $rang->catName; ?>">
        </p>
        <p>Prefix<br>
          <input type="text" name="prefix" value="<?php echo $rang->catPrefix; ?>">
        </p>
        <p>Last used number<br>
          <input type="text" name="lastUsedNumber" value="<?php echo $rang->lastUsedNumber; ?>">
        </p>
        <input type="submit" value="Save">
        </form>
        <?php
      } 
      else if ($faire == "delete") {

        $item = $_GET["item"];
        $sql = "DELETE FROM product_lines WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Deleted! <a href="manage.php">Back</a></div>
        <?php
      }
      else if ($faire == "save") {
        $item = $_GET["item"];
        $catname = $_GET["catname"];
        $prefix = $_GET["prefix"];
        $lastnumber = $_GET["lastUsedNumber"];
        $sql = "UPDATE product_lines SET catName = '".$catname."', catPrefix = '".$prefix."', lastUsedNumber = '".$lastnumber."' WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }

      else if ($faire == "ajouter") {
        $brandname = $_GET["brand"];
        $prefix = $_GET["prefix"];
        $number = $_GET["number"];
        $sql = "INSERT INTO product_lines (catName,catPrefix,lastUsedNumber) VALUES ('".$brandname."','".$prefix."','".$number."');";
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }


    } else if ($quoi == "pack") {

      if ($faire == "edit") {
        $sql = "SELECT * FROM pack_info WHERE id = ".$_GET["item"];
        $st = $pdo->query($sql);
        $rang = $st->fetchObject();
        ?>
        <form action="manage.php" method="get">
        <input type="hidden" name="quoi" value="pack">
        <input type="hidden" name="faire" value="save">
        <input type="hidden" name="item" value="<?php echo $rang->id; ?>">
        <p>Code<br>
          <input type="text" name="code" value="<?php echo $rang->code; ?>">
        </p>
        <p>Description<br>
          <input type="text" name="desc" value="<?php echo $rang->description; ?>">
        </p>
        <input type="submit" value="Save">
        </form>
        <?php
      } 

      else if ($faire == "save") {
        $item = $_GET["item"];
        $code = $_GET["code"];
        $desc = $_GET["desc"];
        $sql = "UPDATE pack_info SET code = '".$code."', description = '".$desc."' WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }

      else if ($faire == "ajouter") {
        $code = $_GET["code"];
        $description = $_GET["description"];
        $sql = "INSERT INTO pack_info (code,description) VALUES ('".$code."','".$description."');";
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }

      else if ($faire == "delete") {
        $item = $_GET["item"];
        $sql = "DELETE FROM pack_info WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Deleted! <a href="manage.php">Back</a></div>
        <?php
      }


    } else if ($quoi == "private") {


      if ($faire == "edit") {
        $sql = "SELECT * FROM private_label WHERE id = ".$_GET["item"];
        $st = $pdo->query($sql);
        $rang = $st->fetchObject();
        ?>
        <form action="manage.php" method="get">
        <input type="hidden" name="quoi" value="private">
        <input type="hidden" name="faire" value="save">
        <input type="hidden" name="item" value="<?php echo $rang->id; ?>">
        <p>Label<br>
          <input type="text" name="label" value="<?php echo $rang->label; ?>">
        </p>
        <p>Label Name<br>
          <input type="text" name="name" value="<?php echo $rang->labelName; ?>">
        </p>
        <input type="submit" value="Save">
        </form>
        <?php
      } 

      else if ($faire == "save") {
        $item = $_GET["item"];
        $label = $_GET["label"];
        $name = $_GET["name"];
        $sql = "UPDATE private_label SET label = '".$label."', labelName = '".$name."' WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }

      else if ($faire == "ajouter") {
        $label = $_GET["label"];
        $name = $_GET["name"];
        $sql = "INSERT INTO private_label (label,labelName) VALUES ('".$label."','".$name."');";
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }
      
      else if ($faire == "delete") {
        $item = $_GET["item"];
        $sql = "DELETE FROM private_label WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Deleted! <a href="manage.php">Back</a></div>
        <?php
      }


    } else if ($quoi == "manager") {


      if ($faire == "edit") {
        $sql = "SELECT * FROM product_managers WHERE id = ".$_GET["item"];
        $st = $pdo->query($sql);
        $rang = $st->fetchObject();
        ?>
        <form action="manage.php" method="get">
        <input type="hidden" name="quoi" value="manager">
        <input type="hidden" name="faire" value="save">
        <input type="hidden" name="item" value="<?php echo $rang->id; ?>">
        <p>Code<br>
          <input type="text" name="code" value="<?php echo $rang->code; ?>">
        </p>
        <p>Name<br>
          <input type="text" name="name" value="<?php echo $rang->name; ?>">
        </p>
        <input type="submit" value="Save">
        </form>
        <?php
      } 

      else if ($faire == "save") {
        $item = $_GET["item"];
        $code = $_GET["code"];
        $name = $_GET["name"];
        $sql = "UPDATE product_managers SET code = '".$code."', name = '".$name."' WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }

      else if ($faire == "ajouter") {
        $code = $_GET["code"];
        $name = $_GET["name"];
        $sql = "INSERT INTO product_managers (code,name) VALUES ('".$code."','".$name."');";
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Saved! <a href="manage.php">Back</a></div>
        <?php
      }

      else if ($faire == "delete") {
        $item = $_GET["item"];
        $sql = "DELETE FROM product_managers WHERE id = ".$item;
        $pdo->query($sql);
        ?>
        <div class="alert alert-success" role="alert">Deleted! <a href="manage.php">Back</a></div>
        <?php
      }

    }

}
 else {
?>
<script>
function accepte() {
  if (confirm("Are you sure you want to delete this item?") == true) {
    return true;
  } else {
    return false;
  }
}
</script>
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#brands">Brands</a></li> 
      <li><a data-toggle="tab" href="#packinfo">Pack Info</a></li>
      <li><a data-toggle="tab" href="#private">Private Label</a></li>
      <li><a data-toggle="tab" href="#pm">Project manager</a></li>
  </ul>

<div class="tab-content">


  <div id="brands" class="tab-pane fade in active">
     <h2>Brands</h2>
     <table>
      <tr>
        <th>Category</th>
        <th>Prefix</th>
        <th>Last Used Number</th>
       <!-- <th>Edit</th>
        <th>Delete</th> -->
      </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM product_lines ORDER BY catName");
        while ($row = $stmt->fetch()) {
          echo "<tr><td>".$row["catName"]."</td><td>".$row["catPrefix"]."</td><td>".$row["lastUsedNumber"]."</td></tr>";
          /*<td><a href=\"manage.php?quoi=brand&faire=edit&item=".$row["id"]."\">edit</a></td><td><a onclick=\"return accepte();\" href=\"manage.php?quoi=brand&faire=delete&item=".$row["id"]."\">delete</a></td></tr>";*/
        }
        ?>
    </table>
<hr>


<form action="manage.php" method="get">
<input type="hidden" name="quoi" value="brand">
<input type="hidden" name="faire" value="ajouter">
<fieldset>
<legend>Add a new brand</legend>
<p>Name<br>
  <input type="text" name="brand" id="brand">
</p>
<p>Prefix<br>
  <input type="text" name="prefix" id="prefix">
</p>
<p>Starting number<br>
  <input type="text" name="number" id="number">
</p>
<input type="submit" value="Save">
</fieldset>
</form>
</div>



<div id="packinfo" class="tab-pane fade">
     <h2>Pack Info</h2>
     <table>
      <tr>
        <th>Code</th>
        <th>Description</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM pack_info ORDER BY code");
        while ($row = $stmt->fetch()) {
          echo "<tr><td>".$row["code"]."</td><td>".$row["description"]."</td><td><a href=\"manage.php?quoi=pack&faire=edit&item=".$row["id"]."\">edit</a></td><td><a onclick=\"return accepte();\" href=\"manage.php?quoi=pack&faire=delete&item=".$row["id"]."\">delete</a></td></tr>";
        }
        ?>
    </table>
<hr>
<form action="manage.php" method="get">
<input type="hidden" name="quoi" value="pack">
<input type="hidden" name="faire" value="ajouter">
<fieldset>
<legend>Add a new pack info</legend>
<p>Code<br>
  <input type="text" name="code" id="code">
</p>
<p>Description<br>
  <input type="text" name="description" id="description">
</p>
<input type="submit" value="Save">
</fieldset>
</form>
</div>

<div id="private" class="tab-pane fade">
     <h2>Pivate Label</h2>
     <table>
      <tr>
        <th>Label</th>
        <th>Label Name</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM private_label ORDER BY label");
        while ($row = $stmt->fetch()) {
          echo "<tr><td>".$row["label"]."</td><td>".$row["labelName"]."</td><td><a href=\"manage.php?quoi=private&faire=edit&item=".$row["id"]."\">edit</a></td><td><a onclick=\"return accepte();\" href=\"manage.php?quoi=private&faire=delete&item=".$row["id"]."\">delete</a></td></tr>";
        }
        ?>
    </table>     
<hr>
<form action="manage.php" method="get">
  <input type="hidden" name="quoi" value="private">
<input type="hidden" name="faire" value="ajouter">
<fieldset>
<legend>Add a new private label</legend>
<p>Label<br>
  <input type="text" name="label" id="label">
</p>
<p>Label Name<br>
  <input type="text" name="name" id="name">
</p>
<input type="submit" value="Save">
</fieldset>
</form>
</div>

<div id="pm" class="tab-pane fade">
     <h2>Project Managers</h2>
     <table>
      <tr>
        <th>Code</th>
        <th>Name</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM product_managers ORDER BY name");
        while ($row = $stmt->fetch()) {
          echo "<tr><td>".$row["code"]."</td><td>".$row["name"]."</td><td><a href=\"manage.php?quoi=manager&faire=edit&item=".$row["id"]."\">edit</a></td><td><a onclick=\"return accepte();\" href=\"manage.php?quoi=manager&faire=delete&item=".$row["id"]."\">delete</a></td></tr>";
        }
        ?>
    </table>    
<hr>
<form action="manage.php" method="get">
<input type="hidden" name="quoi" value="manager">
<input type="hidden" name="faire" value="ajouter">
<fieldset>
<legend>Add a new project managers</legend>
<p>Code<br>
  <input type="text" name="code" id="code">
</p>
<p>Name<br>
  <input type="text" name="name" id="name">
</p>
</fieldset>
<input type="submit" value="Save">
</form>
 </div>

</div>
<?php } ?>

   </div>
  </body>
</html>