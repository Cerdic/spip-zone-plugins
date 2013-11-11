<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / Modification d'un quickvote
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_editer_quickvote_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_quickvote n'est pas un nombre, c'est une creation
	if (!$id_quickvote = intval($arg)) {
		$id_quickvote = insert_quickvote();
	}

	// Enregistre l'envoi dans la BD
	if ($id_quickvote > 0) $err = quickvote_set($id_quickvote);

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_quickvote', $id_quickvote, '&') . $err;

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return array($id_quickvote,$err);
}

/**
 * Crée un nouveau quickvote et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_quickvote
 */
function insert_quickvote($champs=array()) {
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_quickvotes',
			),
			'data' => $champs
		)
	);
	// Insérer l'objet
	$id_quickvote = sql_insertq('spip_quickvotes', $champs);
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_quickvotes',
				'id_objet' => $id_quickvote
			),
			'data' => $champs
		)
	);

	return $id_quickvote;
}

/**
 * Appelle la fonction de modification d'un quickvote
 *
 * @param int $id_quickvote
 * @param unknown_type $set
 * @return $err
 */
function quickvote_set($id_quickvote, $set=null) {
	$err = '';

	include_spip('inc/saisies');
	$saisies = saisies_chercher_formulaire('editer_quickvote', array($id_quickvote));
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
	revision_quickvote($id_quickvote, $c);
	
	if ($instituer){
		instituer_quickvote($id_quickvote, $instituer);
	}

	return $err;
}

/**
 * Enregistre une révision de quickvote
 *
 * @param int $id_quickvote
 * @param array $c
 * @return
 */
function revision_quickvote($id_quickvote, $c=false) {
	$invalideur = "id='id_quickvote/$id_quickvote'";

	modifier_contenu('quickvote', $id_quickvote,
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
 * @param int $id_quickvote
 * @param array $c
 * @return
 */
function instituer_quickvote($id_quickvote, $c){
	include_spip('inc/autoriser');
	
	if (isset($c['actif']) and in_array(($actif = $c['actif']), array(0,1))){
		$ancien = sql_getfetsel('actif', 'spip_quickvotes', 'id_quickvote = '.$id_quickvote);
		$champs = array();
		
		// Seulement si on change le statut et qu'on a le droit
		if ($actif != $ancien and autoriser('modifier', 'quickvote', $id_quickvote)){
			$champs['actif'] = $actif;
		}
		
		// Envoyer aux plugins
		$champs = pipeline(
			'pre_edition',
			array(
				'args' => array(
					'table' => 'spip_quickvotes',
					'id_objet' => $id_quickvote,
					'action' => 'instituer',
					'statut_ancien' => $ancien,
				),
				'data' => $champs
			)
		);
		
		if (!count($champs)) return;
		
		// Invalider les caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_quickvote/$id_quickvote'");
		
		sql_updateq(
			'spip_quickvotes',
			$champs,
			'id_quickvote = '.$id_quickvote
		);
		
		// Pipeline
		pipeline(
			'post_edition',
			array(
				'args' => array(
					'table' => 'spip_quickvotes',
					'id_objet' => $id_quickvote,
					'action' => 'instituer',
					'statut_ancien' => $ancien,
				),
				'data' => $champs
			)
		);
		
	}
}

?>