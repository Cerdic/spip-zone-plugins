<?php

include_spip('inc/ckeditor_constantes') ;

function formulaires_configurer_ckeditor_charger_dist() {
	$skinsdir = @opendir($skinsdir_name = (defined(_DIR_LIB)?_DIR_LIB:_DIR_RACINE.'lib/').'ckeditor/skins') ;
	$cke_skins = array() ;
	while ($skin = @readdir($skinsdir)) {
		if (@is_dir($skinsdir_name.'/'.$skin) && @is_file($skinsdir_name.'/'.$skin.'/editor.css')) {
			$cke_skins[$skin] = ucfirst($skin) ;
		}
	}
	$saisies_cke_page1 = array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_editeur',
				'label' => _T('ckeditor:options_editeur'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'editmode',
						'label' => _T('ckeditor:mode_edition_defaut'),
						'defaut' => 'ckeditor',
						'obligatoire' => 'oui',
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'spip' => _T('ckeditor:spip_defaut'),
							'ckeditor' => _T('ckeditor:ckeditor_defaut'),
							'ckeditor-exclu' => _T('ckeditor:ckeditor_exclu')
						)
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'skin',
						'label' => _T('ckeditor:choisir_skin'),
						'defaut' => 'kama',
						'cacher_option_intro' => 'oui',
						'datas' => $cke_skins
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'taille',
						'label' => _T('ckeditor:hauteur_editeur')
					)
				),
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'pliable' => 'oui',
				'plie' => 'oui',
				'nom' => 'option_apercu',
				'label' => _T('ckeditor:options_vignettes')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'apercu',
						'label' => _T('ckeditor:utiliser_une_vignette_pour_les_images')
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'aide_apercu',
						'texte' => _T('ckeditor:aide_vignette')
					)
				)		
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'pliable' => 'oui',
				'plie' => 'oui',
				'nom' => 'options_orthographe',
				'label' => _T('ckeditor:options_orthographe')
			),
			'saisies' => array(
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'startspellcheck',
						'label_case' => _T('ckeditor:demarrer_correction_ortho')
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'spellchecklang',
						'label' => _T('ckeditor:choisir_correction_ortho'),
						'defaut' => 'fr_FR',
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'en_US' => 'English',
							'en_GB' => 'British English',
							'en_CA' => 'English Canadian',
							'da_DK' => 'Dansk',
							'nl_NL' => 'Nederlandse',
							'fi_FI' => 'Suomen',
							'fr_FR' => 'Français',
							'fr_CA' => 'Français Canadien',
							'de_DE' => 'Deutsch',
							'el_GR' => 'Ελληνικά',
							'it_IT' => 'Italiano',
							'nb_NO' => 'Norsk',
							'pt_PT' => 'Português',
							'pt_BR' => 'Português do Brasil',
							'es_ES' => 'Español',
							'sv_SE' => 'Svenska'
						)
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'pliable' => 'oui',
				'plie' => 'oui',
				'nom' => 'options_developpeur',
				'label' => _T('ckeditor:options_developpeur')
			),
			'saisies' => array(
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'ignoreversion',
						'label_case' => _T('ckeditor:ignore_mauvaise_version')
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'devtools',
						'label_case' => _T('ckeditor:affiche_les_informations_de_developpement')
					)
				)
			)
		)
	);

	($cfg = lire_config("ckeditor")) || ($cfg = array()) ;

	$valeurs = array(
                //'' => array_key_exists('', $cfg)?$cfg['']:_CKE__DEF,
		'editmode' => $cfg['editmode']?$cfg['editmode']:_CKE_EDITMODE_DEF,
		'skin' => $cfg['editmode']?$cfg['skin']:_CKE_SKIN_DEF,
		'taille' => $cfg['taille']?$cfg['taille']:_CKE_HAUTEUR_DEF,
		'apercu' => $cfg['apercu']?$cfg['apercu']:_CKE_VIGNETTE_DEF,
		'startspellcheck' => array_key_exists('startspellcheck', $cfg)?$cfg['startspellcheck']:_CKE_SCAYT_START_DEF,
		'spellchecklang' => array_key_exists('spellchecklang', $cfg)?$cfg['spellchecklang']:_CKE_SCAYT_LANG_DEF,
		'ignoreversion' => array_key_exists('ignoreversion', $cfg)?$cfg['ignoreversion']:_CKE_IGNOREVERSION_DEF,
		'devtools' => array_key_exists('devtools', $cfg)?$cfg['devtools']:_CKE_DEVTOOLS_DEF,
		'saisies_cke_page1' => $saisies_cke_page1
	) ;

	return $valeurs ;
}

function formulaires_configurer_ckeditor_verifier_dist() {
	return array() ;
}


function formulaires_configurer_ckeditor_traiter_dist() {
	print('request:'._request('startspellcheck').'<br/>') ;
	ecrire_config('ckeditor/editmode',_request('editmode'));
	ecrire_config('ckeditor/skin',_request('skin'));
	ecrire_config('ckeditor/taille',_request('taille'));
	ecrire_config('ckeditor/apercu',_request('apercu'));
	ecrire_config('ckeditor/startspellcheck',_request('startspellcheck')=='on'?'on':'off');
	ecrire_config('ckeditor/spellchecklang',_request('spellchecklang'));
	ecrire_config('ckeditor/ignoreversion',_request('ignoreversion')=='on'?'on':'off');
	ecrire_config('ckeditor/devtools',_request('devtools')=='on'?'on':'off');
	return array('message_ok' => _T('ckeditor:ck_ok')) ;
}


?>
