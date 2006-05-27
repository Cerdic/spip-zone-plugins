<?php
if (!isset($patron)) {   // appel simple (hors include),  simple consultation page
  if (isset($_GET["patron"]))  $patron = $_GET["patron"];   
				    else $patron = "patron_simple";    // on essaie de charger le patron par défaut                                    
}

if (!isset($date)) {   
  if (isset($_GET["date"]))  $date = $_GET["date"];   
				else $date = date("Y/m/d");    // maintenant                                     
}

if (isset($_GET['format'])) $format = $_GET['format'];                                 
				else $format = "HTML";

$contexte_inclus['date']= $date ;

if($format == "texte") {header("Location: patron-texte.php3?patron=$patron&date=$date");exit();}
$fond = "patrons/$patron";
$delais = 1;
$flag_preserver=true ;

include ("inc-public.php3");


?>
