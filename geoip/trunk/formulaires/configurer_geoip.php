<?php
/**
 * Peupler geoip
 *
 * @plugin     geoip
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\geoip\formulaires_configurer_geoip
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_geoip_charger_dist() {

	$valeurs = array();
	if ($_SERVER['GEOIP_ADDR']) {
		$valeurs = _T('geoip:libapache_installe');
	} else {
		foreach (array('geoip_version',) as $m) {
			$valeurs[$m] = $GLOBALS['meta'][$m];
		}
	}

	return $valeurs;
}

/**
 * Vérification du formulaire
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_geoip_verifier_dist() {

	$erreur = array();
	return $erreurs;
}

/**
 * Traiter les données du formulaire
 *
 * @return string
 *     Environnement du formulaire
 **/
function formulaires_configurer_geoip_traiter_dist() {

	include_spip('geoip_fonctions');

	if (_request('geoip_version')) {
		
		foreach (array('geoip_version',) as $m) {
			if (!is_null($v = _request($m))) {
				$v == 'oui' ? $version = 1 : $version = 2;
				$installer = installer_databases_geoip($version);
				ecrire_meta($m, $v == 'oui' ? 'oui' : 'non');
			}
		}

		// On renomme un fichier de la librairie qui a pour extension .inc
		$vieux_fichier = find_in_path('lib/geoip-api-php/src/geoip.inc');
		$nouveau_fichier = '../lib/geoip-api-php/src/geoip.php';
		rename($vieux_fichier, $nouveau_fichier);		

	}

	$res = array(
			'message_ok' => _T('geoip:message_confirmation_installer_databases_geoip')
	);

	return $res;
}
