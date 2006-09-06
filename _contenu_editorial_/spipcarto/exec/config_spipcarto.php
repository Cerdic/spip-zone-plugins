<?php
define('_DIR_PLUGIN_SPIPCARTO',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));

function exec_config_spipcarto() {
  global $connect_statut, $connect_toutes_rubriques;

  include_spip ("inc/presentation");
  include_spip ("base/abstract_sql");

  debut_page('&laquo; '._T('spipcarto:configuration').' &raquo;', 'configurations', 'mots_partout');

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	exit;
  }

  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {
	
	$table_pref = 'spip';
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
	
	/************************************************************************/
	/*MODIFICATION/CREATION des tables*/
	/************************************************************************/

if ($_REQUEST['installation']=='oui'){
	spip_query("CREATE TABLE IF NOT EXISTS `".$table_pref."_carto_cartes` (
  `id_carto_carte` smallint(5) unsigned NOT NULL auto_increment,
  `url_carte` varchar(255) NOT NULL default '',
  `titre` varchar(255) NOT NULL default '',
  `texte` text NOT NULL,
  `callage` text NOT NULL,
  `id_srs` smallint(5) unsigned default NULL,
  `statut` VARCHAR(8) NOT NULL default 'publie',
  `idx` enum('','1','non','oui','idx') NOT NULL default '',
  PRIMARY KEY  (`id_carto_carte`),
  KEY `titre` (`titre`)
) TYPE=MyISAM;");
	spip_query("CREATE TABLE IF NOT EXISTS `".$table_pref."_carto_cartes_articles` (
  `id_carto_carte` smallint(5) unsigned NOT NULL default '0',
  `id_article` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_carto_carte`,`id_article`)
) TYPE=MyISAM;");
	spip_query("CREATE TABLE IF NOT EXISTS `".$table_pref."_carto_objets` (
  `id_carto_objet` smallint(5) unsigned NOT NULL auto_increment,
  `id_carto_carte` smallint(5) unsigned NOT NULL default '0',
  `titre` varchar(255) NOT NULL default '',
  `texte` text NOT NULL,
  `url_objet` text,
  `url_logo` text,
  `geometrie` text,
  `statut` VARCHAR(8) NOT NULL default 'publie',
  `idx` enum('','1','non','oui','idx') NOT NULL default '',
  PRIMARY KEY  (`id_carto_objet`),
  KEY `id_carte` (`id_carto_carte`),
  KEY `titre` (`titre`),
  KEY `statut` (`statut`)
) TYPE=MyISAM;");
	spip_query("CREATE TABLE IF NOT EXISTS `".$table_pref."_carto_srs` (
  `id_carto_srs` smallint(5) unsigned NOT NULL auto_increment,
  `label` varchar(50) NOT NULL default '',
  `code` varchar(20) NOT NULL default '-1',
  PRIMARY KEY  (`id_carto_srs`)
) TYPE=MyISAM;");
	spip_query("CREATE TABLE IF NOT EXISTS `".$table_pref."_documents_carto_cartes` (
  `id_carto_carte` smallint(5) unsigned NOT NULL default '0',
  `id_document` smallint(5) unsigned NOT NULL default '0',
  `callage` text,
  PRIMARY KEY  (`id_carto_carte`,`id_document`)
) TYPE=MyISAM;");
	spip_query("CREATE TABLE IF NOT EXISTS `".$table_pref."_mots_carto_objets` (
  `id_carto_objet` smallint(5) unsigned NOT NULL default '0',
  `id_mot` smallint(5) unsigned NOT NULL default '0',
  `ordre` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_carto_objet`,`id_mot`),
  KEY `ordre` (`ordre`)
) TYPE=MyISAM;");
	spip_query("ALTER TABLE ".$table_pref."_groupes_mots ADD carto_objets CHAR( 3 ) NOT NULL AFTER syndic ;");
	spip_query("ALTER TABLE ".$table_pref."_groupes_mots ADD INDEX ( carto_objets ) ;");
	
	$r=spip_query("SELECT code FROM ".$table_pref."_carto_srs WHERE code='-1';");
	if ($row=spip_fetch_array($r)){
		spip_query("UPDATE ".$table_pref."_meta SET valeur='oui', maj=now() WHERE nom='activer_carto';");
//		spip_query("UPDATE ".$table_pref."_meta SET valeur='oui', maj=now() WHERE nom='carto_mots';");
	} else {
		spip_query("INSERT INTO ".$table_pref."_carto_srs (id_carto_srs, label, code) VALUES ('-1', 'Par defaut', '-1');");
		spip_query("INSERT INTO ".$table_pref."_carto_srs (label, code) VALUES ('NTF (Paris) / Lambert zone II etendu', 'EPSG:27582');");
		spip_query("INSERT INTO ".$table_pref."_carto_srs (label, code) VALUES ('NTF (Paris) / Lambert zone II', 'EPSG:27572');");
		spip_query("INSERT INTO ".$table_pref."_carto_srs (label, code) VALUES ('NTF (Paris) / Lambert zone III', 'EPSG:27573');");
		spip_query("INSERT INTO ".$table_pref."_carto_srs (label, code) VALUES ('NTF (Paris) / Lambert zone I', 'EPSG:27571');");
		spip_query("INSERT INTO ".$table_pref."_carto_srs (label, code) VALUES ('NTF (Paris) / Lambert zone IV', 'EPSG:27574');");
		spip_query("INSERT INTO ".$table_pref."_carto_srs (label, code) VALUES ('WGS 84', 'epsg:4326');");
		spip_query("INSERT INTO ".$table_pref."_carto_srs (label, code) VALUES ('ED50', 'epsg:4230');");
		spip_query("INSERT INTO ".$table_pref."_meta(nom, valeur, maj) VALUES ('activer_carto','oui',now());");
//		spip_query("INSERT INTO ".$table_pref."_meta(nom, valeur, maj) VALUES ('carto_mots','oui',now());");
	}
	$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='MotsPartout:tables_installees';");
	if ($row=spip_fetch_array($r)){
		$tables=unserialize($row[0]);
		$tables['carto_objets']=true;
		spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='MotsPartout:tables_installees';");
	}
	else {
		$tables=array('articles'=>true,'rubriques'=>true,'breves'=>true,'syndic'=>true,'documents'=>true,'carto_objets'=>true);
		spip_query("INSERT INTO ".$table_pref."_meta(nom, valeur, maj) VALUES ('MotsPartout:tables_installees','".addslashes(serialize($tables))."',now());");
	}
	spip_query("INSERT INTO ".$table_pref."_meta(nom, valeur, maj) VALUES ('config_precise_groupes','oui',now());");
	spip_query("UPDATE ".$table_pref."_meta SET valeur='oui', maj=now() WHERE nom='config_precise_groupes';");
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_elements_objet';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = array('titre'=>8,'texte'=>5);
			$tables['spip_carto_objets'] = array('titre'=>4,'texte'=>2,'url_objet'=>1);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_elements_objet';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_objet_associes';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
						$tables['spip_articles']['spip_carto_cartes'] = 2;
						$tables['spip_carto_cartes'] = array('spip_carto_objets'=>1);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_objet_associes';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_elements_associes';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = array('titre'=>3,'texte'=>1);
			$tables['spip_carto_objets'] = array('titre'=>3,'texte'=>1);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_elements_associes';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_critere_indexation';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = "statut='publie'";
			$tables['spip_carto_objets'] = "statut='publie'";
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_critere_indexation';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_critere_optimisation';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = "statut<>'publie'";
			$tables['spip_carto_objets'] = "statut<>'publie'";
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_critere_optimisation';");
		}

		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='index_table';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables[]='spip_carto_cartes';
			$tables[]='spip_carto_objets';
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='index_table';");
		}
}
//desinstallation
elseif (($_REQUEST['installation']=='non')&&(($connect_statut == '0minirezo') AND $connect_toutes_rubriques)){
	spip_query("UPDATE ".$table_pref."_meta SET valeur='non', maj=now() WHERE nom='activer_carto';");
	$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='MotsPartout:tables_installees';");
	if ($row=spip_fetch_array($r)){
		$tables=unserialize($row[0]);
		$tables['carto_objets']=false;
		spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='MotsPartout:tables_installees';");
	}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_elements_objet';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			unset($tables['spip_carto_cartes']);
			unset($tables['spip_carto_objets']);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_elements_objet';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_objet_associes';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			unset($tables['spip_carto_cartes']);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_objet_associes';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_elements_associes';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			unset($tables['spip_carto_cartes']);
			unset($tables['spip_carto_objets']);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_elements_associes';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_critere_indexation';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			unset($tables['spip_carto_cartes']);
			unset($tables['spip_carto_objets']);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_critere_indexation';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_critere_optimisation';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			unset($tables['spip_carto_cartes']);
			unset($tables['spip_carto_objets']);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_critere_optimisation';");
		}

		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='index_table';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			unset($tables['spip_carto_cartes']);
			unset($tables['spip_carto_objets']);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='index_table';");
		}
}
//upgrade
elseif (lire_meta("carto_mots")=='oui') {
		spip_query("DELETE FROM ".$table_pref."_meta WHERE nom='carto_mots';");
		spip_query("ALTER TABLE ".$table_pref."_carto_cartes ADD statut VARCHAR(8) NOT NULL default 'publie';");
		spip_query("ALTER TABLE ".$table_pref."_carto_cartes ADD idx enum('','1','non','oui','idx') NOT NULL default '';");
		spip_query("ALTER TABLE ".$table_pref."_carto_objets ADD statut VARCHAR(8) NOT NULL default 'publie';");
		spip_query("ALTER TABLE ".$table_pref."_carto_objets ADD idx enum('','1','non','oui','idx') NOT NULL default '';");	
	$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='MotsPartout:tables_installees';");
	if ($row=spip_fetch_array($r)){
		$tables=unserialize($row[0]);
		$tables['carto_objets']=true;
		spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='MotsPartout:tables_installees';");
	}
	else {
		$tables=array('articles'=>true,'rubriques'=>true,'breves'=>true,'syndic'=>true,'documents'=>true,'carto_objets'=>true);
		spip_query("INSERT INTO ".$table_pref."_meta(nom, valeur, maj) VALUES ('MotsPartout:tables_installees','".addslashes(serialize($tables))."',now());");
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_elements_objet';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = array('titre'=>8,'texte'=>5);
			$tables['spip_carto_objets'] = array('titre'=>4,'texte'=>2,'url_objet'=>1);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_elements_objet';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_objet_associes';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
						$tables['spip_carto_cartes'] = array('spip_carto_objets'=>1);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_objet_associes';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_elements_associes';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = array('titre'=>3,'texte'=>1);
			$tables['spip_carto_objets'] = array('titre'=>3,'texte'=>1);
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_elements_associes';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_critere_indexation';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = "statut='publie'";
			$tables['spip_carto_objets'] = "statut='publie'";
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_critere_indexation';");
		}
		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='INDEX_critere_optimisation';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables['spip_carto_cartes'] = "statut<>'publie'";
			$tables['spip_carto_objets'] = "statut<>'publie'";
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='INDEX_critere_optimisation';");
		}

		$r=spip_query("SELECT valeur FROM ".$table_pref."_meta WHERE nom='index_table';");
		if ($row=spip_fetch_array($r)){
			$tables=unserialize($row[0]);
			$tables[]='spip_carto_cartes';
			$tables[]='spip_carto_objets';
			spip_query("UPDATE ".$table_pref."_meta SET valeur='".addslashes(serialize($tables))."', maj=now() WHERE nom='index_table';");
		}
	}
}	
	ecrire_metas();
	/*Affichage*/

	echo '<br><br><br>';
	
	gros_titre(_T('spipcarto:config'));

	barre_onglets("configuration", "config_spipcarto");

	debut_gauche();

	debut_droite();
	

//	include_spip('inc/config');
//	avertissement_config();

	debut_cadre_enfonce();

	if (lire_meta("activer_carto")=='oui')
		echo "<a href=\"".generer_url_ecrire('config_spipcarto',"installation=non")."\">D&eacute;sinstaller</a>";
	else
		echo "<a href=\"".generer_url_ecrire('config_spipcarto',"installation=oui")."\">Installer</a>";

	fin_cadre_enfonce();

  } 

  fin_page();
  
}

?>
