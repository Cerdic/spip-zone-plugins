<?php
/**
 * Gestion du formulaire de d'édition d'un annuaire
 *
 * @plugin annuaires & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 * 
 * @package SPIP\annuaires\Formulaires
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/editer');

/**
 * Saisies d'un annuaire
 *
 * @param int|string $id_annuaire
 *     Identifiant du annuaire. 'new' pour un nouveau annuaire.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_annuaire_saisies_dist($id_annuaire = 'new', $redirect = '') {
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('contacts:annuaire_champ_titre_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'identifiant',
				'label' => _T('contacts:annuaire_champ_identifiant_label'),
				'explication' => _T('contacts:annuaire_champ_identifiant_explication'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'label' => _T('contacts:annuaire_champ_descriptif_label'),
				'rows' => 5,
				'inserer_barre' => 'edition',
			),
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition d'un annuaire
 *
 * @param int|string $id_annuaire
 *     Identifiant du annuaire. 'new' pour un nouveau annuaire.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_annuaire_charger_dist($id_annuaire = 'new', $redirect = '') {
	$contexte = formulaires_editer_objet_charger('annuaire', $id_annuaire, $rien, 0, $redirect, '');
	
	return $contexte;
}


/**
 * Vérification du formulaire d'édition d'un annuaire
 *
 * @param int|string $id_annuaire
 *     Identifiant du annuaire. 'new' pour un nouveau annuaire.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Tableau des éventuelles erreurs
**/
function formulaires_editer_annuaire_verifier_dist($id_annuaire = 'new', $redirect = '') {
	$erreurs = formulaires_editer_objet_verifier('annuaire', $id_annuaire, array('titre', 'identifiant'));
	
	// Pour une création, on teste l'identifiant
	if (
		$identifiant = _request('identifiant')
		and sql_getfetsel('id_annuaire', 'spip_annuaires', array('identifiant = '.sql_quote($identifiant), 'id_annuaire != '.intval($id_annuaire)))
	) {
		$erreurs['identifiant'] = _T('contacts:erreur_annuaire_identifiant_existant');
	}
	
	return $erreurs;
}

/**
 * Traitements du formulaire d'édition d'un annuaire
 *
 * Crée l'enregistrement et l'association éventuelle avec un objet
 * indiqué
 * 
 * @param int|string $id_annuaire
 *     Identifiant du annuaire. 'new' pour un nouveau annuaire.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Retour des traitements
**/
function formulaires_editer_annuaire_traiter_dist($id_annuaire = 'new', $redirect = '') {
	$res = formulaires_editer_objet_traiter('annuaire', $id_annuaire, $rien, 0, $redirect);
	return $res;
}
