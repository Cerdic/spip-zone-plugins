<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function newsletters_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_newsletters', 'spip_newsletters_liens')),
		array('newsletters_import_from_spiplistes'),
		array('newsletters_import_from_spiplettres'),
		array('newsletters_import_from_clevermail'),
	);

	$maj['0.1.1'] = array(
		array('sql_alter', "table spip_newsletters ADD baked tinyint NOT NULL DEFAULT 0"),
	);

	$maj['0.2.1'] = array(
		array('sql_alter', "table spip_newsletters ADD recurrence text NOT NULL DEFAULT ''"),
		array('sql_alter', "table spip_newsletters ADD email_test text NOT NULL DEFAULT ''"),
		array('sql_alter', "table spip_newsletters ADD liste text NOT NULL DEFAULT ''"),
	);
	$maj['0.3.0'] = array(
		array('maj_tables', array('spip_newsletters')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function newsletters_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_newsletters");
	sql_drop_table("spip_newsletters_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('newsletter')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('newsletter')));
	sql_delete("spip_forum",                 sql_in("objet", array('newsletter')));

	effacer_meta($nom_meta_base_version);
}




/**
 * Importe les lettres du plugin SPIP Listes
 *
 * @note
 *     Très proche de l'import fait par 'mailshot' également.
 * 
 * @return void|null
**/
function newsletters_import_from_spiplistes(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($desc = $trouver_table('spip_courriers')){

		include_spip('inc/charsets');
		include_spip("action/editer_objet");

		sql_alter("TABLE spip_courriers ADD id_newsletter bigint(21) NOT NULL DEFAULT 0");
		$res = sql_select(
			"C.id_courrier,C.titre as sujet, C.texte as html, C.message_texte as texte," .
			"C.date_fin_envoi as date,C.date_debut_envoi as date_start, C.statut AS statut",
			'spip_courriers AS C',"C.id_newsletter=0 AND C.total_abonnes>0 AND ".sql_in("C.type",array('nl','auto')));
		while ($row = sql_fetch($res)){

			$id_courrier = $row['id_courrier'];
			unset($row['id_courrier']);

			// nettoyer les vieux hacks de spip-listes
			$row['html'] = preg_replace(",__bLg__[0-9@\.A-Z_-]+__bLg__,","",$row['html']);

			if ($GLOBALS['meta']['charset']=='utf-8'){
				if (!is_utf8($row['sujet'])) $row['sujet'] = importer_charset($row['sujet'] ,'iso-8859-15');
				if (!is_utf8($row['html']))  $row['html']  = importer_charset($row['html']  ,'iso-8859-15');
				if (!is_utf8($row['texte'])) $row['texte'] = importer_charset($row['texte'] ,'iso-8859-15');
			}

			// id dans mailshot
			$hash_id = md5(serialize(array('sujet'=>&$row['sujet'],'html'=>&$row['html'],'texte'=>&$row['texte'])));

			// remettre le format pour newsletter.
			$row['titre']        = $row['sujet'];
			$row['texte_email']  = $row['texte'];
			$row['html_email']   = $row['html'];
			$row['html_page']    = $row['html'];
			unset($row['sujet'], $row['texte'], $row['html'], $row['id'], $row['date_start']);
			if ($row['statut']=="auto")
				$row['statut'] = "publie";
			if (!in_array($row['statut'],array("publie","prop","prepa")))
				$row['statut'] = 'publie';
			$row['baked'] = 1;

			// inserer la lettre dans newsletter
			if ($id_newsletter = newsletters_import_one($row)){
				sql_updateq("spip_courriers",array('id_newsletter'=>$id_newsletter),"id_courrier=".intval($id_courrier));
				sql_updateq("spip_mailshots", array('id'=>$id_newsletter), "id=".sql_quote($hash_id));
				spip_log("import from spip_listes newsletters $id_newsletter ".var_export($row,true),"newsletters");
			}

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}
		sql_alter("TABLE spip_courriers DROP id_newsletter");
	}
}



/**
 * Importe les lettres & abonnés du plugin SPIP Lettres
 *
 * @note
 *     Très proche de l'import fait par 'mailshot' également.
 * 
 * @return void|null
**/
function newsletters_import_from_spiplettres(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_lettres')
	  AND $trouver_table('spip_abonnes_lettres')){

		include_spip('inc/charsets');
		include_spip("action/editer_objet");

		// importer les envois
		sql_alter("TABLE spip_lettres ADD id_newsletter bigint(21) NOT NULL DEFAULT 0");
		$res = sql_select(
			"id_lettre,titre as sujet,message_html as html,message_texte as texte,date_fin_envoi as date,date_debut_envoi as date_start",
			'spip_lettres',"id_newsletter=0 AND statut=".sql_quote('envoyee'));
		while ($row = sql_fetch($res)){

			$id_lettre = $row['id_lettre'];
			unset($row['id_lettre']);

			if ($GLOBALS['meta']['charset']=='utf-8'){
				if (!is_utf8($row['sujet'])) $row['sujet'] = importer_charset($row['sujet'] ,'iso-8859-15');
				if (!is_utf8($row['html']))  $row['html']  = importer_charset($row['html']  ,'iso-8859-15');
				if (!is_utf8($row['texte'])) $row['texte'] = importer_charset($row['texte'] ,'iso-8859-15');
			}

			// id dans mailshot
			$hash_id = md5(serialize(array('sujet'=>&$row['sujet'],'html'=>&$row['html'],'texte'=>&$row['texte'])));

			$row['statut'] = 'publie';

			// remettre le format pour newsletter.
			$row['titre']        = $row['sujet'];
			$row['texte_email']  = $row['texte'];
			$row['html_email']   = $row['html'];
			$row['html_page']    = $row['html'];
			unset($row['sujet'], $row['texte'], $row['html'], $row['date_start']);
			$row['baked'] = 1;

			if ($id_newsletter = newsletters_import_one($row)){
				sql_updateq("spip_lettres",array('id_newsletter'=>$id_newsletter),"id_lettre=".intval($id_lettre));
				sql_updateq("spip_mailshots", array('id'=>$id_newsletter), "id=".sql_quote($hash_id));
				spip_log("import from spip_lettres newsletters $id_newsletter ".var_export($row,true),"newsletters");
			}

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}

		// et c'est tout !
		sql_alter("TABLE spip_lettres DROP id_newsletter");
	}
}


/**
 * Importe les lettres du plugin Clevermail
 *
 * @note
 *     Très proche de l'import fait par 'mailshot' également.
 * 
 * @return void|null
**/
function newsletters_import_from_clevermail(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_cm_posts')
	  AND $trouver_table('spip_cm_posts_done')) {
		spip_log('Import des lettres clevermail', 'newsletters');

		include_spip("inc/charsets");
		include_spip("action/editer_objet");

		// Importer les lettres
		// ajout d'un champ le temps de l'import. Évite d'attraper 2 fois une même lettre et de reprendre sur timeout.
		sql_alter("TABLE spip_cm_posts ADD id_newsletter bigint(21) NOT NULL DEFAULT 0");
		$res = sql_select(array(
			"C.pst_id",
			"C.pst_subject AS sujet",
			"C.pst_html AS html",
			"C.pst_text AS texte",
			"C.pst_date_create AS date",
			"C.pst_date_sent AS date_start",
			),
			'spip_cm_posts AS C',
			"C.id_newsletter=0");
		while ($row = sql_fetch($res)){

			$pst_id = $row['pst_id'];
			unset($row['pst_id']);

			// Tant qu'à faire, remplacer des mauvais restes
			$row['html']  = str_replace('@@NOM_LETTRE@@', $row['sujet'], $row['html']);
			$row['texte'] = str_replace('@@NOM_LETTRE@@', $row['sujet'], $row['texte']);

			if ($GLOBALS['meta']['charset']=='utf-8'){
				if (!is_utf8($row['sujet'])) $row['sujet'] = importer_charset($row['sujet'] ,'iso-8859-15');
				if (!is_utf8($row['html']))  $row['html']  = importer_charset($row['html']  ,'iso-8859-15');
				if (!is_utf8($row['texte'])) $row['texte'] = importer_charset($row['texte'] ,'iso-8859-15');
			}

			// id dans mailshot
			$hash_id = md5(serialize(array('sujet'=>&$row['sujet'],'html'=>&$row['html'],'texte'=>&$row['texte'])));

			// corriger les dates actuellement en time
			$row['date']       = date('Y-m-d H:i:s', $row['date']);
			#$row['date_start'] = date('Y-m-d H:i:s', $row['date_start']);

			$row['statut'] = 'publie';

			// remettre le format pour newsletter.
			$row['titre']        = $row['sujet'];
			$row['texte_email']  = $row['texte'];
			$row['html_email']   = $row['html'];
			$row['html_page']    = $row['html'];
			unset($row['sujet'], $row['texte'], $row['html'], $row['date_start']);
			$row['baked'] = 1;

			// inserer la lettre dans newsletter
			if ($id_newsletter = newsletters_import_one($row)){
				sql_updateq("spip_cm_posts", array('id_newsletter'=>$id_newsletter), "pst_id=".intval($pst_id));
				sql_updateq("spip_mailshots", array('id'=>$id_newsletter), "id=".sql_quote($hash_id));
				spip_log("import from spip_cm_posts newsletter $id_newsletter ".var_export($row,true),"newsletters");
			}

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}

		// C'est fini !
		sql_alter("TABLE spip_cm_posts DROP id_newsletter");
	}

}


/**
 * Insère une newsletter en base
 *
 * @param array $set
 *     Couples de données à enregistrer
 * @return int
 *     Identifiant de la newsletter
**/
function newsletters_import_one($set){
	include_spip("inc/drapeau_edition");
	$id = objet_inserer("newsletter",0,$set);
	objet_modifier("newsletter",$id,$set); // double detente
	debloquer_tous($GLOBALS['visiteur_session']['id_auteur']);
	return $id;
}


?>
