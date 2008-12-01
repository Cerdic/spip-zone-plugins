<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : base/spipbb - tables necessaires au plugin    #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

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
if (defined("_BASE_SPIPBB")) return; else define("_BASE_SPIPBB", true);
include_spip('inc/spipbb_common'); //if (!defined("_INC_SPIPBB_COMMON")) die("bye");
//spipbb_log('included',2,__FILE__);
//
// Structure des tables
//

// maintenant dans spipbb_common
//$tables_spipbb = array( 'spip_visites_forums', 'spip_auteurs_spipbb', 'spip_spam_words', 'spip_spam_words_log', 'spip_ban_liste' );

// suivi des visites (sur la base de spip_visites_articles)

function spipbb_declarer_tables_principales($tables_principales){


$spip_visites_forums = array(
	"date"		=> "date NOT NULL",
	"id_forum" 	=> "bigint(21) NOT NULL",
	"visites" 	=> "int(10) NOT NULL default '0'",
	"maj" 		=> "timestamp" , // NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
	);

$spip_visites_forums_key = array(
	'PRIMARY KEY'	=> "date,id_forum"
	); //(`date`,`id_forum`)

$tables_principales['spip_visites_forums'] = array(
	'field' => &$spip_visites_forums,
	'key' => &$spip_visites_forums_key);

$spip_auteurs_spipbb = array( // table spip_auteurs_spipbb
	"id_auteur"	=> "bigint(21) NOT NULL", // primary key
	"spam_warnings"	=> "int(10) NOT NULL default '0'",
	'ip_auteur'	=> "varchar(16) default NULL",
	'ban_date'	=> "timestamp", // NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
	'ban'		=> "varchar(3) default 'non'"	// ban 'oui' 'non' default 'non'
	);

$spip_auteurs_spipbb_key = array(
		'KEY id_auteur' => "id_auteur"
		);

$tables_principales['spip_auteurs_spipbb'] = array(
		'field' => &$spip_auteurs_spipbb,
		'key' => &$spip_auteurs_spipbb_key );

$spip_spam_words = array(
	"id_spam_word"	=> "bigint(21) NOT NULL auto_increment",
	"spam_word"	=> "varchar(255) NOT NULL" );

$spip_spam_words_key = array( 'PRIMARY KEY' => "id_spam_word",
				'KEY spam_word' => "spam_word" );

$tables_principales['spip_spam_words'] = array(
		'field' => &$spip_spam_words,
		'key' => &$spip_spam_words_key );

$spip_spam_words_log = array(
	"id_spam_log"	=> "bigint(21) NOT NULL auto_increment",
	"id_auteur"	=> "bigint(21) NOT NULL",
	"ip_auteur"	=> "varchar(16) default NULL",
	"login"		=> "varchar(255) default NULL",
	"log_date"	=> "timestamp", // NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"
	"titre"		=> "text",
	"message"	=> "mediumtext",
	"id_forum"	=> "bigint(21) NOT NULL",
	"id_article"	=> "bigint(21) NOT NULL"
		);

$spip_spam_words_log_key = array( 'PRIMARY KEY' => "id_spam_log" );

$tables_principales['spip_spam_words_log'] = array(
		'field' => &$spip_spam_words_log,
		'key' => &$spip_spam_words_log_key );


$spip_ban_liste = array(
	"id_ban"	=> "bigint(21) NOT NULL auto_increment",
	"ban_login"	=> "text",
	"ban_ip"	=> "varchar(16) default NULL",
	"ban_email"	=> "tinytext",
	"maj"		=> "timestamp" //  NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
		);

$spip_ban_liste_key = array( 'PRIMARY KEY' => "id_ban" );

$tables_principales['spip_ban_liste'] = array(
		'field' => &$spip_ban_liste,
		'key' => &$spip_ban_liste_key );

	return $tables_principales;
} // declarer_tables_principales

function spipbb_declarer_tables_auxiliaires($tables_auxiliaires){
	return $tables_auxiliaires;
} // declarer_tables_auxiliaires

//-- Relations ----------------------------------------------------
function spipbb_declarer_tables_interfaces($interface){

	//global $tables_jointures;
	$interface['tables_jointures']['visites_forums'][] = 'forums';
	$interface['tables_jointures']['auteurs_spipbb'][] = 'auteurs';

	// definir les noms raccourcis pour les <BOUCLE_(VISITES_FORUMS) ...
	$interface['table_des_tables']['visites_forums'] = 'visites_forums';

	
	return $interface;
} // declarer_tables_interfaces

/*
global $table_des_tables;
$table_des_tables['visites_forums'] = 'visites_forums';
$table_des_tables['auteurs_spipbb'] = 'auteurs_spipbb';
$table_des_tables['spam_words'] = 'spam_words';
$table_des_tables['spam_words_log'] = 'spam_words_log';
$table_des_tables['ban_liste'] = 'ban_liste';
*/

?>