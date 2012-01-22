<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_jquerysuperfish_saisies_dist(){
	$config = lire_config('jquerysuperfish');

	return array(
		array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'explication',
				'texte' => _T('jquerysuperfish:configurer_explication')
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fmenu_hori',
				'label' => _T('jquerysuperfish:legend_menu',array('type'=>'horizontal'))
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'menu_hori',
						'label' => _T('jquerysuperfish:label_menu',array('type'=>'horizontal')),
						'explication' => _T('jquerysuperfish:explication_menu',array('type'=>'horizontal')),
						'defaut' => $config['menu_hori']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'classe_hori',
						'label' => _T('jquerysuperfish:label_classe'),
						'explication' => _T('jquerysuperfish:explication_classe'),
						'defaut' => $config['classe_hori'],
                  'afficher_si' => '@menu_hori@ == "on"' 
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'animation_hori',
						'label' => _T('jquerysuperfish:label_animation'),
						'explication' => _T('jquerysuperfish:explication_animation'),
						'defaut' => $config['animation_hori'],
                  'afficher_si' => '@menu_hori@ == "on"' 
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'delai_hori',
						'label' => _T('jquerysuperfish:label_delai'),
						'explication' => _T('jquerysuperfish:explication_delai'),
						'defaut' => $config['delai_hori'],
                  'afficher_si' => '@menu_hori@ == "on"' 
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'tester_hori',
						'texte' => _T('jquerysuperfish:texte_tester',
										  array('lien'=>url_de_base().'/spip.php?page=demo/jquerysuperfish&amp;classe_menu='.$config['classe_hori'])),
                  'afficher_si' => '@menu_hori@ == "on"' 
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fmenu_vert',
				'label' => _T('jquerysuperfish:legend_menu',array('type'=>'vertical'))
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'menu_vert',
						'label' => _T('jquerysuperfish:label_menu',array('type'=>'vertical')),
						'explication' => _T('jquerysuperfish:explication_menu',array('type'=>'vertical')),
						'defaut' => $config['menu_vert']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'classe_vert',
						'label' => _T('jquerysuperfish:label_classe'),
						'explication' => _T('jquerysuperfish:explication_classe'),
						'defaut' => $config['classe_vert'],
                  'afficher_si' => '@menu_vert@ == "on"' 
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'animation_vert',
						'label' => _T('jquerysuperfish:label_animation'),
						'explication' => _T('jquerysuperfish:explication_animation'),
						'defaut' => $config['animation_vert'],
                  'afficher_si' => '@menu_vert@ == "on"' 
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'delai_vert',
						'label' => _T('jquerysuperfish:label_delai'),
						'explication' => _T('jquerysuperfish:explication_delai'),
						'defaut' => $config['delai_vert'],
                  'afficher_si' => '@menu_vert@ == "on"' 
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'tester_vert',
						'texte' => _T('jquerysuperfish:texte_tester',
										  array('lien'=>url_de_base().'/spip.php?page=demo/jquerysuperfish&amp;classe_menu='.$config['classe_vert'])),
                  'afficher_si' => '@menu_vert@ == "on"' 
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fmenu_navbar',
				'label' => _T('jquerysuperfish:legend_menu',array('type'=>'navbar'))
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'menu_navbar',
						'label' => _T('jquerysuperfish:label_menu',array('type'=>'navbar')),
						'explication' => _T('jquerysuperfish:explication_menu',array('type'=>'navbar')),
						'defaut' => $config['menu_navbar']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'classe_navbar',
						'label' => _T('jquerysuperfish:label_classe'),
						'explication' => _T('jquerysuperfish:explication_classe'),
						'defaut' => $config['classe_navbar'],
                  'afficher_si' => '@menu_navbar@ == "on"' 
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'animation_navbar',
						'label' => _T('jquerysuperfish:label_animation'),
						'explication' => _T('jquerysuperfish:explication_animation'),
						'defaut' => $config['animation_navbar'],
                  'afficher_si' => '@menu_navbar@ == "on"' 
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'delai_navbar',
						'label' => _T('jquerysuperfish:label_delai'),
						'explication' => _T('jquerysuperfish:explication_delai'),
						'defaut' => $config['delai_navbar'],
                  'afficher_si' => '@menu_navbar@ == "on"' 
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'tester_navbar',
						'texte' => _T('jquerysuperfish:texte_tester',
										  array('lien'=>url_de_base().'/spip.php?page=demo/jquerysuperfish&amp;classe_menu='.$config['classe_navbar'])),
                  'afficher_si' => '@menu_navbar@ == "on"' 
					)
				)
			)
		)
	);

}
?>