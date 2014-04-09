<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/editer');

function formulaires_editer_quickvote_saisies_dist($id_quickvote='new', $retour=''){
	$saisies = array(
		   
    array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('quickvote:champ_titre_label'),
        'explication' => _T('quickvote:champ_titre_explication'),
				'obligatoire' => 'oui'
			)
		),    
    array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse1',
				'label' => _T('quickvote:champ_reponse1_label'),
        'explication' => _T('quickvote:champ_reponse1_explication'),
				'obligatoire' => 'oui'
			)
		),    
    array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse2',
				'label' => _T('quickvote:champ_reponse2_label'),
        'explication' => _T('quickvote:champ_reponse2_explication'),
				'obligatoire' => 'oui'
			)
		),
   array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse3',
				'label' => _T('quickvote:champ_reponse3_label'),
        'explication' => _T('quickvote:champ_reponse3_explication'),				
			)
		),      
   array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse4',
				'label' => _T('quickvote:champ_reponse4_label') 				
			)
		), 
       array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse5',
				'label' => _T('quickvote:champ_reponse5_label') 				
			)
		),      
   array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse6',
				'label' => _T('quickvote:champ_reponse6_label') 				
			)
		),
       array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse7',
				'label' => _T('quickvote:champ_reponse7_label') 				
			)
		),      
   array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse8',
				'label' => _T('quickvote:champ_reponse8_label') 				
			)
		), 
       array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse9',
				'label' => _T('quickvote:champ_reponse9_label') 				
			)
		),      
   array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reponse10',
				'label' => _T('quickvote:champ_reponse10_label') 				
			)
		),
    array(
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'hasard',
				'label' => _T('quickvote:champ_hasard_label'),
        'explication' => _T('quickvote:champ_hasard_explication'),         
				'datas' => array(	
          '0' => _T('quickvote:champ_hasard_non'),
          '1' => _T('quickvote:champ_hasard_oui'),
				),
				'cacher_option_intro' => 'on'
			)
		),    
    array(
			'saisie' => 'selection',         
			'options' => array(
				'nom' => 'actif',
				'label' => _T('quickvote:champ_actif_label'),
				'explication' => _T('quickvote:champ_actif_explication'),
				'datas' => array(	
          '0' => _T('quickvote:champ_actif_non'),
          '1' => _T('quickvote:champ_actif_oui'),
				),
				'cacher_option_intro' => 'on'
			)
		),

	);
	
	return $saisies;
}

function formulaires_editer_quickvote_charger_dist($id_quickvote='new', $retour=''){
	$contexte = formulaires_editer_objet_charger('quickvote', $id_quickvote, 0, 0, $retour, '');
	return $contexte;
}

function formulaires_editer_quickvote_verifier_dist($id_quickvote='new', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('quickvote', $id_quickvote);
	return $erreurs;
}

function formulaires_editer_quickvote_traiter_dist($id_quickvote='new', $retour=''){
	if ($retour) refuser_traiter_formulaire_ajax(); 	
	$retours = formulaires_editer_objet_traiter('quickvote', $id_quickvote, 0, 0, $retour, '');
	
	return $retours;
}

?>