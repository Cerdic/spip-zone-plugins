<?php
/**
 * Ce fichier contient l'API complémentaire spécifique au noiZetier de gestion des types de noisettes.
 *
 * @package SPIP\NOIZETIER\TYPE_NOISETTE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * @filtre
 *
 * @param $page
 *
 * @return array
 */
function noizetier_type_noisette_compter($page) {

	// Initialisation des compteurs par bloc
	$nb_types = array(
		'composition' => 0,
		'type'        => 0,
		'commun'      => 0
	);

	// Acquisition du type et de la composition éventuelle.
	$type = noizetier_page_type($page);
	$composition = noizetier_page_composition($page);

	// Les compteurs de types de noisette d'une page sont calculés par une lecture de la table 'spip_types_noisettes'.
	$from = array('spip_types_noisettes');
	$where = array(
		'plugin=' . sql_quote('noizetier'),
		'type=' . sql_quote($type),
		'composition=' . sql_quote($composition));
	$compteur = sql_countsel($from, $where);

	// On cherche maintenant les 3 compteurs possibles :
	if ($composition) {
		// - les types de noisette spécifiques de la composition si la page en est une.
		if ($compteur) {
			$nb_types['composition'] = $compteur;
		}
		$where[2] = 'composition=' . sql_quote('');
		$compteur = sql_countsel($from, $where);
		if ($compteur) {
			$nb_types['type'] = $compteur;
		}
	} else {
		// - les types de noisette spécifiques de la page ou du type de la composition
		if ($compteur) {
			$nb_types['type'] = $compteur;
		}
	}
	// - les types de noisette communs à toutes les pages.
	$where[1] = 'type=' . sql_quote('');
	$compteur = sql_countsel($from, $where);
	if ($compteur) {
		$nb_types['commun'] = $compteur;
	}

	$nb_types['total'] = array_sum($nb_types);

	return $nb_types;
}
