<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * API de gestion des TOKEN
 *
 * @access public
 */


/**
 * Enregistrer un token d'auteur
 *
 * @param int $id_auteur
 * @param sting $type Type du token (ex: facebook)
 * @param mixed $token le token sous forme brut
 * @access public
 */
function connecteur_save_token($id_auteur, $type, $token) {

	// Sérializer le token
	$token = serialize($token);
	sql_insertq(
		'spip_connecteur',
		array(
			'id_auteur' => $id_auteur,
			'type' => $type,
			'token' => $token
		)
	);
}

/**
 * Récupérer un token précis
 *
 * @param int $id_auteur
 * @param string $type
 * @access public
 */
function connecteur_get_token($id_auteur, $type) {
	$token = sql_getfetsel(
		'token',
		'spip_connecteur',
		array(
			'id_auteur='.intval($id_auteur),
			'type='.sql_quote($type)
		)
	);

	return unserialize($token);
}
