<?php
/**
 * Gestion du formulaire de d'édition d'une organisation
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 * 
 * @package SPIP\Contacts\Formulaires
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/editer');
include_spip('inc/config');
include_spip('inc/abstract_sql');

/**
 * Saisies d'une organisation
 *
 * @param int|string $id_organisation
 *     Identifiant de l'organisation. 'new' pour une nouvelle organisation.
 * @param int $id_parent
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier l'organisation à cet objet,
 *     tel que 'article|3'   
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_organisation_saisies_dist($id_organisation = 'new', $id_parent = 0, $redirect = '', $associer_objet = '') {
	$saisies = array();
	
	// Champ de quel annuaire si on a activé les annaires
	if (lire_config('contacts_et_organisations/utiliser_annuaires')) {
		$saisies[] = array(
			'saisie' => 'annuaires',
			'options' => array(
				'nom' => 'id_annuaire',
				'label' => _T('contacts:annuaire'),
				'option_intro' => _T('contacts:annuaire_aucun'),
			),
		);
	}
	
	// Champ nom de l’orga
	$saisies[] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'nom',
			'label' => _T('contacts:label_nom'),
		),
	);
	
	// Champ de l'orga parente si on a activté
	if (lire_config('contacts_et_organisations/utiliser_organisations_arborescentes')) {
		$saisies[] = array(
			'saisie' => 'organisations',
			'options' => array(
				'nom' => 'id_parent',
				'label' => _T('contacts:label_organisation_parente'),
			),
		);
	}
	
	// Champ statut juridique
	$saisies[] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'statut_juridique',
			'label' => _T('contacts:label_statut_juridique'),
			'explication' => _T('contacts:explication_statut_juridique'),
		),
	);
	
	// Champ identification
	$saisies[] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'identification',
			'label' => _T('contacts:label_identification'),
			'explication' => _T('contacts:explication_identification'),
		),
	);
	
	// Champ activité avec une map des valeurs déjà remplies
	if ($activites = sql_allfetsel('activite', 'spip_organisations', '', 'activite', 'activite asc')) {
		$activites = array_map('reset', $activites);
	}
	else {
		$activites = array();
	}
	$saisies[] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'activite',
			'label' => _T('contacts:label_activite'),
			'explication' => _T('contacts:explication_activite'),
			'data' => $activites,
		),
	);
	
	// Champ URL de l’orga
	$saisies[] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'url_site',
			'label' => _T('contacts:label_url_site'),
			'placeholder' => 'https://…',
		),
	);
	
	// Champ date de création
	$saisies[] = array(
		'saisie' => 'date_jour_mois_annee',
		'options' => array(
			'nom' => 'date_creation',
			'label' => _T('contacts:label_date_creation'),
		),
	);
	
	// Champ descriptif
	$saisies[] = array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'descriptif',
			'label' => _T('contacts:label_descriptif'),
			'rows' => 10,
			'conteneur_class' => 'pleine_largeur',
			'class' => 'inserer_barre_edition inserer_previsualisation',
		),
	);
	
	// Champ des horaires d'ouverture
	$saisies[] = array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'ouvertures',
			'label' => _T('contacts:label_ouvertures'),
			'explication' => _T('contacts:explication_ouvertures'),
			'rows' => 5,
			'conteneur_class' => 'pleine_largeur',
			'class' => 'inserer_barre_edition inserer_previsualisation',
		),
	);
	
	// Champ des tarifs
	$saisies[] = array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'tarifs',
			'label' => _T('contacts:label_tarifs'),
			'rows' => 5,
			'conteneur_class' => 'pleine_largeur',
			'class' => 'inserer_barre_edition inserer_previsualisation',
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition d'une organisation
 *
 * @param int|string $id_organisation
 *     Identifiant de l'organisation. 'new' pour une nouvelle organisation.
 * @param int $id_parent
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier l'organisation à cet objet,
 *     tel que 'article|3'   
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_organisation_charger_dist($id_organisation = 'new', $id_parent = 0, $redirect = '', $associer_objet = '') {
	$valeurs = formulaires_editer_objet_charger('organisation', $id_organisation, $id_parent, 0, $redirect, '');
	
	if (!intval($id_organisation) and $id_annuaire = _request('id_annuaire')) {
		$valeurs['id_annuaire'] = $id_annuaire;
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition d'une organisation
 *
 * @param int|string $id_organisation
 *     Identifiant de l'organisation. 'new' pour une nouvelle organisation.
 * @param int $id_parent
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier l'organisation à cet objet,
 *     tel que 'article|3'   
 * @return array
 *     Tableau des erreurs
**/
function formulaires_editer_organisation_verifier_dist($id_organisation = 'new', $id_parent = 0, $redirect = '', $associer_objet = '') {
	$erreurs = formulaires_editer_objet_verifier('organisation', $id_organisation);

	if ($editer_organisation_verifier = charger_fonction('editer_organisation_verifier', 'inc', true)){
		$erreurs = array_merge($erreurs, $editer_organisation_verifier($id_organisation, $id_parent));
	}

	// verifier que la hiearchie n'introduit pas une boucle infinie de parente
	if (!isset($erreurs['id_parent'])
		and intval($id_organisation) and $idp = intval(_request('id_parent'))) {
		$deja = [$id_organisation];
		while ($idp and !in_array($idp, $deja)) {
			$deja[] = $idp;
			$idp = sql_getfetsel("id_parent", "spip_organisations", "id_organisation=".intval($idp));
		}
		if ($idp) {
			$erreurs['id_parent'] = _T('contacts:erreur_parent_organisation_boucle_infinie');
		}
	}
	
	return $erreurs;
}

