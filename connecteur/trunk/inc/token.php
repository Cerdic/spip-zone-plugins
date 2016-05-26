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
 * Cette variable peut également être un tableau de token type_token => token
 * @access public
 */
function connecteur_save_token($id_auteur, $type, $token) {

	// dans le cas d'un tableau de token, on les enregistre tous
	if (is_array($token)) {
		foreach ($token as $tk_type => $tk) {
			connecteur_save_token($id_auteur, $tk_type, $tk);
		}
	} else {
		// Est-ce qu'il y a déjà un token ?
		$update = connecteur_get_token($id_auteur, $type);
		if (!empty($update)) {
			connecteur_update_token($id_auteur, $type, $token);
		} else {
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
	}
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

/**
 * Mettre à jour un token
 *
 * @param int $id_auteur
 * @param string $type
 * @param mixed $token
 * @access public
 */
function connecteur_update_token($id_auteur, $type, $token) {
	$token = serialize($token);
	sql_updateq(
		'spip_connecteur',
		array('token' => $token),
		array(
			'id_auteur='.intval($id_auteur),
			'type='.sql_quote($type)
		)
	);
}
