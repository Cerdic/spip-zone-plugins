<?php
/******************************************************************************************
 * Dépublication permet de dépublier un article à une date donnée.						  *
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


include_spip('inc/meta');
include_spip('inc/plugin');
include_spip('base/create');
include_spip('base/abstract_sql');

$get_infos = charger_fonction('get_infos','plugins');
// _NOM_PLUGIN_DEPUBLICATION;
$info_plugin_depublication = $get_infos('depublication');
$version_plugin = $info_plugin_depublication['version'];
  
       
// fonction d'installation, mise a jour de la base
function depublication_verifier_base() {
	
	$version_base = $GLOBALS['meta']['depublication_base_version'];
	$current_version = 0.0;
	
	if ( ! isset($GLOBALS['meta']['depublication_base_version'])) {
		
		creer_base();
		ecrire_meta('depublication_base_version', "0.8" );
		ecrire_config("depublication/etatdep","poubelle");
		ecrire_config("depublication/delai","1");
		ecrire_config("depublication/delaiunite","mois");
		
		spip_log('Tables du plugin Depublication correctement installées en version 0.8','depublication');
	} else {
		
		if (version_compare($current_version,"0.6","<=")) {
			
			spip_log('Tables du plugin Depublication correctement passsées en version 0.6','depublication');
			ecrire_meta('depublication_base_version', $current_version = "0.6");
		}
		
		if (version_compare($current_version,"0.8","<=")) {
			creer_base();
			
			sql_alter("TABLE spip_articles_depublication CHANGE id_art_depub id_article_depublication BIGINT(21) NOT NULL AUTO_INCREMENT");
			
			spip_log('Tables du plugin Depublication correctement passsées en version 0.8','depublication');
			ecrire_meta('depublication_base_version', $current_version = "0.8");
			ecrire_meta('depublication_version', "1.0.2");
		}
	}	
}

// fonction de desinstallation
function depublication_vider_tables() {
	
	sql_drop_table("spip_articles_depublication");
	sql_drop_table("spip_auteurs_depublication");
		
	effacer_meta('depublication_base_version');
	effacer_meta('depublication');
}


function depublication_install($action) {
	switch ($action) {
		case 'test':
			return (isset($GLOBALS['meta']['depublication_base_version']) AND ($GLOBALS['meta']['depublication_base_version'] >= $version_plugin));
			break;
		case 'install':
			depublication_verifier_base();
			break;
		case 'uninstall':
			depublication_vider_tables();
			break;
	}
}

?>