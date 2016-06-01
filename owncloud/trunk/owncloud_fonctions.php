<?php
/**
 * Fonctions pour owncloud
 *
 * @plugin     owncloud
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\owncloud\fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Contruire l'URL d'accès à owncloud
 * 
 * @return array
 */
function construire_url() {

	// Construire l'url de la forme http://login:password@owncloud.me/remote.php/webdav/
	include_spip('inc/config');
	$config = lire_config('owncloud');

	if ($config['url_remote']) {
		// Supprime le slash de fin sur l'url owncloud
		$url_remote = (strrpos($config['url_remote'], '/')) ? rtrim($config['url_remote'], '/') : $config['url_remote'];
		$url_protocle = parse_url($url_remote, PHP_URL_SCHEME);
		$url_courte = preg_replace('(' . $url_protocle . '?://)', $url_protocle . '://'.$config['login'].':'.$config['password'].'@', $url_remote);
		$url_webdav = $url_courte . '/remote.php/webdav/';
		// Supprimer les slashs dans le repertoire owncloud au début et à la fin de la chaine pour gérer les sous répertoires.
		$directory_remote = ltrim($config['directory_remote'], '/');
		$directory_remote = rtrim($config['directory_remote'], '/');
		$url = $url_webdav.$directory_remote;
	} else {
		return false;
	}

	return array('url_courte' => $url_courte, 'url' => $url);
}


/**
 * Sécurise les URL pour éviter de voir le mot de passe dans le HTML
 * 
 * @param string $document l'URL cible
 * @param string $reverse sécurise ou pas
 * @return string
 */
function securise_identifiants($document, $reverse = false) {

	include_spip('inc/config');
	$config = lire_config('owncloud');
	if (!$config['cle']) {
		$cle = ecrire_config('owncloud/cle', generer_chaine_aleatoire());
	}

	if ($document && $reverse == false) {
		$document = preg_replace('/' . $config['login'] . ':' . $config['password'] . '/', $config['cle'], $document);
	} else {
		$document = preg_replace('/' . $config['cle'] . '/', $config['login'] . ':' . $config['password'], $document);
	}

	return $document;

}

/**
 * Importer les médias dans SPIP
 * 
 * @param string $arg l'URL cible
 * @return string
 */
function importer_media_owncloud($url) {
	$url_propre = securise_identifiants($url, true);
	$parts = parse_url($url_propre);

	parse_str($parts['query'], $query);
	$md5 = $parts['query'];
	$url = preg_replace('/' . $md5 . '/', '', $url_propre);

	$existe = sql_getfetsel('md5', 'spip_ownclouds', 'md5=' . sql_quote($md5));
	if (!$existe) {
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		$name = basename($parts['path']);
		$ajouts = $ajouter_documents(false, array(array('tmp_name' => $url, 'name' => $name, 'distant' => true)), null, null, 'auto');
		
		if (is_array($ajouts)) {
			foreach ($ajouts as $id_document) {
				$copier_local = charger_fonction('copier_local', 'action');
				$copier_local($id_document);
				// Virer l'url du document distant des crédits pour éviter la visu user / pass
				// Ajouter aussi le md5 du document
				document_modifier($id_document, array('credits' => '', 'md5' => $md5));
			}
			$stock_import = sql_insertq('spip_ownclouds', array('titre' => $name, 'md5' => $md5, 'date_modif' => date('Y-m-d H:i:s')));
		}
	} else {
		spip_log('Doc inséré ou inexistant', 'owncloud.' . _LOG_ERREUR);
	}

	return $ajouts;
}

/**
 * Fonction utiliser pour supprimer les fichier distant avec webdav
 * 
 * @return boolean
 */
function supprimer_fichier_distant() {

	$url = construire_url();

	include_spip('lib/SabreDAV/vendor/autoload');

	$settings = array(
		'baseUri' => $url['url']
	);

	// TODO: delete webdav
	$client = new Sabre\DAV\Client($settings);

	include_spip('inc/flock');
	$lire_fichier = lire_fichier(_DIR_TMP . 'owncloud.json', $contenu);
	$lire_json = json_decode($contenu, true);
	foreach ($lire_json as $cle => $valeur) {
		$url_propre = securise_identifiants($valeur['document'], true);
		$liste = $client->request('DELETE', $url_propre);
	}

	return false;
}

/**
 * Purger la base des médias dans spip_ownclouds
 * 
 * @return boolean
 */
function purger_media_spip() {

	$document_owncloud = sql_allfetsel('md5', 'spip_ownclouds');
	if ($document_owncloud) {
		foreach ($document_owncloud as $valeur) {
			$nom_fichier = sql_getfetsel('fichier', 'spip_documents', 'md5=' . sql_quote($valeur['md5']));
			if ($nom_fichier) {
				$document_spip = sql_delete('spip_documents', 'md5=' . sql_quote($valeur['md5']));
			}
		}
		// Supprime les données dans la base spip_ownclouds
		$supprimer = sql_delete('spip_ownclouds');
		// Supprimer fichier
		unlink(_DIR_TMP . 'owncloud.json');
	}

	return false;
}

/**
 * Générer la chaine de caractères
 * 
 * @param string $length taille de la chaîne de caractère 
 * @return string
 */
function generer_chaine($length) {
	return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

/**
 * Créer une chaine unique pour cacher le user et le mot de passe
 * 
 * @param string $length taille de la chaîne de caractère 
 * @return string
 */
function generer_chaine_aleatoire($length = 15) {

	$pass = generer_chaine($length);
	do {
		$pass = generer_chaine($length);
	} while (!preg_match('/(?=.*\d)/', $pass));
	return $pass;
}
