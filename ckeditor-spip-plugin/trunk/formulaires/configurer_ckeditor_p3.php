<?php

include_spip('inc/ckeditor_constantes') ;
include_spip('inc/ckeditor_tools') ;

function formulaires_configurer_ckeditor_p3_charger_dist() {
	include_spip('inc/headers') ;
	if (_request('_cfg_reinit')) {
        	effacer_config('ckeditor') ;
		ckeditor_fix_default_values() ;
		redirige_url_ecrire('configurer_ckeditor','') ;
		return ;
	}
	ckeditor_ecrire_protectedtags() ;
	($cfg = lire_config("ckeditor")) || ($cfg = array()) ;
	$default_ce = is_array($cfg['extras_tb'])?$cfg['extras_tb']:array() ;
	foreach(liste_CHAMPS_EXTRAS() as $CE) {
		if (is_null($default_ce[$CE])) 
			$default_ce[$CE]= _CKE_CHAMPS_EXTRAS_TB_DEF;
	}
	$valeurs = array(
		'protectedtags' => $cfg['protectedtags'],
		'conversion' => array_key_exists('conversion', $cfg)?$cfg['conversion']:_CKE_CONVERSION_DEF,
		'html2spip_limite' => array_key_exists('html2spip_limite', $cfg)?$cfg['html2spip_limite']:_CKE_HTML2SPIP_LIMITE_DEF,
		'html2spip_identite' => array_key_exists('html2spip_identite', $cfg)?$cfg['html2spip_identite']:_CKE_HTML2SPIP_IDENTITE,
		'spiplinks' => array_key_exists('spiplinks', $cfg)?$cfg['spiplinks']:_CKE_SPIPLINKS_DEF,
		'insertall' => array_key_exists('insertall', $cfg)?$cfg['insertall']:_CKE_INSERTALL_DEF,
		'pastetext' => array_key_exists('pastetext', $cfg)?$cfg['pastetext']:_CKE_PASTETEXT_DEF,
		'selecteurs_public' => array_key_exists('selecteurs_public', $cfg)?$cfg['selecteurs_public']:_CKE_PUBLIC_DEF,
		'selecteurs_prive' => array_key_exists('selecteurs_prive', $cfg)?$cfg['selecteurs_prive']:_CKE_PRIVE_DEF,

		'cktoolslenlarge' => array_key_exists('cktoolslenlarge', $cfg)?$cfg['cktoolslenlarge']:_CKE_LARGE_DEF,
		'cktoolslenetroit' => array_key_exists('cktoolslenetroit', $cfg)?$cfg['cktoolslenetroit']:_CKE_ETROIT_DEF,
		'cklanguage' => array_key_exists('cklanguage', $cfg)?$cfg['cklanguage']:_CKE_LANGAGE_DEF,
		'entermode' => array_key_exists('entermode', $cfg)?$cfg['entermode']:_CKE_ENTERMODE_DEF,
		'shiftentermode' => array_key_exists('shiftentermode', $cfg)?$cfg['shiftentermode']:_CKE_SHIFTENTERMODE_DEF,
		'csssite' => array_key_exists('csssite', $cfg)?$cfg['csssite']:'',
		'contextes' => array_key_exists('contextes', $cfg)?$cfg['contextes']:'',
		'siteurl' => array_key_exists('siteurl', $cfg)?$cfg['siteurl']:''
	) ;

	$cvt_options = array(
		'aucune' => _T('ckeditor:aucune_conversion'),
		'partielle' => _T('ckeditor:conversion_partielle_vers_spip'),
	) ;
	
	if (find_in_path('lib/'._CKE_HTML2SPIP_VERSION)) {
		$cvt_options['complete'] = _T('ckeditor:utiliser_html2spip') ;
		$cvt_explication = array(
			'saisie' => 'explication_spip',
			'options' => array(
				'nom'=>'explication_conversion',
				'texte' => _T('ckeditor:html2spip_detecte'))
			) ;
	} else {
		$cvt_explication = array(
			'saisie' => 'explication_spip',
			'options' => array(
				'nom'=>'explication_conversion',
				'texte' => _T('ckeditor:aide_html2spip_non_trouvee'))
			) ;
	}

	$valeurs['saisies_cke_page3'] = array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_conversion',
				'label' => _T('ckeditor:options_conversion'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'protectedtags',
						'label' => _T('ckeditor:balises_spip_autoriser')
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'html2spip_explication',
						'texte' => _T('ckeditor:utiliser_html2spip_descriptif')
					)
				),
				$cvt_explication,
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'conversion',
						'cacher_option_intro' => 'oui',
						'label' => _T('ckeditor:options_html2spip'),
						'datas' => $cvt_options
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'html2spip_identite',
						'label' => _T('ckeditor:html2spip_identite')
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'html2spip_limite',
						'label_case' => _T('ckeditor:html2spip_limite')
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'pastetext',
						'label_case' => _T('ckeditor:forcer_copie_comme_texte')
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'entermode',
						'label' => _T('ckeditor:entermode'),
						'datas' => array(
							'ENTER_P' => _T('ckeditor:enter_p'),
							'ENTER_BR' => _T('ckeditor:enter_br'),
							'ENTER_DIV' => _T('ckeditor:enter_div')
						)
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'shiftentermode',
						'label' => _T('ckeditor:shiftentermode'),
						'datas' => array(
							'ENTER_P' => _T('ckeditor:enter_p'),
							'ENTER_BR' => _T('ckeditor:enter_br'),
							'ENTER_DIV' => _T('ckeditor:enter_div')
						)
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'explication_entermode',
						'texte' => "-* "._T('ckeditor:explique_p')."\n-* "._T('ckeditor:explique_div')
					)
				)
			)			
		),
		array(	'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_activation',
				'label' => _T('ckeditor:utiliser_ckeditor_avec'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(	'saisie' => 'explication_spip',
					'options' => array(
						'nom' => 'explication_selecteurs',
						'texte' => _T('ckeditor:aide_selecteurs')
					)),
				array(	'saisie' => 'textarea',
					'options' => array(
						'nom' => 'selecteurs_public',
						'label' => _T('ckeditor:selecteurs_espace_public'),
						'rows' => 6
					)),
				array(	'saisie' => 'textarea',
					'options' => array(
						'nom' => 'selecteurs_prive',
						'label' => _T('ckeditor:selecteurs_espace_prive'),
						'rows' => 6
					)),
			)
		),
		array(	'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_spip',
				'label' => _T('ckeditor:options_spip'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(	'saisie' => 'case',
					'options' => array(
						'nom' => 'spiplinks',
						'label_case' => _T('ckeditor:autoriser_liens_spip')
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'insertall',
						'label_case' => _T('ckeditor:autoriser_insertion_documents')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'siteurl',
						'label' => _T('ckeditor:url_site'),
						'explication' => _T('ckeditor:normalement_detectee').lire_meta('adresse_site')
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_gui',
				'label' => _T('ckeditor:options_gui'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'cktoolslenetroit',
						'label' => _T('ckeditor:etroit')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'cktoolslenlarge',
						'label' => _T('ckeditor:large')
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'cklanguage',
						'label' => _T('ckeditor:langue_ckeditor'),
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'auto'=>'Auto détection',
							'af'=>'Afrikaans',
							'ar'=>'Arabic',
							'bg'=>'Bulgarian',
							'bn'=>'Bengali/Bangla',
							'bs'=>'Bosnian',
							'ca'=>'Catalan',
							'cs'=>'Czech',
							'da'=>'Danish',
							'de'=>'German',
							'el'=>'Greek',
							'en'=>'English',
							'en-au'=>'English (Australia)',
							'en-ca'=>'English (Canadian)',
							'en-uk'=>'English (United Kingdom)',
							'eo'=>'Esperanto',
							'es'=>'Spanish',
							'et'=>'Estonian',
							'eu'=>'Basque',
							'fa'=>'Persian',
							'fi'=>'Finnish',
							'fo'=>'Faroese',
							'fr'=>'French',
							'fr-ca'=>'French (Canada)',
							'gl'=>'Galician',
							'gu'=>'Gujarati',
							'he'=>'Hebrew',
							'hi'=>'Hindi',
							'hr'=>'Croatian',
							'hu'=>'Hungarian',
							'is'=>'Icelandic',
							'it'=>'Italian',
							'ja'=>'Japanese',
							'km'=>'Khmer',
							'ko'=>'Korean',
							'lt'=>'Lithuanian',
							'lv'=>'Latvian',
							'mn'=>'Mongolian',
							'ms'=>'Malay',
							'nb'=>'Norwegian Bokmal',
							'nl'=>'Dutch',
							'no'=>'Norwegian',
							'pl'=>'Polish',
							'pt'=>'Portuguese (Portugal)',
							'pt-br'=>'Portuguese (Brazil)',
							'ro'=>'Romanian',
							'ru'=>'Russian',
							'sk'=>'Slovak',
							'sl'=>'Slovenian',
							'sr'=>'Serbian (Cyrillic)',
							'sr-latn'=>'Serbian (Latin)',
							'sv'=>'Swedish',
							'th'=>'Thai',
							'tr'=>'Turkish',
							'uk'=>'Ukrainian',
							'vi'=>'Vietnamese',
							'zh'=>'Chinese Traditional',
							'zh-cn'=>'Chinese Simplified'
						)
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_css',
				'label' => _T('ckeditor:options_css'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'csssite',
						'label' => _T('ckeditor:css_site')
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'explication_csssite',
						'texte' => _T('ckeditor:aide_css_site')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'contextes',
						'label' => _T('ckeditor:liste_de_contextes')
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'explication_contextes',
						'texte' => _T('ckeditor:aide_contextes')
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'nettoyage_ckeditor',
				'label' => _T('ckeditor:nettoyage_de_ckeditor'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'radio',
					'options' => array(
						'nom' => 'nettoyage',
						'datas' => array(
							'sources'=>_T('ckeditor:les_sources'),
							'exemples'=>_T('ckeditor:les_exemples'),
							'tout'=>_T('ckeditor:les_sources_et_les_exemples')
						)
					)
				),
				array(
					'saisie' => 'bouton',
					'options' => array(
						'nom' => 'nettoyer',
						'type' => 'submit',
						'texte' => _T('ckeditor:nettoyer')
					)
				)
			)
		)

	) ;
	return $valeurs ;
}

function formulaires_configurer_ckeditor_p3_verifier_dist() {
	$result = array() ;
	if (_request("protectedtags") && ! preg_match("~^(/?\w+)(\s*;\s*/?\w+)*$~", _request("protectedtags"))) $result["protectedtags"] = _T("ckeditor:err_mauvaise_syntaxe_de_tag") ;
	if (_request("cktoolslenlarge") && ! ctype_digit(_request("cktoolslenlarge"))) $result["cktoolslenlarge"] = _T("ckeditor:err_la_largeur_ecran_large_doit_etre_numerique") ;
	if (_request("cktoolslenetroit") && ! ctype_digit(_request("cktoolslenetroit"))) $result["cktoolslenetroit"] = _T("ckeditor:err_la_largeur_ecran_etroit_doit_etre_numerique") ;
	if (! preg_match("~^ENTER_(DIV|BR|P)$~", _request("entermode"))) $result["entermode"] = _T("ckeditor:err_mauvais_mode_entree") ;
	if (! preg_match("~^ENTER_(DIV|BR|P)$~", _request("shiftentermode"))) $result["shiftentermode"] = _T("ckeditor:err_mauvais_mode_shiftentree") ;
	if (! preg_match("~^([a-z]{2}|auto)$~", _request("cklanguage"))) $result["cklanguage"] = _T("ckeditor:err_mauvais_langage") ;

	if (count($result)) {
		$result['message_erreur'] = _T('ckeditor:ck_ko').
			"<ul>\n".
				(count($result)?'<li>'.join("</li>\n<li>", $result)."</li>\n":'').
			"</ul>\n" ;
	}
		
	return $result ;
}

function ck_set($val) {
	return is_null($val)?false:$val ;
}

function formulaires_configurer_ckeditor_p3_traiter_dist() {
	if (_request('_cfg_delete')) {
		$valeurs = formulaires_configurer_ckeditor_p3_charger_dist() ;
		foreach($valeurs as $cle =>$valeur) {
			effacer_config('ckeditor/'.$cle) ;
			unset($_POST[$cle]) ;
		}
		return array('message_ok' => _T('ckeditor:ck_delete')) ;
	} else if (_request('nettoyer')) {
		$cke_path = dirname(_CKE_JS) ;
		$sources = $cke_path.'/_source' ;
		$exemples = $cke_path.'/_samples' ;
		switch (_request('nettoyage')) {
			case 'sources':
				if (is_dir($sources)) {
					ckeditor_efface_repertoire($sources) ;
					return array('message_ok' => _T('ckeditor:ok_nettoyage_des_sources')) ;
				} else {
					return array('message_ok', _T('ckeditor:ok_repertoire_introuvable',array('repertoire'=>$sources))) ;
				}
				break ;
			case 'exemples':
				if (is_dir($exemples)) {
					ckeditor_efface_repertoire($exemples) ;
					return array('message_ok' => _T('ckeditor:ok_nettoyage_des_exemples')) ;
				} else {
					return array('message_ok', _T('ckeditor:ok_repertoire_introuvable',array('repertoire'=>$exemples))) ;
				}
				break ;
			case 'tout':
				$result = array() ;
				if (is_dir($sources)) {
					ckeditor_efface_repertoire($sources) ;
					$result[] = _T('ckeditor:ok_nettoyage_des_sources') ;
				} else {
					$result[] = _T('ckeditor:ok_repertoire_introuvable',array('repertoire'=>$sources)) ;
				}
				if (is_dir($exemples)) {
					ckeditor_efface_repertoire($exemples) ;
					$result[] = _T('ckeditor:ok_nettoyage_des_sources') ;
				} else {
					$result[] = _T('ckeditor:ok_repertoire_introuvable',array('repertoire'=>$exemples)) ;
				}
				return array('message_ok' => join("<br/>\n",$result)) ;
				break ;
			default: 
				return array('message_erreur' => _T('ckeditor:ko_nettoyage_indetermine')) ;
				break ;
		}
	} else {
		foreach(preg_split('~\s*;\s*~', _request("protectedtags")) as $tag) {
			$tag = trim($tag) ;
			$tag_closed = false ;
			if (preg_match('~^/(.*)$~', trim($tag), $m)) {
				$tag = $m[1] ;
				$tag_closed = true ;
			}
			$type = '' ;
			if (preg_match('~(.+)(xx|XX)$~', $tag, $m)) {
				$tag = $m[1] ;
				if ($m[2] == 'xx') {
					$type = 'num-facultatif' ;
				}
				if ($m[2] == 'XX') {
					$type = 'num-obligatoire' ;
				}
			}
			if (!is_array(lire_config("ckeditor/tags/".$tag)) || $tag_closed) {
				ecrire_config("ckeditor/tags/".$tag."/fermante", $tag_closed) ;
			}
			if (!$tag_closed) {
				ecrire_config("ckeditor/tags/".$tag."/type",$type) ;
			}
		}
		ecrire_config("ckeditor/conversion", _request("conversion")) ;
		ecrire_config("ckeditor/html2spip_limite", (bool) _request("html2spip_limite")) ;
		ecrire_config("ckeditor/html2spip_identite", _request("html2spip_identite")) ;
		ecrire_config("ckeditor/spiplinks", (bool) _request("spiplinks")) ;
		ecrire_config("ckeditor/insertall",(bool) _request("insertall")) ;
		ecrire_config("ckeditor/pastetext",(bool) _request("pastetext")) ;
		ecrire_config("ckeditor/selecteurs_public", _request("selecteurs_public")) ;
		ecrire_config("ckeditor/selecteurs_prive", _request("selecteurs_prive")) ;

		ecrire_config("ckeditor/cktoolslenlarge", _request("cktoolslenlarge")) ;
		ecrire_config("ckeditor/cktoolslenetroit", _request("cktoolslenetroit")) ;
		ecrire_config("ckeditor/cklanguage", _request("cklanguage")) ;
		ecrire_config("ckeditor/entermode", _request("entermode")) ;
		ecrire_config("ckeditor/shiftentermode", _request("shiftentermode")) ;
		ecrire_config("ckeditor/csssite", _request("csssite")) ;
		ecrire_config("ckeditor/contextes", _request("contextes")) ;
		ecrire_config("ckeditor/siteurl", _request("siteurl")) ;
		return array('message_ok' => _T('ckeditor:ck_ok')) ;
	}
}

 ?>
