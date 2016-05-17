<?php
/**
 * Purger owncloud
 *
 * @plugin     owncloud
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\owncloud\formulaire_purger_owncloud
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de création du fichier json
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_purger_owncloud_charger_dist($id_owncloud = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {

	$valeurs = array();
	return $valeurs;
}

/**
 * Vérification du formulaire de création du fichier json
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_purger_owncloud_verifier_dist($id_owncloud = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {

	$erreur = array();
	return $erreurs;
}

/**
 * Traiter les données du formulaire de création du fichier json
 *
 * @return string
 *     Environnement du formulaire
 **/
function formulaires_purger_owncloud_traiter_dist($id_owncloud = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {

	include_spip('inc/actions');
	$purger_media = charger_fonction('purger_media', 'action');
	$action = $purger_media();

	$res = array(
			'editable' => true,
			'message_ok' => _T('owncloud:message_confirmation_purger_owncloud')
	);

	$res['message_ok'] .= "<script type='text/javascript'>if (window.jQuery) $('.liste-objets.owncloud').ajaxReload();</script>";
	return $res;
}
