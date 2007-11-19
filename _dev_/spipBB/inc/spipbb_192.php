<?php
#------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                          #
#  File    : inc/spipbb_192 - compatibilite ancienne version #
#  Adaptation : Chryjs, 2007                                 #
#  Authors :                                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs   #
#  Contact : chryjs!@!free!.!fr                              #
#------------------------------------------------------------#

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

/*
[fr] Ce fichier est inclu pour les problemes de compatibilite, 
lorsqu'on est en 192 et que l'on veut utiliser le nouveau plugin
Il utilise du code importe de Spip 193 [SVN] et de spip 192
La plupart du code est donc Copyright les auteurs de SPIP
Les function_exists sont ajoutes pour resoudre les conflits de 
redefinitions lorsque d'autres plugins actifs utilisent ce type 
de mecanisme de compatibilite.
svn://zone.spip.org/spip-zone/_dev_/compat/ est insuffisant pour nos besoins.
[en] This file is included to solve compatibility problems
when used with SPIP 1.9.2 and this new plugin.
I used code copied from SPIP 193 [SVN] and Spip 192
therefore most of the code is copyright SPIP authors.
The function_exisits was added to protect from conflicts with 
other plugins that also redefine the same function for the same 
reasons aka compatibility.
svn://zone.spip.org/spip-zone/_dev_/compat/ isn't enough for our needs.
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

if (defined("_INC_SPIPBB_192")) return; else define("_INC_SPIPBB_192", true);

spip_log('inc/spipbb_192.php: included','spipbb');

include_spip('base/abstract_sql'); // SPIP 192
include_spip('base/db_mysql'); // SPIP 192

/*
Principales fonctions definies dans l'ordre alphanumerique :

sql_count
sql_delete
sql_fetch
sql_fetsel
sql_getfetsel
sql_insertq
sql_query
sql_select
sql_updateq

*/

#------------------------------------------------------------#
//  sql_fetsel
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_fetsel')) {
function sql_fetsel($select, $from = array(), $where = array(), $groupby = '', 
	$orderby = array(), $limit = '', $having = array(), $serveur='')
{
	return spip_abstract_fetsel($select,$from,$where,$groupby,$orderby,$limit,'',$having,'','',$serveur) ;
} } //  sql_fetsel

