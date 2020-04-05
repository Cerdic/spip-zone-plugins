<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_w3css_saisies_dist(){

	$saisies = array(
		array(//1
			'saisie' => 'couleur',
			'options' => array(
				'nom' => 'theme',
				'label' => _T('w3css:choisir_theme_titre'),
				'defaut' => lire_config('w3css/theme'),
				)
		),//.1
		array(//2
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'suggestion_theme',
				'label' => _T('w3css:suggestion_theme_titre'),
				'texte' => recuperer_fond('prive/squelettes/inclure/w3css_color_picker', array(), array('ajax' => false) ),
				)
		),//.2
		array(//3
			'saisie' => 'input',
			'options' => array(
				'nom' => 'namespace',
				'label' => _T('w3css:choisir_namespace_titre'),
				'explication' => _T('w3css:choisir_namespace_texte'),
				'defaut' => lire_config('w3css/namespace'),
				)
		),//.3
		array(//4
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'extend',
				'label' => _T('w3css:activer_extend_titre'),
				'explication' => _T('w3css:activer_extend_texte'),
				'defaut' => lire_config('w3css/extend'),
				)
		),//.4			
	);

	return $saisies;
}
