<?php
/**
 * Owncloud
 *
 * @plugin	 Owncloud
 * @copyright  2015
 * @author	 cyp
 * @licence	GNU/GPL
 * @package	SPIP\genie\owncloud
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Tâche de fond pour le la synchonisation des fichiers
 *
 * @param string $t
 *
 */
function genie_owncloud_dist() {

	include_spip('owncloud_fonctions');
	include_spip('inc/config');
	$config = lire_config('owncloud');

	$url = construire_url();

	include_spip('lib/SabreDAV/vendor/autoload');

	$settings = array(
		'baseUri' => $url['url'],
		'userName' => $config['login'],
		'password' => $config['password']
	);

	if ($settings['baseUri']) {
		try {
			$client = new Sabre\DAV\Client($settings);
			$liste = $client->request('POST', $settings['baseUri']);
		} catch (Exception $e) {
			$code = $e->getMessage();
		}

		if (in_array($code, array('401', '404', '405', '501')) || $code) {
			spip_log('Erreur de connexion a webdav: ' . $code, 'owncloud.' . _LOG_ERREUR);
			return false;
		} else {
			$recuperer_media = charger_fonction('recuperer_media', 'action');
			$action = $recuperer_media();

			include_spip('inc/flock');
			$lire_fichier = lire_fichier(_DIR_TMP . 'owncloud.json', $contenu);
			$lire_json = json_decode($contenu, true);
			foreach ($lire_json as $cle => $valeur) {
				$url = $valeur['document'] . '?' . $valeur['md5'];
				importer_media_owncloud($url);
			}

			if ($config['activer_effacement_distant'] == 'on') {
				supprimer_fichier_distant();
			}
		}
	} else {
			return false;
	}
	
	return true;
}
