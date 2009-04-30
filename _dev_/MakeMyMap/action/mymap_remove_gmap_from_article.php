<?php	

	include_spip('inc/config');
	
	function action_mymap_remove_gmap_from_article() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		$result = spip_query("DELETE FROM spip_mymap_articles WHERE id_article='".$_POST['id_article']."'");
		$result = spip_query("DELETE FROM spip_mymap WHERE id_article='".$_POST['id_article']."'");
		//echo "DELETE FROM spip_mymap_articles WHERE id_article='".$_POST["id_article"]."'";
	}
?>