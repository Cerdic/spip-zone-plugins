<?php


/*
 * Plugin openPublishing pour SPIP
 * (c) edd 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_COMPAT_OP_192', true);


/* fichier de compatibilite vers spip 1.9.2 */
if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')
	AND $f = charger_fonction('compat_op', 'inc'))
		$f();


## ceci n'est pas l'original du plugin compat mais la copie pour OP

// En termes de distribution ce fichier PEUT etre recopie dans chaque plugin
// qui desire en avoir une version autonome (voire forkee), A CONDITION DE
// RENOMMER le fichier et ses deux fonctions ; c'est un peu lourd a maintenir
// mais c'est le prix a payer pour l'independance des plugins entre eux :-(

// la version commune a tous est developpee sur
// svn://zone.spip.org/spip-zone/_dev_/compat/


function inc_compat_op_dist($quoi = NULL) {
	if (!function_exists($f = 'compat_op_defs')) $f .= '_dist';
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

function compat_op_defs_dist() {
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
		
		'sql_drop_table' =>
			'(
				$table
			) {
				return spip_query("DROP TABLE $table");
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
		}'
		

		/*
		'sql_count' => 
			'(
				$res, 
				$serveur=\'\'
			) {
				return spip_mysql_count($res);
			}'
		
		
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


















/* compatiblité descendante avec spip 1.9.1 et 1.9.2
 *
 * içi sur surchagée les fonctions spécifiques à spip 1.9.3
 * afin de permettre au nouveau code de fonctionner sur les
 * anciens spip. Cette surcharge est déstinée à disparaitre
 * dans les version future du plugin openPublishing
 */

// dejat pris en charge par le compat cfg
/*function sql_quote($var) {
	return spip_abstract_quote($var);
}*/

/*function sql_delete($table,$where) {
	return spip_query("DELETE FROM $table WHERE $where");
}*/

/*function sql_insertq($table,$array) {

	$expend_key = '';
	$expend_value = '';
	foreach ($array as $key => $value) {
		$expend_key .= $key.',';
		$expend_value .= $value.',';
	}
	$expend_array = '('.substr($expend_key,0,strlen($expend_key)-1).') VALUES ('
			.substr($expend_value,0,strlen($expend_value)-1).')';

	spip_query("INSERT INTO $table $expend_array");
	return mysql_insert_id();
}*/
/*


/*function sql_update($table,$array,$where) {

	$expend = '';
	foreach ($array as $key => $value) {
		$expend .= $key.' = '.$value .',';
	}
	$expend = substr($expend,0,strlen($expend)-1);

	return spip_query('UPDATE '.$table.' SET '.$expend.' WHERE '.$where);
}
*//*
function sql_drop_table($table) {
	return spip_query('DROP TABLE '.$table);
}
*/
?>