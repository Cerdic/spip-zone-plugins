<?php
	
	include_once('inc/config');
	global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
	//echo "<script type=\"text/javascript\">alert('".$_POST["id"]."==".$_POST["x"]."==".$_POST["y"]."');</script>";
	//TO DO
	// INSERT
	$result = spip_query("INSERT INTO `spip_mymap` (`id_mymap` ,`id_article` ,`lat` ,`lonx`)VALUES (NULL , '".$_POST["id"]."', '".$_POST["x"]."', '".$_POST["y"]."');");
?>