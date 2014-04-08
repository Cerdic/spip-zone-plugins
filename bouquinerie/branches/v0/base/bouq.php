<?php

/*
 *  Plugin Bouquinerie pour SPIP
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

//declaration des tables | spip_livres | spip_catalogues

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
include_spip('base/auxiliaires');

global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;

//----------------------------------------------------------
//-- TABLES PRINCIPALES ------------------------------------
//----------------------------------------------------------


//-- Table LIVRES ------------------------------------------
$livres = array(
  "id_livre"			=> "bigint(21) NOT NULL auto_increment",
  "id_catalogue"		=> "bigint(21) DEFAULT '0' NOT NULL",
  "id_reference"		=> "bigint(21) DEFAULT '0' NOT NULL", // numéro de référence
  "titre"			=> "text NOT NULL",
  "auteur"			=> "text NOT NULL",
  "illustrateur"		=> "text NOT NULL",
  "edition"			=> "text NOT NULL",
  "prix_vente"			=> "float DEFAULT '0' NOT NULL",
  "isbn"			=> "text NOT NULL",
  "statut"			=> "ENUM('a_vendre', 'vendu', 'reserve') NOT NULL DEFAULT 'a_vendre'",
  "etat_livre"			=> "mediumtext NOT NULL",
  "format"			=> "text NOT NULL",
  "etat_jaquette"		=> "mediumtext NOT NULL",
  "reliure"			=> "text NOT NULL",
  "type_livre"			=> "text NOT NULL",
  "lieu_edition"		=> "text NOT NULL",
  "annee_edition"		=> "text NOT NULL",
  "num_edition"			=> "text NOT NULL",
  "inscription"			=> "text NOT NULL",
  "remarque"			=> "mediumtext NOT NULL",
  "commentaire"			=> "mediumtext NOT NULL",
  "prix_achat"			=> "text NOT NULL",
  "lieu"			=> "text NOT NULL",
  "url_image"			=> "text NOT NULL",
  "date_ajout"			=> "TIMESTAMP",
  "maj"				=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
  "id_auteur"			=> "bigint(21) DEFAULT '0' NOT NULL",
  "num_facture"			=> "text NOT NULL",
  "type_import"			=> "ENUM('aucun', 'priceminister') NOT NULL DEFAULT 'aucun'"
);
                    
$livres_key = array(
  "PRIMARY KEY"	=> "id_livre",
  "KEY"		=> "id_catalogue"
);
                                
$tables_principales['spip_livres'] =
   array('field' => &$livres, 'key' => &$livres_key);

//-- Table CATALOGUES --------------------------------------------------

$catalogues = array(
  "id_catalogue"		=> "bigint(21) NOT NULL auto_increment",
  "titre"			=> "text NOT NULL",
  "descriptif"			=> "mediumtext NOT NULL"
);

$catalogues_key = array(
  "PRIMARY KEY" => "id_catalogue"
);

$tables_principales['spip_catalogues'] =
  array('field' => &$catalogues, 'key' => &$catalogues_key);
  

//-- Table MOTS_LIVRES -------------------------------------------------

$mots_livres = array(
  "id_mot"			=> "bigint(21) DEFAULT '0' NOT NULL",
  "id_livre"			=> "bigint(21) DEFAULT '0' NOT NULL"
);
  
$mots_livres_key = array(
  "PRIMARY KEY" => "id_mot, id_livre",
  "KEY id_mot" => "id_mot"
);


$tables_principales['spip_mots_livres'] =
  array('field' => &$mots_livres, 'key' => &$mots_livres_key);


//-- Table DOCUMENTS_LIVRES -------------------------------------------

$documents_livres = array(
  "id_document"		=> "bigint(21) DEFAULT '0' NOT NULL",
  "id_livre"		=> "bigint(21) DEFAULT '0' NOT NULL"
);

$documents_livres_key = array(
  "PRIMARY KEY" => "id_document, id_livre",
  "KEY id_livre" => "id_livre"
);

$tables_principales['spip_documents_livres'] =
  array('field' => &$documents_livres, 'key' => &$documents_livres_key);

//----------------------------------------------------------------------
//-- TABLES AUXILLIAIRES -----------------------------------------------
//----------------------------------------------------------------------


//-- Table LIVRES_CATALOGUES -------------------------------------------

$livres_catalogues = array(
  "id_livre"			=> "bigint(21) DEFAULT '0' NOT NULL",
  "id_catalogue"		=> "bigint(21) DEFAULT '0' NOT NULL"
);

$livres_catalogues_key = array(
  "PRIMARY KEY" => "id_livre, id_catalogue",
  "KEY id_catalogue" => "id_catalogue"
);

$tables_auxiliaires['spip_livres_catalogues'] =
  array('field' => &$livres_catalogues, 'key' => &$livres_catalogues_key);

//-- table des table et table primary ------------------------------------

global $table_des_tables, $table_primary;
global $exceptions_des_tables;
global $tables_relations;

$table_primary['livres']='id_livre';
$table_primary['catalogues']='id_catalogue';
$table_primary['mots_livres']='id_mot, id_livre';
$table_primary['documents_livres']='id_document, id_livre';

$table_des_tables['livres']='livres';
$table_des_tables['catalogues']='catalogues';
$table_des_tables['mots_livres']='mots_livres';
$table_des_tables['documents_livres']='documents_livres';
$table_des_tables['livres_catalogues']='livres_catalogues';

$tables_jointures['spip_mots'][]= 'mots_livres';
$tables_jointures['spip_livres'][]= 'mots_livres';

$tables_jointures['spip_documents'][]= 'documents_livres';
$tables_jointures['spip_livres'][]= 'documents_livres';

$exceptions_des_tables['mots']['id_livre']=array('spip_mots_livres', 'id_livre');
$exceptions_des_tables['documents']['id_livre']=array('spip_documents_livres', 'id_livre');

$tables_relations['mots']['id_livre']='mots_livres';
$tables_relations['livres']['id_mot']='mots_livres';

$tables_relations['documents']['id_livre']='documents_livres';
$tables_relations['livres']['id_document']='documents_livres';

?>
