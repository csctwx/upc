<?php
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
<ol>

<?php
/* FIND NEXT UPC AVAILABLE */

$sql = "SELECT BarCodeOrig + 1 as newBC, id FROM products WHERE NOT EXISTS (SELECT 1 FROM products t2 WHERE t2.BarCodeOrig = products.BarCodeOrig + 1) LIMIT 0,15";

$stmt = $pdo->query($sql);
while ($row = $stmt->fetch()) {

  $upc = $row["newBC"];

  $upc = str_pad($upc,5,"0",STR_PAD_LEFT);

  $upc = "062243".$upc;

  $upcFinal = generate_upc_checkdigit($upc);

  $upc = $upc.$upcFinal;

  echo $upc."<br>";
}





/* POPULATE BarCodeOrig */
/*
$sql = "SELECT * FROM products";
$stmt = $pdo->query($sql);
while ($row = $stmt->fetch()) {

  $upc = $row["BarCodeNo"];
  $id = $row["id"];

  $upc = substr($upc,6);
  $upc = substr($upc,0,-1);

  $sql = "UPDATE products SET BarCodeOrig = '".$upc."' WHERE id = ".$id;

  echo "<li>".$sql."</li>";
  $go = $pdo->query($sql);

}
*/

?>
</ol>