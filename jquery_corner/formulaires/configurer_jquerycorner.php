<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_jquerycorner_saisies_dist(){
	$saisies = array();
	$config = lire_config('jquerycorner');

	$saisies = array(
		array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'explication',
				'texte' => _T('jquerycorner:configurer_explication')
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fjquerycorner_nombre',
				'label' => _T('jquerycorner:legend_jquerycorner_nombre')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'nombre',
						'label' => _T('jquerycorner:label_nombre'),
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
				'nom' => 'fjquerycorner'.$i,
				'label' => _T('jquerycorner:legend_jquerycorner',array('numero'=>$i))
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'element'.$i,
						'label' => _T('jquerycorner:label_element'),
						'explication' => _T('jquerycorner:explication_element'),
						'defaut' => $config['element'.$i]
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'param'.$i,
						'label' => _T('jquerycorner:label_param'),
						'explication' => _T('jquerycorner:explication_param'),
						'defaut' => $config['param'.$i]
					)
				),
			)
		)) ;
	}

	return($saisies);
}
?>