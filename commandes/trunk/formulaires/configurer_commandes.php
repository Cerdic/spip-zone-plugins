<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_commandes_saisies_dist(){
	include_spip('inc/config');
	include_spip('inc/plugin');
	include_spip('commandes_fonctions');
	include_spip('inc/puce_statut');
	$config = lire_config('commandes');

	$choix_expediteurs = array(
			'webmaster' => _T('commandes:notifications_expediteur_choix_webmaster'),
			'administrateur' => _T('commandes:notifications_expediteur_choix_administrateur'),
			'email' => _T('commandes:notifications_expediteur_choix_email')
	);
	
	if (defined('_DIR_PLUGIN_FACTEUR')){
		$choix_expediteurs['facteur'] = _T('commandes:notifications_expediteur_choix_facteur');
	}

	// liste des statuts précédés de leur puce
	foreach (commandes_lister_statuts() as $k=>$v)
		$statuts[$k] = http_img_pack(statut_image('commande',$k),'')."&nbsp;".$v;

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('commandes:parametres_cfg_titre')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'duree_vie',
						'label' => _T('commandes:parametres_duree_vie_label'),
						'explication' => _T('commandes:parametres_duree_vie_explication'),
						'defaut' => $config['duree_vie']
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications',
				'label' => _T('commandes:notifications_cfg_titre')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'exp1',
						'texte' => _T('commandes:notifications_explication')
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'activer',
						'label' => _T('commandes:notifications_activer_label'),
						'explication' => _T('commandes:notifications_activer_explication'),
						'defaut' => $config['activer']
					)
				)
			)
		),	
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications_parametres',
				'label' => _T('commandes:notifications_parametres'),
				'afficher_si' => '@activer@ == "on"',
			),
			'saisies' => array(
				array(
					'saisie' => 'checkbox',
					'options' => array(
						'nom' => 'quand',
						'label' => _T('commandes:notifications_quand_label'),
						'explication' => _T('commandes:notifications_quand_explication'),
						'datas' => $statuts,
						'defaut' => $config['quand']
					)
				),
				
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'expediteur',
						'label' => _T('commandes:notifications_expediteur_label'),
						'explication' => _T('commandes:notifications_expediteur_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['expediteur'],
						'datas' => $choix_expediteurs
					)
				),
				
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_webmaster',
						'label' => _T('commandes:notifications_expediteur_webmaster_label'),
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
						'label' => _T('commandes:notifications_expediteur_administrateur_label'),
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
						'label' => _T('commandes:notifications_expediteur_email_label'),
						'defaut' => $config['expediteur_email'],
						'afficher_si' => '@expediteur@ == "email"',
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'vendeur',
						'label' => _T('commandes:notifications_vendeur_label'),
						'explication' => _T('commandes:notifications_vendeur_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['vendeur'],
						'datas' => array(
							'webmaster' => _T('commandes:notifications_vendeur_choix_webmaster'),
							'administrateur' => _T('commandes:notifications_vendeur_choix_administrateur'),
							'email' => _T('commandes:notifications_vendeur_choix_email')
						)
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'vendeur_webmaster',
						'label' => _T('commandes:notifications_vendeur_webmaster_label'),
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
						'label' => _T('commandes:notifications_vendeur_administrateur_label'),
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
						'label' => _T('commandes:notifications_vendeur_email_label'),
						'explication' => _T('commandes:notifications_vendeur_email_explication'),
						'defaut' => $config['vendeur_email'],
						'afficher_si' => '@vendeur@ == "email"',
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'client',
						'label' => _T('commandes:notifications_client_label'),
						'explication' => _T('commandes:notifications_client_explication'),
						'defaut' => $config['client'],
					)
				)
			)
		)
	);
}

?>
