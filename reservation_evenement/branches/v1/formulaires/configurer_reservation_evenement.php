
<?php



// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_reservation_evenement_saisies_dist(){

    $liste_objets=lister_tables_objets_sql();
    $statuts=array();
    $statuts_selectionnees=array();
	include_spip('inc/config');
	include_spip('inc/plugin');
	$config = lire_config('reservation_evenement',array());
	$quand=isset($config['quand'])?$config['quand']:array();
     
     //Le statuts du plugin
     foreach($liste_objets['spip_reservations']['statut_textes_instituer'] AS $statut=>$label){
         $statuts[$statut]=_T($label);
		 if(in_array($statut,$quand))$statuts_selectionnees[$statut]=_T($label);
     }
     
	$choix_expediteurs = array(
			'webmaster' => _T('reservation:notifications_expediteur_choix_webmaster'),
			'administrateur' => _T('reservation:notifications_expediteur_choix_administrateur'),
			'email' => _T('reservation:notifications_expediteur_choix_email')
	);
	
	if (defined('_DIR_PLUGIN_FACTEUR')){
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
                        'defaut'=> 'valide',
                        'cacher_option_intro' => 'on',
                        'label' => _T('reservation:label_statut_defaut'),
                        'defaut'=> $config['statut_defaut']
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
				)
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
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'envoi_differe',
						'label' => _T('reservation:notifications_envoi_differe'),
						'explication' => _T('reservation:notifications_envoi_differe_explication'),
						'cacher_option_intro' => 'on',
						'datas' => $statuts_selectionnees,
						'defaut' => $config['envoi_differe']
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
						'label' => _T('reservation:notifications_vendeur_label'),
						'explication' => _T('reservation:notifications_vendeur_explication'),
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
				)											
			)
		)
	);
}

?>