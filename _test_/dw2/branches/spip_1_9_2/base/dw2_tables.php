<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Description Tables MySQL
| 2.13 -> Skedus : prefix spip sur tables
+--------------------------------------------+
*/
if (!defined("_ECRIRE_INC_VERSION")) return;


global $tables_principales;
global $table_des_tables;

$table_des_tables['dw2_doc'] = 'dw2_doc';


$spip_dw2_doc = array(
"id_document"	=> "BIGINT(21) NOT NULL",
"nom"			=> "TEXT NOT NULL",
"url"			=> "VARCHAR(255) NOT NULL",
"total"			=> "INTEGER UNSIGNED DEFAULT '0' NOT NULL",
"dateur"		=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
"doctype"		=> "TINYTEXT NOT NULL",
"id_doctype"	=> "BIGINT(21) NOT NULL",
"categorie"		=> "TEXT NOT NULL",
"date_crea"		=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
"heberge"		=> "VARCHAR(255) DEFAULT 'local' NOT NULL",
"id_serveur"	=> "BIGINT(21) NOT NULL",
"statut"		=> "VARCHAR(10) DEFAULT 'actif' NOT NULL"
);

$spip_dw2_doc_key = array(
"PRIMARY KEY"	=> "id_document"
);

$spip_dw2_triche = array(
"id"			=> "INT NOT NULL AUTO_INCREMENT",
"ip"			=> "VARCHAR(30) NOT NULL",
"idsite"		=> "VARCHAR(100) NOT NULL",
"time"			=> "INT"
);

$spip_dw2_triche_key = array(
"PRIMARY KEY"	=> "id"
);

$spip_dw2_stats = array(
"date"			=> "DATE NOT NULL",
"id_doc"		=> "INTEGER UNSIGNED NOT NULL",
"telech"		=> "INTEGER UNSIGNED NOT NULL"
);

# h.26/9 // "PRIMARY KEY"	=> "(date, id_doc)",
$spip_dw2_stats_key = array(
"PRIMARY KEY"	=> "date, id_doc"
);

$spip_dw2_serv_ftp = array(
"id_serv"		=> "INT NOT NULL AUTO_INCREMENT",
"serv_ftp"		=> "VARCHAR(255) NOT NULL",
"host_dir"		=> "VARCHAR(255) NOT NULL",
"port"			=> "MEDIUMINT DEFAULT '21' NOT NULL",
"login"			=> "VARCHAR(255) BINARY NOT NULL",
"mot_passe"		=> "VARCHAR(255) NOT NULL",
"site_distant"	=> "VARCHAR(255) NOT NULL",
"chemin_distant"=> "VARCHAR(255) NOT NULL",
"designe"		=> "TEXT NOT NULL",
"date_crea"		=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL"
);

$spip_dw2_serv_ftp_key = array(
"PRIMARY KEY"	=> "id_serv"
);

# h.14/10 -> 2.13
$spip_dw2_config = array(
"nom"			=> "VARCHAR (255) NOT NULL",
"valeur"		=> "text DEFAULT ''",
"maj"			=> "TIMESTAMP"
);

$spip_dw2_config_key = array(
"PRIMARY KEY"	=> "nom"
);

#h.28/12/06 -> 2.13 - restreint
$spip_dw2_acces_restreint = array(
"id"			=> "INT NOT NULL AUTO_INCREMENT",
"id_document"	=> "BIGINT(21) NOT NULL",
"id_article"	=> "BIGINT(21) NOT NULL",
"id_rubrique"	=> "BIGINT(21) NOT NULL",
"restreint"		=> "TINYINT UNSIGNED DEFAULT '0' NOT NULL",
"maj"			=> "TIMESTAMP"
);

$spip_dw2_acces_restreint_key = array(
"PRIMARY KEY"	=> "id, id_document, id_article, id_rubrique"
);

#h.15/01/07 -> 2.13 - restreint
$spip_dw2_stats_auteurs = array(
"date"			=> "DATE NOT NULL",
"id_auteur"		=> "BIGINT(21) NOT NULL",
"id_doc"		=> "INTEGER UNSIGNED NOT NULL",
"date_enreg"	=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL"
);

$spip_dw2_stats_auteurs_key = array(
"PRIMARY KEY"	=> "date, id_auteur"
);


$tables_principales['spip_dw2_doc'] =
	array('field' => &$spip_dw2_doc, 'key' => &$spip_dw2_doc_key);
$tables_principales['spip_dw2_acces_restreint'] =
	array('field' => &$spip_dw2_acces_restreint, 'key' => &$spip_dw2_acces_restreint_key);
$tables_principales['spip_dw2_serv_ftp'] =
	array('field' => &$spip_dw2_serv_ftp, 'key' => &$spip_dw2_serv_ftp_key);

?>
