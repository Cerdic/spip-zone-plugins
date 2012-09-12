<?php



function lettres_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	// migration depuis l'ancien systeme de maj
	if (isset($GLOBALS['meta']['spip_lettres_version'])
	  AND !isset($GLOBALS['meta'][$nom_meta_base_version])){
		ecrire_meta($nom_meta_base_version,$GLOBALS['meta']['spip_lettres_version'],'non');
		effacer_meta('spip_lettres_version');
	}
	$maj = array();

	$maj['create'] = array(
		array('creer_base'),
		array('spip_lettres_update_meta'),
		array('spip_lettres_creer_repertoire_documents'),
	);
	
	$maj['0.1'] = array( 
		array('spip_lettres_update_meta',$version_plugin,$nom_meta_base_versio,$current_version,$version_cible),
		array('spip_lettres_creer_repertoire_documents'),
	);
	$maj['3.0'] = array( 
		array('maj_tables',array('spip_abonnes_clics',
								'spip_abonnes_lettres',
								'spip_desabonnes',
								'spip_abonnes_rubriques',
								'spip_articles_lettres',
								'spip_lettres_statistiques',
								'spip_themes',
								'spip_lettres',
								'spip_rubriques_crontabs')),
		array('spip_lettres_update_meta',$version_plugin,$nom_meta_base_versio,$current_version,$version_cible),
	);
	$maj['3.1'] = array( 	
		array('maj_tables',array('spip_lettres')),
		array('spip_lettres_update_meta',$version_plugin,$nom_meta_base_versio,$current_version,$version_cible),
	);
	$maj['3.2'] = array(
		array('spip_lettres_maj_index_elements_objet')
	);
	$maj['3.3'] = array(
		array('maj_tables',array('spip_desabonnes')),
	);
	$maj['3.4'] = array(
		array('maj_tables',array('spip_desabonnes')),
		array('spip_lettres_creer_repertoire_documents')
	);
	$maj['3.6'] = array(
		array('spip_lettres_update_meta',$version_plugin,$nom_meta_base_versio,$current_version,$version_cible),
	);
	$maj['3.7'] = array(
		array('maj_tables',array('spip_rubriques_crontabs')),
	);
	$maj['3.8'] = array(
		array('maj_tables',array('spip_lettres')),
		array('sql_alter',"TABLE spip_lettres DROP idx"),	
		array('sql_drop_table',"spip_documents_lettres",true),
		array('spip_lettres_update_meta',$version_plugin,$nom_meta_base_versio,$current_version,$version_cible),
	);
	$maj['4.0.0'] = array(
		array('spip_lettres_update_fond'),
	);
	$maj['4.0.1'] = array(
		array('sql_alter',"TABLE spip_lettres CHANGE statut statut VARCHAR(15) NOT NULL DEFAULT 'brouillon'"),
	);
	$maj['4.0.2'] = array(
		array('sql_alter',"TABLE spip_lettres CHANGE texte texte longtext DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_lettres CHANGE message_html message_html longtext DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_lettres CHANGE message_texte message_texte longtext DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_lettres CHANGE extra extra longtext NULL"),
	);
	$maj['4.1'] = array(
		array('sql_alter',"TABLE spip_lettres CHANGE texte texte longtext DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_lettres CHANGE message_html message_html longtext DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_lettres CHANGE message_texte message_texte longtext DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_lettres CHANGE extra extra longtext NULL"),
		array('spip_lettres_update_meta',$version_plugin,$nom_meta_base_versio,$current_version,$version_cible),
	);
	$maj['4.2'] = array(
		array('sql_alter',"TABLE spip_themes ADD COLUMN expediteur_type ENUM('default','webmaster','author','custom') NOT NULL DEFAULT 'default'"),
		array('sql_alter',"TABLE spip_themes ADD COLUMN expediteur_id BIGINT(21) NOT NULL DEFAULT '0'"),
		array('sql_alter',"TABLE spip_themes ADD COLUMN retours_type ENUM('default','webmaster','author','custom') NOT NULL DEFAULT 'default'"),
		array('sql_alter',"TABLE spip_themes ADD COLUMN retours_id BIGINT(21) NOT NULL DEFAULT '0'"),
		array('spip_lettres_update_meta',$version_plugin,$nom_meta_base_versio,$current_version,$version_cible),
	);	
	if ('oui' == $GLOBALS['meta']['spip_lettres_signe_par_auteurs'])
		array_push($maj['4.2'][],array('sql_updateq','spip_themes',array('expediteur_type' => 'author'), '1'));
	// Laissons de la place dans la numerotation si la version de base de la branche 2 evolue
	include_spip('maj/svn10000');
	$maj['5.2'] = array(
		array('maj_liens','mot','lettre'),
		array('sql_drop_table',"spip_mots_lettres"),
		array('maj_liens','auteur','lettre'),
		array('sql_drop_table',"spip_auteurs_lettres"),
	);

	// Attention : cette maj ne gère pas le cas où d'autres tables que ABONNES ou AUTEURS 
	// sont utilisées pour les abonnés
	$maj['5.3'] = array(
		array ('sql_updateq', 'spip_abonnes', array('objet'=>'auteur'), "objet='auteurs'"),
		array ('sql_updateq', 'spip_abonnes', array('objet'=>'abonne'), "objet='abonnes'"));
		
	$maj['5.4'] = array(
		array ('sql_alter', "TABLE spip_abonnes CHANGE objet objet VARCHAR(255) NOT NULL DEFAULT 'abonne'"));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function spip_lettres_maj_index_elements_objet() {
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			unset($INDEX_elements_objet['spip_lettres']);
			ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
}


