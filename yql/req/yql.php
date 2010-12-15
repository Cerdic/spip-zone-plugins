<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour la premiere connexion a un serveur MySQL

// http://doc.spip.org/@req_mysql_dist
function req_yql_dist($host, $port, $login, $pass, $db='', $prefixe='') {

	return array(
		'endpoint' => 'http://query.yahooapis.com/v1/public/yql'
	);
}

$GLOBALS['spip_yql_functions_1'] = array(
		'alter' => 'spip_yql_alter',
		'count' => 'spip_yql_count',
		'countsel' => 'spip_yql_countsel',
		'create' => 'spip_yql_create',
		'create_base' => 'spip_yql_create_base',
		'create_view' => 'spip_yql_create_view',
		'date_proche' => 'spip_yql_date_proche',
		'delete' => 'spip_yql_delete',
		'drop_table' => 'spip_yql_drop_table',
		'drop_view' => 'spip_yql_drop_view',
		'errno' => 'spip_yql_errno',
		'error' => 'spip_yql_error',
		'explain' => 'spip_yql_explain',
		'fetch' => 'spip_yql_fetch',
		'seek' => 'spip_yql_seek',
		'free' => 'spip_yql_free',
		'hex' => 'spip_yql_hex',
		'in' => 'spip_yql_in', 
		'insert' => 'spip_yql_insert',
		'insertq' => 'spip_yql_insertq',
		'insertq_multi' => 'spip_yql_insertq_multi',
		'listdbs' => 'spip_yql_listdbs',
		'multi' => 'spip_yql_multi',
		'optimize' => 'spip_yql_optimize',
		'query' => 'spip_yql_query',
		'quote' => 'spip_yql_quote',
		'replace' => 'spip_yql_replace',
		'replace_multi' => 'spip_yql_replace_multi',
		'repair' => 'spip_yql_repair',
		'select' => 'spip_yql_select',
		'selectdb' => 'spip_yql_selectdb',
		'set_charset' => 'spip_yql_set_charset',
		'get_charset' => 'spip_yql_get_charset',
		'showbase' => 'spip_yql_showbase',
		'showtable' => 'spip_yql_showtable',
		'update' => 'spip_yql_update',
		'updateq' => 'spip_yql_updateq',

  // association de chaque nom http d'un charset aux couples MySQL 
		'charsets' => array(
'cp1250'=>array('charset'=>'cp1250','collation'=>'cp1250_general_ci'),
'cp1251'=>array('charset'=>'cp1251','collation'=>'cp1251_general_ci'),
'cp1256'=>array('charset'=>'cp1256','collation'=>'cp1256_general_ci'),
'iso-8859-1'=>array('charset'=>'latin1','collation'=>'latin1_swedish_ci'),
//'iso-8859-6'=>array('charset'=>'latin1','collation'=>'latin1_swedish_ci'),
'iso-8859-9'=>array('charset'=>'latin5','collation'=>'latin5_turkish_ci'),
//'iso-8859-15'=>array('charset'=>'latin1','collation'=>'latin1_swedish_ci'),
'utf-8'=>array('charset'=>'utf8','collation'=>'utf8_general_ci'))
		);

// http://doc.spip.org/@spip_yql_set_charset
function spip_yql_set_charset($charset, $serveur='',$requeter=true,$requeter=true){
	return;
}

// http://doc.spip.org/@spip_yql_get_charset
function spip_yql_get_charset($charset=array(), $serveur='',$requeter=true){
	return 'utf8';
}

// Fonction de requete generale, munie d'une trace a la demande

