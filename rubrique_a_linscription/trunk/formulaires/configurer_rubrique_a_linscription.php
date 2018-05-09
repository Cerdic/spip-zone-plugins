<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_rubrique_a_linscription_saisies() {
	include_spip('inc/config');

	// pas de saisie groupe mots, je ne pense pas que ce soit utile, donc j'en fais une à partir de la selection selection ;-)
	$groupes_mots = array(
		'0' => _T('rubrique_a_linscription:cfg_pas_creer_mot')
	);
	$res = sql_select('id_groupe,titre','spip_groupes_mots');
	while ($row = sql_fetch($res)) {
		$groupes_mots[$row['id_groupe']] = $row['titre']; 
	}

	$saisies = array(
		array (
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'cfg_explication',
				'texte' => _T('rubrique_a_linscription:cfg_explication')
			)
		),
		// Config generale
		array (
			'saisie' => 'fieldset',
			'options' => array (
				'attention' => _T('rubrique_a_linscription:cfg_generale_attention'), 
				'label' => _T('rubrique_a_linscription:cfg_generale_label'), 
				'nom' => 'cfg_generale'
			),
			'saisies' => array (
				array (
					'saisie' => 'selecteur_rubrique',
					'options' => array (
						'nom' => 'rubrique_mere', // rubrique mere, et pas id_parent car id_parent est filtré par l'écran de sec pour n'accepter que des int
						'explication' => _T('rubrique_a_linscription:cfg_rubrique_mere_explication'), 
						'label' => _T('rubrique_a_linscription:cfg_rubrique_mere_label'), 
						'defaut' => lire_config('rubrique_a_linscription/rubrique_mere')
					)
				), 
				array (
					'saisie' => 'case',
					'options' => array (
						'nom' => 'formulaire_explicite',
						'explication' => _T('rubrique_a_linscription:cfg_formulaire_explicite_explication'), 
						'label' => _T('rubrique_a_linscription:cfg_formulaire_explicite_label') 
					)
				),
				array (
					'saisie' => 'selection',
					'options' => array (
						'nom' => 'groupe_mots',
						'data' => $groupes_mots,
						'cacher_option_intro' => true, 
						'label' => _T('rubrique_a_linscription:cfg_groupe_mots')
					)
				),
				array (
					'saisie' => 'selection', 
					'options' => array (
						'nom' => 'statut',
						'data' => array(
							"6forum" => _T('info_visiteur_1'),
							"1comite" => _T('info_statut_redacteur'),
							"0minirezo" => ucfirst(_T('statut_admin_restreint'))
						),
						'cacher_option_intro' => true, 
						'label' => _T('rubrique_a_linscription:cfg_statut')
					)
				)
			)
		),
		// Réglage sur l'espace privé
		array (
			'saisie' => 'fieldset',
			'options' => array (
				'label' => _T('rubrique_a_linscription:cfg_espace_prive'), 
				'nom' => 'cfg_espace_prive'
			),
			'saisies' => array (
				array (
					'saisie' => 'case',
					'options' => array (
						'nom' => 'espace_prive_voir', 
						'label' => _T('rubrique_a_linscription:cfg_espace_prive_voir_label'),
						'explication' => _T('rubrique_a_linscription:cfg_espace_prive_voir_explication')
					)
				), 
				array (
					'saisie' => 'case',
					'options' => array (
						'nom' => 'espace_prive_creer', 
						'label' => _T('rubrique_a_linscription:cfg_espace_prive_creer_label'),
						'explication' => _T('rubrique_a_linscription:cfg_espace_prive_creer_explication')
					)
				)
			)
		),

		// Réglage sur les courriels
		array (
			'saisie' => 'fieldset',
			'options' => array (
				'label' => _T('rubrique_a_linscription:cfg_mail'), 
				'nom' => 'cfg_mail'
			),
			'saisies' => array (
				array (
					'saisie' => 'case',
					'options' => array (
						'nom' => 'mail_public', 
						'label' => _T('rubrique_a_linscription:cfg_mail_public_label'),
					)
				), 
				array (
					'saisie' => 'case',
					'options' => array (
						'nom' => 'mail_prive', 
						'label' => _T('rubrique_a_linscription:cfg_mail_prive_label'),
					)
				), 
			)
		)

	);

	// Si duplicator est activé > proposer de choisir une rubrique.
	// Si une rubrique pour duplicator a été choisie mais que duplicator n'est pas activé > mettre un message
	if (test_plugin_actif('duplicator')) {
		$duplicator = array( 
			array (
				'saisie' => 'selecteur_rubrique',
				'options' => array (
					'nom' => 'duplicator', // rubrique mere, et pas id_parent car id_parent est filtré par l'écran de sec pour n'accepter que des int
					'explication' => _T('rubrique_a_linscription:cfg_duplicator_explication'), 
					'label' => _T('rubrique_a_linscription:cfg_duplicator_label'), 
					'defaut' => lire_config('rubrique_a_linscription/duplicator')
				)
			),
			array (
				'saisie' => 'radio',
				'options' => array (
					'nom' => 'duplicator_arbo', // rubrique mere, et pas id_parent car id_parent est filtré par l'écran de sec pour n'accepter que des int
					'label' => _T('rubrique_a_linscription:cfg_duplicator_arbo_label'), 
					'defaut' => lire_config('rubrique_a_linscription/duplicator'), 
					'obligatoire' => 'on', 
					'data' => array (
						'rub' => _T('duplicator:bouton_confirmer_rub'), 
						'arbo' => _T('duplicator:bouton_confirmer_arbo')
					),
					'defaut' => 'rub' 
				)
			),
			array (
				'saisie' => 'case',
				'options' => array (
					'nom' => 'duplicator_modif_auteur',
					'label' => _T('rubrique_a_linscription:cfg_duplicator_modif_auteur'), 
					'explication' => _T('rubrique_a_linscription:cfg_duplicator_modif_auteur_explication'), 
					'defaut' => lire_config('rubrique_a_linscription/cfg_duplicator_modif_auteur') 
				)
			)
		);
	} elseif (lire_config('rubrique_a_linscription/duplicator')) {
		$duplicator = array(
			array(
				'saisie' => 'explication',
				'options' => array(
					'nom' => 'cfg_duplicator_pb',
					'conteneur_class' => 'erreur', 
					'texte' => _T('rubrique_a_linscription:cfg_duplicator_pb_explication')
				)
			)
		);
	}

	if (isset($duplicator)) {
		$saisies[1]['saisies'] = array_merge($saisies[1]['saisies'], $duplicator);
	}
	return $saisies;
}
