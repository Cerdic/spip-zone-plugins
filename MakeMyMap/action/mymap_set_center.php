<?php	

	include_spip('inc/config');
	
	function action_mymap_set_center() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		$result = spip_query("UPDATE spip_mymap_articles SET lat='".$_POST["lat"]."', lonx='".$_POST["lng"]."' , zoom='".$_POST["zoom"]."' WHERE id_article = '" . $_POST["id_article"]."'");
	}
?>