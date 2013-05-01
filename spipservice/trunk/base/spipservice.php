<?php

/*______________________________________________________________________________
 | Plugin SpipService 1.0 pour Spip 3                                           \
 | Copyright 2012 Sebastien Chandonay - Studio Lambda                            \
 |                                                                                |
 | SpipService est un logiciel libre : vous pouvez le redistribuer ou le          |
 | modifier selon les termes de la GNU General Public Licence tels que            |
 | publiés par la Free Software Foundation : à votre choix, soit la               |
 | version 3 de la licence, soit une version ultérieure quelle qu'elle            |
 | soit.                                                                          |
 |                                                                                |
 | SpipService est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE     |
 | GARANTIE ; sans même la garantie implicite de QUALITÉ MARCHANDE ou             |
 | D'ADÉQUATION À UNE UTILISATION PARTICULIÈRE. Pour plus de détails,             |
 | reportez-vous à la GNU General Public License.                                 |
 |                                                                                |
 | Vous devez avoir reçu une copie de la GNU General Public License               |
 | avec SpipService. Si ce n'est pas le cas, consultez                            |
 | <http://www.gnu.org/licenses/>                                                 |
 ________________________________________________________________________________*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function spipservice_declarer_tables_objets_sql($tables){
	$tables['spip_spipservice'] = array(
			'principale' => "oui",
			'field'=> array(
					"id_spipservice" 	=> "bigint(21) NOT NULL auto_increment",
					"id"   				=> "bigint(21) NULL",
					"type"    			=> "varchar(25) NULL",
					"id_auteur"    		=> "bigint(21) NULL",
					"action"          	=> "varchar(255) NULL",
					"date"          	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
			),
			'key' => array(
					"PRIMARY KEY"	=> "id_spipservice",
			),
			'date' => "date",
			'texte_objets' => "paquet-spipservice:spipservice_nom"
	);
	return $tables;
}

?>
