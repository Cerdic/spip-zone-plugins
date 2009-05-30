<?php
$type_urls = "propres-arbo";

//Court-circuite la fonction url_de_base() en la forçant a voir la 
//page en cours grâce à PHP_SELF et non plus grâce à REQUEST_URI
if(_DIR_RESTREINT && strstr(_DIR_RESTREINT,$_SERVER['PHP_SELF']))
{	$GLOBALS['REQUEST_URI']=str_replace('spip.php','',$_SERVER['PHP_SELF']);	}
?>
