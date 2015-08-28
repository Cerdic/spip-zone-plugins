<?php

/**
 * Action retournant un morceau du plan du site (en ajax)
 *
 * @plugin     Plan du site dans l’espace privé
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Plan\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function action_deplacer_objets_dist() {

	include_spip('inc/autoriser');
	if (!autoriser('ecrire')) {
		return plan_json_erreur("Authorization failed");
	}

	include_spip('base/objets');

	$objet = objet_type(_request('objet'));
	$table = table_objet_sql($objet);
	$_id_table = id_table_objet($table);

	$ids = _request('id_objet');
	$id_rubrique_old = _request('id_rubrique_source');
	$id_rubrique_new = _request('id_rubrique_destination');

	if (!is_array($ids) or !$objet) {
		return plan_json_erreur("Aucun identifiant");
	}
	if ($id_rubrique_old == $id_rubrique_new) {
		return plan_json_erreur("Rubriques parentes incorrectes");
	}
	if ($objet != 'rubrique' and !$id_rubrique_new) {
		return plan_json_erreur("Rubriques parentes incorrectes");
	}

	$ids = array_filter($ids);

	if ($objet == 'rubrique') {
		$champ = 'id_parent';
	} else {
		$champ = 'id_rubrique';
	}

	// ne modifier que si les emplacements n'ont pas déjà changé !
	$ids = sql_allfetsel($_id_table, $table, array(sql_in($_id_table, $ids), $champ . '=' . sql_quote($id_rubrique_old)));
	$ids = array_map('array_shift', $ids);

	include_spip('action/editer_objet');

	$erreurs = array();
	$modifs = array('id_parent' => $id_rubrique_new);

	foreach ($ids as $id) {
		if (autoriser('modifier', $objet, $id)) {
			if ($err = objet_modifier($objet, $id, $modifs)) {
				$erreurs[] = $err;
			}
		}
	}

	return plan_json_envoi(array(
		'success' => true,
		'messages' => $erreurs
	));
}

function plan_json_envoi($data) {
	header("Content-Type: application/json; charset=" . $GLOBALS['meta']['charset']);
	echo json_encode($data);
}

function plan_json_erreur($msg) {
	return plan_json_envoi(array('erreur' => $msg));
}
