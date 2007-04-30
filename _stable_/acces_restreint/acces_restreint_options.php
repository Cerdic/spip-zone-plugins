<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/acces_restreint');
include_spip('inc/acces_restreint');


// Pipeline : calculer les zones autorisees, sous la forme '1,2,3'
// TODO : avec un petit cache pour eviter de solliciter la base de donnees
$GLOBALS['AccesRestreint_zones_autorisees'] =
	pipeline('AccesRestreint_liste_zones_autorisees', '');

// Ajouter un marqueur de cache pour le differencier selon les autorisations
if (!isset($GLOBALS['marqueur'])) $GLOBALS['marqueur'] = '';
$GLOBALS['marqueur'] .= ":AccesRestreint_zones_autorisees="
	.$GLOBALS['AccesRestreint_zones_autorisees'];


//
// Autorisations
//

if(!function_exists('autoriser_rubrique_voir')) {
function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	static $rub_exclues;
	if (!isset($rub_exclues)) {
		$rub_exclues = AccesRestreint_liste_rubriques_exclues(_DIR_RESTREINT!="");
		$rub_exclues = array_flip($rub_exclues);
	}
	return !isset($rub_exclues[$id]);
}
}
if(!function_exists('autoriser_article_voir')) {
function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	static $art_exclus;
	if (!isset($art_exclus)) {
		$art_exclus = AccesRestreint_liste_articles_exclus(_DIR_RESTREINT!="");
		$art_exclus = array_flip($art_exclus);
	}
	return !isset($art_exclus[$id]);
}
}
if(!function_exists('autoriser_breve_voir')) {
function autoriser_breve_voir($faire, $type, $id, $qui, $opt) {
	static $breves_exclus;
	if (!isset($breves_exclus)) {
		$breves_exclus = AccesRestreint_liste_breves_exclues(_DIR_RESTREINT!="");
		$breves_exclus = array_flip($breves_exclus);
	}
	return !isset($breves_exclus[$id]);
}
}
if(!function_exists('autoriser_site_voir')) {
function autoriser_site_voir($faire, $type, $id, $qui, $opt) {
	static $sites_exclus;
	if (!isset($sites_exclus)) {
		$sites_exclus = AccesRestreint_liste_syndic_exclus(_DIR_RESTREINT!="");
		$sites_exclus = array_flip($sites_exclus);
	}
	return !isset($sites_exclus[$id]);
}
}
if(!function_exists('autoriser_evenement_voir')) {
function autoriser_evenement_voir($faire, $type, $id, $qui, $opt) {
	static $evenements_exclus;
	if (!isset($evenements_exclus)) {
		$evenements_exclus = AccesRestreint_liste_evenements_exclus(_DIR_RESTREINT!="");
		$evenements_exclus = array_flip($evenements_exclus);
	}
	return !isset($evenements_exclus[$id]);
}
}

?>