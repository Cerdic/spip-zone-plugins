<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

// CP-20080324
// Script issu de :
//   http://zone.spip.org/trac/spip-zone/browser/_plugins_/_stable_/cfg/inc/compat_cfg.php

// CP-20080508: ajout de sql_countsel()
// CP-20080508: correction sql_insert()
// CP-20080507: ajout de sql_drop_table()
// CP-2080329: ajout de sql_fetsel() et sql_getfetsel()
// Documentation: http://www.spip.net/ecrire/?exec=articles&id_article=3683

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if(!defined("_COMPAT_CFG_192")) {
	define('_COMPAT_CFG_192', true);
}


/* fichier de compatibilite vers spip 1.9.2 */
if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')
	AND $f = charger_fonction('compat_spiplistes', 'inc'))
		$f();


## ceci n'est pas l'original du plugin compat mais la copie pour CFG

// En termes de distribution ce fichier PEUT etre recopie dans chaque plugin
// qui desire en avoir une version autonome (voire forkee), A CONDITION DE
// RENOMMER le fichier et ses deux fonctions ; c'est un peu lourd a maintenir
// mais c'est le prix a payer pour l'independance des plugins entre eux :-(

// la version commune a tous est developpee sur
// svn://zone.spip.org/spip-zone/_dev_/compat/


function inc_compat_spiplistes_dist($quoi = NULL) {
	if (!function_exists($f = 'compat_spiplistes_defs')) $f .= '_dist';
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

function compat_spiplistes_defs_dist() {
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
				return spip_query(\'ALTER \' . $res);
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
			
		// n'existe pas en 1.9.2
		'sql_drop_table' =>
			// $table = string, du style "table1,table2,..."
			'($table, $exist=\'\') {
				if ($exist) $exist =" IF EXISTS";
				return spip_query_db("DROP TABLE$exist $table");
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
					$sousrequete = \'\', 
					$having,
					$table = \'\', 
					$id = \'\', 
					$serveur);
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
		'sql_insert' => 
			'(
				$table
				, $noms
				, $valeurs
			) {
				$query = "INSERT INTO $table $noms VALUES $valeurs";
				$r = sql_query($query);
				return ($r ? mysql_insert_id() : $r);
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
				$r = sql_query($query);
				return($r ? mysql_insert_id() : $r);
			}',
		//
		'sql_showtable' => '($table, $serveur=\'\') { 
			return spip_abstract_showtable($table, \'mysql\', true);
		}',
		
		// n'existe pas en 1.9.2
		// on cree la requete directement
		'sql_fetsel' => 
			'(
				$select = array(), $from = array(), $where = array()
				, $groupby = array(), $orderby = array(), $limit = \'\'
				, $having = array(), $serveur = \'\', $option = true
			) {
				return sql_fetch(sql_select($select, $from, $where
					, $groupby, $orderby, $limit, $having, $serveur, $option!==false), $serveur, $option!==false);
			}',
		
		// n'existe pas en 1.9.2
		// on cree la requete directement
		# Retourne l'unique champ demande dans une requete Select a resultat unique
		'sql_getfetsel' => 
			'(
				$select, $from = array(), $where = array()
				, $groupby = array(), $orderby = array(), $limit = \'\'
				, $having = array(), $serveur = \'\', $option = true
			) {
				$r = sql_fetch(sql_select($select, $from, $where
					,$groupby, $orderby, $limit, $having, $serveur, $option!==false), $serveur, $option!==false);
				$select = trim($select, "`");
				return $r ? $r[$select] : NULL;
			}',
				

		// n'existe pas en 1.9.2
		// on cree la requete directement
		# Nombre de lignes dans le resultat
		'sql_count' => 
			'(
				$res, $serveur=\'\', $option=true
			) {
				return ($res ? mysql_num_rows($res) : 0);
			}',
			
		// n'existe pas en 1.9.2
		'sql_countsel' => 
			'(
				  $from = array()
				, $where = array()
				, $groupby = array()
				, $limit = \'\'
				, $having = array()
				, $serveur = \'\'
				, $requeter = true
			) {
				$r = sql_select(\'COUNT(*)\', $from, $where, $groupby, \'\', $limit, $having, $serveur, $requeter);
				if ($r && $requeter) list($r) = mysql_fetch_array($r, MYSQL_NUM);
				return($r);
			}',
			
		// n'existe pas en 1.9.2
		'sql_errno' => 
			'($serveur=\'\') {
				return(spip_sql_errno());
			}',

		// n'existe pas en 1.9.2
		'sql_error' => 
			'($query=\'\', $serveur=\'\', $option=true) {
				return(spip_sql_error());
			}'

	);
	return $defs;
}

?>
