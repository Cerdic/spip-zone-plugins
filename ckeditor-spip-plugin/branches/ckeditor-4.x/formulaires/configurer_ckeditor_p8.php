<?php

include_spip('inc/ckeditor_constantes') ;
include_spip('inc/ckeditor_tools') ;

function array_entity_decode($array, $charset='UTF-8') {
	if (is_array($array)) {
		$result = array() ;
		foreach($array as $k => $v) {
			$result[html_entity_decode($k,ENT_NOQUOTES,$charset)] = array_entity_decode($v,$charset) ;
		}
		return $result ;
	} else {
		return html_entity_decode($array,ENT_NOQUOTES,$charset) ;
	}
}

function formulaires_configurer_ckeditor_p8_charger_dist() {
	($cfg = lire_config("ckeditor")) || ($cfg = array()) ;
	$cke_tags_def = array_entity_decode(unserialize(_CKE_TAGS_DEF),$GLOBALS['meta']['charset']) ;
	if (! is_array($cfg['tags'])) { ecrire_config('ckeditor/tags', $cfg['tags'] = $cke_tags_def) ; }
	$modele = _request('nom_modele') ? _request('nom_modele') : _request('modeles') ;
	$modele = preg_replace('~xx$~i', '' , $modele) ;
	$valeurs = array(
		'tags' => $cke_tags_def,
	/* faux champs : utilisés pour la gestion du formulaire (ie: initialisation de #ENV) */
		'intitule' => (isset($cfg['tags']) && isset($cfg['tags'][$modele]) && isset($cfg['tags'][$modele]['intitule'])?$cfg['tags'][$modele]['intitule']:''),
		'info' => (isset($cfg['tags']) && isset($cfg['tags'][$modele]) && isset($cfg['tags'][$modele]['info'])?$cfg['tags'][$modele]['info']:''),
		'_cke_nouveau_modele' => _request('_cke_nouveau_modele'),
		'_cke_edite_modele' => _request('_cke_edite_modele'),
		'nouveau_modele' => _request('nouveau_modele'),
		'modeles' => _request('_cke_edite_modele')?_request('modeles'):''
	) ;

	$modeles = array_flip(array_keys($cfg['tags'])) ;
	foreach($modeles as $index => $item) {
		$modeles[$index] = $cfg['tags'][$index]['intitule']?$cfg['tags'][$index]['intitule']:$index ;
	}


	if (_request('_cke_nouveau_modele') || _request('_cke_edite_modele') || _request('_cke_nouveau_parametre')) {

		$valeurs['nom_modele'] = $modele ;
		$valeurs['type_modele'] = $cfg['tags'][$modele]['type'] ;
		$valeurs['type_num'] = $cfg['tags'][$modele]['nombre'] ;
		$valeurs['balise_fermante'] = $cfg['tags'][$modele]['fermante'] ;
		$valeurs['echappe_car'] = $cfg['tags'][$modele]['echappe_car'] ;
		$valeurs['syntaxe_balise'] = $cfg['tags'][$modele]['syntaxe']?$cfg['tags'][$modele]['syntaxe']:'spip' ;
		
		$parametres = array() ;
		if(_request('_cke_nouveau_parametre')) {
			$cfg['tags'][$modele]['noms'][] = _T('ckeditor:nouveau_parametre') ;
			$cfg['tags'][$modele]['valeurs'][] = '' ;
		}

		foreach($cfg['tags'][$modele]['noms'] as $id => $param) {
			$valeurs['parametre_nom_'.$id] = $cfg['tags'][$modele]['noms'][$id] ;
			$valeurs['parametre_id_'.$id] = $cfg['tags'][$modele]['ids'][$id] ;
			$valeurs['parametre_valeur_'.$id] = preg_replace('~;;~', "\n", $cfg['tags'][$modele]['valeurs'][$id]) ;
			$parametres_valeurs = array() ;

			// TODO : remplacer cette liste par un tableau sérialisé
			/*
			foreach(preg_split('~;;~', $cfg['tags'][$modele]['valeurs'][$id]) as $item) {
				$parametres_valeurs[] = $item ;
			}
			 */

			$parametres[] = array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'options_parametre_'.$id,
					'label' => _T('ckeditor:parametre_nomme', array('PARAMETRE'=>$param)),
					'pliable' => 'oui', 'plie' => 'oui'
				),
				'saisies' => array(
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'parametre_nom_'.$id,
							'label' => _T('ckeditor:label_du_parametre')
						)
					),
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'parametre_id_'.$id,
							'label' => _T('ckeditor:identifiant_du_parametre'),
							'explication' => _T('ckeditor:aide_identifiant_parametre_2')
						)
					),
					array(
						'saisie' => 'explication_spip',
						'options' => array(
							'nom' => 'explication_identifiant_'.$id,
							'texte' => _T('ckeditor:aide_identifiant_parametre_1')
						)
					),
					array(
						'saisie' => 'textarea',
						'options' => array(
							'nom' => 'parametre_valeur_'.$id,
							'label' => _T('ckeditor:valeurs_du_parametre'),
							'rows' => 3
						)
					)
				)
			);
		}

		if (count($parametres) == 0) {
			$parametres[] = array(
				'saisie' => 'explication_spip',
				'options' => array(
					'nom' => 'explication_aucun_parametre',
					'texte' => _T('ckeditor:aucun_parametre'),
				)
			) ;
		}
		$parametres[] = array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'nombre_parametres'
			)
		) ;
		$valeurs['nombre_parametres'] = count($parametres) ;

		$valeurs['saisies_cke_page8_2'] = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'edition_modele',
					'label' => _T('ckeditor:edition_du_modele', array('MODELE'=>$modele))
				),
				'saisies' => array(
					array(
						'saisie' => 'hidden',
						'options' => array(
							'nom' => 'nom_modele'
						)
					),
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'intitule',
							'label' => _T('ckeditor:intitule_du_modele')
						)
					),
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'info',
							'label' => _T('ckeditor:info_bulle')
						)
					),
					array(
						'saisie' => 'selection',
						'options' => array(
							'nom' => 'type_modele',
							'label' => _T('ckeditor:type_de_modele'),
							'cacher_option_intro' => 'oui',
							'datas' => array(
								'num-aucun' => _T('ckeditor:pas_numerique'),
								'num-facultatif' => _T('ckeditor:numerique_facultatif'),
								'num-obligatoire' => _T('ckeditor:numerique_obligatoire')
							)
						)
					),
					array(
						'saisie' => 'selection',
						'options' => array(
							'nom' => 'type_num',
							'label' => _T('ckeditor:type_nombre'),
							'cacher_option_intro' => 'oui',
							'datas' => array(
								"" => _T('ckeditor:num_indefini'),
								"articles" => _T('ckeditor:num_article'),
								"rubriques" => _T('ckeditor:num_rubrique'),
								"breves" => _T('ckeditor:num_breve'),
								"auteurs" => _T('ckeditor:num_auteur'),
								"mots" => _T('ckeditor:num_mot'),
								"groupes_mots" => _T('ckeditor:num_groupe_mots'),
								"sites" => _T('ckeditor:num_site'),
								"documents" => _T('ckeditor:num_document'),
								"thisobject" => _T('ckeditor:objet_en_edition')
							)
						)
					),
					array(
						'saisie' => 'case',
						'options' => array(
							'nom' => 'balise_fermante',
							'label_case' => _T('ckeditor:balise_fermante').' </'.$modele.'>',
						)
					),
					array(
						'saisie' => 'explication_spip',
						'options' => array(
							'nom' => 'explication_echappe_car',
							'texte' => _T('ckeditor:aide_echappe_car'),
						),
					), 
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'echappe_car',
							'label' => _T('ckeditor:echappe_caracteres')
						)
					),
					array(
						'saisie' => 'explication_spip',
						'options' => array(
							'nom' => 'explication_syntaxe_balise',
							'texte' => _T('ckeditor:aide_syntaxe_modele')
						)
					), 
					array(
						'saisie' => 'selection',
						'options' => array(
							'nom' => 'syntaxe_balise',
							'label' => _T('ckeditor:syntaxe'),
							'defaut' => 'spip',
							'cacher_option_intro' => 'oui',
							'datas' => array(
								'spip' => 'spip',
								'html' => 'html'
							)
						)
					)
				)
			),
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'liste_parametres',
					'label' => _T('ckeditor:liste_des_parametres')
				),
				'saisies' => $parametres
			)
		) ;
	} else {
		$valeurs['saisies_cke_page8_1'] = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'liste_modeles_spip',
					'label' => _T('ckeditor:configuration_des_modeles_spip'),
				),
				'saisies' => array(
					array(
						'saisie' => 'selection',
						'options' => array(
							'nom' => 'modeles',
							'label' => _T('ckeditor:modeles_spip_autorisees'),
							'datas' => $modeles,
							'cacher_option_intro' => 'oui'
						)
					),
					array(
						'saisie' => 'hidden',
						'options' => array(
							'nom' => 'nouveau_modele'
						)
					)
				)
			)
		) ;
	}
	return $valeurs ;
}

