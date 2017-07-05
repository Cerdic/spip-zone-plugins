<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction vide pour charger ce fichier sans declencher de warning
 *
 * @return void
 */
function rezosocios_autoriser() {
}

/**
 * Autorisation de créer un rezosocio
 *
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_rezosocio_creer_dist($faire, $type, $id, $qui, $opt) {
	if (!in_array($qui['statut'], array('0minirezo', '1comite'))) {
		return false;
	}

	return true;
}

/**
 * Autorisation de modifier un rezosocio
 *
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rezosocio_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('creer', 'rezosocio');
}

/**
 * Autorisation d'associer des rezosocios à un objet
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_associerrezosocios_dist($faire, $type, $id, $qui, $opt) {
	// jamais de rezosocios sur des rezosocios
	if ($type == 'rezosocio') {
		return false;
	}
	return
		(in_array(table_objet_sql($type), lire_config('rezosocios/rezosocios_objets', array('spip_articles', 'spip_rubriques'))))
		and (($id>0 and autoriser('modifier', $type, $id, $qui, $opt))
			or (
				$id<0
				and abs($id) == $qui['id_auteur']
				and autoriser('ecrire', $type, $id, $qui, $opt)
			)
		);
}

/**
 * Autorisation de suppression des rezosocios
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_rezosocio_supprimer_dist($faire, $type, $id, $qui, $opt) {
	$compte = sql_allfetsel('*', 'spip_rezosocios_liens', 'id_rezosocio='.intval($id));
	foreach ($compte as $key => $val) {
		if (!objet_test_si_publie($val['objet'], $val['id_objet']) ) {
			unset($compte[$key]);
		}
	}
	return (count($compte) == 0) and ($qui['statut'] == '0minirezo');
}
