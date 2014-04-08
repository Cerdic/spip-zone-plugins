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

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_COMPAT_BOUQ_192', true);

include_spip('base/abstract_sql');
include_spip('base/db_mysql');

/* fichier de compatibilite vers spip 1.9.2 */
if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')
  AND $f = charger_fonction('compat_bouq', 'inc'))
      $f();

## ceci n'est pas l'original du plugin compat mais la copie pour BOUQ

// En termes de distribution ce fichier PEUT etre recopie dans chaque plugin
// qui desire en avoir une version autonome (voire forkee), A CONDITION DE
// RENOMMER le fichier et ses deux fonctions ; c'est un peu lourd a maintenir
// mais c'est le prix a payer pour l'independance des plugins entre eux :-(

// la version commune a tous est developpee sur
// svn://zone.spip.org/spip-zone/_dev_/compat/


function inc_compat_bouq_dist($quoi = NULL) {
	if (!function_exists($f = 'compat_bouq_defs')) $f .= '_dist';
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

function compat_bouq_defs_dist() {
	$defs = array(
		'sql_fetch' => 
			'(
				$res, 
				$serveur=\'\'
			) {
				return spip_fetch_array($res);
			}',
		
		'sql_query' => 
			'($res, $serveur=\'\') {
				return spip_query_db($res);
			}',	
		
		// n'existe pas en 1.9.2
		'sql_alter' => 
			'($res, $serveur=\'\') {
				return spip_query_db(\'ALTER \' . $res);
			}',	
				
		// n'existe pas en 1.9.2
		// on cree la requete directement
		'sql_delete' => 
			'($table, $where=\'\', $serveur=\'\') {
				if (!is_array($table)) $table = array($table);
				if (!is_array($where)) $where = array($where);
				$query = \'DELETE FROM \'
						. implode(\',\', $table)
						. \' WHERE \'
						. implode(\' AND \', $where);
				return spip_query_db($query);
			}',
			
		// sql_quote : _q directement
		'sql_quote' => 
			'(
				$val, 
				$serveur=\'\'
			) {
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
			}',

		'sql_fetsel' =>
			'(
				$select = array(), 
				$from = array(), 
				$where = array(), 
				$groupby = \'\', 
				$orderby = array(), 
				$limit = \'\', 
				$sousrequete = \'\', 
				$having = array(),
				$table = \'\', 
				$id = \'\', 
				$serveur=\'\'
			) {
				return spip_abstract_fetch(
					spip_abstract_select(
						$select, 
						$from, 
						$where, 
						$groupby, 
						$orderby, 
						$limit, 
						$sousrequete, 
						$having, 
						$table, 
						$id, 
						$serveur)
				);
			}',
			
		// n'existe pas en 1.9.2
		// on cree la requete directement
		'sql_update' => 
			'(
				$table, 
				$champs, 
				$where=\'\', 
				$desc=array(), 
				$serveur=\'\'
			) {
				if (!is_array($table)) 	$table = array($table);
				if (!is_array($champs)) $champs = array($champs);
				if (!is_array($where)) 	$where = array($where);

				$query = $r = \'\';
				foreach ($champs as $champ => $val)
					$r .= \',\' . $champ . "=$val";
				if ($r = substr($r, 1))
					$query = \'UPDATE \'
							. implode(\',\', $table)
							. \' SET \' . $r
							. (empty($where) ? \'\' :\' WHERE \' . implode(\' AND \', $where));
				if ($query)
					return spip_query_db($query);
			}',

		'sql_updateq' => 
			'(
				$table, 
				$champs, 
				$where=\'\', 
				$desc=array(), 
				$serveur=\'\'
			) {
				if (!is_array($champs)) $exp = array($champs);
				
				foreach ($champs as $k => $val) {
					$champs[$k] = sql_quote($val);
				}
				
				return sql_update(				
					$table, 
					$champs, 
					$where, 
					$desc, 
					$serveur
				);
			}',	
			
		
		// n'existe pas en 1.9.2
		// on cree la requete directement
		'sql_insertq' => 
			'(
				$table,
				$champs
			) {
				if (!is_array($champs)) $exp = array($champs);
				
				foreach ($champs as $k => $val) {
					$champs[$k] = sql_quote($val);
				}
				
				$query = "INSERT INTO $table (".implode(",", array_keys($champs)).") VALUES (".implode(",", $champs).")";
				return sql_query($query);
			}',
		
		'sql_showtable' => '($table, $serveur=\'\') {
			return spip_abstract_showtable($table, \'mysql\', true);
		}',
		

		
		'sql_count' => 
			'(
				$res, 
				$serveur=\'\'
			) {
				return spip_mysql_count($res);
			}',

		'sql_free' => 
			'(
				$res, 
				$serveur=\'\'
			) {
				return spip_mysql_free($res);
			}'
		
		/*
		'sql_selectdb' => 
			'(
				$res, 
				$serveur=\'\'
			) {
				$GLOBALS[\'spip_mysql_db\'] = mysql_select_db($res);
				return $GLOBALS[\'spip_mysql_db\'];
			}',	
		
		
		*/
	);
	return $defs;
}

?>
