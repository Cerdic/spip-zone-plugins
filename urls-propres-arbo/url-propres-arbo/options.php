<?php
$type_urls = "propres-arbo";

//Court-circuite la fonction url_de_base() en la for�ant a voir la 
//page en cours gr�ce � PHP_SELF et non plus gr�ce � REQUEST_URI
if(_DIR_RESTREINT && strstr(_DIR_RESTREINT,$_SERVER['PHP_SELF']))
{	$GLOBALS['REQUEST_URI']=str_replace('spip.php','',$_SERVER['PHP_SELF']);	}
?>
