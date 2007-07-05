<?php

function import_origine_continents(){

$continents = file(_DIR_PLUGIN_SPIP_GEO."csv/continents.csv");
if (count($continents)<5)
	die ('fichier mal lu');

array_shift($continents); # supprimer la premiere ligne

foreach ($continents as $ligne) {
	list($id_continent,$nom,$latitude,$longitude,$zoom) = explode(';', $ligne);
	echo propre($nom)." $id_continent<br />\n";
	spip_query("INSERT IGNORE spip_geo_continent (id_continent, nom, latitude, longitude, zoom) VALUES ('$id_continent', "._q($nom).", '$latitude', '$longitude', '$zoom')");
}
}

function import_origine_pays(){

$pays = file(_DIR_PLUGIN_SPIP_GEO."csv/pays.csv");
if (count($pays)<100)
	die ('fichier mal lu');

array_shift($pays); # supprimer la premiere ligne

foreach ($pays as $ligne) {
	list($id_pays,$id_continent,$nom,$latitude,$longitude,$zoom,$indic_tel) = explode(';', $ligne);
	echo propre($nom)." $id_pays<br />\n";
	spip_query("INSERT IGNORE spip_geo_pays (id_pays,id_continent, nom, latitude, longitude, zoom, indic_tel) VALUES ('$id_pays', '$id_continent', "._q($nom).", '$latitude', '$longitude', '$zoom', '$indic_tel')");
}

}
?>