// http://doc.spip.org/@spip_yql_query
function spip_yql_query($query, $serveur='',$requeter=true) {
	global $RESULTS;

	$connexion = &$GLOBALS['connexions'][$serveur ? strtolower($serveur) : 0];

	#$query = yql_traite_query($query, $db, $prefixe);

	// renvoyer la requete inerte si demandee
	if (!$requeter) return $query;

	if (isset($_GET['var_profile'])) {
		include_spip('public/tracer');
		$t = trace_query_start();
	} else $t = 0 ;

	# le compilo me rajoute a pour faire "table.champ"
	# cf. l.83 dans public/references (et autres du meme acabit)
	$query = preg_replace('/yql\.([a-z]+\.)+/', '', $query);
	$query = str_replace('SELECT 1', 'SELECT *', $query);

	spip_log("yql: ".$query);

	$connexion['last'] = $query;
	$url = 'http://query.yahooapis.com/v1/public/yql'; #$connexion['endpoint'];
	$url = parametre_url($url, 'q', $query);

	$e = recuperer_page_cache($url.'&format=json');

	$r = json_decode($e);
	$r = (array) $r->query->results;
	$z = array_shift($r);

	return $z;

#	if ($e = spip_yql_errno($serveur))	// Log de l'erreur eventuelle
#		$e .= spip_yql_error($query, $serveur); // et du fautif
	return $t ? trace_query_end($query, $t, $r, $e, $serveur) : $r;
}

// http://doc.spip.org/@spip_yql_alter
function spip_yql_alter($query, $serveur='',$requeter=true){
	return false;
}

// http://doc.spip.org/@spip_yql_optimize
function spip_yql_optimize($table, $serveur='',$requeter=true){
	return false;
}

// http://doc.spip.org/@spip_yql_explain
function spip_yql_explain($query, $serveur='',$requeter=true){
	return false;
}
// fonction  instance de sql_select, voir ses specs dans abstract.php
// yql_traite_query pourrait y etre fait d'avance ce serait moins cher
// Les \n et \t sont utiles au debusqueur.


// http://doc.spip.org/@spip_yql_select
function spip_yql_select($select, $from, $where='',
			   $groupby='', $orderby='', $limit='', $having='',
			   $serveur='',$requeter=true) {

	if (preg_match('/(\d+),\s*(\d+)/', $limit, $r))
		$limit = $r[2]." offset ".$r[1];

	$from = (!is_array($from) ? $from : spip_yql_select_as($from));
	$query = 
		  calculer_yql_expression('SELECT', $select, ', ')
		. calculer_yql_expression('FROM', $from, ', ')
		. calculer_yql_expression('WHERE', $where)
		. calculer_yql_expression('GROUP BY', $groupby, ',')
		. calculer_yql_expression('HAVING', $having)
		. ($orderby ? ("\nORDER BY " . spip_yql_order($orderby)) :'')
		. ($limit ? "\nLIMIT $limit" : '');

	// renvoyer la requete inerte si demandee
	if ($requeter === false) return $query;
	$r = spip_yql_query($query, $serveur, $requeter);
	return $r ? $r : $query;
}

// 0+x avec un champ x commencant par des chiffres est converti par MySQL
// en le nombre qui commence x.
// Pas portable malheureusement, on laisse pour le moment.

// http://doc.spip.org/@spip_yql_order
function spip_yql_order($orderby)
{
	return (is_array($orderby)) ? join(", ", $orderby) :  $orderby;
}


// http://doc.spip.org/@calculer_yql_where
function calculer_yql_where($v)
{
	if (!is_array($v))
	  return $v ;

	$op = array_shift($v);
	if (!($n=count($v)))
		return $op;
	else {
		$arg = calculer_yql_where(array_shift($v));
		if ($n==1) {
			  return "$op($arg)";
		} else {
			$arg2 = calculer_yql_where(array_shift($v));
			if ($n==2) {
				return "($arg $op $arg2)";
			} else return "($arg $op ($arg2) : $v[0])";
		}
	}
}

// http://doc.spip.org/@calculer_yql_expression
function calculer_yql_expression($expression, $v, $join = 'AND'){
	if (empty($v))
		return '';
	
	$exp = "\n$expression ";
	
	if (!is_array($v)) {
		return $exp . $v;
	} else {
		if (strtoupper($join) === 'AND')
			return $exp . join("\n\t$join ", array_map('calculer_yql_where', $v));
		else
			return $exp . join($join, $v);
	}
}

