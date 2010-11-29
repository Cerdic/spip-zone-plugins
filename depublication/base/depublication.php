<?php
/******************************************************************************************
 * Dépublication permet de dépublier un article ou un auteur à une date donnée.			  *
 * Copyright (C) 2005-2010 Nouveaux Territoires support<at>nouveauxterritoires.fr		  *
 * http://www.nouveauxterritoires.fr							    					  *
 *                                                                                        *
 * Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes *
 * de la Licence Publique Générale GNU publiée par la Free Software Foundation            *
 * (version 3).                                                                           *
 *                                                                                        *
 * Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       *
 * ni explicite ni implicite, y compris les garanties de commercialisation ou             *
 * d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  *
 * pour plus de détails.                                                                  *
 *                                                                                        *
 * Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    *
 * en même temps que ce programme ; si ce n'est pas le cas,								  * 
 * regardez http://www.gnu.org/licenses/ 												  *
 * ou écrivez à la	 																	  *
 * Free Software Foundation,                                                              *
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   *
 ******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function depublication_declarer_tables_objets_surnoms($table) {
	
	$table['articles_depublication'] = 'articles_depublication';
	$table['auteurs_depublication'] = 'auteurs_depublication';
	
	return $table;
}

function depublication_declarer_tables_interfaces($interface){
	// definir les jointures possibles
	$interface['tables_jointures']['spip_articles'][] = 'spip_articles_depublication';
	$interface['tables_jointures']['spip_articles_depublication'][] = 'spip_articles';
	
	$interface['tables_jointures']['spip_auteurs'][] = 'spip_auteurs_depublication';
	$interface['tables_jointures']['spip_auteurs_depublication'][] = 'spip_auteurs';
	
	// definir les noms raccourcis pour les <BOUCLE_(ARTICLES_DEPUBLICATION) ...
	$interface['table_des_tables']['articles_depublication']='articles_depublication';
	
	$interface['table_des_tables']['auteurs_depublication']='auteurs_depublication';
	
	
	return $interface;
}

function depublication_declarer_tables_principales($tables_principales){
	
	// SPIP_ARTICLES_DEPUBLICATIONS
	$spip_articles_depublication = array(
		"id_art_depub"	=> "BIGINT(21) NOT NULL",
		"id_article"		=> "BIGINT(21) NOT NULL",
		"depublication"		=> "DATETIME NOT NULL",
		"statut"			=> "VARCHAR(255) NULL",
		"maj" 				=> "TIMESTAMP");
	
	// definir les cle primaire et secondaires
	$spip_articles_depublication_key = array("PRIMARY KEY" => "id_art_depub");
	
	// inserer dans le tableau
	$tables_principales['spip_articles_depublication'] = array(
		'field' => &$spip_articles_depublication,
		'key' => &$spip_articles_depublication_key);
	
	
	$spip_auteurs_depublication = array(
		"id_auteur_depublication"	=> "BIGINT(21) NOT NULL",
		"id_auteur"					=> "BIGINT(21) NOT NULL",
		"depublication"				=> "DATETIME NOT NULL",
		"statut"					=> "VARCHAR(255) NULL",
		"maj" 						=> "TIMESTAMP");
	
	// definir les cle primaire et secondaires
	$spip_auteurs_depublication_key = array("PRIMARY KEY" => "id_auteur_depublication");
	
	// inserer dans le tableau
	$tables_principales['spip_auteurs_depublication'] = array(
		'field' => &$spip_auteurs_depublication,
		'key' => &$spip_auteurs_depublication_key);
		
		

	return $tables_principales;
}

function depublication_declarer_tables_auxiliaires($tables_auxiliaires){
	
	return $tables_auxiliaires;
}

?>