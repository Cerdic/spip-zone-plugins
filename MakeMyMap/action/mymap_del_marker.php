<?php	
include_spip('inc/config');
function action_mymap_del_marker() {
		global $connect_statut, $connect_toutes_rubriques, $lang, $options, $spip_lang_left, $spip_lang_right, $changer_config, $spip_display;
		$result = spip_query("DELETE FROM spip_mymap WHERE id_mymap='".$_POST["id_mymap"]."'");
		//ICI ON VA RENVOYER LES POINTS QUI SERONT RECUPERER PAR L'APPELANT
		$result= spip_query("SELECT * FROM spip_mymap WHERE id_article='".$_POST["id_article"]."'");
		while ($row = spip_fetch_array($result)){
				echo 'helloworlditstommy';
				echo $row["id_mymap"].'qqqq';
				echo $row["lat"].'qqqq';
				echo $row["lonx"].'qqqq';
				echo $row["descriptif"].'qqqq';
				echo $row["marker"];
				echo 'helloworlditstommy';
		}
 
}
?>
