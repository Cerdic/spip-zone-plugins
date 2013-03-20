<?php
// la cle primaire est declaree avec le reste du sql => recuperer sa seule declaration
function cle_prim($val) {
if ($position_cle=strpos($val,"PRIMARY")) {
$cle_primaire=substr($val,$position_cle);
$cle_primaire=str_replace("))",")",$cle_primaire); 
if (preg_match(",PRIMARY KEY \(([^\)]+)\),Uims", $cle_primaire, $reg)) {
#	echo "<br>".$reg[1];
	return 	$reg[1];
}
}
return $cle_primaire;
}

//sqlite fournit des noms de cle de la forme nom_table_cle => supprimer nom_table
function keyname($nomlong,$nomtable) {
$nomtable=$nomtable."_";
if ($nomlong!=="") {
$nom_cle=str_replace($nomtable,"",$nomlong);
return $nom_cle;
}
else return false;
}

// nettoyer pour MySQL => enlever les entites num, les échappements préexistants + addslashes
function mysql_prep($value) {
	$value = str_replace("&#39;","'",$value);
    $value = str_replace("\'","'",$value);
    $text_mysql = addslashes($value);
    return $text_mysql;
}
?>
