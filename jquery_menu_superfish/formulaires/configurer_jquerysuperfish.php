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
                  'afficher_si' => '@menu_hori@ == "on"',
						'obligatoire' => 'oui',
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
					),
					'verifier' => array(
					   'type' => 'entier',
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
                  'afficher_si' => '@menu_vert@ == "on"',
						'obligatoire' => 'oui',
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
					),
					'verifier' => array(
					   'type' => 'entier',
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
                  'afficher_si' => '@menu_navbar@ == "on"',
						'obligatoire' => 'oui',
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
					),
					'verifier' => array(
					   'type' => 'entier',
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
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fmenu_supersubs',
				'label' => _T('jquerysuperfish:legend_supersubs')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'supersubs',
						'label' => _T('jquerysuperfish:label_supersubs'),
						'explication' => _T('jquerysuperfish:explication_supersubs'),
						'defaut' => $config['supersubs']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'supersubs_minwidth',
						'label' => _T('jquerysuperfish:label_supersubs_minwidth'),
						'explication' => _T('jquerysuperfish:explication_supersubs_minwidth'),
						'defaut' => $config['supersubs_minwidth'],
                  'afficher_si' => '@supersubs@ == "on"',
						'obligatoire' => 'oui',
					),
					'verifier' => array(
					   'type' => 'entier',
						'options' => array(
							'min' => '1',
						)
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'supersubs_maxwidth',
						'label' => _T('jquerysuperfish:label_supersubs_maxwidth'),
						'explication' => _T('jquerysuperfish:explication_supersubs_maxwidth'),
						'defaut' => $config['supersubs_maxwidth'],
                  'afficher_si' => '@supersubs@ == "on"',
						'obligatoire' => 'oui',
					),
					'verifier' => array(
					   'type' => 'entier',
						'options' => array(
							'min' => '1',
						)
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'supersubs_extrawidth',
						'label' => _T('jquerysuperfish:label_supersubs_extrawidth'),
						'explication' => _T('jquerysuperfish:explication_supersubs_extrawidth'),
						'defaut' => $config['supersubs_extrawidth'],
                  'afficher_si' => '@supersubs@ == "on"',
						'obligatoire' => 'oui',
					),
					'verifier' => array(
					   'type' => 'entier',
						'options' => array(
							'min' => '1',
						)
					)
				),
			)
		)
	);
}

function formulaires_configurer_jquerysuperfish_verifier(){

	$erreurs = array();
	include_spip('inc/saisies');
	$erreurs = saisies_verifier(formulaires_configurer_jquerysuperfish_saisies_dist());

	if (_request('supersubs_maxwidth') < _request('supersubs_minwidth'))
		$erreurs['supersubs_maxwidth'] = _T('jquerysuperfish:erreur_min_max');

	if ($erreurs and !isset($erreurs['message_erreur']))
		$erreurs['message_erreur'] = _T('jquerysuperfish:erreur_generique');
   return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

?>