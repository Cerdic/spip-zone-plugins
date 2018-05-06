<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_PAGE_INFOS_dist($p) {

	// Récupération des arguments de la balise.
	// -- seul l'argument information est optionnel.
	$page = interprete_argument_balise(1, $p);
	$page = str_replace('\'', '"', $page);
	$information = interprete_argument_balise(3, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';

	// Calcul de la balise
	$p->code = "calculer_infos_page($page, $information)";

	return $p;
}

function calculer_infos_page($page, $information = '') {

	include_spip('inc/noizetier_page');
	return noizetier_page_lire($page, $information);
}

// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_OBJET_INFOS_dist($p) {

	// Récupération des arguments de la balise.
	// -- seul l'argument information est optionnel.
	$objet = interprete_argument_balise(1, $p);
	$objet = str_replace('\'', '"', $objet);
	$id_objet = interprete_argument_balise(2, $p);
	$id_objet = isset($id_objet) ? $id_objet : '0';
	$information = interprete_argument_balise(3, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';

	// Calcul de la balise
	$p->code = "calculer_infos_objet($objet, $id_objet, $information)";

	return $p;
}

function calculer_infos_objet($objet, $id_objet, $information = '') {

	include_spip('inc/noizetier_objet');
	return noizetier_objet_lire($objet, $id_objet, $information);
}


// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_OBJET_LISTE_dist($p) {

	// Aucun argument à la balise.
	$p->code = "calculer_liste_objets()";

	return $p;
}

function calculer_liste_objets() {

	include_spip('inc/noizetier_objet');
	return noizetier_objet_repertorier();
}


// Cette balise renvoie la description complète ou l'info donnée d'un bloc
function balise_NOIZETIER_BLOC_INFOS_dist($p) {
	$bloc = interprete_argument_balise(1, $p);
	$bloc = str_replace('\'', '"', $bloc);
	$information = interprete_argument_balise(2, $p);
	$information = isset($information) ? str_replace('\'', '"', $information) : '""';
	$p->code = "calculer_infos_bloc($bloc, $information)";

	return $p;
}

function calculer_infos_bloc($bloc = '', $information = '') {

	include_spip('inc/noizetier_bloc');
	return noizetier_bloc_lire($bloc, $information);
}


function balise_NOIZETIER_NOISETTE_PREVIEW_dist($p) {
	$id_noisette = champ_sql('id_noisette', $p);
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);

	$inclusion = "recuperer_fond(
		'noisette_preview',
		array_merge(unserialize($parametres), array('type_noisette' => $type_noisette))
	)";

	$p->code = "$inclusion";
	$p->interdire_scripts = false;

	return $p;
}
