<?php
// Original From SPIP-Listes-V :: $Id: plugin_globales_lib.php paladin@quesaco.org $
// Christian PAULUS : http://www.quesaco.org/ (c) 2007
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// librairie de fonctions et defines globales 
// ind�pendantes de tout plugin

// toutes les fonctions ici sont pr�fix�es '__' (2 tirets)
// sauf re-d�claration inc/vieilles_defs.php
// sauf d�claration des fonctions php5

if (!defined("_ECRIRE_INC_VERSION")) return;

if(defined("_PGL_PLUGIN_GLOBALES_LIB") && _PGL_PLUGIN_GLOBALES_LIB) return;
define("_PGL_PLUGIN_GLOBALES_LIB", 20080224.2026); //date.heure

// HISTORY:
// CP-20081115: integration de __plugin_html_signature dans *_presentation
// idem pour __plugin_current_svnrevision_get, __plugin_svn_revision_read
// CP-20080503: revision __boite_alerte()
// CP-20080224: ajout de __lire_meta()
// CP-20071231: __plugin_current_version_base_get compl�te par lecture plugin.xml si vb manquant dans meta
// CP-20071224: ajout de __plugin_current_svnrevision_get() et modif __plugin_html_signature()
// CP-20071222: optimisation __plugin_boite_meta_info() pour plugin en mode stable et mode dev

include_spip("inc/plugin");

if(!defined("_PGL_SYSLOG_LAN_IP_ADDRESS")) define("_PGL_SYSLOG_LAN_IP_ADDRESS", "/^192\.168\./");
if(!defined("_PGL_USE_SYSLOG_UNIX"))  define("_PGL_USE_SYSLOG_UNIX", true);



/*//////////////////////////
*/





/*
 * ne sert plus ?
 */*/
if(!function_exists('__plugin_meta_version')) {
	// renvoie le num de version du plugin lors de la derniere installation
	// present dans les metas
	function __plugin_get_meta_version($prefix) {
		$result = false;
		$info = spiplistes_get_meta_infos($prefix);
		if(isset($info['version'])) {
			$result = $info['version'];
		}
		return($result);
	}
}

/*
 * ne sert plus
 */
if(!function_exists('__mysql_date_time')) {
	function __mysql_date_time($time = 0) {
		return(date("Y-m-d H:i:s", $time));
	}
}

/*
 * ne sert plus
 */
if(!function_exists('__is_mysql_date')) {
	function __is_mysql_date($date) {
		$t = strtotime($date);
		return($t && ($date == __mysql_date_time($t)));
	}
}

/*
 * ne sert plus
 */
if(!function_exists('__mysql_max_allowed_packet')) {
	// renvoie la taille maxi d'un paquet PySQL (taille d'une requ�te)
	function __mysql_max_allowed_packet() {
		$sql_result = spip_query("SHOW VARIABLES LIKE 'max_allowed_packet'");
		$row = spip_fetch_array($sql_result, SPIP_NUM);
		return($row[1]);
	}
}

/*
 * ne sert plus
 */
// si inc/vieilles_defs.php disparait ?
// CP20080224: nota: ce passage/fonction probablement a supprimer (redondant)
if(!function_exists("lire_meta")) {
	function lire_meta($key) {
		$result = 0;
		if(_FILE_CONNECT && @file_exists(_FILE_CONNECT_INS .'.php')) {
			if(!isset($GLOBALS['meta'][$key])) {
				$sql_result = @spip_query("SELECT valeur FROM spip_meta WHERE nom=".sql_quote($key)." LIMIT 1");
				if($row = spip_fetch_array($sql_result)) {
					$result = $row[$key];
					$GLOBALS['meta'][$key] = $result;
				}
			}
			else {
				$result = $GLOBALS['meta'][$key];
			}
		}
		return($result);
	}
}






/*
 * ne sert plus
 */
if(!function_exists('__table_items_count')) {
	function __table_items_count ($table, $key, $where='') {
		return (
			(($row = spip_fetch_array(spip_query("SELECT COUNT($key) AS n FROM $table $where"))) && $row['n'])
			? intval($row['n'])
			: 0
		);
	}
}

/*
 * ne sert plus
 */
if(!function_exists('__table_items_get')) {
	function __table_items_get ($table, $keys, $where='', $limit='') {
		$result = array();
		$sql_result = spip_query("SELECT $keys FROM $table $where $limit");
		while($row = spip_fetch_array($sql_result)) { $result[] = $row; }
		return ($result);
	}
}

/*
 * ne sert plus
 */
if(!function_exists('__ecrire_metas')) {
	function __ecrire_metas () {
		if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
			include_spip("inc/meta");
			ecrire_metas();
		}
		return(true);
	}
}

/*
 * ne sert plus
 */
if(!function_exists('__lire_meta')) {
	function __lire_meta ($key) {
		global $meta; return $meta[$key];
	}
}


?>