<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_location_objets_saisies_dist() {
	include_spip('inc/config');
	include_spip('inc/plugin');
	include_spip('inc/objets_location');

	$liste_objets = lister_tables_objets_sql('spip_objets_locations');
	$statuts = [];
	$statuts_selectionnees = [];
	$config = lire_config('location_objets', []);
	$quand = isset($config['quand']) ? $config['quand'] : [];

	//Le statuts du plugin, sauf en cours
	foreach ($liste_objets['statut_textes_instituer'] AS $statut => $label) {
			$statuts[$statut] = _T($label);
		if (in_array($statut, $quand))
			$statuts_selectionnees[$statut] = _T($label);
	}


	$choix_expediteurs = [
		'webmaster' => _T('location_objets:notifications_expediteur_choix_webmaster'),
		'administrateur' => _T('location_objets:notifications_expediteur_choix_administrateur'),
		'email' => _T('location_objets:notifications_expediteur_choix_email')
	];

	if (defined('_DIR_PLUGIN_FACTEUR')) {
		$choix_expediteurs['facteur'] = _T('location_objets:notifications_expediteur_choix_facteur');
	}

	return [
		[
			'saisie' => 'fieldset',
			'options' => [
				'nom' => 'fieldset_parametres',
				'label' => _T('location_objets:cfg_titre_parametrages')
			],

			'saisies' => [
				[
					'saisie' => 'selection',
					'options' => [
						'nom' => 'statut_defaut',
						'datas' => $statuts,
						'defaut' => 'valide',
						'cacher_option_intro' => 'on',
						'label' => _T('location_objets:label_statut_defaut'),
						'defaut' => $config['statut_defaut']
					]
				],
				/*array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'duree_vie',
						'label' => _T('location_objets:duree_vie_label'),
						'explication' => _T('location_objets:duree_vie_explication',
								array(
									'statut_defaut' => $statuts[$config['statut_defaut']]
								)
							),
						'defaut' => $config['duree_vie'],
					)
				),*/
				//$poubelle_duree,
			]
		],
		[
			'saisie' => 'fieldset',
			'options' => [
				'nom' => 'formulaire',
				'label' => _T('location_objets:cfg_titre_formulaire'),
			],
			'saisies' => [
				[
					'saisie' => 'choisir_objets',
					'options' => [
						'nom' => 'location_extras_objets',
						'label' => _T('location_objets:champ_location_extras_objets_label'),
						'defaut' => $config['location_extras_objets'],
					],
				],
				[
					'saisie' => 'selection',
					'options' => [
						'nom' => 'entite_duree',
						'label' => _T('objets_location:champ_entite_duree_label'),
						'explication' => _T('objets_location:explication_entite_duree'),
						'data' => entite_duree_definitions(),
						'defaut' => $config['entite_duree'],
					],
				],
			],
		],
		[
			'saisie' => 'fieldset',
			'options' => [
				'nom' => 'fieldset_notifications',
				'label' => _T('location_objets:notifications_cfg_titre')
			],
			'saisies' => [
				[
					'saisie' => 'explication',
					'options' => [
						'nom' => 'exp1',
						'texte' => _T('location_objets:notifications_explication')
					]
				],
				[
					'saisie' => 'oui_non',
					'options' => [
						'nom' => 'activer',
						'label' => _T('location_objets:notifications_activer_label'),
						'explication' => _T('location_objets:notifications_activer_explication'),
						'defaut' => $config['activer']
					]
				],
			]
		],
		[
			'saisie' => 'fieldset',
			'options' => [
				'nom' => 'fieldset_notifications_parametres',
				'label' => _T('location_objets:notifications_parametres'),
				'afficher_si' => '@activer@ == "on"',
			],
			'saisies' => [
				[
					'saisie' => 'selection_multiple',
					'options' => [
						'nom' => 'quand',
						'label' => _T('location_objets:notifications_quand_label'),
						'explication' => _T('location_objets:notifications_quand_explication'),
						'cacher_option_intro' => 'on',
						'datas' => $statuts,
						'defaut' => $config['quand']
					]
				],
				[
					'saisie' => 'selection',
					'options' => [
						'nom' => 'expediteur',
						'label' => _T('location_objets:notifications_expediteur_label'),
						'explication' => _T('location_objets:notifications_expediteur_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['expediteur'],
						'datas' => $choix_expediteurs
					]
				],

				[
					'saisie' => 'auteurs',
					'options' => [
						'nom' => 'expediteur_webmaster',
						'label' => _T('location_objets:notifications_expediteur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'defaut' => $config['expediteur_webmaster'],
						'afficher_si' => '@expediteur@ == "webmaster"',
					]
				],
				[
					'saisie' => 'auteurs',
					'options' => [
						'nom' => 'expediteur_administrateur',
						'label' => _T('location_objets:notifications_expediteur_administrateur_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'defaut' => $config['expediteur_administrateur'],
						'afficher_si' => '@expediteur@ == "administrateur"',
					]
				],
				[
					'saisie' => 'input',
					'options' => [
						'nom' => 'expediteur_email',
						'label' => _T('location_objets:notifications_expediteur_email_label'),
						'defaut' => $config['expediteur_email'],
						'afficher_si' => '@expediteur@ == "email"',
					]
				],
				[
					'saisie' => 'selection',
					'options' => [
						'nom' => 'vendeur',
						'label' => _T('location_objets:notifications_destinataire_label'),
						'explication' => _T('location_objets:notifications_destinataire_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['vendeur'],
						'datas' => [
							'webmaster' => _T('location_objets:notifications_vendeur_choix_webmaster'),
							'administrateur' => _T('location_objets:notifications_vendeur_choix_administrateur'),
							'email' => _T('location_objets:notifications_vendeur_choix_email')
						]
					]
				],
				[
					'saisie' => 'auteurs',
					'options' => [
						'nom' => 'vendeur_webmaster',
						'label' => _T('location_objets:notifications_vendeur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'multiple' => 'oui',
						'defaut' => $config['vendeur_webmaster'],
						'afficher_si' => '@vendeur@ == "webmaster"',
					]
				],
				[
					'saisie' => 'auteurs',
					'options' => [
						'nom' => 'vendeur_administrateur',
						'label' => _T('location_objets:notifications_vendeur_administrateur_label'),
						'statut' => '0minirezo',
						'multiple' => 'oui',
						'cacher_option_intro' => "on",
						'defaut' => $config['vendeur_administrateur'],
						'afficher_si' => '@vendeur@ == "administrateur"',
					]
				],

				[
					'saisie' => 'input',
					'options' => [
						'nom' => 'vendeur_email',
						'label' => _T('location_objets:notifications_vendeur_email_label'),
						'explication' => _T('location_objets:notifications_vendeur_email_explication'),
						'defaut' => $config['vendeur_email'],
						'afficher_si' => '@vendeur@ == "email"',
					]
				],
				[
					'saisie' => 'oui_non',
					'options' => [
						'nom' => 'client',
						'label' => _T('location_objets:notifications_client_label'),
						'explication' => _T('location_objets:notifications_client_explication'),
						'defaut' => $config['client'],
					]
				],
			]
		],
		/*array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_cron',
				'label' => _T('location_objets:formulaire_public')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'enregistrement_inscrit',
						'label' => _T('location_objets:label_enregistrement_inscrit'),
						'explication' => _T('location_objets:explication_enregistrement_inscrit'),
						'defaut' => $config['enregistrement_inscrit'],
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'enregistrement_inscrit_obligatoire',
						'label' => _T('location_objets:label_enregistrement_inscrit_obligatoire'),
						'defaut' => $config['enregistrement_inscrit_obligatoire'],
						'afficher_si' => '@enregistrement_inscrit@ == "on"',
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'email_reutilisable',
						'label' => _T('location_objets:label_email_reutilisable'),
						'explication' => _T('location_objets:explication_email_reutilisable'),
						'defaut' => $config['email_reutilisable'],
						'afficher_si' => '@enregistrement_inscrit_obligatoire@ == ""',
					)
				),
			)
		)*/
	];
}
