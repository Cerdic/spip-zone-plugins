<?php
// -----------------------------------------------------------------------------
// Declaration des tables evenements
// creation 11/03/2006 pour SPIP 1.9

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

//-- Table EVENEMENTS ------------------------------------------
$timeline = array(
		"id_timeline"	=> "bigint(21) NOT NULL",
		"id_article"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"date_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"date_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"lieu"	=> "text NOT NULL",
		"horaire" => "ENUM('oui','non') DEFAULT 'oui' NOT NULL",
		"id_evenement_source"	=> "bigint(21) NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$timeline_key = array(
		"PRIMARY KEY"	=> "id_timeline",
		"KEY date_debut"	=> "date_debut",
		"KEY date_fin"	=> "date_fin",
		"KEY id_article"	=> "id_article"
		);

$tables_principales['spip_timeline'] =
	array('field' => &$timeline, 'key' => &$timeline_key);


//-- Table de relations MOTS_EVENEMENTS----------------------
$spip_mots_evenements = array(
		"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
		"id_evenement"	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_mots_evenements_key = array(
		"PRIMARY KEY"	=> "id_mot, id_evenement",
		"KEY id_evenement"	=> "id_evenement");


//-- Jointures ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_articles'][]= 'timeline';
$tables_jointures['spip_rubriques'][] = 'timeline';

global $table_primary;
$table_primary['evenements']="id_evenement";

global $table_date;
$table_date['timeline'] = 'date_debut';

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['timeline']='timeline';

?>