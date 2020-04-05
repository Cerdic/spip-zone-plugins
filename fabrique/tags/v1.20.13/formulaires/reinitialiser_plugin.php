<?php

/**
 * Gestion du formulaire permettant d'effacer toutes les données
 * du formulaire de fabrique afin de le réinitialiser
 *
 * @package SPIP\Fabrique\Formulaires
 */

// sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire de réinitialisation de la fabrique de plugin
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_reinitialiser_plugin_charger_dist(){
	return array();
}

/**
 * Traitement du formulaire de réinitialisation de la fabrique de plugin
 *
 * @return array
 *     Retour des traitements
 */
function formulaires_reinitialiser_plugin_traiter_dist(){

	// reinit
	session_set(FABRIQUE_ID, null);
	session_set(FABRIQUE_ID_IMAGES, null);

	$res = array(
		'editable'=>'oui',
		'message_ok' => _T('fabrique:reinitialisation_effectuee'),
	);
	return $res;
}



?>
