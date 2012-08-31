<?php

function cle_prim($val) {
if ($position_cle=strpos($val,"PRIMARY")) {
$cle_primaire=substr($val,$position_cle);
$cle_primaire=str_replace("))",")",$cle_primaire); 
}
return $cle_primaire;
}

function keyname($nomlong,$nomtable) {
$nomtable=$nomtable."_";
if ($nomlong!=="") {
$nom_cle=str_replace($nomtable,"",$nomlong);
return $nom_cle;
}
else return false;
}

    function mysql_prep($value)
    {
       $value = str_replace("&#39;","'",$value);
       $value = str_replace("\'","'",$value);
       $text_mysql = addslashes($value);
 
        return $text_mysql;
    }
?>
