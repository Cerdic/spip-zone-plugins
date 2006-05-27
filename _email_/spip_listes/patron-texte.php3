<?php
if (!isset($patron)) {  
  if  (isset($_GET["patron"])) $patron = $_GET["patron"];
                         else $patron = "patron_simple";
}
$fond = "patron-texte";
$delais = 1;
$flag_preserver=true ;

include ("inc-public.php3");


?>
