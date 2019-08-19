<?php

include_spip('inc/ckeditor_constantes') ;
include_spip('inc/ckeditor_tools') ;

function formulaires_configurer_ckeditor_p6_charger_dist() {
	($cfg = lire_config("ckeditor")) || ($cfg = array()) ;
	$modele_edite = _request('modele_a_editer') ;

	if ($modele_edite && !_request('nouveau_modele') && !_request('supprimer_modele')) {
		$var = lire_config('ckeditor/modeles/'.$modele_edite) ;

		$valeurs = array(
			'editer_modele' => _request('editer_modele'),
			'modele_edite' => $modele_edite,
			'modele_description' => $var['desc'],
			'modele_image' =>  $var['image'],
			'modele' => $var['modele'],
		) ;

		$valeurs['saisies_cke_page6_3'] = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'edition_modele',
					'label' => _T('ckeditor:edition_du_modele', array('MODELE' => $modele_edite)),
					'pliable' => 'non'
				),
				'saisies' => array(
					array(
						'saisie' => 'hidden',
						'options' => array(
							'nom' => 'modele_edite'
						)
					),
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'modele_description',
							'label' => _T('ckeditor:description')
						)
					),
					array(
						'saisie' => 'selecteur_images',
						'options' => array(
							'nom' => 'modele_image',
							'label' => _T('ckeditor:image_du_modele'),
							'repertoire' => 'images/templates',
							'commentaire' => _T('ckeditor:aide_images_modele')
						)
					),
					array(
						'saisie' => 'textarea',
						'options' => array(
							'nom' => 'modele',
							'label' => _T('ckeditor:contenu_du_modele'),
							'rows' => 15
						)
					)
				)
			)
		) ;
	} else {

		$valeurs = array(
			'nom_nouveau_modele' => '',
			'editer_modele' => _request('editer_modele')
		) ;

		$valeurs['saisies_cke_page6_1'] = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'options_modeles',
					'label' => _T('ckeditor:configuration_modeles'),
					'pliable' => 'non', 
				),
				'saisies' => array(
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'nom_nouveau_modele',
							'label' => _T('ckeditor:nom_nouveau_modele')
						)
					)
				)
			)
		) ;
		$modeles = array() ;
		$cke_modeles = lire_config('ckeditor/modeles') ;
		if (is_array($cke_modeles)) {
			foreach($cke_modeles  as $id => $modele) {
				$modeles[$id] = ($modele['desc']?$modele['desc']:$id) ;
			}
			$valeurs['saisies_cke_page6_2'] = array(
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'modele_a_editer',
						'label' => _T('ckeditor:nom_nouveau_modele'),
						'cacher_option_intro' => 'oui',
						'datas' => $modeles
					)
				)
			) ;
		}
	}

	return $valeurs ;
}

function formulaires_configurer_ckeditor_p6_verifier_dist() {
	$result = array() ;
	if (_request('nouveau_modele') && ! _request('nom_nouveau_modele')) {
		$result['nom_nouveau_modele'] = _T('ckeditor:nouveau_modele_sans_nom') ;
	}
	if (count($result)) {
		$result['message_erreur'] = _T('ckeditor:ck_ko').
			"<ul>\n".
				(count($result)?'<li>'.join("</li>\n<li>", $result)."</li>\n":'').
			"</ul>\n" ;
	}
		
	return $result ;
}

function formulaires_configurer_ckeditor_p6_traiter_dist() {
	if (_request('_cfg_delete')) {
		$valeurs = formulaires_configurer_ckeditor_p6_charger_dist() ;
		foreach($valeurs as $cle =>$valeur) {
			ecrire_config('ckeditor/'.$cle, $valeur) ;
			$_GET[$cle] = $valeur ;
		}
		 return array('message_ok' => _T('ckeditor:ck_delete')) ;
	} else if (_request('nouveau_modele')) {
		ecrire_config('ckeditor/modeles/'._request('nom_nouveau_modele'), array('modele'=>'', 'desc'=>'', 'image'=>'')) ;
		return array('message_ok' => _T('ckeditor:modele_cree', array('modele'=>_request('nom_nouveau_modele')))) ;
	} else if (_request('supprimer_modele')) {
		effacer_config('ckeditor/modeles/'._request('modele_a_editer')) ;
		return array('message_ok' => _T('ckeditor:modele_supprime', array('modele'=>_request('modele_a_editer')))) ;
	} else if (_request('editer_modele')) {
		return array() ;
	} else if (_request('enregistrer_modele') && _request('modele_edite')) {
		ecrire_config('ckeditor/modeles/'._request('modele_edite').'/modele', _request('modele')) ;
		ecrire_config('ckeditor/modeles/'._request('modele_edite').'/desc', _request('modele_description')) ;
		ecrire_config('ckeditor/modeles/'._request('modele_edite').'/image', _request('modele_image')) ;
		return array('message_ok' => _T('ckeditor:modele_enregistre', array('modele'=>_request('modele_edite')))) ;
	}
	return array() ;
}

 ?>
