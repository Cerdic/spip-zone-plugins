<?php	

	include_spip('inc/config');
	
	function action_mymap_add_marker() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		$result = spip_query("INSERT INTO `spip_mymap` (`id_mymap` ,`id_article` ,`lat` ,`lonx`)VALUES 
							(NULL , '".$_POST["id"]."', '".$_POST["y"]."', '".$_POST["x"]."');");
		//echo "ok c'est passé";
		echo mysql_insert_id(); 
	}
?>