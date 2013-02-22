<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function genie_gis_nettoyer_base_dist($t){

	$liens = array();
	
	# liens vers un article inexistant
	if ($articles = sql_allfetsel("A.id_article,L.id_gis,L.objet,L.id_objet","spip_gis_liens AS L 
			INNER JOIN spip_articles AS A 
			ON (A.id_article = L.id_objet AND L.objet='article')",
			"A.id_article IS NULL")) {
		$liens = array_merge($liens, $articles);
	}
			
	# liens vers une breve inexistante
	if ($breves = sql_allfetsel("B.id_breve,L.id_gis,L.objet,L.id_objet","spip_gis_liens AS L 
			INNER JOIN spip_breves AS B 
			ON (B.id_breve = L.id_objet AND L.objet='breve')",
			"B.id_breve IS NULL")) {
		$liens = array_merge($liens, $breves);
	}
	
	foreach ($liens as $row) {
		sql_delete("spip_gis_liens","id_gis=".$row['id_gis']." AND objet=".$row['objet']." AND id_objet=".$row['id_objet']);
		spip_log("GIS GENIE : Suppression du lien gis ". $row['id_gis'] ." => ". $row['objet'] ." ". $row['id_objet'],"gis");
	}

	return 1;
}

?>