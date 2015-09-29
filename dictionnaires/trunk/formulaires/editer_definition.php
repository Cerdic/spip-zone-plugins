<?php

/**
 * Gestion du formulaire de d'édition d'une définition
 *
 * @package SPIP\Dictionnaires\Formulaires
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');


/**
 * Définition des champs de saisie du formulaire d'édition de définition
 *
 * @param int|string $id_definition
 *     Identifiant de la définition. 'new' pour une nouvelle définition.
 * @param int $id_dictionnaire
 *     Identifiant du dictionnaire parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Liste de saisies et leurs options
**/
function formulaires_editer_definition_saisies_dist($id_definition='new', $id_dictionnaire=0, $retour='', $lier_trad=0){
	$saisies = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'id_definition',
				'valeur' => $id_definition
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
			'saisie' => 'dictionnaires',
			'options' => array(
				'nom' => 'id_dictionnaire',
				'label' => _T('dictionnaire:titre_dictionnaire'),
				'cacher_option_intro' => 'oui',
				'obligatoire' => 'oui',
				'defaut' => $id_dictionnaire
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'url_externe',
				'label' => _T('definition:champ_url_externe_label')
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'texte',
				'label' => _T('definition:champ_texte_label'),
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'termes',
				'label' => _T('definition:champ_termes_label'),
				'explication' => _T('definition:champ_termes_explication'),
			)
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'type',
				'label' => _T('definition:champ_type_label'),
				'label_case' => _T('definition:champ_type_label_case'),
				'valeur_oui' => 'abbr',
			)
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'casse',
				'label' => _T('definition:champ_casse_label'),
				'label_case' => _T('definition:champ_casse_label_case'),
				'valeur_oui' => '1',
			)
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition de définition
 *
 * @see formulaires_editer_objet_charger()
 * 
 * @param int|string $id_definition
 *     Identifiant de la définition. 'new' pour une nouvelle définition.
 * @param int $id_dictionnaire
 *     Identifiant du dictionnaire parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_definition_charger_dist($id_definition='new',  $id_dictionnaire=0, $retour='',$lier_trad=0){
	$contexte = formulaires_editer_objet_charger('definition', $id_definition, $id_dictionnaire, $lier_trad, $retour, '');
	
	// Pour une case à cocher on est obligé de faire ça ici car la valeur "defaut" de la saisie ne marche pas
	if (!($id_definition>0) and $id_dictionnaire>0){
		$contexte['type'] = sql_getfetsel('type_defaut', 'spip_dictionnaires', 'id_dictionnaire = '.$id_dictionnaire);
	}
	
	return $contexte;
}


/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_definition_identifier_dist($id_definition='new',  $id_dictionnaire=0, $retour='',$lier_trad=0){
	return serialize(array(intval($id_definition),$lier_trad));
}

/**
 * Vérifications du formulaire d'édition de définition
 *
 * @see formulaires_editer_objet_verifier()
 * 
 * @param int|string $id_definition
 *     Identifiant de la définition. 'new' pour une nouvelle définition.
 * @param int $id_dictionnaire
 *     Identifiant du dictionnaire parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Tableau des erreurs
**/
function formulaires_editer_definition_verifier_dist($id_definition='new',  $id_dictionnaire=0, $retour='',$lier_trad=0){
	$erreurs = formulaires_editer_objet_verifier('definition', $id_definition);
	return $erreurs;
}

/**
 * Traitements du formulaire d'édition de définition
 *
 * @see formulaires_editer_objet_traiter()
 * 
 * @param int|string $id_definition
 *     Identifiant de la définition. 'new' pour une nouvelle définition.
 * @param int $id_dictionnaire
 *     Identifiant du dictionnaire parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Retour des traitements
**/
function formulaires_editer_definition_traiter_dist($id_definition='new',  $id_dictionnaire=0, $retour='',$lier_trad=0){

	if (is_null(_request('type'))){ set_request('type', ''); }
	if (is_null(_request('casse'))){ set_request('casse', ''); }

	$retours = formulaires_editer_objet_traiter('definition', $id_definition, $id_dictionnaire, $lier_trad, $retour, '');
	return $retours;
}

?>
