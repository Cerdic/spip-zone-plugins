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

//
// Structure des tables
//

global $tables_principales;
global $tables_auxiliaires;
global $tables_spipbb;

$tables_spipbb = array( 'spip_visites_forums', 'spip_auteurs_spipbb', 'spip_spam_words', 'spip_spam_words_log', 'spip_ban_liste' );

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

$spip_auteurs_spipbb = array( // table spip_auteurs_spipbb
	"id_auteur"	=> "BIGINT(21) UNSIGNED NOT NULL", // primary key
	"spam_warnings"	=> "INT(10) unsigned not null default '0'",
	'ip_auteur'	=> "varchar(16)",
	'ban_date'	=> "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
	'ban'		=> "VARCHAR(3) default 'non'"	// ban 'oui' 'non' default 'non'
	);

$spip_auteurs_spipbb_key = array(
		'PRIMARY KEY' => "id_auteur"
		);

$tables_principales['spip_auteurs_spipbb'] = array(
		'field' => &$spip_auteurs_spipbb,
		'key' => &$spip_auteurs_spipbb_key );

$spip_spam_words = array(
	"id_spam_word"	=> "BIGINT(21) unsigned not null auto_increment",
	"spam_word"	=> "varchar(255) not null" );

$spip_spam_words_key = array( 'PRIMARY KEY' => "id_spam_word",
				'KEY' => "spam_word" );

$tables_principales['spip_spam_words'] = array(
		'field' => &$spip_spam_words,
		'key' => &$spip_spam_words_key );

$spip_spam_words_log = array(
	"id_spam_log"	=> "BIGINT(21) unsigned not null auto_increment",
	"id_auteur"	=> "bigint(21) unsigned not null",
	"ip_auteur"	=> "varchar(16)",
	"login"		=> "varchar(255)",
	"log_date"	=> "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
	"titre"		=> "text",
	"message"	=> "mediumtext",
	"id_forum"	=> "bigint(21) unsigned not null",
	"id_article"	=> "bigint(21) unsigned not null"
		);

$spip_spam_words_log_key = array( 'PRIMARY KEY' => "id_spam_log" );

$tables_principales['spip_spam_words_log'] = array(
		'field' => &$spip_spam_words_log,
		'key' => &$spip_spam_words_log_key );


$spip_ban_liste = array(
	"id_ban"	=> "BIGINT(21) unsigned not null auto_increment",
	"ban_login"	=> "text",
	"ban_ip"	=> "varchar(16)",
	"ban_email"	=> "tinytext",
	"maj"		=> "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
		);

$spip_ban_liste_key = array( 'PRIMARY KEY' => "id_ban" );

$tables_principales['spip_ban_liste'] = array(
		'field' => &$spip_ban_liste,
		'key' => &$spip_ban_liste_key );

//-- Relations ----------------------------------------------------
global $tables_jointures;
$tables_jointures['spip_visites_forums'][] = 'spip_forums';
$tables_jointures['spip_auteurs_spipbb'][] = 'spip_auteurs';

global $table_des_tables;
$table_des_tables['spip_visites_forums'] = 'spip_visites_forums';
$table_des_tables['spip_auteurs_spipbb'] = 'spip_auteurs_spipbb';
$table_des_tables['spip_spam_words'] = 'spip_spam_words';
$table_des_tables['spip_spam_words_log'] = 'spip_spam_words_log';
$table_des_tables['spip_ban_liste'] = 'spip_ban_liste';

?>
