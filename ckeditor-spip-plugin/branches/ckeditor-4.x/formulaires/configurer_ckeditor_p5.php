<?php

include_spip('inc/ckeditor_constantes') ;

function formulaires_configurer_ckeditor_p5_charger_dist() {
	($cfg = lire_config("ckeditor")) || ($cfg = array()) ;
	$valeurs = array(
		"formats" => array_key_exists('formats', $cfg)?$cfg['formats']:_CKE_FORMATS_DEF,
		"formatsclass" => array_key_exists('formatsclass', $cfg)?$cfg['formatsclass']:_CKE_FORMATS_CLASS_DEF,
		"webfonts" => array_key_exists('webfonts', $cfg)?$cfg['webfonts']:_CKE_WEBFONTS_DEF,
		"fontkit" => array_key_exists('fontkit', $cfg)?$cfg['fontkit']:_CKE_FONTKIT_DEF,
		"insertcsspublic" => array_key_exists('insertcsspublic', $cfg)?$cfg['insertcsspublic']:_CKE_INSERT_CSSPUBLIC_DEF,
		"insertcssprive" => array_key_exists('insertcssprive', $cfg)?$cfg['insertcssprive']:_CKE_INSERT_CSSPRIVEE_DEF,
		"styles" => array_key_exists('styles', $cfg)?$cfg['styles']:_CKE_STYLES_DEF,
		"fontsizes" => array_key_exists('fontsizes', $cfg)?$cfg['fontsizes']:_CKE_FONTSIZES_DEF,
		"liste_couleurs" => array_key_exists('liste_couleurs', $cfg)?$cfg['liste_couleurs']:null,
		"autres_couleurs" => array_key_exists('autres_couleurs', $cfg)?$cfg['autres_couleurs']:''
	) ;

	$valeurs['saisies_cke_page5'] = array(
		array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'avertissement',
				'texte' => _T('ckeditor:avertissement_css')
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_configuration_formats',
				'label' => _T('ckeditor:configuration_formats'),
				'pliable' => 'oui', 'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'formats',
						'explication' => _T('ckeditor:aide_formats'),
						'label' => _T('ckeditor:formats')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'formatsclass',
						'label' => _T('ckeditor:class_des_formats')
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'styles',
						'label' => _T('ckeditor:styles'),
						'cols' => 50, 'rows' => 10,
					)
				),
				array(
					'saisie' => 'explication_spip',
					'options' => array(
						'nom' => 'explication_styles',
						'texte' => _T('ckeditor:aide_styles')
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_configuration_des_couleurs',
				'label' => _T('ckeditor:configuration_des_couleurs'),
				'pliable' => 'oui', 'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'explication_spip',
					'options' => array(
						'nom' => 'explication_liste_couleurs',
						'texte' => _T('ckeditor:aide_couleurs')
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'liste_couleurs',
						'label' => _T('ckeditor:listes_des_couleurs_presentees'),
						'cols' => '50', 'rows' => '3',
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'autres_couleurs',
						'label_case' => _T('ckeditor:autoriser_autres_couleurs')
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_configuration_des_polices',
				'label' => _T('ckeditor:configuration_des_polices'),
				'pliable' => 'oui', 'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'explication_spip',
					'options' => array(
						'nom' => 'explication_fontsizes',
						'texte' => _T('ckeditor:aide_fontsizes')
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'fontsizes',
						'label' => _T('ckeditor:tailles_de_police'),
						'cols'=> 50, 'rows' => 4
					)
				),
				array(
					'saisie' => 'explication_spip',
					'options' => array(
						'nom' => 'explication_webfonts',
						'texte' => _T('ckeditor:aide_webfonts')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'webfonts',
						'label' => _T('ckeditor:liste_google_webfonts'),
					)
				),
				array(
					'saisie' => 'explication_spip',
					'options' => array(
						'nom' => 'explication_fontkit',
						'texte' => _T('ckeditor:aide_fontkit')
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'fontkit',
						'label_case' => _T('ckeditor:utilise_fontkit'),
					)
				),
				array(
					'saisie' => 'explication_spip',
					'options' => array(
						'nom' => 'explication_insertcsspublic',
						'texte' => _T('ckeditor:aide_inserer_la_css')
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'insertcsspublic',
						'label_case' => _T('ckeditor:inserer_la_css_public'),
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'insertcssprivee',
						'label_case' => _T('ckeditor:inserer_la_css_privee'),
					)
				),




			)
		)
	) ;

	return $valeurs ;
}

function formulaires_configurer_ckeditor_p5_verifier_dist() {
	$result = array() ;
	if (_request("formats") && ! preg_match("~^\s*\w+(\s*;\s*\w+)*\s*$~", _request("formats"))) $result["formats"] = _T("ckeditor:err_mauvaise_liste_de_formats") ;
	if (_request("formatsclass") && ! preg_match("~^\s*\w+\s*$~", _request("formatsclass"))) $result["formatsclass"] = _T("ckeditor:err_mauvaise_class_pour_formats") ;
	if (count($result)) {
		$result['message_erreur'] = _T('ckeditor:ck_ko').
			"<ul>\n".
				(count($result)?'<li>'.join("</li>\n<li>", $result)."</li>\n":'').
			"</ul>\n" ;
	}
		
	return $result ;
}

function formulaires_configurer_ckeditor_p5_traiter_dist() {
	if (_request('_cfg_delete')) {
		$valeurs = formulaires_cke_charger_dist() ;
		foreach($valeurs as $cle =>$valeur) {
			ecrire_config('ckeditor/'.$cle, $valeur) ;
			$_GET[$cle] = $valeur ;
		}
		 return array('message_ok' => _T('ckeditor:ck_delete')) ;
	} else {
		ecrire_config("ckeditor/formats", _request("formats")) ;
		ecrire_config("ckeditor/formatsclass", _request("formatsclass")) ;
		ecrire_config("ckeditor/webfonts", _request("webfonts")) ;
		ecrire_config("ckeditor/fontkit", _request("fontkit")) ;
		ecrire_config("ckeditor/insertcsspublic", _request("insertcsspublic")) ;
		ecrire_config("ckeditor/insertcssprive", _request("insertcssprive")) ;
		ecrire_config("ckeditor/styles", _request("styles")) ;
		ecrire_config("ckeditor/fontsizes", _request("fontsizes")) ;
		ecrire_config("ckeditor/liste_couleurs", _request("liste_couleurs")) ;
		ecrire_config("ckeditor/autres_couleurs", _request("autres_couleurs")) ;
		return array('message_ok' => _T('ckeditor:ck_ok')) ;
	}
}

 ?>
