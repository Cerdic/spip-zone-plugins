<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_uploadhtml5_saisies_dist() {
	$uploadmaxsize = ini_get('upload_max_filesize');
	$saisies = array(
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'charger_public',
				'label' => _T('uploadhtml5:label_charger_public'),
				'label_case' => _T('uploadhtml5:case_charger_public')
			)
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'remplacer_editer_logo',
				'label' => _T('uploadhtml5:label_remplacer_editer_logo'),
				'label_case' => _T('uploadhtml5:case_remplacer_editer_logo')
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'max_file_size',
				'label' => _T('uploadhtml5:max_file_size'),
				'explication' => _T('uploadhtml5:explication_max_file_size', array('uploadmaxsize' => $uploadmaxsize))
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'max_file',
				'label' => _T('uploadhtml5:max_file'),
				'explication' => _T('uploadhtml5:explication_max_file')
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'images',
				'label' => _T('uploadhtml5:titre_fieldset_image'),
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'resizeWidth',
						'label' => _T('uploadhtml5:resizeWidth'),
						'explication' => _T('uploadhtml5:explication_resizeWidth')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'resizeHeight',
						'label' => _T('uploadhtml5:resizeHeight'),
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'resizeQuality',
						'label' => _T('uploadhtml5:resizeQuality'),
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'resizeMethod',
						'label' => _T('uploadhtml5:resizeMethod'),
						'explication' => _T('uploadhtml5:explication_resizeMethod'),
						'cacher_option_intro' => true,
						'afficher_si' => '@resizeHeight@!="" && @resizeWidth@!=""',
						'datas' => array(
							'contain' => _T('uploadhtml5:contain'),
							'crop' => _T('uploadhtml5:crop')
						)
					)
				)
			)
		)
	);

	return $saisies;
}


function formulaires_configurer_uploadhtml5_charger_dist() {
	return lire_config('uploadhtml5');
}
