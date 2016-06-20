<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * API de gestion des TOKEN
 *
 * @access public
 */

include_spip('inc/securiser_action');

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
					'token' => base64_encode($token),
					'signature' => calculer_cle_action($token)
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

	// Afin de prévenir des problèmes de nom de class type "__PHP_Incomplete_Class_Name"
	// On va charger l'éventuel fichier référencé dans la config du plugin
	$connecteur_config = charger_fonction($type.'_config', 'connecteur');
	$config = $connecteur_config();

	if (isset($config['charger_fichier'])) {
		include_spip($config['charger_fichier']);
	}

	// Récupérer le token
	$recup_token = sql_fetsel(
		'token, signature',
		'spip_connecteur',
		array(
			'id_auteur='.intval($id_auteur),
			'type='.sql_quote($type)
		)
	);

	if ($recup_token) {
		// On vérifie que la signature du token est toujours bonne
		if (calculer_cle_action(base64_decode($recup_token['token'])) == $recup_token['signature']) {
			return unserialize(base64_decode($recup_token['token']));
		} else {
			// Si la signature n'est pas valide, on active un minipres
			include_spip('inc/minipres');
			echo minipres(_T('info_acces_interdit'));
			die();
		}
	}
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
		array(
			'token' => base64_encode($token),
			'signature' => calculer_cle_action($token)
		),
		array(
			'id_auteur='.intval($id_auteur),
			'type='.sql_quote($type)
		)
	);
}

/**
 * Supprimer le token d'un utilisateur
 *
 * @param int $id_auteur
 * @param string $type
 * @access public
 */
function connecteur_delete_token($id_auteur, $type) {
	sql_delete(
		'spip_connecteur',
		array(
			'id_auteur='.intval($id_auteur),
			'type='.sql_quote($type)
		)
	);
}
