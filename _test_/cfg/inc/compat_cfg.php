<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_COMPAT_CFG_192', true);


/* fichier de compatibilite vers spip 1.9.2 */
if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')
	AND $f = charger_fonction('compat_cfg', 'inc'))
		$f();


## ceci n'est pas l'original du plugin compat mais la copie pour CFG

// En termes de distribution ce fichier PEUT etre recopie dans chaque plugin
// qui desire en avoir une version autonome (voire forkee), A CONDITION DE
// RENOMMER le fichier et ses deux fonctions ; c'est un peu lourd a maintenir
// mais c'est le prix a payer pour l'independance des plugins entre eux :-(

// la version commune a tous est developpee sur
// svn://zone.spip.org/spip-zone/_dev_/compat/


function inc_compat_cfg_dist($quoi = NULL) {
	if (!function_exists($f = 'compat_cfg_defs')) $f .= '_dist';
	$defs = $f();

	if (is_string($quoi))
		$quoi = array($quoi);
	else if (is_null($quoi))
		$quoi = array_keys($defs);

	foreach ($quoi as $d) {
		if (!function_exists($d)
		AND isset($defs[$d])) {
			eval ("function $d".$defs[$d]);
		}
	}
}

function compat_cfg_defs_dist() {
	$defs = array(
		'sql_fetch' => 
			'($res, $serveur=\'\') {
				return spip_fetch_array($res);
			}',
		
		/*'sql_selectdb' => 
			'($res, $serveur=\'\') {
				$GLOBALS[\'spip_mysql_db\'] = mysql_select_db($res);
				return $GLOBALS[\'spip_mysql_db\'];
			}',	*/
		
		'sql_query' => 
			'($res, $serveur=\'\') {
				return spip_query_db($res);
			}',	
		
		// sql_quote : mysql_escape_string depuis 1.9.3, ici _q
		'sql_quote' => 
			'($val, $serveur=\'\') {
				return _q($val);
			}',	
						
		'sql_select' => 
			'(
				$select = array(), 
				$from = array(), 
				$where = array(),
				$groupby = array(), 
				$orderby = array(), 
				$limit = \'\', 
				$having = array(),
				$serveur=\'\'
			) {
				return spip_abstract_select(
					$select, 
					$from, 
					$where, 
					$groupby, 
					$orderby, 
					$limit, 
					$limit, 
					$sousrequete = \'\', 
					$having,
					$table = \'\', 
					$id = \'\', 
					$serveur);
			}'
		
		/*'sql_count' => 
			'($res, $serveur=\'\') {
				return spip_mysql_count($res);
			}'*/
	);
	return $defs;
}

?>
