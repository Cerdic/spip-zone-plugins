<?php

function iecfg_saisies_export() {
	$saisies = array (
		// Options d'export
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'iecfg_export',
				'label' => '<:iecfg:label_iecfg_export:>'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'iecfg_export_nom',
						'label' => '<:iecfg:label_iecfg_export_nom:>',
						'obligatoire' => 'oui',
						'defaut' => $GLOBALS['meta']['nom_site'].' - '.date('Y/m/d')
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'iecfg_export_description',
						'label' => '<:iecfg:label_iecfg_export_description:>',
						'rows' => 4
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'iecfg_export_explication',
						'texte' => '<:iecfg:texte_iecfg_export_explication:>'
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'iecfg_export_choix',
						'label' => '<:iecfg:label_iecfg_export_choix:>',
						'cacher_option_intro' => 'oui',
						'defaut' => 'telecharger',
						'datas' => array(
							'sauvegarder' => '<:iecfg:item_sauvegarder:>',
							'telecharger' => '<:iecfg:item_telecharger:>'
						)
					)
				)
			)
		),
		// Exporter la configuration du contenu du site SPIP
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'spip_contenu',
				'label' => '<:spip:onglet_contenu_site:>'
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'spip_contenu_explication',
						'texte' => '<:iecfg:texte_spip_contenu_export_explication:>'
					)
				),
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'spip_contenu_choix',
						'label' => '<:iecfg:label_elements_a_exporter:>',
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'articles' => '<:iecfg:label_configuration_articles:>',
							'rubriques' => '<:iecfg:label_configuration_rubriques:>',
							'breves' => '<:iecfg:label_configuration_breves:>',
							'mots' => '<:iecfg:label_configuration_mots:>',
							'logos' => '<:iecfg:label_configuration_logos:>',
							'documents' => '<:iecfg:label_configuration_documents:>',
							'sites' => '<:iecfg:label_configuration_sites:>'
						)
					)
				)
			)
		)
	);
	// On passe via le pipeline iecfg
	$saisies = pipeline('iecfg',array(
		'args' => array(
			'action' => 'form_export'
		),
		'data' => $saisies
	));
	return $saisies;
}

function formulaires_iecfg_export_charger_dist() {
	$contexte = array(
		'_saisies' => iecfg_saisies_export()
	);
	return array_merge($_POST,$contexte);
}

function formulaires_iecfg_export_verifier_dist() {
	include_spip('inc/saisies');
	return saisies_verifier(iecfg_saisies_export());
}

function formulaires_iecfg_export_traiter_dist() {
	$export = array();
	$export['nom'] = _request('iecfg_export_nom');
	if (_request('iecfg_export_description') != '')
		$export['description'] = _request('iecfg_export_description');
	
	// Configuration du contenu du site SPIP
	if (count(_request('spip_contenu_choix'))>0) {
		$export['spip_contenu'] = array();
		foreach(_request('spip_contenu_choix') as $choix) 
			switch ($choix) {
			case 'articles':
				$export['spip_contenu']["articles_surtitre"] = $GLOBALS['meta']["articles_surtitre"];
				$export['spip_contenu']["articles_soustitre"] = $GLOBALS['meta']["articles_soustitre"];
				$export['spip_contenu']["articles_descriptif"] = $GLOBALS['meta']["articles_descriptif"];
				$export['spip_contenu']["articles_chapeau"] = $GLOBALS['meta']["articles_chapeau"];
				$export['spip_contenu']["articles_texte"] = $GLOBALS['meta']["articles_texte"];
				$export['spip_contenu']["articles_ps"] = $GLOBALS['meta']["articles_ps"];
				$export['spip_contenu']["articles_redac"] = $GLOBALS['meta']["articles_redac"];
				$export['spip_contenu']["articles_urlref"] = $GLOBALS['meta']["articles_urlref"];
				$export['spip_contenu']["post_dates"] = $GLOBALS['meta']["post_dates"];
				$export['spip_contenu']["articles_redirection"] = $GLOBALS['meta']["articles_redirection"];
				break;
			case 'rubriques':
				$export['spip_contenu']["rubriques_descriptif"] = $GLOBALS['meta']["rubriques_descriptif"];
				$export['spip_contenu']["rubriques_texte"] = $GLOBALS['meta']["rubriques_texte"];
				break;
			case 'breves':
				$export['spip_contenu']["activer_breves"] = $GLOBALS['meta']["activer_breves"];
				break;
			case 'mots':
				$export['spip_contenu']["articles_mots"] = $GLOBALS['meta']["articles_mots"];
				$export['spip_contenu']["config_precise_groupes"] = $GLOBALS['meta']["config_precise_groupes"];
				$export['spip_contenu']["mots_cles_forums"] = $GLOBALS['meta']["mots_cles_forums"];
				break;
			case 'logos':
				$export['spip_contenu']["activer_logos"] = $GLOBALS['meta']["activer_logos"];
				$export['spip_contenu']["activer_logos_survol"] = $GLOBALS['meta']["activer_logos_survol"];
				break;
			case 'documents':
				$export['spip_contenu']["documents_article"] = $GLOBALS['meta']["documents_article"];
				$export['spip_contenu']["documents_rubrique"] = $GLOBALS['meta']["documents_rubrique"];
				$export['spip_contenu']["documents_date"] = $GLOBALS['meta']["documents_date"];
				break;
			case 'sites':
				$export['spip_contenu']["activer_sites"] = $GLOBALS['meta']['activer_sites'];
				$export['spip_contenu']["activer_syndic"] = $GLOBALS['meta']["activer_syndic"];
				$export['spip_contenu']["proposer_sites"] = $GLOBALS['meta']["proposer_sites"];
				$export['spip_contenu']["moderation_sites"] = $GLOBALS['meta']["moderation_sites"];
				break;
			}
	}
	
	// On passe via le pipeline iecfg
	$export = pipeline('iecfg',array(
		'args' => array(
			'action' => 'export',
			'request' => $_POST
		),
		'data' => $export
	));
	
	// On encode en yaml
	include_spip('inc/yaml');
	$export = yaml_encode($export);
	
	// Nom du fichier
	include_spip('inc/texte');
	$site = isset($GLOBALS['meta']['nom_site'])
	  ? preg_replace(array(",\W,is",",_(?=_),",",_$,"),array("_","",""), couper(translitteration(trim($GLOBALS['meta']['nom_site'])),30,""))
	  : 'spip';
	$filename = $site.'_'.date('Y-m-d_H-i').'.yaml';
	
	// Si telechargement
	if(_request('iecfg_export_choix')=='telecharger') {
		refuser_traiter_formulaire_ajax();
		Header("Content-Type: text/x-yaml;");
		Header("Content-Disposition: attachment; filename=$filename");
		Header("Content-Length: ".strlen($export));
		echo $export;
		exit;
	} else {
		sous_repertoire(_DIR_TMP, 'iecfg');
		if (ecrire_fichier(_DIR_TMP . 'iecfg/'.$filename , $export))
			return array('message_ok' => _T('iecfg:message_ok_export',array('filename'=>$filename)));
		else
			return array('message_erreur' => _T('iecfg:message_erreur_export',array('filename'=>$filename)));
	}
}

?>