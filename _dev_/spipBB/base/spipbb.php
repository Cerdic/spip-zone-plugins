<?php
#--------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                      #
#  File    : base/spipbb - tables necessaires au plugin  #
#  Authors : Chryjs, 2007 et als                         #
#  Contact : chryjs¡@!free¡.!fr                          #
#--------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// Structure des tables
//

global $tables_principales;
global $tables_auxiliaires;

// suivi des visites (sur la base de spip_visites_articles)

$spip_visites_forums = array(
	"date"		=> "date NOT NULL",
	"id_forum" 	=> "BIGINT(21) UNSIGNED NOT NULL",
	"visites" 	=> "INT(10) UNSIGNED NOT NULL DEFAULT '0'",
	"maj" 		=> "TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
	);

$spip_visites_forums_key = array(
	'PRIMARY KEY'	=> "date, id_forum"
	); //(`date`,`id_forum`)

// spip_referers_forums ?

$tables_principales['spip_visites_forums'] = array(
	'field' => &$spip_visites_forums,
	'key' => &$spip_visites_forums_key);

//-- Relations ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_visites_forums'][] = 'spip_forums';

global $table_des_tables;
$table_des_tables['spip_visites_forums'] = 'spip_visites_forums';

?>