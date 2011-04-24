<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / Modification d'une organisation
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_editer_organisation_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_organisation n'est pas un nombre, c'est une creation
	if (!$id_organisation = intval($arg)) {
		$id_organisation = insert_organisation();
	}

	// Enregistre l'envoi dans la BD
	if ($id_organisation > 0) $err = organisation_set($id_organisation);

	return array($id_organisation,$err);
}

/**
 * Crée une nouvelle organisation et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_organisation
 */
function insert_organisation($champs=array()) {
	$id_organisation = false;
	
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_organisations',
			),
			'data' => $champs
		)
	);
	
	// Insérer l'objet
	$id_organisation = sql_insertq('spip_organisations', $champs);
	
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_organisations',
			),
			'data' => $champs
		)
	);
	
	return $id_organisation;
}


/**
 * Appelle la fonction de modification d'une organisation
 *
 * @param int $id_organisation
 * @param unknown_type $set
 * @return $err
 */
function organisation_set($id_organisation, $set=null) {
	$err = '';
	
	$champs = array(
		'nom', 'statut_juridique', 'identification',
		'activite', 'date_creation', 'descriptif'
	);
	
	$c = array();
	foreach ($champs as $champ)
		$c[$champ] = _request($champ,$set);

		
	include_spip('inc/modifier');
	revision_organisation($id_organisation, $c);
	
	// Modification de statut, changement de rubrique ?
	$c = array();
	foreach (array(
		'id_parent'
	) as $champ)
		$c[$champ] = _request($champ, $set);
	$err .= instituer_organisation($id_organisation, $c);
	
	return $err;
}

/**
 * Enregistre une révision de organisation
 *
 * @param int $id_produit
 * @param array $c
 * @return
 */
function revision_organisation($id_organisation, $c=false) {
	$invalideur = "id='id_organisation/$id_organisation'";

	modifier_contenu('organisation', $id_organisation,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur
		),
		$c);

	return ''; // pas d'erreur
}

/**
 * Modifie des éléments à part que sont le parent, la date, le statut
 *
 * @param int $id_organisation
 * @param array $c
 * @return
 */
function instituer_organisation($id_organisation, $c, $calcul_rub=true){
	include_spip('inc/autoriser');
	include_spip('inc/rubriques');
	include_spip('inc/modifier');
	
	$row = sql_fetsel("id_parent", "spip_organisations", "id_organisation=$id_organisation");
	$id_parent_actuel = $row['id_parent'];
	$champs = array();

	
	// Verifier que le parent demandee existe et est different
	// du parent actuel
	if (isset($c['id_parent'])
		AND $id_parent = intval($c['id_parent'])
		AND $id_parent != $id_parent_actuel
		AND sql_getfetsel('1', 'spip_organisations', 'id_organisation='.$id_parent))
	{
		$champs['id_parent'] = intval($id_parent);
	}
	
	// Envoyer aux plugins
	$champs = pipeline(
		'pre_edition',
		array(
			'args' => array(
				'table' => 'spip_organisations',
				'id_objet' => $id_organisation,
				'action' => 'instituer',
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;

	// sauver les changements
	sql_updateq('spip_organisations', $champs, "id_organisation=$id_organisation");
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_organisation/$id_organisation'");
	
	// Pipeline
	pipeline(
		'post_edition',
		array(
			'args' => array(
				'table' => 'spip_organisations',
				'id_objet' => $id_organisation,
				'action' => 'instituer'
			),
			'data' => $champs
		)
	);
	
	
	return '';
}


?>
