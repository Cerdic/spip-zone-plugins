<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_produits_saisies_dist(){
	include_spip('inc/config');
	$config = lire_config('produits') ;
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'taxe',
				'label' => _T('produits:configurer_taxe_defaut_label'),
				'explication' => _T('produits:configurer_taxe_defaut_explication'),
				'defaut' => $config['taxe'],
			),
			'verifier' => array(
				'type' => 'decimal'
			)
		),
		array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'editer_ttc',
				'label' => _T('produits:configurer_editer_ttc_label'),
				'explication' => _T('produits:configurer_editer_ttc_explication'),
				'defaut' => $config['editer_ttc'],
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'precision_ttc',
				'label' => _T('produits:configurer_precision_ttc_label'),
				'explication' => _T('produits:configurer_precision_ttc_explication'),
				'defaut' => 2,
                'afficher_si' => '@editer_ttc@ == "on"' 
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 1,
					'max' => 6,
				),
			)
		),

		array(
			'saisie' => 'oui_non',
			'options' => array(
				'nom' => 'limiter_ajout',
				'label' => _T('produits:limiter_ajout_label'),
				'explication' => _T('produits:limiter_ajout_explication'),
				'defaut' => $config['limiter_ajout'],
			)
		),
		array(
			'saisie' => 'secteur',
			'options' => array(
				'nom' => 'limiter_ident_secteur',
				'label' => _T('produits:limiter_ident_secteur_label'),
				'explication' => _T('produits:limiter_ident_secteur_explication'),
				'multiple' => 'oui',
				'defaut' => isset($config['limiter_ident_secteur']) ? $config['limiter_ident_secteur'] : '',
				'afficher_si' => '@limiter_ajout@ == "on"' 
			)
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'publier_rubriques',
				'label' => _T('produits:publier_rubriques_label'),
				'label_case' => _T('produits:publier_rubriques_label_case'),
				'explication' => _T('produits:publier_rubriques_explication'),
				'defaut' => $config['publier_rubriques'],
			)
		),
	);
}
