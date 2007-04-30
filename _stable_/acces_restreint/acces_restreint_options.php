<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/acces_restreint');
include_spip('inc/acces_restreint');


// Pipeline : calculer les zones autorisees, sous la forme '1-2-3'
// TODO : avec un petit cache pour eviter de solliciter la base de donnees
$GLOBALS['AccesRestreint_zones_autorisees'] =
	pipeline('AccesRestreint_liste_zones_autorisees', '');


// Ajouter un marqueur de cache pour le differencier selon les autorisations
if (!isset($GLOBALS['marqueur'])) $GLOBALS['marqueur'] = '';
$GLOBALS['marqueur'] .= ":AccesRestreint_zones_autorisees="
	.$GLOBALS['AccesRestreint_zones_autorisees'];


// Etablir la liste des rubriques interdites a ce visiteur
// TODO : avec un petit cache pour eviter de solliciter la base de donnees
$GLOBALS['AccesRestreint_rubriques_exclues'] =
	AccesRestreint_liste_rubriques_exclues(_DIR_RESTREINT!="");


//
// Autorisations
//

// Voir une rubrique
function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	static $rub_exclues=NULL;
	if ($rub_exclues===NULL){
		$rub_exclues = array_flip($GLOBALS['AccesRestreint_rubriques_exclues']);
	}
	
	return !isset($rub_exclues[$id]);
}
function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	static $art_exclus=NULL;
	if ($art_exclus===NULL){
		$art_exclus = AccesRestreint_liste_articles_exclus(_DIR_RESTREINT!="");
		$art_exclus = array_flip($art_exclus);
	}
	return !isset($art_exclus[$id]);
}
function autoriser_breve_voir($faire, $type, $id, $qui, $opt) {
	static $breves_exclus=NULL;
	if ($breves_exclus===NULL){
		$breves_exclus = AccesRestreint_liste_breves_exclues(_DIR_RESTREINT!="");
		$breves_exclus = array_flip($breves_exclus);
	}
	return !isset($breves_exclus[$id]);
}
function autoriser_site_voir($faire, $type, $id, $qui, $opt) {
	static $sites_exclus=NULL;
	if ($sites_exclus===NULL){
		$sites_exclus = AccesRestreint_liste_syndic_exclus(_DIR_RESTREINT!="");
		$sites_exclus = array_flip($sites_exclus);
	}
	return !isset($sites_exclus[$id]);
}

function autoriser_evenement_voir($faire, $type, $id, $qui, $opt) {
	static $evenements_exclus=NULL;
	if ($evenements_exclus===NULL){
		$evenements_exclus = AccesRestreint_liste_evenements_exclus(_DIR_RESTREINT!="");
		$evenements_exclus = array_flip($evenements_exclus);
	}
	return !isset($evenements_exclus[$id]);
}

?>