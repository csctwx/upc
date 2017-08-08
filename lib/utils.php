<?php

/* get productLine from db by category id */
function getProductLineById($brand, $pdo){
	$sql = "SELECT * FROM product_lines WHERE id = ".$brand;
	$st = $pdo->query($sql);
	return $st->fetchObject();	
}

/* Get UPC check digit */
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

function getFormInfo(){
  return (object) ['projectManager' => $_POST["pm"],
               'brand' => $_POST["brand"],
               'packInfo' => $_POST["pi"],
               'privateLabel' => $_POST["pl"],
               'description' => $_POST["description"]
              ];    
}

function createBattatNumber($rang, $formObj){  
  return $rang->catPrefix
        .$rang->lastUsedNumber
        .$formObj->packInfo
        .$formObj->privateLabel;
}

function getUpc($pdo){
  $sql = "SELECT BarCodeOrig + 1 as newBC, id FROM products WHERE NOT EXISTS (SELECT 1 FROM products t2 WHERE t2.BarCodeOrig = products.BarCodeOrig + 1) LIMIT 0,1";

  $stmt = $pdo->query($sql);
  while ($row = $stmt->fetch()) {
    $upc = $row["newBC"];
    $upc = str_pad($upc,5,"0",STR_PAD_LEFT);
    $upcOrig = $upc;
    $upc = "062243".$upc;
    $upcFinale = generate_upc_checkdigit($upc);
    $upc = $upc.$upcFinale;
    return (object)['upcOrig' => $upcOrig,
            'upcCode' => $upc];
  }
}