// http://doc.spip.org/@spip_yql_select_as
function spip_yql_select_as($args)
{
	$res = '';
	foreach($args as $k => $v) {
		if (substr($k,-1)=='@') {
			// c'est une jointure qui se refere au from precedent
			// pas de virgule
		  $res .= '  ' . $v ;
		}
		else {
		  if (!is_numeric($k)) {
#		  	$p = strpos($v, " ");
#			if ($p)
#			  $v = substr($v,0,$p) . " AS `$k`" . substr($v,$p);
#			else $v .= " AS `$k`";
		  }
		      
		  $res .= ', ' . $v ;
		}
	}
	return substr($res,2);
}

//
// Changer les noms des tables ($table_prefix)
// Quand tous les appels SQL seront abstraits on pourra l'ameliorer

define('_SQL_PREFIXE_TABLE', '/([,\s])spip_/S');

// http://doc.spip.org/@yql_traite_query
function yql_traite_query($query, $db='', $prefixe='') {

	return $query;
}

// http://doc.spip.org/@spip_yql_selectdb
function spip_yql_selectdb($db) {
	return false;
}


// Retourne les bases accessibles
// Attention on n'a pas toujours les droits

// http://doc.spip.org/@spip_yql_listdbs
function spip_yql_listdbs($serveur='',$requeter=true) {
	return false;
}

// Fonction de creation d'une table SQL nommee $nom
// a partir de 2 tableaux PHP :
// champs: champ => type
// cles: type-de-cle => champ(s)
// si $autoinc, c'est une auto-increment (i.e. serial) sur la Primary Key
// Le nom des caches doit etre inferieur a 64 caracteres

// http://doc.spip.org/@spip_yql_create
function spip_yql_create($nom, $champs, $cles, $autoinc=false, $temporary=false, $serveur='',$requeter=true) {
	return false;

}

function spip_yql_create_base($nom, $serveur='',$requeter=true) {
	return false;
}

// Fonction de creation d'une vue SQL nommee $nom
// http://doc.spip.org/@spip_yql_create_view
function spip_yql_create_view($nom, $query_select, $serveur='',$requeter=true) {
	return false;
}


// http://doc.spip.org/@spip_yql_drop_table
function spip_yql_drop_table($table, $exist='', $serveur='',$requeter=true)
{
	return false;
}

// supprime une vue 
// http://doc.spip.org/@spip_yql_drop_view
function spip_yql_drop_view($view, $exist='', $serveur='',$requeter=true) {
	return false;
}

// http://doc.spip.org/@spip_yql_showbase
function spip_yql_showbase($match, $serveur='',$requeter=true)
{
	# ????
	return spip_yql_query("SHOW TABLES LIKE '$match'", $serveur, $requeter);
}

// http://doc.spip.org/@spip_yql_repair
function spip_yql_repair($table, $serveur='',$requeter=true)
{
	return false;
}

// Recupere la definition d'une table ou d'une vue MySQL
// colonnes, indexes, etc.
// au meme format que la definition des tables de SPIP
// http://doc.spip.org/@spip_yql_showtable
function spip_yql_showtable($nom_table, $serveur='',$requeter=true)
{
	$s = spip_yql_query('DESC '.$nom_table);

	$field = array();

	$t = $s->request->select;

	if (is_array($t->key)) {
		foreach ($t->key as $champ) {
			$field[$champ->name] = $champ->type;
		}
	}
	else
		if (is_array($t)) {
			foreach ($t as $u) {
				$u = $u->key[0];
				$field[$u->name] = $u->type;
			}
		}

#var_dump($field);
	# on lance la requete suggeree pour choper les champs de resultat
	$g = spip_yql_query($s->meta->sampleQuery);

	if (is_array($g))
		$z = array_pop($g);
	else if (is_object($g))
		$z = (array) $g;

	foreach($z as $k => $v)
		$field[$k] = 'text';
	return array('field' => $field);
}

