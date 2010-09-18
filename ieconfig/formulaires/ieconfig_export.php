<?php

function ieconfig_saisies_export() {
	$saisies = array (
		// Options d'export
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'ieconfig_export',
				'label' => '<:ieconfig:label_ieconfig_export:>',
				'icone' => 'img/ieconfig-export.png'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'ieconfig_export_nom',
						'label' => '<:ieconfig:label_ieconfig_export_nom:>',
						'obligatoire' => 'oui',
						'defaut' => $GLOBALS['meta']['nom_site'].' - '.date('Y/m/d')
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'ieconfig_export_description',
						'label' => '<:ieconfig:label_ieconfig_export_description:>',
						'rows' => 4
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'ieconfig_export_explication',
						'texte' => '<:ieconfig:texte_ieconfig_export_explication:>'
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'ieconfig_export_choix',
						'label' => '<:ieconfig:label_ieconfig_export_choix:>',
						'cacher_option_intro' => 'oui',
						'defaut' => 'telecharger',
						'datas' => array(
							'sauvegarder' => '<:ieconfig:item_sauvegarder:>',
							'telecharger' => '<:ieconfig:item_telecharger:>'
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
				'label' => '<:spip:onglet_contenu_site:>',
				'icone' => 'images/racine-site-24.gif'
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'spip_contenu_explication',
						'texte' => '<:ieconfig:texte_spip_contenu_export_explication:>'
					)
				),
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'spip_contenu_choix',
						'label' => '<:ieconfig:label_elements_a_exporter:>',
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'articles' => '<:ecrire:titre_page_articles_page:>',
							'rubriques' => '<:icone_rubriques:>',
							'breves' => '<:ecrire:titre_breves:>',
							'mots' => '<:info_mots_cles:>',
							'logos' => '<:info_logos:>',
							'documents' => '<:titre_documents_joints:>',
							'sites' => '<:titre_referencement_sites:>'
						)
					)
				)
			)
		),
				// Onglet Interactivité
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'spip_interactivite',
				'label' => '<:spip:onglet_interactivite:>',
				'icone' => 'images/forum-interne-24.gif'
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'spip_interactivite_explication',
						'texte' => '<:ieconfig:texte_spip_interactivite_export_explication:>'
					)
				),
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'spip_interactivite_choix',
						'label' => '<:ieconfig:label_elements_a_exporter:>',
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'participants' => '<:ecrire:info_mode_fonctionnement_defaut_forum_public:>',
							'contenu_forums' => '<:spip:titre_forum:>',
							'redacteurs' => '<:ecrire:info_inscription_automatique:>',
							'visiteurs' => '<:ecrire:info_visiteurs:>',
							'forums_prives' => '<:ecrire:titre_config_forums_prive:>',
							'messagerie_agenda' => '<:ecrire:titre_messagerie_agenda:>',
							'annonces' => '<:ecrire:info_envoi_email_automatique:>',
							'notifications_forum' => '<:ecrire:info_envoi_forum:>'
						)
					)
				)
			)
		)
	);
	// On passe via le pipeline ieconfig
	$saisies = pipeline('ieconfig',array(
		'args' => array(
			'action' => 'form_export'
		),
		'data' => $saisies
	));
	return $saisies;
}

function formulaires_ieconfig_export_charger_dist() {
	$saisies = ieconfig_saisies_export();
	$contexte = array(
		'_saisies' => $saisies
	);
	
	return array_merge(saisie_charger_champs($saisies),$contexte);
}

function formulaires_ieconfig_export_verifier_dist() {
	include_spip('inc/saisies');
	return saisies_verifier(ieconfig_saisies_export());
}

function formulaires_ieconfig_export_traiter_dist() {
	$export = array();
	$export['nom'] = _request('ieconfig_export_nom');
	if (_request('ieconfig_export_description') != '')
		$export['description'] = _request('ieconfig_export_description');
	
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
	
	// Onglet Interactivité (configuration de spip)
	if (count(_request('spip_interactivite_choix'))>0) {
		$export['spip_interactivite'] = array();
		foreach(_request('spip_interactivite_choix') as $choix) 
			switch ($choix) {
			case 'participants':
				$export['spip_interactivite']['forums_publics'] = $GLOBALS['meta']["forums_publics"];
				break;
			case 'contenu_forums':
				$export['spip_interactivite']['forums_titre'] = $GLOBALS['meta']["forums_titre"];
				$export['spip_interactivite']['forums_texte'] = $GLOBALS['meta']["forums_texte"];
				$export['spip_interactivite']['forums_urlref'] = $GLOBALS['meta']["forums_urlref"];
				$export['spip_interactivite']['forums_afficher_barre'] = $GLOBALS['meta']["forums_afficher_barre"];
				break;
			case 'redacteurs':
				$export['spip_interactivite']['accepter_inscriptions'] = $GLOBALS['meta']["accepter_inscriptions"];
				break;
			case 'visiteurs':
				$export['spip_interactivite']['accepter_visiteurs'] = $GLOBALS['meta']["accepter_visiteurs"];
				break;
			case 'forums_prives':
				$export['spip_interactivite']['forum_prive_objets'] = $GLOBALS['meta']["forum_prive_objets"];
				$export['spip_interactivite']['forum_prive'] = $GLOBALS['meta']["forum_prive"];
				$export['spip_interactivite']['forum_prive_admin'] = $GLOBALS['meta']["forum_prive_admin"];
				break;
			case 'messagerie_agenda':
				$export['spip_interactivite']['messagerie_agenda'] = $GLOBALS['meta']["messagerie_agenda"];
				break;
			case 'annonces':
				$export['spip_interactivite']['suivi_edito'] = $GLOBALS['meta']["suivi_edito"];
				$export['spip_interactivite']['adresse_suivi'] = $GLOBALS['meta']["adresse_suivi"];
				$export['spip_interactivite']['adresse_suivi_inscription'] = $GLOBALS['meta']["adresse_suivi_inscription"];
				$export['spip_interactivite']['quoi_de_neuf'] = $GLOBALS['meta']["quoi_de_neuf"];
				$export['spip_interactivite']['adresse_neuf'] = $GLOBALS['meta']["adresse_neuf"];
				$export['spip_interactivite']['jours_neuf'] = $GLOBALS['meta']["jours_neuf"];
				$export['spip_interactivite']['email_envoi'] = $GLOBALS['meta']["email_envoi"];
				break;
			case 'notifications_forum':
				$export['spip_interactivite']['prevenir_auteurs'] = $GLOBALS['meta']["prevenir_auteurs"];
				break;
			}
	}
	
	// On passe via le pipeline ieconfig
	$export = pipeline('ieconfig',array(
		'args' => array(
			'action' => 'export'
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
	if(_request('ieconfig_export_choix')=='telecharger') {
		refuser_traiter_formulaire_ajax();
		Header("Content-Type: text/x-yaml;");
		Header("Content-Disposition: attachment; filename=$filename");
		Header("Content-Length: ".strlen($export));
		echo $export;
		exit;
	} else {
		sous_repertoire(_DIR_TMP, 'ieconfig');
		if (ecrire_fichier(_DIR_TMP . 'ieconfig/'.$filename , $export))
			return array('message_ok' => _T('ieconfig:message_ok_export',array('filename'=>$filename)));
		else
			return array('message_erreur' => _T('ieconfig:message_erreur_export',array('filename'=>$filename)));
	}
}

?>