<?php
include_spip('base/acces_restreint');
include_spip('inc/acces_restreint');

//$GLOBALS['surcharge']['exec/auteurs_edit']=dirname(__FILE__).'/exec/auteurs_edit.php';

// ajouter un marqueur de cache pour permettre de differencier le cache en fonction des zones autorisees
// potentiellement une version de cache differente par combinaison de zones habilitees + le cache de base sans autorisation
if (isset($auteur_session['id_auteur'])){
	$zones = AccesRestreint_liste_zones_appartenance_auteur(intval($auteur_session['id_auteur']));
	$zones = join("-",$zones);
	if (!isset($GLOBALS['marqueur'])) $GLOBALS['marqueur']="";
	$GLOBALS['marqueur'].=":zones_acces_autorises $zones";
}

// Voir une rubrique

function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	static $rub_exclues=NULL;
	if ($rub_exclues===NULL){
		$rub_exclues = AccesRestreint_liste_rubriques_exclues(_DIR_RESTREINT!="");
		$rub_exclues = array_flip($rub_exclues);
	}
	
	if (isset($rub_exclues[$id]))
		return false;
	return true;
}
function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	static $art_exclus=NULL;
	if ($art_exclus===NULL){
		$art_exclus = AccesRestreint_liste_articles_exclus(_DIR_RESTREINT!="");
		$art_exclus = array_flip($art_exclus);
	}
	
	if (isset($art_exclus[$id]))
		return false;
	return true;
}
function autoriser_breve_voir($faire, $type, $id, $qui, $opt) {
	static $breves_exclus=NULL;
	if ($breves_exclus===NULL){
		$breves_exclus = AccesRestreint_liste_breves_exclues(_DIR_RESTREINT!="");
		$breves_exclus = array_flip($breves_exclus);
	}
	
	if (isset($breves_exclus[$id]))
		return false;
	return true;
}
function autoriser_site_voir($faire, $type, $id, $qui, $opt) {
	static $sites_exclus=NULL;
	if ($sites_exclus===NULL){
		$sites_exclus = AccesRestreint_liste_syndic_exclus(_DIR_RESTREINT!="");
		$sites_exclus = array_flip($sites_exclus);
	}
	
	if (isset($sites_exclus[$id]))
		return false;
	return true;
}

function autoriser_evenement_voir($faire, $type, $id, $qui, $opt) {
	static $evenements_exclus=NULL;
	if ($evenements_exclus===NULL){
		$evenements_exclus = AccesRestreint_liste_evenements_exclus(_DIR_RESTREINT!="");
		$evenements_exclus = array_flip($evenements_exclus);
	}
	
	if (isset($evenements_exclus[$id]))
		return false;
	return true;
}

?>