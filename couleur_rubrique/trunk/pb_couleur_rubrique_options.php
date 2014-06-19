<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// functions
//
function pb_couleur_rubrique($id_rubrique) {
	$pb_couleur_rubrique = lire_meta("pb_couleur_rubrique$id_rubrique");
	return $pb_couleur_rubrique;
}

function couleur_rubrique($id_rubrique) {
	return pb_couleur_rubrique($id_rubrique);
}

function couleur_site() {
	$pb_couleur_site = lire_meta("pb_couleur_rubrique0");
	return $pb_couleur_site;
}

function couleur_secteur($id_rubrique){
	$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . intval($id_rubrique));
	$couleur_secteur = lire_meta("pb_couleur_rubrique$id_secteur");
	return $couleur_secteur;
}

?>