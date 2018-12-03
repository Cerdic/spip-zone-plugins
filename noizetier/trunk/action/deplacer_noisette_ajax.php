<?php

/**
 * Action déplacer une noisette en ajax
 *
 * Permet de déplacer une noisette avec n'importe quel rang, dans n'importe quel conteneur
 * Met à jour les rangs des autres noisettes si nécessaire.
 * Retourne du JSON.
 *
 * @Note : cette action diffère de deplacer_noisette.php qui permet de déplacer d'un unique rang, au sein du même conteneur
 *
 * @plugin     Noizetier
 * @copyright  2018
 * @licence    GNU/GPL
 * @package    SPIP\Noizetier\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_deplacer_noisette_ajax_dist() {

	//include_spip('inc/autoriser');
	include_spip('inc/ncore_noisette');
	include_spip('action/editer_objet');

	$id_noisette              = _request('_id_noisette');
	$rang                     = intval(_request('rang'));
	$id_conteneur_destination = _request('_id_conteneur_destination');
	$id_conteneur_origine     = _request('_id_conteneur_origine');
	$done                     = false;
	$success                  = $errors = array();

	// Rustine temporaire : l'API de déplacement ne prévoit pas de changement de conteneur
	// Dans ce cas on modifie le conteneur avec un rang libre en amont afin de forcer le changement
	$nouveau_conteneur = ($id_conteneur_destination != $id_conteneur_origine);
	if ($nouveau_conteneur) {
		$set = array(
			'id_conteneur'  => $id_conteneur_destination,
			'rang_noisette' => 9999,
		);
		$update = objet_modifier('noisette', $id_noisette, $set);
	}

	$deplacer = noisette_deplacer('noizetier', $id_noisette, $rang);
	$deplacer = true;

	if ($deplacer) {
		$done = true;
		$success = array('id_noisette' => $id_noisette);
	} else {
		// TODO : remettre le rang d'origine
		$done = false;
		$errors = array('msg' => _T('erreur'));
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