//
// Recuperation des resultats
//

// http://doc.spip.org/@spip_yql_fetch
function spip_yql_fetch($r, $t='', $serveur='',$requeter=true) {
	static $ident = array();
	$k = md5(serialize($r));

	if (!isset($ident[$k]))
		$ident[$k] = $r;

	if (list(,$c) = each($ident[$k]))
		return (array) $c;
}

function spip_yql_seek($r, $row_number, $serveur='',$requeter=true) {
	return false;
}


// http://doc.spip.org/@spip_yql_countsel
function spip_yql_countsel($from = array(), $where = array(),
			     $groupby = '', $having = array(), $serveur='',$requeter=true)
{
	$c = !$groupby ? '*' : ('DISTINCT ' . (is_string($groupby) ? $groupby : join(',', $groupby)));

	$r = spip_yql_select("COUNT($c)", $from, $where,'', '', '', $having, $serveur, $requeter);

	if (!$requeter) return $r;
	if (!is_resource($r)) return 0;
	list($c) = yql_fetch_array($r, MYSQL_NUM);
	yql_free_result($r);
	return $c;
}

// Bien specifier le serveur auquel on s'adresse,
// mais a l'install la globale n'est pas encore completement definie
// http://doc.spip.org/@spip_yql_error
function spip_yql_error($query='', $serveur='',$requeter=true) {
	$link = $GLOBALS['connexions'][$serveur ? $serveur : 0]['link'];
	$s = $link ? mysql_error($link) : mysql_error();
	if ($s) spip_log("$s - $query", 'yql');
	return $s;
}

// A transposer dans les portages
// http://doc.spip.org/@spip_yql_errno
function spip_yql_errno($serveur='',$requeter=true) {
	$link = $GLOBALS['connexions'][$serveur ? $serveur : 0]['link'];
	$s = $link ? yql_errno($link) : mysql_errno();
	// 2006 MySQL server has gone away
	// 2013 Lost connection to MySQL server during query
	if (in_array($s, array(2006,2013)))
		define('spip_interdire_cache', true);
	if ($s) spip_log("Erreur yql $s");
	return $s;
}

// Interface de abstract_sql
// http://doc.spip.org/@spip_yql_count
function spip_yql_count($r, $serveur='',$requeter=true) {
	global $RESULTS;
	if ($r)	return count($RESULTS[$r]);
}


// http://doc.spip.org/@spip_yql_free
function spip_yql_free($r, $serveur='',$requeter=true) {
	global $RESULTS;
	unset($RESULTS[$r]);
}

// http://doc.spip.org/@spip_yql_insert
function spip_yql_insert($table, $champs, $valeurs, $desc='', $serveur='',$requeter=true) {
	return false;
}

// http://doc.spip.org/@spip_yql_insertq
function spip_yql_insertq($table, $couples=array(), $desc=array(), $serveur='',$requeter=true) {
	return false;
}


// http://doc.spip.org/@spip_yql_insertq_multi
function spip_yql_insertq_multi($table, $tab_couples=array(), $desc=array(), $serveur='',$requeter=true) {
	return false;
}

// http://doc.spip.org/@spip_yql_update
function spip_yql_update($table, $champs, $where='', $desc='', $serveur='',$requeter=true) {
	return false;
}

// idem, mais les valeurs sont des constantes a mettre entre apostrophes
// sauf les expressions de date lorsqu'il s'agit de fonctions SQL (NOW etc)
// http://doc.spip.org/@spip_yql_updateq
function spip_yql_updateq($table, $champs, $where='', $desc=array(), $serveur='',$requeter=true) {
	return false;
}

// http://doc.spip.org/@spip_yql_delete
function spip_yql_delete($table, $where='', $serveur='',$requeter=true) {
	return false;
}

// http://doc.spip.org/@spip_yql_replace
function spip_yql_replace($table, $couples, $desc=array(), $serveur='',$requeter=true) {
	return false;
}


