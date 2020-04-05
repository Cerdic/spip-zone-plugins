<?php
/**
 * Importer tous les médias owncloud
 *
 * @plugin     owncloud
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\owncloud\formulaire_importer_owncloud
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
function formulaires_importer_owncloud_charger_dist($id_owncloud = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {

	$valeurs = array();
	return $valeurs;
}

/**
 * Vérification du formulaire de création du fichier json
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_importer_owncloud_verifier_dist($id_owncloud = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {

	$erreur = array();
	return $erreurs;
}

/**
 * Traiter les données du formulaire de création du fichier json
 *
 * @return string
 *     Environnement du formulaire
 **/
function formulaires_importer_owncloud_traiter_dist($id_owncloud = 'new', $objet = '', $id_objet = '', $retour = '', $ajaxload = 'oui', $options = '') {

	include_spip('owncloud_fonctions');
	include_spip('inc/flock');
	
	$lire_fichier = lire_fichier(_DIR_TMP . 'owncloud.json', $contenu);
	$lire_json = json_decode($contenu, true);
	foreach ($lire_json as $cle => $valeur) {
		$url_propre = securise_identifiants($valeur['document'], true);
		$ajouts = importer_media_owncloud($url_propre . '?' . $valeur['md5']);
	}

	if ($ajouts) {
		$res = array(
			'editable' => true,
			'message_ok' => _T('owncloud:message_confirmation_importer_tout_media')
		);
		$res['message_ok'] .= "<script type='text/javascript'>if (window.jQuery) $('.liste-objets.owncloud').ajaxReload();</script>";
	} else {
		$res['message_erreur'] = _T('owncloud:message_confirmation_importer_tout_media_erreur');
	}

	return $res;
}
