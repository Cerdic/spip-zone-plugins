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
		'crayons' => array_key_exists('crayons', $cfg)?$cfg['crayons']:_CKE_CRAYONS_DEF,
		'forums' => array_key_exists('forums', $cfg)?$cfg['forums']:_CKE_FORUMS_DEF,
		'cisf' => array_key_exists('cisf', $cfg)?$cfg['cisf']:_CKE_CISF_DEF,
		'champs_extras' => array_key_exists('champs_extras', $cfg)?$cfg['champs_extras']:array(),
		'crayons_tb' => array_key_exists('crayons_tb', $cfg)?$cfg['crayons_tb']:_CKE_CRAYONS_TB_DEF,
		'forums_tb' => array_key_exists('forums_tb', $cfg)?$cfg['forums_tb']:_CKE_FORUMS_TB_DEF,
		'extras_tb' => $default_ce,
		'cisf_tb' => array_key_exists('cisf_tb', $cfg)?$cfg['cisf_tb']:_CKE_CISF_TB_DEF,
		'partie_publique' => array_key_exists('partie_publique', $cfg)?$cfg['partie_publique']:_CKE_PARTIE_PUBLIQUE_DEF,
		'class_publique' => array_key_exists('class_publique', $cfg)?$cfg['class_publique']:_CKE_CLASS_PUBLIQUE_DEF,
		'publique_tb' => array_key_exists('publique_tb', $cfg)?$cfg['publique_tb']:_CKE_PUBLIQUE_TB_DEF,
		'partie_privee' => array_key_exists('partie_privee', $cfg)?$cfg['partie_privee']:_CKE_PARTIE_PRIVE_DEF,
		'class_privee' => array_key_exists('class_privee', $cfg)?$cfg['class_privee']:_CKE_CLASS_PRIVE_DEF,
		'prive_tb' => array_key_exists('prive_tb', $cfg)?$cfg['prive_tb']:_CKE_PRIVE_TB_DEF,


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
		//$cvt_explication = _T('ckeditor:html2spip_detecte') ;
	} else {
		$cvt_explication = array(
			'saisie' => 'explication_spip',
			'options' => array(
				'nom'=>'explication_conversion',
				'texte' => _T('ckeditor:aide_html2spip_non_trouvee'))
			) ;
		//$cvt_explication= _T('ckeditor:aide_html2spip_non_trouvee') ;
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
						'nom' => 'convertion',
						//'explication' => $cvt_explication,
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
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_activation',
				'label' => _T('ckeditor:utiliser_ckeditor_avec'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'crayons',
						'label_case' => _T('ckeditor:les_crayons')
					)
				),
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'pliable' => 'oui',
						'plie' => 'oui',
						'nom' => 'active_crayon',
						'li_class' => 'niveau2',
						'label' => _T('ckeditor:options_crayons')
					),
					'saisies' => array(
						array(
							'saisie' => 'radio',
							'options' => array(
								'nom' => 'crayons_tb',
								'label' => _T('ckeditor:toolbar'),
								'datas' => array(
									'Basic' => _T('ckeditor:tb_basic') ,
									'Full' =>_T('ckeditor:tb_full')
								)
							)
						)
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'forums',
						'label_case' => _T('ckeditor:les_forums')
					)
				),
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'pliable' => 'oui',
						'plie' => 'oui',
						'nom' => 'active_forums',
						'li_class' => 'niveau2',
						'label' => _T('ckeditor:options_forums')
					),
					'saisies' => array(
						array(
							'saisie' => 'radio',
							'options' => array(
								'nom' => 'forums_tb',
								'label' => _T('ckeditor:toolbar'),
								'datas' => array(
									'Basic' => _T('ckeditor:tb_basic') ,
									'Full' =>_T('ckeditor:tb_full')
								)
							)
						)
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'cisf',
						'label_case' => _T('ckeditor:cisf')
					)
				),
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'pliable' => 'oui',
						'plie' => 'oui',
						'nom' => 'active_cisf',
						'li_class' => 'niveau2',
						'label' => _T('ckeditor:options_cisf')
					),
					'saisies' => array(
						array(
							'saisie' => 'radio',
							'options' => array(
								'nom' => 'cisf_tb',
								'label' => _T('ckeditor:toolbar'),
								'datas' => array(
									'Basic' => _T('ckeditor:tb_basic') ,
									'Full' =>_T('ckeditor:tb_full')
								)
							)
						)
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'partie_publique',
						'label_case' => _T('ckeditor:partie_publique')
					)
				),
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'pliable' => 'oui',
						'plie' => 'oui',
						'nom' => 'active_publique',
						'li_class' => 'niveau2',
						'label' => _T('ckeditor:options_publique')
					),
					'saisies' => array(
						array(
							'saisie' => 'explication',
							'options' => array(
								'nom' => 'explication_class_publique',
								'texte' => _T('ckeditor:aide_class_css_formulaire')
							)
						),
						array(
							'saisie' => 'input',
							'options' => array(
								'nom' => 'class_publique',
								'label' => _T('ckeditor:class_partie_publique')
							)
						),
						array(
							'saisie' => 'radio',
							'options' => array(
								'nom' => 'publique_tb',
								'label' => _T('ckeditor:toolbar'),
								'datas' => array(
									'Basic' => _T('ckeditor:tb_basic') ,
									'Full' =>_T('ckeditor:tb_full')
								)
							)
						)
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'partie_privee',
						'label_case' => _T('ckeditor:partie_privee')
					)
				),
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'pliable' => 'oui',
						'plie' => 'oui',
						'nom' => 'active_privee',
						'li_class' => 'niveau2',
						'label' => _T('ckeditor:options_privee')
					),
					'saisies' => array(
						array(
							'saisie' => 'explication',
							'options' => array(
								'nom' => 'explication_class_publique',
								'texte' => _T('ckeditor:aide_class_css_formulaire')
							)
						),
						array(
							'saisie' => 'input',
							'options' => array(
								'nom' => 'class_privee',
								'label' => _T('ckeditor:class_partie_privee')
							)
						),
						array(
							'saisie' => 'radio',
							'options' => array(
								'nom' => 'prive_tb',
								'label' => _T('ckeditor:toolbar'),
								'datas' => array(
									'Basic' => _T('ckeditor:tb_basic') ,
									'Full' =>_T('ckeditor:tb_full')
								)
							)
						)
					)
				)

			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'options_spip',
				'label' => _T('ckeditor:options_spip'),
				'pliable' => 'oui',
				'plie' => 'oui'
			),
			'saisies' => array(
				array(
					'saisie' => 'case',
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
'af'=>'Afrikaans','ar'=>'Arabic','bg'=>'Bulgarian','bn'=>'Bengali/Bangla','bs'=>'Bosnian','ca'=>'Catalan','cs'=>'Czech','da'=>'Danish','de'=>'German','el'=>'Greek','en'=>'English','en-au'=>'English (Australia)','en-ca'=>'English (Canadian)','en-uk'=>'English (United Kingdom)','eo'=>'Esperanto','es'=>'Spanish','et'=>'Estonian','eu'=>'Basque','fa'=>'Persian','fi'=>'Finnish','fo'=>'Faroese','fr'=>'French','fr-ca'=>'French (Canada)','gl'=>'Galician','gu'=>'Gujarati','he'=>'Hebrew','hi'=>'Hindi','hr'=>'Croatian','hu'=>'Hungarian','is'=>'Icelandic','it'=>'Italian','ja'=>'Japanese','km'=>'Khmer','ko'=>'Korean','lt'=>'Lithuanian','lv'=>'Latvian','mn'=>'Mongolian','ms'=>'Malay','nb'=>'Norwegian Bokmal','nl'=>'Dutch','no'=>'Norwegian','pl'=>'Polish','pt'=>'Portuguese (Portugal)','pt-br'=>'Portuguese (Brazil)','ro'=>'Romanian','ru'=>'Russian','sk'=>'Slovak','sl'=>'Slovenian','sr'=>'Serbian (Cyrillic)','sr-latn'=>'Serbian (Latin)','sv'=>'Swedish','th'=>'Thai','tr'=>'Turkish','uk'=>'Ukrainian','vi'=>'Vietnamese','zh'=>'Chinese Traditional','zh-cn'=>'Chinese Simplified'
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
			ecrire_config('ckeditor/'.$cle, $valeur) ;
			$_GET[$cle] = $valeur ;
		}
		return array('message_ok' => _T('ckeditor:ck_delete')) ;
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
		ecrire_config("ckeditor/html2spip_limite", _request("html2spip_limite")) ;
		ecrire_config("ckeditor/html2spip_identite", _request("html2spip_identite")) ;
		ecrire_config("ckeditor/spiplinks", (bool) _request("spiplinks")) ;
		ecrire_config("ckeditor/insertall",(bool) _request("insertall")) ;
		ecrire_config("ckeditor/crayons", (bool) _request("crayons")) ;
		ecrire_config("ckeditor/forums", (bool) _request("forums")) ;
		ecrire_config("ckeditor/cisf", _request("cisf")) ;
		ecrire_config("ckeditor/crayons_tb", _request("crayons_tb")) ;
		ecrire_config("ckeditor/forums_tb", _request("forums_tb")) ;
		ecrire_config("ckeditor/cisf_tb", _request("cisf_tb")) ;
		ecrire_config("ckeditor/champs_extras", _request("champs_extras")) ;
		ecrire_config("ckeditor/extras_tb", _request("extras_tb")) ;
		ecrire_config("ckeditor/partie_publique", (bool) _request("partie_publique")) ;
		ecrire_config("ckeditor/class_publique", _request("class_publique")) ;
		ecrire_config("ckeditor/publique_tb", _request("publique_tb")) ;
		ecrire_config("ckeditor/partie_prive", (bool) _request("partie_prive")) ;
		ecrire_config("ckeditor/class_prive", _request("class_prive")) ;
		ecrire_config("ckeditor/prive_tb", _request("prive_tb")) ;
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
