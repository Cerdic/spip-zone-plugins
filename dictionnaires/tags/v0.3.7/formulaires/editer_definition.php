<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');

function formulaires_editer_definition_saisies_dist($id_definition='new', $id_dictionnaire=0, $retour=''){
	$saisies = array(
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

function formulaires_editer_definition_charger_dist($id_definition='new',  $id_dictionnaire=0, $retour=''){
	$contexte = formulaires_editer_objet_charger('definition', $id_definition, $id_dictionnaire, 0, $retour, '');
	
	// Pour une case à cocher on est obligé de faire ça ici car la valeur "defaut" de la saisie ne marche pas
	if (!($id_definition>0) and $id_dictionnaire>0){
		$contexte['type'] = sql_getfetsel('type_defaut', 'spip_dictionnaires', 'id_dictionnaire = '.$id_dictionnaire);
	}
	
	return $contexte;
}

function formulaires_editer_definition_verifier_dist($id_definition='new',  $id_dictionnaire=0, $retour=''){
	$erreurs = formulaires_editer_objet_verifier('definition', $id_definition);
	return $erreurs;
}

function formulaires_editer_definition_traiter_dist($id_definition='new',  $id_dictionnaire=0, $retour=''){
	if ($retour) refuser_traiter_formulaire_ajax();
	if (is_null(_request('type'))){ set_request('type', ''); }
	if (is_null(_request('casse'))){ set_request('casse', ''); }
	$retours = formulaires_editer_objet_traiter('definition', $id_definition, $id_dictionnaire, 0, $retour, '');
	
	return $retours;
}

?>
