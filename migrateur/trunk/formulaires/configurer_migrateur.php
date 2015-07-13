<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion du formulaire de configuration du migrateur
 *
 * @package SPIP\Migrateur\Formulaires
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Chargement du formulaire de configuration du migrateur
 *
 * @return array
 *     Liste des données
**/
function formulaires_configurer_migrateur_charger_dist(){
	$liste = array(
		'type' => '',
		'aes_key' => '',
		'auth_key' => '',
		'url_source' => '',
	);

	include_spip('inc/config');
	$conf = lire_config('migrateur', array());
	// aie !
	if (!is_array($conf)) {
		$conf = array();
	}
	$liste = $conf + $liste;

	if (!empty($liste['auth_key'])) {
		include_spip('migrateur_fonctions');
		$valid = migrateur_verifier_peremption_auth_key($liste['auth_key']);
		if ($valid === false) {
			$liste['erreur_auth_key'] = _T('migrateur:erreur_cle_authentification');
		}
	}

	return $liste;
}

/**
 * Vérifications du formulaire de configuration du migrateur
 *
 * @return array
 *     Tableau des erreurs
**/
function formulaires_configurer_migrateur_verifier_dist(){
	$erreurs = array();

	if (!in_array(_request('type'), array('source', 'destination'))) {
		$erreurs['type'] = _T('info_obligatoire');
	}

	// [TODO] tester la validité des clés (tailles) et de l'url

	return $erreurs;
}

/**
 * Traitement du formulaire de configuration du migrateur
 *
 * @return array
 *     Retours du traitement
**/
function formulaires_configurer_migrateur_traiter_dist(){
	include_spip('inc/config');
	$data = $actuel = lire_config('migrateur', array());

	$type = _request('type');
	$data['type'] = $type;

	if ($type == 'source') {
		if (
			empty($actuel['type'])
			or ($actuel['type'] != 'source')
			or _request('generer_cles'))
		{
			$data['aes_key'] = migrateur_get_new_aes_key();
			$data['auth_key'] = migrateur_get_new_auth_key();
			set_request('aes_key',  $data['aes_key']);
			set_request('auth_key', $data['auth_key']);
		}
		if (_request('generer_auth')) {
			$data['auth_key'] = migrateur_get_new_auth_key();
			set_request('auth_key', $data['auth_key']);
		}
	} else {
		$data['aes_key']  = _request('aes_key');
		$data['auth_key'] = _request('auth_key');
		$data['url_source'] = _request('url_source');
	}

	ecrire_config('migrateur/', $data);

	return array(
		'message_ok'=>_T('config_info_enregistree'),
		'editable'=>true
	);
}

/**
 * Retourne une nouvelle clé d'authentification
 *
 * Nécessite lib mcrypt & openssl
 *
 * @return string
**/
function migrateur_get_new_auth_key() {
	$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
	return date('Y-m-d H:i:s') . '@' . bin2hex($iv);
}

/**
 * Retourne une nouvelle clé AES
 * 
 * Nécessite lib mcrypt & openssl
 * 
 * @return string
**/
function migrateur_get_new_aes_key() {
	include_spip('lib/php-encryption/Crypto');
	return bin2hex(\Crypto::CreateNewRandomKey());
}
