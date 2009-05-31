<?php	

	include_spip('inc/config');
	
	function action_mymap_get_get_marker_desc() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		$result = spip_query("SELECT descriptif,marker FROM spip_mymap WHERE id_mymap = '" . $_POST["id"]."'");
		//echo "ok c'est passé";
		while ($row = mysql_fetch_assoc($result)) {
			echo $row['descriptif']."qqqq".$row['marker'];
		}
	}
?>