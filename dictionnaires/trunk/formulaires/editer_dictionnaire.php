<?php

/**
 * Gestion du formulaire de d'édition d'un dictionnaire
 *
 * @package SPIP\Dictionnaires\Formulaires
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');

/**
 * Définition des champs de saisie du formulaire d'édition de dictionnaire
 *
 * @param int|string $id_dictionnaire
 *     Identifiant du dictionnaire. 'new' pour un nouveau dictionnaire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Liste de saisies et leurs options
**/
function formulaires_editer_dictionnaire_saisies_dist($id_dictionnaire='new', $retour=''){
	$saisies = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'id_dictionnaire',
				'valeur' => $id_dictionnaire
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('definition:champ_titre_label'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'label' => _T('dictionnaire:champ_descriptif_label'),
				'class' => 'inserer_barre_edition inserer_previsualisation',
			)
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'type_defaut',
				'label' => _T('dictionnaire:champ_type_defaut_label'),
				'label_case' => _T('dictionnaire:champ_type_defaut_label_case'),
				'valeur_oui' => 'abbr',
			)
		),
		array(
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'statut',
				'label' => _T('dictionnaire:champ_actif_label'),
				'explication' => _T('dictionnaire:champ_actif_explication'),
				'datas' => array(
					'inactif' => _T('dictionnaire:champ_actif_non'),
					'actif'   => _T('dictionnaire:champ_actif_oui'),
				),
				'cacher_option_intro' => 'on'
			)
		),
	);
	
	return $saisies;
}


/**
 * Chargement du formulaire d'édition de dictionnaire
 *
 * @see formulaires_editer_objet_charger()
 * 
 * @param int|string $id_dictionnaire
 *     Identifiant du dictionnaire. 'new' pour un nouveau dictionnaire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_dictionnaire_charger_dist($id_dictionnaire='new', $retour=''){
	$contexte = formulaires_editer_objet_charger('dictionnaire', $id_dictionnaire, 0, 0, $retour, '');
	return $contexte;
}

/**
 * Vérifications du formulaire d'édition de dictionnaire
 *
 * @see formulaires_editer_objet_verifier()
 * 
 * @param int|string $id_dictionnaire
 *     Identifiant du dictionnaire. 'new' pour un nouveau dictionnaire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Tableau des erreurs
**/
function formulaires_editer_dictionnaire_verifier_dist($id_dictionnaire='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('dictionnaire', $id_dictionnaire);
	return $erreurs;
}

/**
 * Traitements du formulaire d'édition de dictionnaire
 *
 * @see formulaires_editer_objet_traiter()
 * 
 * @param int|string $id_dictionnaire
 *     Identifiant du dictionnaire. 'new' pour un nouveau dictionnaire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Retour des traitements
**/
function formulaires_editer_dictionnaire_traiter_dist($id_dictionnaire='new', $retour=''){
	if (is_null(_request('type_defaut'))){ set_request('type_defaut', ''); }
	$retours = formulaires_editer_objet_traiter('dictionnaire', $id_dictionnaire, 0, 0, $retour, '');
	return $retours;
}

?>
