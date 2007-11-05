<?php
#--------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                      #
#  File    : inc/spipbb_192 - compatibilite ancienne version #
#  Authors : Chryjs, 2007 et als                         #
#  Contact : chryjs¡@!free¡.!fr                          #
#--------------------------------------------------------#

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

// [fr] Ce fichier est inclu pour les problemes de compatibilite, lorsqu'on est en 192 et que l'on veut utiliser le nouveau plugin
// [fr] Il utilise du code importe de Spip 193 [SVN] et de spip 192

if (!defined("_ECRIRE_INC_VERSION")) return;

if (defined("_INC_SPIPBB_192")) return; else define("_INC_SPIPBB_192", true);

include_spip('base/abstract_sql'); // SPIP 192
include_spip('base/db_mysql'); // SPIP 192

/* 
Principales fonctions definies dans l'ordre alphanumerique :

sql_insertq
sql_fetch
sql_fetsel
sql_getfetsel
sql_query
sql_updateq

*/

//  sql_fetsel
function sql_fetsel($select, $from = array(), $where = array(), $groupby = '', 
	$orderby = array(), $limit = '', $having = array(), $serveur='')
{
	return spip_abstract_fetsel($select,$from,$where,$groupby,$orderby,$limit,'',$having,'','',$serveur) ;
} //  sql_fetsel

// sql_insertq
// from spip req/mysql.php 193
// http://doc.spip.org/@spip_mysql_insertq
function sql_insertq($table, $couples, $desc=array(), $serveur='')
{ 
	if (!$desc) $desc = description_table($table);
	if (!$desc) die("$table insertion sans description");
	$fields =  $desc['field'];

	foreach ($couples as $champ => $val) {
		$couples[$champ]= spip_mysql_cite($val, $fields[$champ]);
	}

	return spip_mysql_insert($table, "(".join(',',array_keys($couples)).")", "(".join(',', $couples).")", $desc, $serveur);
} // sql_insertq

// from http://doc.spip.org/@spip_mysql_cite
function spip_mysql_cite($v, $type) {
	if (sql_test_date($type) AND preg_match('/^\w+\(/', $v)
	OR (sql_test_int($type)
		 AND (is_numeric($v)
		      OR (ctype_xdigit(substr($v,2))
			  AND $v[0]=='0' AND $v[1]=='x'))))
		return $v;
	else return  ("'" . addslashes($v) . "'");
}

// from http://doc.spip.org/@sql_query
function sql_query($ins, $serveur='') {
	return spip_mysql_query($ins,$serveur);
}

// from http://doc.spip.org/@spip_mysql_query
function spip_mysql_query($query, $serveur='') {

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
}

// from http://doc.spip.org/@trace_query_start
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
}

// from http://doc.spip.org/@trace_query_end
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
}
// from http://doc.spip.org/@trace_query_chrono
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
}

// from http://doc.spip.org/@spip_mysql_explain
function spip_mysql_explain($query, $serveur=''){
	if (strpos($query, 'SELECT') !== 0) return array();
	$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];
	$link = $connexion['link'];
	$db = $connexion['db'];

	$query = 'EXPLAIN ' . traite_query($query, $db, $prefixe);
	$r = $link ? mysql_query($query, $link) : mysql_query($query);
	return spip_mysql_fetch($r, NULL, $serveur);
}

// from http://doc.spip.org/@traite_query
function traite_query($query, $db='', $prefixe='') {

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
}

// from http://doc.spip.org/@sql_fetch
function sql_fetch($res, $serveur='') {
	return spip_mysql_fetch($res, NULL, $serveur);
} // spip_mysql_fetch in base/db_mysql.php  192

// sql_getfetsel
// from http://doc.spip.org/@sql_getfetsel
function sql_getfetsel(
	$select, $from = array(), $where = array(), $groupby = '', 
	$orderby = array(), $limit = '', $having = array(), $serveur='') {
	$r = sql_fetch(sql_select($select, $from, $where,	$groupby, $orderby, $limit, $having, $serveur), $serveur);
	return $r ? $r[$select] : NULL;
}

// sql_updateq
// from base/abstract_sql.php 193
function sql_updateq($table, $exp, $where='', $desc=array(), $serveur='')
{
	return spip_mysql_updateq($table, $exp, $where, $desc, $serveur);
}

// from req/mysql.php 193
// http://doc.spip.org/@spip_mysql_updateq
function spip_mysql_updateq($table, $champs, $where='', $desc=array(), $serveur='') {

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
}

// from base/abstract_sql.php 193
function sql_count($res, $serveur='')
{
	if ($res) return mysql_num_rows($res);
}

?>