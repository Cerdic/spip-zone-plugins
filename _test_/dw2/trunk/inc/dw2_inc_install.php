<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| fonctions : Création / MaJ des Tables.
+--------------------------------------------+
*/


//
// creation des tables de DW2
//

# duplique de function in create.php
function dw2_create_table($nom, $champs, $cles, $autoinc=false) {
	$query = ''; $keys = ''; $s = ''; $p='';

	foreach($cles as $k => $v) {
		$keys .= "$s\n\t\t$k ($v)";
		if ($k == "PRIMARY KEY")
			$p = $v;
		$s = ",";
	}
	$s = '';

	foreach($champs as $k => $v) {
		$query .= "$s\n\t\t$k $v" .
		(($autoinc && ($p == $k)) ? " auto_increment" : '');
		$s = ",";
	}
	spip_query_db("CREATE TABLE IF NOT EXISTS $nom ($query" . ($keys ? ",$keys" : '') . ")\n");
}


function ecriture_tables_dw2() {
	// charge def de tables
	# h.17/10 -> disfonctionne  ==> include(_DIR_PLUGIN_DW2."/base/dw2_tables.php");
	include(_DIR_PLUGINS."/dw2/base/dw2_tables.php");
	
	// prepa tableau tables .. 
	# Skedus 09/2006: prefixage des tables avec spip_ ou le prefix choisi
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix']."_";
	else $table_pref = "spip_";
	
	$tables_dw2[$table_pref.'dw2_doc']      = array('field' => &$spip_dw2_doc,     'key' => &$spip_dw2_doc_key);
	$tables_dw2[$table_pref.'dw2_triche']   = array('field' => &$spip_dw2_triche,  'key' => &$spip_dw2_triche_key);
	$tables_dw2[$table_pref.'dw2_stats']    = array('field' => &$spip_dw2_stats,   'key' => &$spip_dw2_stats_key);
	$tables_dw2[$table_pref.'dw2_serv_ftp'] = array('field' => &$spip_dw2_serv_ftp,'key' => &$spip_dw2_serv_ftp_key);
	$tables_dw2[$table_pref.'dw2_config']   = array('field' => &$spip_dw2_config,  'key' => &$spip_dw2_config_key);
	$tables_dw2[$table_pref.'dw2_acces_restreint']   = array('field' => &$spip_dw2_acces_restreint,  'key' => &$spip_dw2_acces_restreint_key);
	$tables_dw2[$table_pref.'dw2_stats_auteurs']   = array('field' => &$spip_dw2_stats_auteurs,  'key' => &$spip_dw2_stats_auteurs_key);

	// créer tables dans bdd
	foreach($tables_dw2 as $k => $v) {
		dw2_create_table($k, $v['field'], $v['key'], false);// false : auto_increm dans def. des tables !
	}
}	

	
//
// maj des anciennes tables
//
function maj_tables_dw2($old_vers) {
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix']."_";
	else $table_pref = "spip_";
	
		if ($old_vers < 2.013)
			{
			spip_query("ALTER TABLE dw2_doc ADD heberge VARCHAR(255) DEFAULT 'local' NOT NULL");
			spip_query("ALTER TABLE dw2_doc ADD id_serveur BIGINT(21) NOT NULL");
			spip_query("ALTER TABLE dw2_doc MODIFY doctype TINYTEXT NOT NULL");
			spip_query("ALTER TABLE dw2_doc ADD statut VARCHAR(10) DEFAULT 'actif' NOT NULL");
			spip_query("ALTER TABLE dw2_doc DROP COLUMN id_rubrique");
			spip_query("ALTER TABLE dw2_doc DROP COLUMN id_secteur");
			spip_query("ALTER TABLE dw2_serv_ftp ADD designe TEXT NOT NULL");
			}

		if ($old_vers < 2.016)
			{
			spip_query("ALTER TABLE dw2_doc DROP PRIMARY KEY");
			spip_query("ALTER TABLE dw2_doc CHANGE id_doc id_document BIGINT(21) NOT NULL");
			spip_query("ALTER TABLE dw2_doc ADD PRIMARY KEY (id_document)");
			}

		if ($old_vers < 2.11)
			{
			spip_query("ALTER TABLE dw2_stats DROP PRIMARY KEY");
			spip_query("ALTER TABLE dw2_stats DROP INDEX id_doc");
			spip_query("ALTER TABLE dw2_stats ADD PRIMARY KEY (date, id_doc)");
			}
		
		if ($old_vers < 2.13)
			{
			// (skedus) passer les tables en prefixage spip_ ou autre..
			spip_query("RENAME TABLE dw2_doc TO ".$table_pref."dw2_doc");
			spip_query("RENAME TABLE dw2_triche TO ".$table_pref."dw2_triche");
			spip_query("RENAME TABLE dw2_stats TO ".$table_pref."dw2_stats");
			spip_query("RENAME TABLE dw2_serv_ftp TO ".$table_pref."dw2_serv_ftp");
			// ajout champ 'port'  pour serveur distant (deloc)
			spip_query("ALTER TABLE spip_dw2_serv_ftp ADD port MEDIUMINT DEFAULT '21' NOT NULL AFTER host_dir");
			}
			
		/*if ($old_vers < 2.1x) { }*/
		
}


//
// param de config de DW2 et val par defaut (install + maj sur old vers.)
//
function dw2_init_param($old_version) {
	//
	#h.09/03/07 passe 'forcer_url_dw2' à "non" .. à suivre!
	$liste_param = array(
		'version_installee' => $old_version,
		'anti_triche' => 'oui',
		'nbr_lignes_tableau' => '15',
		'type_categorie' => 'secteur',
		'extens_logo_serveur' => 'gif',
		'mode_enregistre_doc' => 'manuel',
		'jours_affiche_nouv' => '3',
		'mode_affiche_images' => '2',
		'avis_maj' => 'non',
		'squelette_cata_public' => 'catalogue',
		'mode_restreint' => 'non',
		'criteres_auto_doc' => '',
		'message_maj' => '',
		'forcer_url_dw2' => 'non'
	);
	// si param pas déjà declare .. on le fait
	while (list($nom, $valeur) = each($liste_param)) {
		if(!$GLOBALS['dw2_param'][$nom]) {
			$GLOBALS['dw2_param'][$nom]=$valeur;
		}
	}
}

?>