/**
 * Traitements du formulaire d'édition d'une organisation
 *
 * Crée l'enregistrement et l'association éventuelle avec un objet
 * indiqué
 * 
 * @param int|string $id_organisation
 *     Identifiant de l'organisation. 'new' pour une nouvelle organisation.
 * @param int $id_parent
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier l'organisation à cet objet,
 *     tel que 'article|3'   
 * @return array
 *     Retour des traitements
**/
function formulaires_editer_organisation_traiter_dist($id_organisation = 'new', $id_parent = 0, $redirect = '', $associer_objet = '') {
	if (!intval($id_organisation) and intval($id_parent) and !_request('id_parent')){
		set_request('id_parent', intval($id_parent));
	}
	
	$res = formulaires_editer_objet_traiter('organisation', $id_organisation, $id_parent, 0, $redirect);

	// bugfix core : rediriger sur la page de l'organisation qui vient d'etre cree et pas sur la page des organisations
	if (!intval($id_organisation)
		and !empty($res['id_organisation'])
		and !empty($res['redirect'])) {
		$res['redirect'] = str_replace("exec=organisations", "exec=organisation", $res['redirect']);
	}

	// eviter le changement de id_organisation si on veut rediriger sur le parent
	// au moment d'une creation d'une organisation fille.
	if (_request('id_parent')) {
		$res['redirect'] = $redirect;
	}
	
	include_spip('inc/config');
	if (!intval($id_organisation)
	  and lire_config('contacts_et_organisations/associer_aux_auteurs','') == 'obli'
		and $id_organisation = $res['id_organisation']){
		$creer_auteur_lie = charger_fonction('creer_auteur_lie', 'action');
		$id_auteur = $creer_auteur_lie("organisation/$id_organisation");
	}
	
	// Un lien contact ou autre a prendre en compte ?
	if ($associer_objet and $id_organisation = $res['id_organisation']){
		$objet = '';
		if (intval($associer_objet)){
			$objet = 'contact';
			$id_objet = intval($associer_objet);
		}
		elseif(preg_match(',^\w+\|[0-9]+$,', $associer_objet)){
			list($objet, $id_objet) = explode('|', $associer_objet);
		}
		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('organisation' => $id_organisation), array($objet => $id_objet));
			if (isset($res['redirect']))
				$res['redirect'] = parametre_url($res['redirect'], "id_lien_ajoute", $id_organisation, '&');
		}
	}

	return $res;
}
