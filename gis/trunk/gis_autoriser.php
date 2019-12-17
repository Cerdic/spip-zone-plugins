<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function gis_autoriser() {
}

/**
 * Autorisation a modifier le logo d'un point
 * Si on est autorisé à modifier le point en question
 *
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_gis_iconifier_dist($faire, $quoi, $id, $qui, $opts) {
	return autoriser('modifier', 'gis', $id, $qui, $opts);
}

/**
 * Autorisation a modifier un point
 * Avoir un statut dans les 3 fournis par SPIP
 * (On n'a pas d'auteur pour un point ...)
 *
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_gis_modifier_dist($faire, $quoi, $id, $qui, $opts) {
	return (in_array($qui['statut'], array('0minirezo', '1comite', '6forum')));
}

/**
 * Autorisation a creer un point
 * Avoir un statut dans les 3 fournis par SPIP
 * (On n'a pas d'auteur pour un point ...)
 *
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_gis_creer_dist($faire, $quoi, $id, $qui, $opts) {
	return (in_array($qui['statut'], array('0minirezo', '1comite', '6forum')));
}

/**
 * Autorisation d'associer un point à un objet
 * Un auteur peut lier un point à un autre objet que s'il peut modifier l'objet à lier en question
 *
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_associergis_dist($faire, $quoi, $id, $qui, $opts) {
	return autoriser('lier', 'gis', '', $qui, array('objet' => $quoi,'id_objet'=>$id));
}

/**
 * Autorisation a lier un point d'un objet
 * Un auteur peut lier un point à un autre objet que s'il peut modifier l'objet à lier en question
 *
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_gis_lier_dist($faire, $quoi, $id, $qui, $opts) {
	if (is_array($opts) and isset($opts['objet']) and isset($opts['id_objet'])) {
		return autoriser('modifier', $opts['objet'], $opts['id_objet'], $qui);
	}
	return false;
}

/**
 * Autorisation a délier un point d'un objet
 * Un auteur peut délier un point d'un autre objet que s'il peut modifier l'objet en question
 * Si l'objet lié n'existe plus, on vérifie que l'auteur a le droit de modifier le point
 *
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_gis_delier_dist($faire, $quoi, $id, $qui, $opts) {
	$table = table_objet_sql($opts['objet']);
	$_id_objet = id_table_objet($table);
	if (!sql_getfetsel($_id_objet, $table, "$_id_objet=" . intval($opts['id_objet']))) {
		return autoriser('modifier', 'gis', $id, $qui, $opts);
	} else {
		return autoriser('lier', 'gis', $id, $qui, $opts);
	}
}

/**
 * Autorisation a supprimer un point
 * Un auteur peut supprimer un point s'il peut délier tous les objets et modifier le point
 *
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
 */
function autoriser_gis_supprimer_dist($faire, $quoi, $id, $qui, $opts) {
	include_spip('base/objets');
	
	$objets_legitimes = array_map('objet_type', array_keys(lister_tables_objets_sql()));
	$liaisons = sql_select(
		'*',
		'spip_gis_liens',
		array('id_gis=' . intval($id), sql_in('objet', $objets_legitimes))
	);
	
	while ($liaison = sql_fetch($liaisons)) {
		if (!autoriser('delier', 'gis', $liaison['id_gis'], $qui, $liaison)) {
			return false;
		}
	}
	
	return autoriser('modifier', 'gis', $id, $qui, $opts);
}