// http://doc.spip.org/@spip_yql_replace_multi
function spip_yql_replace_multi($table, $tab_couples, $desc=array(), $serveur='',$requeter=true) {
	return false;
}


// http://doc.spip.org/@spip_yql_multi
function spip_yql_multi ($objet, $lang) {
	return false;
}

// http://doc.spip.org/@spip_yql_hex
function spip_yql_hex($v)
{
	return "0x" . $v;
}

function spip_yql_quote($v, $type='') {
	if ($type) {
		if (!is_array($v))
			return spip_yql_cite($v,$type);
		// si c'est un tableau, le parcourir en propageant le type
		foreach($v as $k=>$r)
			$v[$k] = spip_yql_quote($r, $type='');
		return $v;
	}
	// si on ne connait pas le type, s'en remettre a _q :
	// on ne fera pas mieux
	else
		return _q($v);
}

function spip_yql_date_proche($champ, $interval, $unite)
{
	return '('
	. $champ
        . (($interval <= 0) ? '>' : '<')
        . (($interval <= 0) ? 'DATE_SUB' : 'DATE_ADD')
	. '('
	. sql_quote(date('Y-m-d H:i:s'))
	. ', INTERVAL '
	. (($interval > 0) ? $interval : (0-$interval))
	. ' '
	. $unite
	. '))';
}

//
// IN (...) est limite a 255 elements, d'ou cette fonction assistante
//
// http://doc.spip.org/@spip_yql_in
function spip_yql_in($val, $valeurs, $not='', $serveur='',$requeter=true) {
	$n = $i = 0;
	$in_sql ="";
	while ($n = strpos($valeurs, ',', $n+1)) {
	  if ((++$i) >= 255) {
			$in_sql .= "($val $not IN (" .
			  substr($valeurs, 0, $n) .
			  "))\n" .
			  ($not ? "AND\t" : "OR\t");
			$valeurs = substr($valeurs, $n+1);
			$i = $n = 0;
		}
	}
	$in_sql .= "($val $not IN ($valeurs))";

	return "($in_sql)";
}

// pour compatibilite. Ne plus utiliser.
// http://doc.spip.org/@calcul_yql_in
function calcul_yql_in($val, $valeurs, $not='') {
	if (is_array($valeurs))
		$valeurs = join(',', array_map('_q', $valeurs));
	elseif ($valeurs[0]===',') $valeurs = substr($valeurs,1);
	if (!strlen(trim($valeurs))) return ($not ? "0=0" : '0=1');
	return spip_yql_in($val, $valeurs, $not);
}

// http://doc.spip.org/@spip_yql_cite
function spip_yql_cite($v, $type) {
	if (sql_test_date($type) AND preg_match('/^\w+\(/', $v))
		return $v;
	if (sql_test_int($type)) {
		if (is_numeric($v) OR (ctype_xdigit(substr($v,2))
			  AND $v[0]=='0' AND $v[1]=='x'))
			return $v;
		// si pas numerique, forcer le intval
		else
			return intval($v);
	}
	return  ("'" . addslashes($v) . "'");
}


// Renvoie false si on n'a pas les fonctions yql (pour l'install)
// http://doc.spip.org/@spip_versions_yql
function spip_versions_yql() {
	return '1.0';
}

// Tester si yql ne veut pas du nom de la base dans les requetes

// http://doc.spip.org/@test_rappel_nom_base_yql
function test_rappel_nom_base_yql($server_db)
{
	return false;
}

// http://doc.spip.org/@test_sql_mode_yql
function test_sql_mode_yql($server_db){
	return '';
}

function recuperer_page_cache($url) {
	include_spip('inc/memoization');
	$key = "MEMO_".$url;
	$ttl = 3600;
	if (!cache_isset($key)) {
		cache_set($key, null, $ttl);
		$r = recuperer_page($url);
		cache_set($key, $r, $ttl);
		return $r;
	}
	return cache_get($key);


#	if(!is_null($c=cache_me())) { var_dump($c);exit; };
#	return recuperer_page($url);
}

?>
