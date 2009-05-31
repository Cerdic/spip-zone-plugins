<?php	

	include_spip('inc/config');
	
	function action_mymap_up_marker_from_ico() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		$result = spip_query("UPDATE spip_mymap SET marker='".$_POST["marker"]."' WHERE id_mymap = '" . $_POST["id"]."'");
	}
?>