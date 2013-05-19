<?php
/**
 * Plugin Manuel du site
 *
 * Formulaire de configuration du plugin
 * 
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_manuelsite_saisies_dist(){
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	$config = lire_config('manuelsite',array('id_article'=>null,'cacher_public'=>'','intro'=>'','email'=>'','afficher_bord_gauche'=>'','largeur'=>'300','background_color'=>''));
	
	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fcontenu_manuelsite',
				'label' => _T('manuelsite:legende_contenu')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'id_article',
						'label' => _T('manuelsite:label_id_article'),
						'explication' => _T('manuelsite:explication_id_article'),
						'obligatoire' => 'oui',
						'defaut' => $config['id_article']
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'cacher_public',
						'label' => _T('manuelsite:label_cacher_public'),
						'explication' => _T('manuelsite:explication_cacher_public'),
						'defaut' => $config['cacher_public']
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'intro',
						'label' => _T('manuelsite:label_intro'),
						'explication' => _T('manuelsite:explication_intro'),
						'class' => 'porte_plume_partout',
						'defaut' => $config['intro']
					)
				),
				array(
					'saisie' => 'email',
					'options' => array(
						'nom' => 'email',
						'label' => _T('manuelsite:label_email'),
						'explication' => _T('manuelsite:explication_email'),
						'defaut' => $config['email']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'formu',
						'label' => _T('manuelsite:label_formu'),
						'explication' => _T('manuelsite:explication_formu'),
						'defaut' => $config['formu']
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fapparence_manuelsite',
				'label' => _T('manuelsite:legende_apparence')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'afficher_bord_gauche',
						'label' => _T('manuelsite:label_afficher_bord_gauche'),
						'explication' => _T('manuelsite:explication_afficher_bord_gauche'),
						'defaut' => $config['afficher_bord_gauche']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'largeur',
						'label' => _T('manuelsite:label_largeur'),
						'explication' => _T('manuelsite:explication_largeur'),
						'defaut' => $config['largeur'],
						'afficher_si' => '@afficher_bord_gauche@ == "on"' 
					)
				),
				array(
					'saisie' => 'couleur',
					'options' => array(
						'nom' => 'background_color',
						'label' => _T('manuelsite:label_background_color'),
						'explication' => _T('manuelsite:explication_background_color'),
						'defaut' => $config['background_color'],
						'afficher_si' => '@afficher_bord_gauche@ == "on"' 
					)
				)
			)
		)
	);

}

?>