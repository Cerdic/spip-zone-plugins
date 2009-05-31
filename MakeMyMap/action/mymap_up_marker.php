<?php	

	include_spip('inc/config');
	
	function action_mymap_up_marker() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		$result = spip_query("UPDATE spip_mymap SET lat='".$_POST["glat"]."', lonx='".$_POST["glonx"]."' , descriptif='".mysql_escape_string($_POST["desc"])."' WHERE id_mymap = '" . $_POST["id"]."'");
	
	//	echo "UPDATE spip_mymap SET lat=".$_POST["glat"].", lonx=".$_POST["glonx"]." , descriptif=".mysql_escape_string($_POST["desc"])." WHERE id_mymap = '" . $_POST["id"]."'";
	}
?>