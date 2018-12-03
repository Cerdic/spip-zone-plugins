<?php

/**
 * Action ajouter une noisette en ajax
 *
 * Crée la noisette dans un conteneur donné à un rang donné.
 * Retourne du JSON.
 *
 * @plugin     Noizetier
 * @copyright  2018
 * @licence    GNU/GPL
 * @package    SPIP\Noizetier\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_ajouter_noisette_ajax_dist() {

	//include_spip('inc/autoriser');
	include_spip('inc/noizetier_conteneur');
	include_spip('inc/ncore_noisette');

	$type_noisette = _request('_type_noisette');
	$id_conteneur  = _request('_id_conteneur');
	$rang          = intval(_request('rang'));
	$done          = false;
	$success       = $errors = array();

	// Décomposition du conteneur en tableau associatif.
	$conteneur = noizetier_conteneur_decomposer($id_conteneur);
	$id_noisette = noisette_ajouter('noizetier', $type_noisette, $conteneur, $rang);

	if (intval($id_noisette)) {
		$done = true;
		$success = array('id_noisette' => $id_noisette);
	} else {
		$done = false;
		$errors = array('msg' => _T('noizetier:erreur_ajout_noisette', array('noisettes' => $type_noisette)));
	}

	return envoyer_json_envoi(array(
		'done'    => $done,
		'success' => $success,
		'errors'  => $errors,
	));
}

function envoyer_json_envoi($data) {
	header('Content-Type: application/json; charset=' . $GLOBALS['meta']['charset']);
	echo json_encode($data);
}