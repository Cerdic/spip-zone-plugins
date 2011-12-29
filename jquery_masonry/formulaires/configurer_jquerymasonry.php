<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_jquerymasonry_saisies_dist(){
	$saisies = array();
	$config = lire_config('jquerymasonry');

	$saisies = array(
		array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'explication',
				'texte' => _T('jquerymasonry:configurer_explication')
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fjquerymasonry_nombre',
				'label' => _T('jquerymasonry:legend_jquerymasonry_nombre')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'nombre',
						'label' => _T('jquerymasonry:label_nombre'),
						'obligatoire' => 'oui',
						'defaut' => $config['nombre']
					),
					'verifier' => array(
						'type' => 'entier',
						'options' => array(
							'min' => 1,
							'max' => 10
						)
					)
				)
			)
		)
	);

	for($i=1; $i<=$config['nombre']; $i++) {
		array_push( $saisies, array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fjquerymasonry'.$i,
				'label' => _T('jquerymasonry:legend_jquerymasonry',array('numero'=>$i))
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'container'.$i,
						'label' => _T('jquerymasonry:label_container'),
						'explication' => _T('jquerymasonry:explication_container'),
						'defaut' => $config['container'.$i]
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'items'.$i,
						'label' => _T('jquerymasonry:label_items'),
						'explication' => _T('jquerymasonry:explication_items'),
						'defaut' => $config['items'.$i]
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'largeur'.$i,
						'label' => _T('jquerymasonry:label_largeur'),
						'explication' => _T('jquerymasonry:explication_largeur'),
						'defaut' => $config['largeur'.$i]
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'marge'.$i,
						'label' => _T('jquerymasonry:label_marge'),
						'explication' => _T('jquerymasonry:explication_marge'),
						'defaut' => $config['marge'.$i]
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'multicolonne'.$i,
						'label' => _T('jquerymasonry:label_multicolonne'),
						'explication' => _T('jquerymasonry:explication_multicolonne'),
						'defaut' => $config['multicolonne'.$i]
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'animation'.$i,
						'label' => _T('jquerymasonry:label_animation'),
						'explication' => _T('jquerymasonry:explication_animation'),
						'defaut' => $config['animation'.$i]
					)
				)
			)
		)) ;
	}

	return($saisies);
}
?>