function formulaires_configurer_ckeditor_p8_verifier_dist() {
	$result = array() ;
	if (($nom_modele =_request('nom_modele')) && preg_match('~^/~', $nom_modele)) {
		$result['nom_modele'] = _T('ckeditor:modele_commence_pas_par_slash') ;
	}
	if (count($result)) {
		$result['message_erreur'] = _T('ckeditor:ck_ko').
			"<ul>\n".
				(count($result)?'<li>'.join("</li>\n<li>", $result)."</li>\n":'').
			"</ul>\n" ;
	}
	return $result ;
}

function formulaires_configurer_ckeditor_p8_traiter_dist() {
	if (_request('_cfg_delete')) {
		$valeurs = formulaires_configurer_ckeditor_p8_charger_dist() ;
		foreach($valeurs as $cle =>$valeur) {
			ecrire_config('ckeditor/'.$cle, $valeur) ;
			$_GET[$cle] = $valeur ;
		}
		return array('message_ok' => _T('ckeditor:ck_delete')) ;
	} else if (_request('_cke_supprime_modele') && _request('modeles')) {
		$tags = lire_config('ckeditor/tags') ;
		if (is_array($tags)) {
			unset($tags[_request('modeles')]) ;
			ecrire_config('ckeditor/tags', $tags) ;
			ckeditor_ecrire_protectedtags($tags) ;
		}
		return array('message_ok' => _T('ckeditor:modele_supprime', array('modele' => _request('modeles')))) ;
	} else if (_request('_cke_nouveau_modele') && _request('nouveau_modele')) {
		$tags = lire_config('ckeditor/tags') ;
		$modele = _request('nouveau_modele') ;
		$type = '' ; $tag_closed = false ;
		if (preg_match('~^/(.+)$/~', $modele, $m)) {
			$modele = $m[1] ;
			$tag_closed = true ;
		}
		if (preg_match('~(.+)(xx|XX)$~', $modele, $m)) {
			$modele = $m[1] ;
			($m[2] == 'xx') && ($type = 'num-facultatif') ;
			($m[2] == 'XX') && ($type = 'num-obligatoire') ;
		}
		ecrire_config('ckeditor/tags/'.$modele.'/type', $type) ;
		ecrire_config('ckeditor/tags/'.$modele.'/fermante', $tag_closed) ;
		ecrire_config('ckeditor/tags/'.$modele.'/syntaxe', 'spip') ;
		ckeditor_ecrire_protectedtags($tags) ;
	} else if (_request('_cke_valide_modele') || _request('_cke_nouveau_parametre')) {
		$modele = _request('nom_modele') ;

		$cpt = 0 ; $max_cpt = _request('nombre_parametres') ;
		$entree_modele = array('nom'=>array(),'ids'=>array(),'valeurs'=>array()) ;
		while($cpt<$max_cpt) {
			if (_request('parametre_nom_'.$cpt) !== null) {
				$entree_modele['noms'][] = _request('parametre_nom_'.$cpt) ;
				$entree_modele['ids'][] = _request('parametre_id_'.$cpt) ;
				$entree_modele['valeurs'][] = preg_replace("~\n~s", ";;",_request('parametre_valeur_'.$cpt)) ;
			}
			$cpt ++ ;
		}

		ecrire_config('ckeditor/tags/'.$modele.'/type', _request('type_modele') ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/syntaxe', _request('syntaxe_balise') ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/fermante', _request('balise_fermante') ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/echappe_car', _request('echappe_car') ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/nombre', _request('type_num') ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/intitule', _request('intitule') ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/info', _request('info') ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/noms', $entree_modele['noms'] ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/ids', $entree_modele['ids'] ) ;
		ecrire_config('ckeditor/tags/'.$modele.'/valeurs', $entree_modele['valeurs'] ) ;
		ckeditor_ecrire_protectedtags($tags) ;

		return array('message_ok' => _T('ckeditor:modification_de_modele')) ;
	}
}

 ?>