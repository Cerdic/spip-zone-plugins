<?php
// -----------------------------------------------------------------------------
// Declaration des tables notes de frais
// creation 18/10/2011 pour SPIP 2.1

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

//-- Table FRAISDONS ------------------------------------------
$fraisdons = array(
		"id_fraisdon"	=> "bigint(21) NOT NULL auto_increment",
		"id_auteur"	=> "bigint(21) NOT NULL",
		"anneecomptable"	=> "int(11) default 0 NOT NULL",
		"regroupement"	=> "varchar(16) default '' NOT NULL",
		"datefrais"	=> "datetime default '000-00-00 00:00:00' NOT NULL",
		"typefrais"	=> "varchar(16) default '' NOT NULL",
		"titre"	=> "mediumtext default '' NOT NULL",
		"km"	=> "decimal(11,2) default 0 NOT NULL",
		"coef"	=> "decimal(11,3) default 0 NOT NULL",
		"montant"	=> "decimal(11,2) default 0 NOT NULL",
		"choixremb"	=> "varchar(16) default '' NOT NULL",
		"etat"	=> "varchar(16) default '' NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP"
		);

$fraisdons_key = array(
		"PRIMARY KEY"	=> "id_fraisdon",
		"KEY id_auteur_cpt"	=> "id_auteur, anneecomptable"
		);

$tables_principales['spip_fraisdons'] =
	array('field' => &$fraisdons, 'key' => &$fraisdons_key);


//-- Jointures ----------------------------------------------------
// pour les imports / exports
global $tables_jointures;
$tables_jointures['spip_auteurs'][]= 'fraisdons';
$tables_jointures['spip_fraisdons'][] = 'auteurs';

// jamais utilisé dans SPIP
global $table_primary;
$table_primary['fraisdons']="id_fraisdon";

// si on declare les tables dans $table_des_tables, il faut mettre le prefixe
// 'spip_' dans l'index de $tables_principales
global $table_des_tables;
$table_des_tables['fraisdons']='fraisdons';



global  $table_des_traitements;
/*
   $table_des_traitements['FRAISDON'][]= 'propre(%s)';
*/

?>