function spip_lettres_update_meta($version_plugin,$nom_meta_base_versio,$current_version,$version_cible) {
			ecrire_meta('spip_lettres_version', $version_plugin);
			ecrire_meta('spip_lettres_fond_formulaire_lettres', 'lettres');
			ecrire_meta('spip_lettres_fond_lettre_titre', 'emails/lettre_titre');
			ecrire_meta('spip_lettres_fond_lettre_html', 'emails/lettre_html');
			ecrire_meta('spip_lettres_fond_lettre_texte', 'emails/lettre_texte');
			ecrire_meta('spip_lettres_notifier_suppression_abonne', 'non');
			ecrire_meta('spip_lettres_utiliser_articles', 'non');
			ecrire_meta('spip_lettres_utiliser_descriptif', 'non');
			ecrire_meta('spip_lettres_utiliser_chapo', 'non');
			ecrire_meta('spip_lettres_utiliser_ps', 'non');
			ecrire_meta('spip_lettres_envois_recurrents', 'non');
			ecrire_meta('spip_lettres_cron', md5(uniqid(rand())));
			ecrire_meta('spip_lettres_cliquer_anonyme', 'oui');
			ecrire_meta('spip_lettres_admin_abo_toutes_rubriques', 'non');
			ecrire_meta('spip_lettres_log_utiliser_email', 'non');
			if (!strpos($GLOBALS['meta']['preview'],',0minirezo,'))
				ecrire_meta('preview',',0minirezo,');
			ecrire_metas();
}


function spip_lettres_update_fond() {
			if ($GLOBALS['meta']['spip_lettres_fond_lettre_titre']=='lettre_titre'
			  AND !find_in_path('lettre_titre.html'))
				ecrire_meta('spip_lettres_fond_lettre_titre', 'emails/lettre_titre');
			if ($GLOBALS['meta']['spip_lettres_fond_lettre_texte']=='lettre_texte'
			  AND !find_in_path('lettre_texte.html'))
				ecrire_meta('spip_lettres_fond_lettre_texte', 'emails/lettre_texte');
			if ($GLOBALS['meta']['spip_lettres_fond_lettre_html']=='lettre_html'
			  AND !find_in_path('lettre_html.html'))
				ecrire_meta('spip_lettres_fond_lettre_html', 'emails/lettre_html');
}


function spip_lettres_creer_repertoire_documents() {
			include_spip('inc/documents');
			creer_repertoire_documents('lettres');
}


function lettres_vider_tables($nom_meta_base_version) {
	
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	include_spip('classes/lettre');

	$res = sql_select('id_lettre', 'spip_lettres');
	if ($res) {
		while ($arr = sql_fetch($res)) {
			$lettre = new lettre($arr['id_lettre']);
			$lettre->supprimer();
		}
	}

	sql_drop_table('spip_abonnes', true);
	sql_drop_table('spip_clics', true);
	sql_drop_table('spip_desabonnes', true);
	sql_drop_table('spip_lettres', true);
	sql_drop_table('spip_rubriques_crontabs', true);
	sql_drop_table('spip_themes', true);
	sql_drop_table('spip_abonnes_clics', true);
	sql_drop_table('spip_abonnes_lettres', true);
	sql_drop_table('spip_abonnes_rubriques', true);
	sql_drop_table('spip_abonnes_statistiques', true);
	sql_drop_table('spip_articles_lettres', true);
	sql_drop_table('spip_documents_lettres', true);
	sql_drop_table('spip_lettres_statistiques', true);
	sql_drop_table('spip_mots_lettres', true);
	effacer_meta('spip_lettres_version');
	effacer_meta('spip_lettres_fond_formulaire_lettres');
	effacer_meta('spip_lettres_fond_lettre_titre');
	effacer_meta('spip_lettres_fond_lettre_html');
	effacer_meta('spip_lettres_fond_lettre_texte');
	effacer_meta('spip_lettres_notifier_suppression_abonne');
	effacer_meta('spip_lettres_utiliser_articles');
	effacer_meta('spip_lettres_utiliser_descriptif');
	effacer_meta('spip_lettres_utiliser_chapo');
	effacer_meta('spip_lettres_utiliser_ps');
	effacer_meta('spip_lettres_envois_recurrents');
	effacer_meta('derniere_modif_lettre');
	effacer_meta('spip_lettres_cron');
	effacer_meta('spip_lettres_abonnement_par_defaut');
	effacer_meta('spip_lettres_cliquer_anonyme');
	effacer_meta('spip_lettres_admin_abo_toutes_rubriques');
	supprimer_repertoire(_DIR_LETTRES);
	effacer_meta($nom_meta_base_version);
}

?>