#------------------------------------------------------------#
// sql_insertq
// from spip req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_insertq')) {
function sql_insertq($table, $couples, $desc=array(), $serveur='')
{
	if (!$desc) $desc = description_table($table);
	if (!$desc) die("$table insertion sans description");
	$fields =  $desc['field'];

	foreach ($couples as $champ => $val) {
		$couples[$champ]= spip_mysql_cite($val, $fields[$champ]);
	}

	return spip_mysql_insert($table, "(".join(',',array_keys($couples)).")", "(".join(',', $couples).")", $desc, $serveur);
} } // sql_insertq

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('spip_mysql_cite')) {
function spip_mysql_cite($v, $type)
{
	if (sql_test_date($type) AND preg_match('/^\w+\(/', $v)
	OR (sql_test_int($type)
		 AND (is_numeric($v)
		      OR (ctype_xdigit(substr($v,2))
			  AND $v[0]=='0' AND $v[1]=='x'))))
		return $v;
	else return  ("'" . addslashes($v) . "'");
} } // spip_mysql_cite

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_query')) {
function sql_query($ins, $serveur='')
{
	return spip_mysql_query($ins,$serveur);
} } // sql_query

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('spip_mysql_query')) {
function spip_mysql_query($query, $serveur='')
{
	$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];
	$link = $connexion['link'];
	$db = $connexion['db'];

	$query = traite_query($query, $db, $prefixe);

	$t = !isset($_GET['var_profile']) ? 0 : trace_query_start();
	$r = $link ? mysql_query($query, $link) : mysql_query($query);

	if ($e = spip_mysql_errno())	// Log de l'erreur eventuelle
		$e .= spip_mysql_error($query); // et du fautif
	return $t ? trace_query_end($query, $t, $r, $e) : $r;
} } // spip_mysql_query

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('trace_query_start')) {
function trace_query_start()
{
	static $trace = '?';

	if ($trace === '?') {
		include_spip('inc/autoriser');
		// gare au bouclage sur calcul de droits au premier appel
		// A fortiori quand on demande une trace
		$trace = !isset($_GET['var_profile']);
		$trace = autoriser('debug');
	}
	return  $trace ?  microtime() : 0;
} } // trace_query_start

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('trace_query_end')) {
function trace_query_end($query, $start, $result, $err)
{
	global $tableau_des_erreurs;
	if ($start) trace_query_chrono($start, microtime(), $query, $result);
	if (!($err = sql_errno())) return $result;
	$err .= ' '.sql_error();
	if (autoriser('voirstats')) {
		include_spip('public/debug');
		$tableau_des_erreurs[] = array(
		_T('info_erreur_requete'). " "  .  htmlentities($query),
		"&laquo; " .  htmlentities($err)," &raquo;");
	}
	return $err;
} } // trace_query_end

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('trace_query_chrono')) {
function trace_query_chrono($m1, $m2, $query, $result)
{
	static $tt = 0, $nb=0;
	global $tableau_des_temps;

	list($usec, $sec) = explode(" ", $m1);
	list($usec2, $sec2) = explode(" ", $m2);
 	$dt = $sec2 + $usec2 - $sec - $usec;
	$tt += $dt;
	$nb++;

	$explain = '';
	foreach (sql_explain($query) as $k => $v) {
		$explain .= "<tr><td>$k</td><td>" .str_replace(';','<br />',$v) ."</td></tr>";
	}
	if ($explain) $explain = "<table border='1'>$explain</table>";
	$result = str_replace('Resource id ','',$result);
	$query = preg_replace('/([a-z)`])\s+([A-Z])/', '$1<br />$2',$query);
	$tableau_des_temps[] = array(sprintf("%3f", $dt), 
				     sprintf(" %3de", $nb),
				     $query,
				     $explain,
				     $result);
} } // trace_query_chrono

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('spip_mysql_explain')) {
function spip_mysql_explain($query, $serveur='')
{
	if (strpos($query, 'SELECT') !== 0) return array();
	$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];
	$link = $connexion['link'];
	$db = $connexion['db'];

	$query = 'EXPLAIN ' . traite_query($query, $db, $prefixe);
	$r = $link ? mysql_query($query, $link) : mysql_query($query);
	return spip_mysql_fetch($r, MYSQL_ASSOC);
} } // spip_mysql_explain

#------------------------------------------------------------#
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('traite_query')) {
function traite_query($query, $db='', $prefixe='')
{
	if ($GLOBALS['mysql_rappel_nom_base'] AND $db)
		$pref = '`'. $db.'`.';
	else $pref = '';

	if ($prefixe)
		$pref .= $prefixe . "_";

	if (preg_match('/\s(SET|VALUES|WHERE)\s/i', $query, $regs)) {
		$suite = strstr($query, $regs[0]);
		$query = substr($query, 0, -strlen($suite));
	} else $suite ='';

	$r = preg_replace('/([,\s])spip_/', '\1'.$pref, $query) . $suite;
#	spip_log("traite_query: " . substr($r,0, 50) . ".... $db, $prefixe");
	return $r;
} } // traite_query

#------------------------------------------------------------#
//spip_mysql_error
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('spip_mysql_error')) {
function spip_mysql_error($query='')
{
	$s = mysql_error();
	if ($s) spip_log("$s - $query", 'mysql');
	return $s;
} } // spip_mysql_error

#------------------------------------------------------------#
// from req/mysql.php 193 et base/db_mysql.php 192
#------------------------------------------------------------#
if (!function_exists('sql_fetch')) {
function sql_fetch($res, $serveur='')
{
	return spip_mysql_fetch($res, MYSQL_ASSOC);
} } // spip_mysql_fetch in base/db_mysql.php  192

#------------------------------------------------------------#
// sql_getfetsel
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_getfetsel')) {
function sql_getfetsel(
	$select, $from = array(), $where = array(), $groupby = '', 
	$orderby = array(), $limit = '', $having = array(), $serveur='')
{
	$r = sql_fetch(sql_select($select, $from, $where, $groupby, $orderby, $limit, $having, $serveur), $serveur);
	return $r ? $r[$select] : NULL;
} } // sql_getfetsel

#------------------------------------------------------------#
// sql_updateq
// from base/abstract_sql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_updateq')) {
function sql_updateq($table, $exp, $where='', $desc=array(), $serveur='')
{
	return spip_mysql_updateq($table, $exp, $where, $desc, $serveur);
} } // sql_updateq

