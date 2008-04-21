<?php
// -----------------------------------------------------------------------------
// Declaration des tables visites_virtuelles
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;



//-- Table visites_virtuelles ------------------------------------------
$visites_virtuelles = array(
		"id_visite"	=> "bigint(21) NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$visites_virtuelles_key = array(
		"PRIMARY KEY"	=> "id_visite"
		);

$tables_principales['spip_visites_virtuelles'] =
	array('field' => &$visites_virtuelles, 'key' => &$visites_virtuelles_key);

global $table_primary;
$table_primary['visites_virtuelles']="id_visitevirtuelle";

global $table_date;
$table_date['visites_virtuelles'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['visites_virtuelles']='visites_virtuelles';



//-- Table visites_virtuelles_lieux ------------------------------------------
$visites_virtuelles_lieux = array(
		"id_lieu"	=> "bigint(21) NOT NULL",
		"id_visite"	=> "bigint(21) NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"id_photo"	=> "bigint(21) NOT NULL",
		"boucler"	=> "ENUM('oui', 'non') DEFAULT 'oui' NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$visites_virtuelles_lieux_key = array(
		"PRIMARY KEY"	=> "id_lieu"
		);

$tables_principales['spip_visites_virtuelles_lieux'] =
	array('field' => &$visites_virtuelles_lieux, 'key' => &$visites_virtuelles_lieux_key);

global $table_primary;
$table_primary['visites_virtuelles_lieux']="id_visitevirtuelle";

global $table_date;
$table_date['visites_virtuelles_lieux'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['visites_virtuelles_lieux']='visites_virtuelles_lieux';



//-- Table visites_virtuelles_interactions ------------------------------------------
$visites_virtuelles_interactions = array(
		"id_interaction"	=> "bigint(21) NOT NULL",
		"id_lieu"	=> "bigint(21) NOT NULL",
		"id_visite"	=> "bigint(21) NOT NULL",
		"titre"	=> "text NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"x1"	=> "bigint(21) NOT NULL",
		"y1"	=> "bigint(21) NOT NULL",
		"x2"	=> "bigint(21) NOT NULL",
		"y2"	=> "bigint(21) NOT NULL",
		"type"	=> "text NOT NULL",
		"x_lieu_pointe"	=> "bigint(21)",
		"id_article_pointe"	=> "bigint(21)",
		"id_lieu_pointe"	=> "bigint(21)",
		"id_document_pointe"	=> "bigint(21)",
		"id_image"	=> "bigint(21)",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$visites_virtuelles_interactions_key = array(
		"PRIMARY KEY"	=> "id_interaction"
		);

$tables_principales['spip_visites_virtuelles_interactions'] =
	array('field' => &$visites_virtuelles_interactions, 'key' => &$visites_virtuelles_interactions_key);

global $table_primary;
$table_primary['visites_virtuelles_interactions']="id_visitevirtuelle";

global $table_date;
$table_date['visites_virtuelles_interactions'] = 'date_debut';
// si on declare les tables dans $table_des_tables, il faut mettre le prefixe

// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['visites_virtuelles_interactions']='visites_virtuelles_interactions';


?>