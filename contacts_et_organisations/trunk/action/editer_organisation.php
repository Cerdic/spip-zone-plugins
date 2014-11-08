<?php

/**
 * Gestion de l'action `editer_organisation` et des fonctions d'insertion
 * et modification d'organisations
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Actions
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / modification d'une organisation
 * 
 * @param null|int $arg
 *     Identifiant de l'organisation.
 *     En absence utilise l'argument de l'action sécurisée.
 * @return array
 *     Liste (identifiant de l'organisation, Texte d'erreur éventuel)
 */
function action_editer_organisation_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_organisation n'est pas un nombre, c'est une creation
	if (!$id_organisation = intval($arg)) {
		$id_organisation = organisation_inserer();
	}

	// Enregistre l'envoi dans la BD
	if ($id_organisation > 0) $err = organisation_modifier($id_organisation);

	return array($id_organisation,$err);
}

/**
 * Crée une nouvelle organisation et retourne son ID
 *
 * @pipeline_appel pre_insertion
 * @pipeline_appel post_insertion
 * 
 * @param array $champs
 *     Un tableau avec les champs par défaut lors de l'insertion
 * @return int
 *     Identifiant de l'organisation créée
 */
function organisation_inserer($id_parent=null, $champs=array()) {

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
 * Modifie les données d'une organisation
 *
 * Récupère les valeurs qui ont été postées d'un formulaire d'édition
 * automatiquement.
 * 
 * @param int $id_organisation
 *     Identifiant de l'organisation
 * @param null|array $set
 *     Couples de valeurs à affecter d'office
 * @return string
 *     Vide en cas de succès, texte d'erreur sinon.
 */
function organisation_modifier($id_organisation, $set=null) {

	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('organisation','champs_editables'),
		// black list
		array('id_parent'),
		// donnees eventuellement fournies
		$set
	);

	if ($err = objet_modifier_champs('organisation', $id_organisation,
		array(
			'nonvide' => array('titre' => _T('contacts:organisation_nouveau_titre')." "._T('info_numero_abbreviation').$id_organisation),
		),
		$c)) {
		return $err;
	}

	// Modification de statut, changement de rubrique ?
	$c = collecter_requests(array('id_parent'),array(),$set);
	$err = organisation_instituer($id_organisation, $c);

	return $err;
}

/**
 * Modifie des éléments spécifiques (le parent, la date, le statut)
 * d'une organisation
 *
 * @pipeline_appel pre_edition
 * @pipeline_appel post_edition
 * 
 * @param int $id_organisation
 *     Identifiant de l'organisation
 * @param array $c
 *     Couples de valeurs à affecter
 * @param bool $calcul_rub
 *     ? Inutilisé.
 * @return string|null
 *     Null si aucun champ n'est modifié, chaîne vide en cas de succès.
 */
function organisation_instituer($id_organisation, $c, $calcul_rub=true){
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
		$champs['id_parent'] = $id_parent;
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