#------------------------------------------------------------#
// from req/mysql.php 193
// http://doc.spip.org/@spip_mysql_updateq
#------------------------------------------------------------#
if (!function_exists('spip_mysql_updateq')) {
function spip_mysql_updateq($table, $champs, $where='', $desc=array(), $serveur='')
{
	if (!$champs) return;
	if (!$desc) $desc = description_table($table);
	if (!$desc) die("$table insertion sans description");
	$fields =  $desc['field'];
	$r = '';
	foreach ($champs as $champ => $val) {
		$r .= ',' . $champ . '=' . spip_mysql_cite($val, $fields[$champ]);
	}
	$r = "UPDATE $table SET " . substr($r, 1) . ($where ? " WHERE $where" : '');
	spip_mysql_query($r, $serveur);
} } // spip_mysql_updateq

#------------------------------------------------------------#
// from base/abstract_sql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_count')) {
function sql_count($res, $serveur='')
{
	if ($res) return mysql_num_rows($res);
} } // sql_count


#------------------------------------------------------------#
// from base/abstract_sql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_test_date')) {
function sql_test_date($type, $serveur='')
{
  return (preg_match('/^datetime/i',$type)
	  OR preg_match('/^timestamp/i',$type));
} } // sql_test_date

#------------------------------------------------------------#
// from base/abstract_sql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_test_int')) {
function sql_test_int($type, $serveur='')
{
  return (preg_match('/^bigint/i',$type)
	  OR preg_match('/^int/i',$type)
	  OR preg_match('/^tinyint/i',$type));
} } // sql_test_int

#------------------------------------------------------------#
// from req/mysql.php 193
// A transposer dans les portages
#------------------------------------------------------------#
if (!function_exists('spip_mysql_errno')) {
function spip_mysql_errno()
{
	$s = mysql_errno();
	// 2006 MySQL server has gone away
	// 2013 Lost connection to MySQL server during query
	if (in_array($s, array(2006,2013)))
		define('spip_interdire_cache', true);
	if ($s) spip_log("Erreur mysql $s",'mysql');
	return $s;
} } // spip_mysql_errno

#------------------------------------------------------------#
// sql_select
// from base/abstract.php 193
#------------------------------------------------------------#
if (!function_exists('sql_select')) {
function sql_select (
	$select = array(), $from = array(), $where = array(),
	$groupby = '', $orderby = array(), $limit = '', $having = array(),
	$serveur='')
{
	if ($order_by AND !$is_array($order_by)) $order_by = array($order_by);

	return spip_mysql_select($select, $from, $where, $groupby, $orderby, $limit, '', $having, '', '', $serveur);
} } // sql_select

#------------------------------------------------------------#
// sql_delete
// from req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_delete')) {
function sql_delete ($table, $where='', $serveur='')
{
	return spip_mysql_query("DELETE FROM $table" . ($where ? " WHERE $where" : ''), $serveur);
} } // sql_delete

#------------------------------------------------------------#
// sql_showbase
// from base/abstract_sql.php 193 et req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_showbase')) {
function sql_showbase($spip=NULL, $serveur='')
{
	if ($spip == NULL){
		$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
		$spip = $connexion['prefixe'] . '%';
	}

	return spip_mysql_query("SHOW TABLES LIKE '$spip'", $serveur);
} } // sql_showbase

#------------------------------------------------------------#
// sql_showtable
// from base/abstract_sql.php 193 et req/mysql.php 193
#------------------------------------------------------------#
if (!function_exists('sql_showtable')) {
function sql_showtable($table, $table_spip = false, $serveur='')
{
	if ($table_spip){
		$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
		$prefixe = $connexion['prefixe'];
		$table = preg_replace('/^spip/', $prefixe, $table);
	}

	$f = spip_mysql_showtable($table, $serveur);
	if (!$f) return array();
	if (isset($GLOBALS['tables_principales'][$table]['join']))
		$f['join'] = $GLOBALS['tables_principales'][$table]['join'];
	elseif (isset($GLOBALS['tables_auxiliaires'][$table]['join']))
		$f['join'] = $GLOBALS['tables_auxiliaires'][$table]['join'];
	return $f;
} } // sql_showtable


?>
