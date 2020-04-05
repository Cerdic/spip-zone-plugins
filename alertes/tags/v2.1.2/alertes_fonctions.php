<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction transformant une liste de valeurs séparées par des virgules en array
 *
 * @param string $texte
 *
 * @return array
 */
function to_array($texte) {
	$texte = preg_replace('/\s/', '', trim($texte));
	$array = explode(",", $texte);

	return $array;
}

/**
 * Retrouver le secteur d'une rubrique
 *
 * @param int $id_rubrique
 *
 * @return bool|mixed
 */
function trouver_secteur($id_rubrique) {
	include_spip('base/abstract_sql');
	$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', "id_rubrique=" . sql_quote($id_rubrique));

	if ($id_secteur > 0) {
		return $id_secteur;
	}

	return false;
}

/**
 * Savoir si l'alerte demandée est une alerte de l'auteur
 *
 * @param int $id_alerte
 *
 * @return bool
 */
function alerte_auteur($id_alerte, $id_auteur = null) {
	include_spip('base/abstract_sql');
	if ($id_auteur == null) {
		include_spip('inc/session');
		$id_auteur = session_get('id_auteur');
	}

	$association = sql_getfetsel('id_alerte', 'spip_alertes',
		"id_alerte=" . sql_quote($id_alerte) . " AND id_auteur=" . sql_quote($id_auteur));
	if ($association == $id_alerte) {
		return true;
	}

	return false;
}