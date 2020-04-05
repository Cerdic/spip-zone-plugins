<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / Modification d'un dictionnaire
 * @param unknown_type $arg
 * @return unknown_type
 */
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

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_dictionnaire', $id_dictionnaire, '&') . $err;

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return array($id_dictionnaire,$err);
}

/**
 * Crée un nouveau dictionnaire et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_dictionnaire
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
 * Appelle la fonction de modification d'un dictionnaire
 *
 * @param int $id_dictionnaire
 * @param unknown_type $set
 * @return $err
 */
function dictionnaire_set($id_dictionnaire, $set=null) {
	$err = '';

	include_spip('inc/saisies');
	$saisies = saisies_chercher_formulaire('editer_dictionnaire', array($id_dictionnaire));
	$champs = saisies_lister_champs($saisies, false);
	
	$c = array();
	foreach ($champs as $champ)
		$c[$champ] = _request($champ,$set);
	
	// Pour le statut on fera le travail autre part
	$instituer = false;
	if (isset($c['actif'])){
		$instituer = array('actif' => $c['actif']);
		unset($c['actif']);
	}

	include_spip('inc/modifier');
	revision_dictionnaire($id_dictionnaire, $c);
	
	if ($instituer){
		instituer_dictionnaire($id_dictionnaire, $instituer);
	}

	return $err;
}

/**
 * Enregistre une révision de dictionnaire
 *
 * @param int $id_dictionnaire
 * @param array $c
 * @return
 */
function revision_dictionnaire($id_dictionnaire, $c=false) {
	$invalideur = "id='id_dictionnaire/$id_dictionnaire'";

	modifier_contenu('dictionnaire', $id_dictionnaire,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur
		),
		$c);

	return ''; // pas d'erreur
}

/**
 * Modifie des éléments à part
 *
 * @param int $id_dictionnaire
 * @param array $c
 * @return
 */
function instituer_dictionnaire($id_dictionnaire, $c){
	include_spip('inc/autoriser');
	
	if (isset($c['actif']) and in_array(($actif = $c['actif']), array(0,1))){
		$ancien = sql_getfetsel('actif', 'spip_dictionnaires', 'id_dictionnaire = '.$id_dictionnaire);
		$champs = array();
		
		// Seulement si on change le statut et qu'on a le droit
		if ($actif != $ancien and autoriser('modifier', 'dictionnaire', $id_dictionnaire)){
			$champs['actif'] = $actif;
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
		
		// On refait le cache des définitions
		include_spip('inc/dictionnaires');
		dictionnaires_lister_definitions(true);
	}
}

?>
