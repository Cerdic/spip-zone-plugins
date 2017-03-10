<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_reservation_evenement_saisies_dist() {

	$liste_objets = lister_tables_objets_sql();
	$statuts = array();
	$statuts_selectionnees = array();
	include_spip('inc/config');
	include_spip('inc/plugin');
	$config = lire_config('reservation_evenement', array());
	$quand = isset($config['quand']) ? $config['quand'] : array();

	//Le statuts du plugin, sauf en cours
	foreach ($liste_objets['spip_reservations']['statut_textes_instituer'] AS $statut => $label) {
			$statuts[$statut] = _T($label);
		if (in_array($statut, $quand))
			$statuts_selectionnees[$statut] = _T($label);
	}

	$choix_expediteurs = array(
		'webmaster' => _T('reservation:notifications_expediteur_choix_webmaster'),
		'administrateur' => _T('reservation:notifications_expediteur_choix_administrateur'),
		'email' => _T('reservation:notifications_expediteur_choix_email')
	);

	if (defined('_DIR_PLUGIN_FACTEUR')) {
		$choix_expediteurs['facteur'] = _T('reservation:notifications_expediteur_choix_facteur');
	}

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('reservation_evenement:cfg_titre_parametrages')
			),

			'saisies' => array(
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'statut_defaut',
						'datas' => $statuts,
						'defaut' => 'valide',
						'cacher_option_intro' => 'on',
						'label' => _T('reservation:label_statut_defaut'),
						'defaut' => $config['statut_defaut']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'duree_vie',
						'label' => _T('reservation:duree_vie_label'),
						'explication' => _T('reservation:duree_vie_explication',
								array(
									'statut_defaut' => $config['statut_defaut']
								)
							),
						'defaut' => $config['duree_vie'],
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'statut_calculer_auto',
						'label' => _T('reservation:label_statut_calculer_auto'),
						'explication' => _T('reservation:label_statut_calculer_auto_explication'),
						'defaut' => $config['statut_calculer_auto']
					)
				),
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'statuts_complet',
						'datas' => $statuts,
						'defaut' => 'valide',
						'cacher_option_intro' => 'on',
						'label' => _T('reservation:label_statuts_complet'),
						'explication' => _T('reservation:statuts_complet_explication'),
						'defaut' => $config['statuts_complet']
					)
				),
				array(
					'saisie' => 'selecteur_rubrique',
					'options' => array(
						'nom' => 'rubrique_reservation',
						'label' => _T('reservation:rubrique_reservation_label'),
						'explication' => _T('reservation:rubrique_reservation_explication'),
						'defaut' => $config['rubrique_reservation'],
						'multiple' => 'oui'
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'afficher_inscription_agenda',
						'label' => _T('reservation:label_afficher_inscription_agenda'),
						'explication' => _T('reservation:afficher_inscription_agenda_explication'),
						'defaut' => $config['afficher_inscription_agenda']
					)
				),
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications',
				'label' => _T('reservation:notifications_cfg_titre')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'exp1',
						'texte' => _T('reservation:notifications_explication')
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'activer',
						'label' => _T('reservation:notifications_activer_label'),
						'explication' => _T('reservation:notifications_activer_explication'),
						'defaut' => $config['activer']
					)
				),
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications_parametres',
				'label' => _T('reservation:notifications_parametres'),
				'afficher_si' => '@activer@ == "on"',
			),
			'saisies' => array(
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'quand',
						'label' => _T('reservation:notifications_quand_label'),
						'explication' => _T('reservation:notifications_quand_explication'),
						'cacher_option_intro' => 'on',
						'datas' => $statuts,
						'defaut' => $config['quand']
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'expediteur',
						'label' => _T('reservation:notifications_expediteur_label'),
						'explication' => _T('reservation:notifications_expediteur_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['expediteur'],
						'datas' => $choix_expediteurs
					)
				),

				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_webmaster',
						'label' => _T('reservation:notifications_expediteur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'defaut' => $config['expediteur_webmaster'],
						'afficher_si' => '@expediteur@ == "webmaster"',
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_administrateur',
						'label' => _T('reservation:notifications_expediteur_administrateur_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'defaut' => $config['expediteur_administrateur'],
						'afficher_si' => '@expediteur@ == "administrateur"',
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'expediteur_email',
						'label' => _T('reservation:notifications_expediteur_email_label'),
						'defaut' => $config['expediteur_email'],
						'afficher_si' => '@expediteur@ == "email"',
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'vendeur',
						'label' => _T('reservation:notifications_destinataire_label'),
						'explication' => _T('reservation:notifications_destinataire_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['vendeur'],
						'datas' => array(
							'webmaster' => _T('reservation:notifications_vendeur_choix_webmaster'),
							'administrateur' => _T('reservation:notifications_vendeur_choix_administrateur'),
							'email' => _T('reservation:notifications_vendeur_choix_email')
						)
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'vendeur_webmaster',
						'label' => _T('reservation:notifications_vendeur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'multiple' => 'oui',
						'defaut' => $config['vendeur_webmaster'],
						'afficher_si' => '@vendeur@ == "webmaster"',
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'vendeur_administrateur',
						'label' => _T('reservation:notifications_vendeur_administrateur_label'),
						'statut' => '0minirezo',
						'multiple' => 'oui',
						'cacher_option_intro' => "on",
						'defaut' => $config['vendeur_administrateur'],
						'afficher_si' => '@vendeur@ == "administrateur"',
					)
				),

				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'vendeur_email',
						'label' => _T('reservation:notifications_vendeur_email_label'),
						'explication' => _T('reservation:notifications_vendeur_email_explication'),
						'defaut' => $config['vendeur_email'],
						'afficher_si' => '@vendeur@ == "email"',
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'client',
						'label' => _T('reservation:notifications_client_label'),
						'explication' => _T('reservation:notifications_client_explication'),
						'defaut' => $config['client'],
					)
				),
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'envoi_separe',
						'label' => _T('reservation:notifications_envoi_separe'),
						'explication' => _T('reservation:notifications_envoi_separe_explication'),
						'cacher_option_intro' => 'on',
						'datas' => $statuts_selectionnees,
						'defaut' => $config['envoi_separe']
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_cron',
				'label' => _T('reservation:cron_fieldset')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'cron',
						'label' => _T('reservation:cron_label'),
						'explication' => _T('reservation:cron_explication'),
						'defaut' => $config['cron'],
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'periodicite_cron',
						'label' => _T('reservation:periodicite_cron_label'),
						'explication' => _T('reservation:periodicite_cron_explication'),
						'defaut' => $config['periodicite_cron'],
						'afficher_si' => '@cron@ == "on"',
						'size' => '10',
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_cron',
				'label' => _T('reservation:formulaire_public')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'enregistrement_inscrit',
						'label' => _T('reservation:label_enregistrement_inscrit'),
						'explication' => _T('reservation:explication_enregistrement_inscrit'),
						'defaut' => $config['enregistrement_inscrit'],
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'enregistrement_inscrit_obligatoire',
						'label' => _T('reservation:label_enregistrement_inscrit_obligatoire'),
						'defaut' => $config['enregistrement_inscrit_obligatoire'],
						'afficher_si' => '@enregistrement_inscrit@ == "on"',
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'email_reutilisable',
						'label' => _T('reservation:label_email_reutilisable'),
						'explication' => _T('reservation:explication_email_reutilisable'),
						'defaut' => $config['email_reutilisable'],
						'afficher_si' => '@enregistrement_inscrit_obligatoire@ == ""',
					)
				),
			)
		)
	);
}
?>
