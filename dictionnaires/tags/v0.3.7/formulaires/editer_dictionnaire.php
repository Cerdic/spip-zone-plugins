<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');

function formulaires_editer_dictionnaire_saisies_dist($id_dictionnaire='new', $retour=''){
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
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'label' => _T('dictionnaire:champ_descriptif_label'),
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
				'nom' => 'actif',
				'label' => _T('dictionnaire:champ_actif_label'),
				'explication' => _T('dictionnaire:champ_actif_explication'),
				'datas' => array(
					'0' => _T('dictionnaire:champ_actif_non'),
					'1' => _T('dictionnaire:champ_actif_oui'),
				),
				'cacher_option_intro' => 'on'
			)
		),
	);
	
	return $saisies;
}

function formulaires_editer_dictionnaire_charger_dist($id_dictionnaire='new', $retour=''){
	$contexte = formulaires_editer_objet_charger('dictionnaire', $id_dictionnaire, 0, 0, $retour, '');
	return $contexte;
}

function formulaires_editer_dictionnaire_verifier_dist($id_dictionnaire='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('dictionnaire', $id_dictionnaire);
	return $erreurs;
}

function formulaires_editer_dictionnaire_traiter_dist($id_dictionnaire='new', $retour=''){
	if ($retour) refuser_traiter_formulaire_ajax();
	if (is_null(_request('type_defaut'))){ set_request('type_defaut', ''); }
	$retours = formulaires_editer_objet_traiter('dictionnaire', $id_dictionnaire, 0, 0, $retour, '');
	
	return $retours;
}

?>
