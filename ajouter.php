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
      $formObj = getFormInfo();  

      /* Get the next Battat Number available for this category */
      $rang = getProductLineById($formObj->brand, $pdo);

      /* BUILD BATTAT NUMBER */
      $battatNumber = createBattatNumber($rang, $formObj);
         
    ?>
    <dl>
      <dt>Project Manager</dt>
    <?php
      $param = $formObj->projectManager;
      $sql = "SELECT name FROM product_managers WHERE code = '".$param."'";
      $st = $pdo->query($sql);
      $rang = $st->fetchObject();
    ?>
        <dd class="b"><?php echo $rang->name; ?></dd>
      <dt>Brand</dt>
    <?php
      $param = $formObj->brand;
      $sql = "SELECT catName FROM product_lines WHERE id = ".$param;
      $st = $pdo->query($sql);
      $rang = $st->fetchObject();
    ?>
        <dd class="b"><?php echo $rang->catName; ?></dd>

      <dt>Pack Info</dt>
    <?php
      $param = $formObj->packInfo;
      $piDescription = '';
      if ($param) {
        $sql = "SELECT description FROM pack_info WHERE code = '".$param."'";
        $st = $pdo->query($sql);
        $rang = $st->fetchObject();
        $piDescription = $rang->description;
      }      
      
    ?>
      <dd class="b"><?php echo $formObj->packInfo; ?> (<?php echo $piDescription; ?>)</dd>
      
      <dt>Private Label</dt>
        <dd class="b"><?php echo $formObj->privateLabel; ?></dd>
      
      <dt>Description</dt>
        <dd class="b"><?php echo $formObj->description; ?></dd>
    
    </dl>
    <?php 
      echo "<p>New Battat Number :  ".$battatNumber."</p>";
      /* FIND NEXT UPC AVAILABLE */
$upc = getUpc($pdo);

echo "<hr /><p><a href=\"popupcode.php?upc=".$upc->upcCode."\" target=\"_blank\">Download Barcode</a>";
      
echo "<img src=\"svg.php?code=$upc->upcCode\" /></p><hr />";
    ?>  
<table>
  <tr>
    <td>
      <button onclick="history.back();return false">Back</button>
    </td>
    <td>
  <form action="save.php" method="post">
    <input type="hidden" name="pm" value="<?php echo $formObj->projectManager; ?>">
    <input type="hidden" name="brand" value="<?php echo $formObj->brand; ?>">
    <input type="hidden" name="pi" value="<?php echo $formObj->packInfo; ?>">
    <input type="hidden" name="pl" value="<?php echo $formObj->privateLabel; ?>">
    <input type="hidden" name="description" value="<?php echo $formObj->description; ?>">
    <input type="submit" value="Save">
  </form>
  </td>
 </tr>
</table>


  </div>
  </body>
</html>