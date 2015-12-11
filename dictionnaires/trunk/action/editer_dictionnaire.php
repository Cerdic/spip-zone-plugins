<?php

/**
 * Gestion de l'action editer_dictionnaire
 *
 * @package SPIP\Dictionnaires\Actions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Action d'édition d'un dictionnaire dans la base de données dont
 * l'identifiant est donné en paramètre de cette fonction ou
 * en argument de l'action sécurisée
 *
 * Si aucun identifiant n'est donné, on crée alors un nouveau dictionnaire.
 * 
 * @param null|int $arg
 *     Identifiant du dictionnaire. En absence utilise l'argument
 *     de l'action sécurisée.
 * @return array
 *     Liste (identifiant du dictionnaire, Texte d'erreur éventuel)
**/
function action_editer_dictionnaire_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_dictionnaire n'est pas un nombre, c'est une creation
	if (!$id_dictionnaire = intval($arg)) {
		$id_dictionnaire = insert_dictionnaire();
	}

	// Enregistre l'envoi dans la BD
	if ($id_dictionnaire > 0) $err = dictionnaire_set($id_dictionnaire);

	return array($id_dictionnaire,$err);
}

/**
 * Crée un nouveau dictionnaire et retourne son ID
 *
 * @param array $champs
 *     Un tableau avec les champs par défaut lors de l'insertion
 * @return int
 *     Identifiant du nouveau dictionnaire
 */
function insert_dictionnaire($champs=array()) {
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_dictionnaires',
			),
			'data' => $champs
		)
	);
	// Insérer l'objet
	$id_dictionnaire = sql_insertq('spip_dictionnaires', $champs);
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_dictionnaires',
				'id_objet' => $id_dictionnaire
			),
			'data' => $champs
		)
	);

	return $id_dictionnaire;
}

/**
 * Modifier un dictionnaire
 * 
 * @param int $id_dictionnaire
 *     Identifiant du dictionnaire à modifier
 * @param array|null $set
 *     Couples (colonne => valeur) de données à modifier.
 *     En leur absence, on cherche les données dans les champs éditables
 *     qui ont été postés (via _request())
 * @return string|null
 *     Chaîne vide si aucune erreur,
 *     Null si aucun champ à modifier,
 *     Chaîne contenant un texte d'erreur sinon.
 */
function dictionnaire_set($id_dictionnaire, $set=null) {
	$err = '';

	include_spip('base/objets');
	$desc = lister_tables_objets_sql('spip_dictionnaires');

	include_spip('inc/modifier');
	$c = collecter_requests(
		// white list
		$desc['champs_editables'],
		// black list
		array(),
		// donnees eventuellement fournies
		$set
	);


	if ($err = objet_modifier_champs('dictionnaire', $id_dictionnaire,
		array(
			'data' => $set,
			'nonvide' => array('titre' => _T('info_sans_titre'))
		),
		$c)) {
		return $err;
	}

	$c = collecter_requests(array('statut'),array(),$set);
	$err = instituer_dictionnaire($id_dictionnaire, $c);
	return $err;
}


/**
 * Instituer un dictionnaire : modifier son statut
 *
 * @pipeline_appel pre_insertion
 * @pipeline_appel post_insertion
 * 
 * @param int $id_dictionnaire
 *     Identifiant du dictionnaire
 * @param array $c
 *     Couples (colonne => valeur) des données à instituer
 * @return null|string
 *     Null si aucun champ à modifier, chaîne vide sinon.
 */
function instituer_dictionnaire($id_dictionnaire, $c){
	include_spip('inc/autoriser');
	include_spip('base/objets');
	$desc = lister_tables_objets_sql('spip_dictionnaires');

	if (isset($c['statut']) and in_array(($statut = $c['statut']), array_keys($desc['statut_textes_instituer']))){
		$ancien = sql_getfetsel('statut', 'spip_dictionnaires', 'id_dictionnaire = '.$id_dictionnaire);
		$champs = array();

		// Seulement si on change le statut et qu'on a le droit
		if ($statut != $ancien and autoriser('modifier', 'dictionnaire', $id_dictionnaire)){
			$champs['statut'] = $statut;
		}
		
		// Envoyer aux plugins
		$champs = pipeline(
			'pre_edition',
			array(
				'args' => array(
					'table' => 'spip_dictionnaires',
					'id_objet' => $id_dictionnaire,
					'action' => 'instituer',
					'statut_ancien' => $ancien,
				),
				'data' => $champs
			)
		);
		
		if (!count($champs)) return;
		
		// Invalider les caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_dictionnaire/$id_dictionnaire'");
		
		sql_updateq(
			'spip_dictionnaires',
			$champs,
			'id_dictionnaire = '.$id_dictionnaire
		);
		
		// Pipeline
		pipeline(
			'post_edition',
			array(
				'args' => array(
					'table' => 'spip_dictionnaires',
					'id_objet' => $id_dictionnaire,
					'action' => 'instituer',
					'statut_ancien' => $ancien,
				),
				'data' => $champs
			)
		);

		// Notifications
		if ($notifications = charger_fonction('notifications', 'inc')) {
			$notifications('instituerdictionnaire', $id_dictionnaire, $champs);
		}

		// On refait le cache des définitions
		include_spip('inc/dictionnaires');
		dictionnaires_lister_definitions(true);
	}

	return '';
}

?>
