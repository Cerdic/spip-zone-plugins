<?php

function import_origine_continents(){
	spip_log('import des continents','spip_geo');
	$continents = file(_DIR_PLUGIN_SPIP_GEO."csv/continents.csv");
	if (count($continents)<5){
		spip_log('Fichier csv continent mal lu','spip_geo');
		die ('fichier mal lu');
	}
	array_shift($continents); # supprimer la premiere ligne
	
	foreach ($continents as $ligne) {
		list($id_continent,$nom,$code_onu,$latitude,$longitude,$zoom) = explode(';', $ligne);
		spip_query("INSERT IGNORE spip_geo_continent (id_continent,continent, code_onu, latitude, longitude, zoom) VALUES ('$id_continent', "._q($nom).", '$code_onu', '$latitude', '$longitude', '$zoom')");
	}
}

function import_origine_pays(){
	spip_log('import des pays','spip_geo');
	$pays = file(find_in_path("csv/pays.csv"));
	if (count($pays)<100){
		spip_log('Fichier csv mal lu','spip_geo');
		die ('fichier mal lu');
	}

	array_shift($pays); # supprimer la premiere ligne

	foreach ($pays as $ligne) {
		list($id_pays,$id_continent,$nom,$code_iso,$latitude,$longitude,$indic_tel) = explode(';', $ligne);
		spip_query("INSERT IGNORE spip_geo_pays (id_pays,id_continent, pays, code_iso, latitude, longitude, zoom, indic_tel) VALUES ('$id_pays', '$id_continent', ".sql_quote($nom).", '$code_iso', '$latitude', '$longitude', '$zoom', '$indic_tel')");
	}
}
?>
