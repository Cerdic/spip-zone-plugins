<?php

/*
 *  Plugin Atelier pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

//declaration des tables | spip_projets | spip_taches | spip_taches_projets

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
include_spip('base/auxiliaires');

global $tables_principales;
global $tables_auxiliaires;

//----------------------------------------------------------
//-- TABLES PRINCIPALES ------------------------------------
//----------------------------------------------------------


//-- Table PROJETS ------------------------------------------
$projets = array(
  "id_projet"			=> "bigint(21) NOT NULL auto_increment",
  "titre"			=> "text NOT NULL",
  "descriptif"			=> "text NOT NULL",
  "type"			=> "ENUM('plugin', 'squelette') NOT NULL DEFAULT 'plugin'",
  "prefixe"			=> "text NOT NULL",
  "versions"			=> "LONGTEXT NULL DEFAULT NULL"

);
                    
$projets_key = array(
  "PRIMARY KEY"	=> "id_projet"
);
                                
$tables_principales['spip_projets'] =
   array('field' => &$projets, 'key' => &$projets_key);


//-- Table TACHES ------------------------------------------
$taches = array(
  "id_tache"			=> "bigint(21) NOT NULL auto_increment",
  "id_projet"			=> "bigint(21) NOT NULL",
  "id_auteur"			=> "bigint(21) NOT NULL",
  "titre"			=> "text NOT NULL",
  "descriptif"			=> "text NOT NULL",
  "etat"			=> "ENUM('ouverte', 'fermee') NOT NULL DEFAULT 'ouverte'",
  "urgence"			=> "ENUM('tres_forte','forte','moyenne','faible','tres_faible') NOT NULL DEFAULT 'moyenne'",
  "version"			=> "text NOT NULL"
);
                    
$taches_key = array(
  "PRIMARY KEY"	=> "id_tache"
);
                                
$tables_principales['spip_taches'] =
   array('field' => &$taches, 'key' => &$taches_key);

//-- Table BUGS --------------------------------------------------------
$bugs = array(
  "id_bug"			=> "bigint(21) NOT NULL auto_increment",
  "id_projet"			=> "bigint(21) NOT NULL",
  "titre"			=> "text NOT NULL",
  "descriptif"			=> "text NOT NULL",
  "version"			=> "text NOT NULL",
  "version_spip"		=> "text NOT NULL",
  "date"			=> "TIMESTAMP"
);

$bugs_key = array(
  "PRIMARY KEY" => "id_bug"
);

$tables_principales['spip_bugs'] = 
   array('field' => &$bugs, 'key' => &$bugs_key);

//----------------------------------------------------------------------
//-- TABLES AUXILLIAIRES -----------------------------------------------
//----------------------------------------------------------------------

//-- Table TACHES_PROJETS ------------------------------------------
$taches_projets = array(
  "id_tache"			=> "bigint(21) DEFAULT '0' NOT NULL",
  "id_projet"			=> "bigint(21) DEFAULT '0' NOT NULL"
);

$taches_projets_key = array(
  "PRIMARY KEY" => "id_tache, id_projet",
  "KEY id_projet" => "id_projet"
);

$tables_auxiliaires['spip_taches_projets'] =
  array('field' => &$taches_projets, 'key' => &$taches_projets_key);

//-- table des table et table primary ------------------------------------

global $table_des_tables, $table_primary;

$table_primary['taches']='id_tache';
$table_primary['projets']='id_projet';
$table_primary['bugs']='id_bug';

$table_des_tables['taches']='taches';
$table_des_tables['projets']='projets';
$table_des_tables['bugs']='bugs';

?>
