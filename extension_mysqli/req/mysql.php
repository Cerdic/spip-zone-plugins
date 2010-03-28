<?php

/**
 * Utilisation du connecteur MySQLi pour SPIP
 *
 * @license    GNU/GPL
 * @package    plugins
 * @subpackage mysqli
 * @category   BDD
 * @version    $Id$
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

if (!extension_loaded('mysqli')) {
	charger_php_extension('mysqli');
}

define('_DEFAULT_DB', 'spip');

/**
 * fonction pour changer la connexion aux serveurs MySQL en gardant les paramètres existants
 *
 * Cette fonction sert de constructeur de l'instance de connexion MySQLi
 * 
 * @staticvar  array    $last_connect  mémorise les paramètres de connexion
 * @param      string   $host          serveur MySQL
 * @param      string   $port          port utilisé
 * @param      string   $login         login MySQL
 * @param      string   $pass          mot de passe
 * @param      string   $db            base utilisée
 * @param      string   $prefixe       préfixe utilisé
 * @param      bool     $reconnect     indique si on utilise $last_connect pour se connecter
 * @return     array
 */
function req_mysql_dist($host, $port, $login, $pass, $db='', $prefixe='', $reconnect=FALSE) {
	static $last_connect = array(); // Pour se reconnecter si neccessaire

	// Il faut initialiser le connexion
	$connexion = mysqli_init();

	if (!$reconnect) {
		// On ne vient pas d'un select_db
		// Possibilite de stocker en php.ini les parametre de connexion
		if (!$host) $host = ini_get('mysqli.default_host');
		if (!$host) $host = 'localhost';
		if (!$port) $port = ini_get('mysqli.default_port');
		if (!$login) $login = ini_get('mysqli.default_user');
		if (!$pass) $pass = ini_get('mysqli.default_pw');
		if (!$db) $db = _DEFAULT_DB;
	} else {
		if (empty($host) && empty($port) && empty($login) && empty($pass)){
			foreach (array('host','port','login','pass','prefixe') as $a){
				$$a = $last_connect[$a];
			}
		}
	}
	
	// Connexion proprement dite
	// On pourrait encore jouer sur des options et sur le socket a utiliser
	if (@$connexion->real_connect($host, $login, $pass, $db, $port)) {
		$last_connect = array (
			'host' => $host,
			'port' => $port,
			'login' => $login,
			'pass' => $pass,
			'db' => $db,
			'prefixe' => $prefixe
		);
	} else {
		return FALSE;
	}

	// Afin d'eviter les bugs, il peut etre utile d'etre en 'STRICT_ALL_TABLES' par ex.
	// voir 'STRICT_ALL_TABLES,ANSI_QUOTES,NO_AUTO_VALUE_ON_ZERO,NO_ZERO_DATE,NO_ZERO_IN_DATE'
	if (defined('_MYSQL_SET_SQL_MODE')) {
		@$connexion->query("set sql_mode='"._q(_MYSQL_SET_SQL_MODE)."'");
	}

	return array(
		'db' => $db,
		'last' => '',
		'prefixe' => $prefixe,
		'link' => $connexion
	);

}

/**
 * Tableau de correspondance des appels sql / API mysqli
 *
 * @global  array   $GLOBALS['spip_mysql_functions_1']
 * @name    $spip_mysql_functions_1
 */
$GLOBALS['spip_mysql_functions_1'] = array(
	'alter' => 'spip_mysqli_alter',
	'count' => 'spip_mysqli_count',
	'countsel' => 'spip_mysqli_countsel',
	'create' => 'spip_mysqli_create',
	'create_base' => 'spip_mysqli_create_base',
	'create_view' => 'spip_mysqli_create_view',
	'date_proche' => 'spip_mysqli_date_proche',
	'delete' => 'spip_mysqli_delete',
	'drop_table' => 'spip_mysqli_drop_table',
	'drop_view' => 'spip_mysqli_drop_view',
	'errno' => 'spip_mysqli_errno',
	'error' => 'spip_mysqli_error',
	'explain' => 'spip_mysqli_explain',
	'fetch' => 'spip_mysqli_fetch',
	'seek' => 'spip_mysqli_seek',
	'free' => 'spip_mysqli_free',
	'hex' => 'spip_mysqli_hex',
	'in' => 'spip_mysqli_in', 
	'insert' => 'spip_mysqli_insert',
	'insertq' => 'spip_mysqli_insertq',
	'insertq_multi' => 'spip_mysqli_insertq_multi',
	'listdbs' => 'spip_mysqli_listdbs',
	'multi' => 'spip_mysqli_multi',
	'optimize' => 'spip_mysqli_optimize',
	'query' => 'spip_mysqli_query',
	'quote' => 'spip_mysqli_quote',
	'replace' => 'spip_mysqli_replace',
	'replace_multi' => 'spip_mysqli_replace_multi',
	'repair' => 'spip_mysqli_repair',
	'select' => 'spip_mysqli_select',
	'selectdb' => 'spip_mysqli_selectdb',
	'set_charset' => 'spip_mysqli_set_charset',
	'get_charset' => 'spip_mysqli_get_charset',
	'showbase' => 'spip_mysqli_showbase',
	'showtable' => 'spip_mysqli_showtable',
	'update' => 'spip_mysqli_update',
	'updateq' => 'spip_mysqli_updateq',
	//// association de chaque nom http d'un charset aux couples MySQL
	'charsets' => array(
		'cp1250'=>array('charset'=>'cp1250','collation'=>'cp1250_general_ci'),
		'cp1251'=>array('charset'=>'cp1251','collation'=>'cp1251_general_ci'),
		'cp1256'=>array('charset'=>'cp1256','collation'=>'cp1256_general_ci'),
		'iso-8859-1'=>array('charset'=>'latin1','collation'=>'latin1_swedish_ci'),
		//'iso-8859-6'=>array('charset'=>'latin1','collation'=>'latin1_swedish_ci'),
		'iso-8859-9'=>array('charset'=>'latin5','collation'=>'latin5_turkish_ci'),
		//'iso-8859-15'=>array('charset'=>'latin1','collation'=>'latin1_swedish_ci'),
		'utf-8'=>array('charset'=>'utf8','collation'=>'utf8_general_ci')
		)
	);


/**
 * Appelée à chaque connexion, cette requête fixe le charset utilisé pour les futures requêtes
 *
 * @link    http://doc.spip.org/@spip_mysql_set_charset
 * @param   string  $charset   Le charset à utiliser
 * @param   string  $serveur   Identifiant du serveur concerné
 * @param   bool    $requeter  Inutilisé
 * @param   bool    $requeter  Inutilisé
 * @return  bool    TRUE si l'affectation a réussi
 */
function spip_mysqli_set_charset($charset, $serveur='',$requeter=true,$requeter=true){
	$link = &$GLOBALS['connexions'][$serveur ? $serveur : 0]['link'];
	$ok = FALSE;
	if (version_compare(PHP_VERSION , '5.1.5', '>=')) {
		$ok = $link->set_charset($charset);
	}
	if (!$ok) {
	    $ok = $link->query("SET NAMES '"._q($charset)."'");
	}
	return $ok;
}

/**
 * Récupère les charsets disponibles
 *
 * @link    http://doc.spip.org/@spip_mysql_get_charset
 * @param   array   $charset   Pattern pour restreindre les résultats
 * @param   string  $serveur   Identifiant du serveur
 * @param   bool    $requeter  Inutilisé
 * @return  mysqli_result
 */
function spip_mysqli_get_charset($charset=array(), $serveur='',$requeter=true){
	$connexion = &$GLOBALS['connexions'][$serveur ? $serveur : 0];
	$connexion['last'] = $c = "SHOW CHARACTER SET"
	. (!$charset ? '' : (" LIKE "._q($charset['charset'])));
	$link = $connexion['link'];
	return $link->query($c);
}

/**
 * Fonction de requête générale, munie d'une trace à la demande
 *
 * @link    http://doc.spip.org/@spip_mysql_query
 * @param   string  $query     La requête MySQL
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  mixed
 */
function spip_mysqli_query($query, $serveur='',$requeter=true) {

	$connexion = &$GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];
	$link = $connexion['link'];
	$db = $connexion['db'];

        $query = traite_mysqli_query($query, $db, $prefixe);

	// renvoyer la requete inerte si demandee
	if (!$requeter) return $query;

	if (isset($_GET['var_profile'])) {
		include_spip('public/tracer');
		$t = trace_query_start();
	} else $t = 0 ;
 
	$connexion['last'] = $query;
	$r = $link->real_query($query);
	if ($r) $result = $link->use_result();
	if ($result) $r = $result;

	return $t ? trace_query_end($query, $t, $r , $serveur) : $r;
}

/**
 * Modifie la structure d'une table ou base
 *
 * S'utilisera comme ceci :
 * <code>
 * sql_alter("TABLE table ADD COLUMN colonne INT");
 * sql_alter("TABLE table ADD colonne INT"); // COLUMN est optionnel
 * sql_alter("TABLE table CHANGE colonne colonne INT DEFAUT '0'");
 * sql_alter("TABLE table ADD INDEX colonne (colonne)");
 * sql_alter("TABLE table DROP INDEX colonne");
 * sql_alter("TABLE table DROP COLUMN colonne");
 * sql_alter("TABLE table DROP colonne"); // COLUMN est optionnel
 * // possibilité de passer plusieurs actions :
 * sql_alter("TABLE table DROP colonneA, DROP colonneB");
 * </code>
 *
 * @link    http://doc.spip.org/@spip_mysql_alter
 * @param   string  $query     La requête MySQL
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  mysqli_result
 */
function spip_mysqli_alter($query, $serveur='',$requeter=true){
	return spip_mysqli_query("ALTER ".$query, $serveur, $requeter); # i.e. que PG se debrouille
}

/**
 * Lance la défragmentation d'une table
 *
 * @link    http://doc.spip.org/@spip_mysql_optimize
 * @param   string  $table     La table à défragmenter
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  bool
 */
function spip_mysqli_optimize($table, $serveur='',$requeter=true){
	spip_mysqli_query("OPTIMIZE TABLE ". $table);
	return true;
}

/**
 * Obtenir des informations sur les SELECT
 *
 * @link    http://doc.spip.org/@spip_mysql_explain
 * @param   string  $query	   La requête MySQL
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter	 Inutilisé
 * @return  array
 */
function spip_mysqli_explain($query, $serveur='',$requeter=true){
	if (strpos(ltrim($query), 'SELECT') !== 0) return array();
	$connexion = &$GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];
	$link = $connexion['link'];
	$db = $connexion['db'];

	$query = 'EXPLAIN ' . traite_mysqli_query($query, $db, $prefixe);
	$r = $link->query($query);
	return $r;
}

/**
 * fonction instance de sql_select
 *
 * voir ses specs dans {@link http://doc.spip.org/abstract_sql-php abstract_sql.php}<br>
 * Traite_mysqli_query pourrait y etre fait d'avance ce serait moins cher<br>
 * Les \n et \t sont utiles au debusqueur.
 *
 * La fonction {@link http://doc.spip.org/@sql_select sql_select()} est souvent couplée à
 * {@link http://doc.spip.org/@sql_select sql_fetch()} comme ceci :
 * <code>
 * // sélection
 * if ($resultats = sql_select('colonne', 'table')) {
 *     // boucler sur les résultats
 *     while ($res = sql_fetch($resultats)) {
 *         // utiliser les résultats
 *         // $res['colonne']
 *     }
 * }
 * </code>
 *
 * @link    http://doc.spip.org/@spip_mysql_select
 * @param   string|array    $select     liste des champs à récupérer
 * @param   string|array    $from       Liste des tables
 * @param   string|array    $where      Conditions que les lignes sélectionnées doivent satisfaire
 * @param   string|array    $groupby    Colonnes qui déterminent le tri des lignes
 * @param   string|array    $orderby    Ordre des résultats
 * @param   string|array    $limit      Nombre de résultats + Offset
 * @param   string          $having     Peut servir de fonction d'aggrégation
 * @param   string          $serveur    Identifiant du connecteur à utiliser
 * @param   bool            $requeter   Inutilisé
 * @return  mixed
 */
function spip_mysqli_select($select, $from, $where='',
			   $groupby='', $orderby='', $limit='', $having='',
			   $serveur='',$requeter=true) {


	$from = (!is_array($from) ? $from : spip_mysqli_select_as($from));
	$query = 
		  calculer_mysqli_expression('SELECT', $select, ', ')
		. calculer_mysqli_expression('FROM', $from, ', ')
		. calculer_mysqli_expression('WHERE', $where)
		. calculer_mysqli_expression('GROUP BY', $groupby, ',')
		. calculer_mysqli_expression('HAVING', $having)
		. ($orderby ? ("\nORDER BY " . spip_mysqli_order($orderby)) :'')
		. ($limit ? "\nLIMIT $limit" : '');

	// renvoyer la requete inerte si demandee
	if ($requeter === false) return $query;
	$r = spip_mysqli_query($query, $serveur, $requeter);
	return $r ? $r : $query;
}

/**
 * Définir l'ordre des résultats
 *
 * 0+x avec un champ x commencant par des chiffres est converti par MySQL
 * en le nombre qui commence x.
 *
 * Pas portable malheureusement, on laisse pour le moment.
 *
 * @link    http://doc.spip.org/@spip_mysql_order
 * @param   string|array    $orderby   Le ou les colonnes
 * @return  string
 */
function spip_mysqli_order($orderby)
{
	return (is_array($orderby)) ? join(", ", $orderby) :  $orderby;
}


/**
 * Construction d'une expression_where
 *
 * @link    http://doc.spip.org/@calculer_mysqli_where
 * @param   array|string    $v   arbre abstrait des conditions
 * @return  string
 */
function calculer_mysqli_where($v)
{
	if (!is_array($v))
	  return $v ;

	$op = array_shift($v);
	if (!($n=count($v)))
		return $op;
	else {
		$arg = calculer_mysqli_where(array_shift($v));
		if ($n==1) {
			  return "$op($arg)";
		} else {
			$arg2 = calculer_mysqli_where(array_shift($v));
			if ($n==2) {
				return "($arg $op $arg2)";
			} else return "($arg $op ($arg2) : $v[0])";
		}
	}
}

/**
 * Calcule un bloc d'expression MySQL
 *
 * @link    http://doc.spip.org/@calculer_mysql_expression
 * @param   string          $expression   Expression déjà évaluée
 * @param   array|string    $v            tableau des éléments à rassembler
 * @param   string          $join         séparateur servant à rassembler les éléments de $v
 * @return  string
 */
function calculer_mysqli_expression($expression, $v, $join = 'AND'){
	if (empty($v))
		return '';
	
	$exp = "\n$expression ";
	
	if (!is_array($v)) {
		return $exp . $v;
	} else {
		if (strtoupper($join) === 'AND')
			return $exp . join("\n\t$join ", array_map('calculer_mysqli_where', $v));
		else
			return $exp . join($join, $v);
	}
}

/**
 * Création de la liste des éléments à sélectionner dans la requête SQL
 *
 * @link    http://doc.spip.org/@spip_mysql_select_as
 * @param   array   $args   Liste des éléments
 * @return  string
 */
function spip_mysqli_select_as($args)
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
		  	$p = strpos($v, " ");
			if ($p)
			  $v = substr($v,0,$p) . " AS `$k`" . substr($v,$p);
			else $v .= " AS `$k`";
		  }
		      
		  $res .= ', ' . $v ;
		}
	}
	return substr($res,2);
}

/**
 * Changer les noms des tables ($table_prefix)
 *
 * Quand tous les appels SQL seront abstraits on pourra l'améliorer
 */
define('_SQL_PREFIXE_TABLE', '/([,\s])spip_/S');

/**
 * Prépare une requête incomplète
 *
 * @link    http://doc.spip.org/@traite_mysql_query
 * @param   string  $query    La requête partielle
 * @param   string  $db	      Le nom de la base
 * @param   string  $prefixe  Prefixe des tables SPIP
 * @return  string
 */
function traite_mysqli_query($query, $db='', $prefixe='') {

	if ($GLOBALS['mysqli_rappel_nom_base'] AND $db)
		$pref = '`'. $db.'`.';
	else $pref = '';

	if ($prefixe)
		$pref .= $prefixe . "_";

	if (!preg_match('/\s(SET|VALUES|WHERE|DATABASE)\s/i', $query, $regs)) {
		$suite ='';
	} else {
		$suite = strstr($query, $regs[0]);
		$query = substr($query, 0, -strlen($suite));
		if (preg_match('/^(.*?)([(]\s*SELECT\b.*)$/si', $suite, $r)) {
		  $suite = $r[1] . traite_mysqli_query($r[2], $db, $prefixe);
		}
	}
	$r = preg_replace(_SQL_PREFIXE_TABLE, '\1'.$pref, $query) . $suite;
	# spip_log("traite_mysqli_query: " . substr($r,0, 50) . ".... $db, $prefixe");
	return $r;
}

/**
 * Sélectionne une base de données par défaut pour les requêtes
 *
 * @link    http://doc.spip.org/@spip_mysql_selectdb
 * @param   string  $db	       Nom de la base
 * @param   string  $serveur   Identifiant du connecteur SPIP
 * @param   bool    $requeter  Inutilisé
 * @return  bool    TRUE en cas de succès
 */
function spip_mysqli_selectdb($db, $serveur='',$requeter=true) {
	$link = &$GLOBALS['connexions'][$serveur ? $serveur : 0]['link'];
	return $link->select_db($db);
}

/**
 * Retourne les bases accessibles
 *
 * Attention on n'a pas toujours les droits
 *
 * @link    http://doc.spip.org/@spip_mysql_listdbs
 * @param   string  $serveur   Identifiant du connecteur SPIP
 * @param   bool    $requeter  Inutilisé
 * @return  mysqli_result
 */
function spip_mysqli_listdbs($serveur='',$requeter=true) {
	return spip_mysqli_query("show databases",$serveur,$requeter);
}

/**
 * Fonction de création d'une table SQL nommée $nom
 *
 * Cette fonction utilise 2 tableaux PHP :
 * - champs: champ => type
 * - cles: type-de-cle => champ(s)
 *
 * si $autoinc, c'est une auto-increment (i.e. serial) sur la Primary Key.<br>
 * Le nom des caches doit être inférieur à 64 caractères
 *
 * @link    http://doc.spip.org/@spip_mysql_create
 * @param   string  $nom        Table à créer
 * @param   array   $champs     Liste des champs
 * @param   array   $cles       Liste des clés
 * @param   bool    $autoinc    TRUE si auto-increment sur la clé primaire
 * @param   bool    $temporary  TRUE si Table temporaire
 * @param   string  $serveur    Identifiant du connecteur concernée
 * @param   bool    $requeter   Inutilisé
 * @return  bool    TRUE si la création s'est bien passée
 */
function spip_mysqli_create($nom, $champs, $cles, $autoinc=false, $temporary=false, $serveur='',$requeter=true) {

	$query = ''; $keys = ''; $s = ''; $p='';

	// certains plugins declarent les tables  (permet leur inclusion dans le dump)
	// sans les renseigner (laisse le compilo recuperer la description)
	if (!is_array($champs) || !is_array($cles)) 
		return;

	foreach($cles as $k => $v) {
		$keys .= "$s\n\t\t$k ($v)";
		if ($k == "PRIMARY KEY")
			$p = $v;
		$s = ",";
	}
	$s = '';
	
	$character_set = "";
	if (@$GLOBALS['meta']['charset_sql_base'])
		$character_set .= " CHARACTER SET ".$GLOBALS['meta']['charset_sql_base'];
	if (@$GLOBALS['meta']['charset_collation_sql_base'])
		$character_set .= " COLLATE ".$GLOBALS['meta']['charset_collation_sql_base'];

	foreach($champs as $k => $v) {
		if (preg_match(',([a-z]*\s*(\(\s*[0-9]*\s*\))?(\s*binary)?),i',$v,$defs)){
			if (preg_match(',(char|text),i',$defs[1]) AND !preg_match(',binary,i',$defs[1]) ){
				$v = $defs[1] . $character_set . ' ' . substr($v,strlen($defs[1]));
			}
		}

		$query .= "$s\n\t\t$k $v"
			. (($autoinc && ($p == $k) && preg_match(',\b(big|small|medium)?int\b,i', $v))
				? " auto_increment"
				: ''
			);
		$s = ",";
	}
	$temporary = $temporary ? 'TEMPORARY':'';
	$q = "CREATE $temporary TABLE IF NOT EXISTS $nom ($query" . ($keys ? ",$keys" : '') . ")".
	($character_set?" DEFAULT $character_set":"")
	."\n";
	return spip_mysqli_query($q, $serveur);
}

/**
 * Créer une base de données sur un connecteur SPIP
 *
 * @param   string  $nom       Nom de la base
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  bool    TRUE si succès
 */
function spip_mysqli_create_base($nom, $serveur='',$requeter=true) {
  return spip_mysqli_query("CREATE DATABASE `$nom`", $serveur, $requeter);
}

/**
 * Fonction de création d'une vue SQL nommée $nom
 *
 * <b>Application :</b><br>
 * Ce petit exemple montre le fonctionnement, en créant une table
 * (bien inutile) à partir de 2 colonnes d’une rubrique :
 * <code>
 * $select = sql_get_select(array(
 *         'r.titre AS t',
 *         'r.id_rubrique AS id'
 *     ), array(
 *         'spip_rubriques AS r'
 *     ));
 * // creer la vue
 * sql_create_view('spip_short_rub', $select);
 * // utiliser :
 * $titre = sql_getfetsel('t', 'spip_short_rub', 'id=8');
 * </code>
 * @link    http://doc.spip.org/@spip_mysql_create_view
 * @param   string  $nom
 * @param   string  $query_select
 * @param   string  $serveur         Identifiant du connecteur à utiliser
 * @param   bool    $requeter        Inutilisé
 * @return  bool
 */
function spip_mysqli_create_view($nom, $query_select, $serveur='',$requeter=true) {
	if (!$query_select) return false;
	// vue deja presente
	if (sql_showtable($nom, false, $serveur)) {
		spip_log("Echec creation d'une vue sql ($nom) car celle-ci existe deja (serveur:$serveur)");
		return false;
	}
	
	$query = "CREATE VIEW $nom AS ". $query_select;
	return spip_mysqli_query($query, $serveur, $requeter);
}


/**
 * Supprime une table MySQL
 *
 * @link    http://doc.spip.org/@spip_mysql_drop_table
 * @param   string  $table      La table
 * @param   string  $exist      Est-ce qu'on teste l'existence de la table
 * @param   string  $serveur    Identifiant du connecteur à utiliser
 * @param   bool    $requeter   Inutilisé
 * @return  bool    TRUE si la suppression a fonctionné
 */
function spip_mysqli_drop_table($table, $exist='', $serveur='',$requeter=true)
{
	if ($exist) $exist =" IF EXISTS";
	return spip_mysqli_query("DROP TABLE$exist $table", $serveur, $requeter);
}

/**
 * Supprime une vue
 *
 * @link    http://doc.spip.org/@spip_mysql_drop_view
 * @param   string  $view      Nom de la vue
 * @param   string  $exist     Doit-on tester son existence ?
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  bool    TRUE si la suppression a fonctionné
 */
function spip_mysqli_drop_view($view, $exist='', $serveur='',$requeter=true) {
	if ($exist) $exist =" IF EXISTS";
	return spip_mysqli_query("DROP VIEW$exist $view", $serveur, $requeter);
}

/**
 * Liste des tables selon un certain critère
 *
 * @link    http://doc.spip.org/@spip_mysql_showbase
 * @param   string  $match     Critère de recherche
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  mysqli_result
 */
function spip_mysqli_showbase($match, $serveur='',$requeter=true)
{
	return spip_mysqli_query("SHOW TABLES LIKE '$match'", $serveur, $requeter);
}

/**
 * Tente la réparation d'une table
 *
 * @link    http://doc.spip.org/@spip_mysql_repair
 * @param   string  $table     La table
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  bool    TRUE en cas de succès
 */
function spip_mysqli_repair($table, $serveur='',$requeter=true)
{
	return spip_mysqli_query("REPAIR TABLE $table", $serveur, $requeter);
}

/**
 * Récupère la definition d'une table ou d'une vue MySQL
 *
 * Les éléments obtenus sont les colonnes, indexes, etc.. <br>
 * au même format que la définition des tables de SPIP
 *
 * @link    http://doc.spip.org/@spip_mysql_showtable
 * @param   string  $nom_table   nom de la table ou vue MySQL
 * @param   string  $serveur     Identifiant du connecteur à utiliser
 * @param   bool    $requeter    TRUE pour créer une représentation abstraite SPIP
 * @return  mixed
 */
function spip_mysqli_showtable($nom_table, $serveur='',$requeter=true)
{
	$s = spip_mysqli_query("SHOW CREATE TABLE `$nom_table`", $serveur, $requeter);
	if (!$s) return '';
	if (!$requeter) return $s;

	list(,$a) = mysqli_fetch_array($s ,MYSQLI_NUM);
	if (preg_match("/^[^(),]*\((([^()]*\([^()]*\)[^()]*)*)\)[^()]*$/", $a, $r)){
		$dec = $r[1];
		if (preg_match("/^(.*?),([^,]*KEY.*)$/s", $dec, $r)) {
		  $namedkeys = $r[2];
		  $dec = $r[1];
		}
		else 
		  $namedkeys = "";

		$fields = array();
		foreach(preg_split("/,\s*`/",$dec) as $v) {
		  preg_match("/^\s*`?([^`]*)`\s*(.*)/",$v,$r);
		  $fields[strtolower($r[1])] = $r[2];
		}
		$keys = array();

		foreach(preg_split('/\)\s*,?/',$namedkeys) as $v) {
		  if (preg_match("/^\s*([^(]*)\((.*)$/",$v,$r)) {
			$k = str_replace("`", '', trim($r[1]));
			$t = strtolower(str_replace("`", '', $r[2]));
			if ($k && !isset($keys[$k])) $keys[$k] = $t; else $keys[] = $t;
		  }
		}
		$s->free();
		return array('field' => $fields, 'key' => $keys);
	}

	$res = spip_mysqli_query("SHOW COLUMNS FROM `$nom_table`", $serveur);
	if($res) {
	  $nfields = array();
	  $nkeys = array();
	  while($val = spip_mysqli_fetch($res)) {
		$nfields[$val["Field"]] = $val['Type'];
		if($val['Null']=='NO') {
		  $nfields[$val["Field"]] .= ' NOT NULL'; 
		}
		if($val['Default'] === '0' || $val['Default']) {
		  if(preg_match('/[A-Z_]/',$val['Default'])) {
			$nfields[$val["Field"]] .= ' DEFAULT '.$val['Default'];		  
		  } else {
			$nfields[$val["Field"]] .= " DEFAULT '".$val['Default']."'";		  
		  }
		}
		if($val['Extra'])
		  $nfields[$val["Field"]] .= ' '.$val['Extra'];
		if($val['Key'] == 'PRI') {
		  $nkeys['PRIMARY KEY'] = $val["Field"];
		} else if($val['Key'] == 'MUL') {
		  $nkeys['KEY '.$val["Field"]] = $val["Field"];
		} else if($val['Key'] == 'UNI') {
		  $nkeys['UNIQUE KEY '.$val["Field"]] = $val["Field"];
		}
	  }
	  $res->free();
	  return array('field' => $nfields, 'key' => $nkeys);
	}
	return "";
}

//
// Récupération des résultats
//

/**
 * Retourne un tableau qui correspond à la ligne lue ou NULL s'il n'y a plus
 * de lignes dans le jeu de résultats $r.
 *
 * Cette fonction s’utilise en partenariat étroit avec sql_select(),
 * souvent employé dans cette association :
 * <code>
 * if ($res = sql_select('colonne', 'table')) {
 *     while ($r = sql_fetch($res)) {
 *         // utilisation des resultats avec $r['colonne']
 *     }
 * }
 * </code>
 *
 * @link http://doc.spip.org/@spip_mysql_fetch
 * @param   mysqli_result   $r         Le jeu de résultats
 * @param   int             $t         Type de résultat : MYSQLI_ASSOC, MYSQLI_NUM ou MYSQLI_BOTH
 * @param   string          $serveur   Inutilisé
 * @param   bool            $requeter  Inutilisé
 * @return  mixed           le résultat sous forme de tableau ou NULL si pas de résultat
 */
function spip_mysqli_fetch($r, $t='', $serveur='',$requeter=true) {
	$res = NULL;
	if (!$t) $t = MYSQLI_ASSOC;
	if ($r) {
	    $res = $r->fetch_array($t);
	}
	return $res;
}

/**
 * Déplace le pointeur interne de résultat associé au jeu de résultats
 * représenté par result, en le faisant pointer sur la ligne spécifiée
 * par row_number.
 *
 * @param   mysqli_result  $r
 * @param   int            $row_number
 * @param   string         $serveur       Inutilisé
 * @param   bool           $requeter       Inutilisé
 * @return  bool           TRUE en cas de succès
 */
function spip_mysqli_seek($r, $row_number, $serveur='',$requeter=true) {
	if ($r instanceof MySQLi_Result) {
	    $res = $r->data_seek($row_number);
	    $r->free();
	    return $res;
	}
}


/**
 * Retourne le nombre de résultats d'un SELECT.
 *
 * <b>Application</b> :<br>
 * Retourner <i>false</i> s’il y a des articles dans une rubrique :
 * <code>
 * if (sql_countsel('spip_articles', array(
 *    "id_rubrique=$id_rubrique",
 *    "statut <> 'poubelle'"
 *    ))) {
 *    return false;
 * }
 * </code>
 *
 * @link    http://doc.spip.org/@spip_mysql_countsel
 * @param   array|string   $from
 * @param   array|string   $where
 * @param   array|string   $groupby
 * @param   array|string   $having
 * @param   string         $serveur   Identifiant du connecteur à utiliser
 * @param   bool           $requeter
 * @return  mixed
 */
function spip_mysqli_countsel($from = array(), $where = array(),
			     $groupby = '', $having = array(), $serveur='',$requeter=true)
{
	$c = !$groupby ? '*' : ('DISTINCT ' . (is_string($groupby) ? $groupby : join(',', $groupby)));

	$r = spip_mysqli_select("COUNT($c)", $from, $where,'', '', '', $having, $serveur, $requeter);

	if (!$requeter) return $r;
	if (!($r)) return 0;
	list($c) = $r->fetch_array(MYSQLI_NUM);
	$r->free();
	return $c;
}

/**
 * Retourne une chaîne décrivant la dernière erreur
 *
 * @link    http://doc.spip.org/@spip_mysql_error
 * @param   string   $serveur   Identifiant du connecteur à utiliser
 * @return  string
 */
function spip_mysqli_error($serveur='') {
	$connexion = &$GLOBALS['connexions'][$serveur ? $serveur : 0];
	$link = $connexion['link'];
	return $link->error . $connexion['last'];
}

/**
 * Retourne le dernier code d'erreur produit.
 *
 * En cas de perte de connexion avec le serveur, il ne faut pas recalculer le cache.
 *
 * @link    http://doc.spip.org/@spip_mysql_errno
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @return  int
 */
function spip_mysqli_errno($serveur='') {
	$connexion = &$GLOBALS['connexions'][$serveur ? $serveur : 0];
	$link = $connexion['link'];
	$s = $link->errno;
	// 2006 MySQL server has gone away
	// 2013 Lost connection to MySQL server during query
	if (in_array($s, array(2006,2013)))
		define('spip_interdire_cache', true);
	return $s;
}

/**
 * Retourne le nombre de lignes dans le jeu de résultats.
 *
 * @link    http://doc.spip.org/@spip_mysql_count
 * @param   mysqli_result   $r
 * @param   string          $serveur   Identifiant du connecteur à utiliser
 * @param   bool            $requeter  Inutilisé
 * @return  int
 */
function spip_mysqli_count($r, $serveur='',$requeter=true) {
	if ($r instanceof MySQLi_Result)
	    return $r->num_rows;
}


/**
 * Libère la mémoire associée à un résultat.
 *
 * À noter que des fonctions de l’API appellent cette fonction automatiquement. C’est le cas de :
 * - {@link http://doc.spip.org/@sql_fetsel sql_fetsel} (et {@link http://doc.spip.org/@sql_getfetsel sql_getfetsel}),
 * - {@link http://doc.spip.org/@sql_fetch_all sql_fetch_all} (et {@link http://doc.spip.org/@sql_allfetsel sql_allfetsel}),
 * - {@link http://doc.spip.org/@sql_in_select sql_in_select}.

 * @link    http://doc.spip.org/@spip_mysql_free
 * @param   mysqli_result   $r
 * @param   string          $serveur    Identifiant du connecteur à utiliser
 * @param   bool            $requeter   Inutilisé
 * @return  void
 */
function spip_mysqli_free($r, $serveur='',$requeter=true) {
	if ($r instanceof MySQLi_Result)
	    $r->free();
}

/**
 * insère une nouvelle ligne dans une table existante.
 *
 * @link    http://doc.spip.org/@spip_mysql_insert
 * @param   string  $table     La table
 * @param   string  $champs    Liste des champs
 * @param   string  $valeurs   valeurs de chaque champ
 * @param   string  $desc      Inutilisé
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  mixed   L'identifiant de l'élément inséré, FALSE en cas d'échec
 */
function spip_mysqli_insert($table, $champs, $valeurs, $desc='', $serveur='',$requeter=true) {

	$connexion = &$GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];
	$link = $connexion['link'];
	$db = $connexion['db'];

	if ($prefixe) $table = preg_replace('/^spip/', $prefixe, $table);

	if (isset($_GET['var_profile'])) {
		include_spip('public/tracer');
		$t = trace_query_start();
	} else $t = 0 ;
 
	$connexion['last'] = $query ="INSERT INTO $table $champs VALUES $valeurs";
#	spip_log($query);
	if ($link->query($query))
		$r = $link->insert_id;
	else $r = false;

	return $t ? trace_query_end($query, $t, $r, $serveur) : $r;

	// return $r ? $r : (($r===0) ? -1 : 0); pb avec le multi-base.
}

/**
 * Insère une nouvelle ligne dans une table existante.
 *
 * @link    http://doc.spip.org/@spip_mysql_insertq
 * @param   string  $table     La table
 * @param   array   $couples   couples champ => valeur
 * @param   array   $desc      description de la table
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  mixed   L'identifiant de l'élément inséré, FALSE en cas d'échec
 */
function spip_mysqli_insertq($table, $couples=array(), $desc=array(), $serveur='',$requeter=true) {

	if (!$desc) $desc = description_table($table);
	if (!$desc) $couples = array();
	$fields =  isset($desc['field'])?$desc['field']:array();

	foreach ($couples as $champ => $val) {
		$couples[$champ]= spip_mysqli_cite($val, $fields[$champ]);
	}

	return spip_mysqli_insert($table, "(".join(',',array_keys($couples)).")", "(".join(',', $couples).")", $desc, $serveur, $requeter);
}


/**
 * Insère plusieures lignes dans une table existante.
 *
 * @link    http://doc.spip.org/@spip_mysql_insertq_multi
 * @param   string  $table         La table
 * @param   array   $tab_couples   couples champ => valeur
 * @param   array   $desc          description de la table
 * @param   string  $serveur       Identifiant du connecteur à utiliser
 * @param   bool    $requeter      Inutilisé
 * @return  mixed   le dernier insert_id ou FALSE en cas d'échec
 */
function spip_mysqli_insertq_multi($table, $tab_couples=array(), $desc=array(), $serveur='',$requeter=true) {

	if (!$desc) $desc = description_table($table);
	if (!$desc) $tab_couples = array();
	$fields =  isset($desc['field'])?$desc['field']:array();
	
	$cles = "(" . join(',',array_keys($tab_couples[0])) . ')';
	$valeurs = array();
	foreach ($tab_couples as $couples) {
		foreach ($couples as $champ => $val){
			$couples[$champ]= spip_mysqli_cite($val, $fields[$champ]);
		}
		$valeurs[] = '(' .join(',', $couples) . ')';
	}

	// Inserer par groupes de 100 max pour eviter un debordement de pile
	$r = false;
	do {
		$ins = array_slice($valeurs,0,100);
		$valeurs = array_slice($valeurs,100);
		$r = spip_mysqli_insert($table, $cles, join(', ', $ins), $desc, $serveur, $requeter);
	}  while (count($valeurs));

	return $r; // dans le cas d'une table auto_increment, le dernier insert_id
}

/**
 * Modifie une ligne dans une table existante.
 *
 * @link    http://doc.spip.org/@spip_mysql_update
 * @param   string  $table    La table
 * @param   array   $champs   tableau associatif champ => valeur
 * @param   string  $where    Condition que doivent respecter les lignes modifiées
 * @param   string  $desc     Inutilisé
 * @param   string  $serveur  Identifiant du connecteur à utiliser
 * @param   bool    $requeter
 * @return  mixed
 */
function spip_mysqli_update($table, $champs, $where='', $desc='', $serveur='',$requeter=true) {
	$set = array();
	foreach ($champs as $champ => $val)
		$set[] = $champ . "=$val";
	if (!empty($set))
		return spip_mysqli_query(
			  calculer_mysqli_expression('UPDATE', $table, ',')
			. calculer_mysqli_expression('SET', $set, ',')
			. calculer_mysqli_expression('WHERE', $where), 
			$serveur, $requeter);
}

/**
 * Modifie une ligne dans une table existante.
 *
 * les valeurs sont des constantes a mettre entre apostrophes
 * sauf les expressions de date lorsqu'il s'agit de fonctions SQL (NOW etc)
 *
 * @link    http://doc.spip.org/@spip_mysql_updateq
 * @param   string          $table    La table
 * @param   array           $champs   tableau associatif champ => valeur
 * @param   array|string    $where    Condition que doivent respecter les lignes modifiées
 * @param   array           $desc     description des champs de la table
 * @param   string          $serveur  Identifiant du connecteur à utiliser
 * @param   bool            $requeter
 * @return  mixed
 */
function spip_mysqli_updateq($table, $champs, $where='', $desc=array(), $serveur='',$requeter=true) {

	if (!$champs) return;
	if (!$desc) $desc = description_table($table);
	if (!$desc) $champs = array(); else $fields =  $desc['field'];
	$set = array();
	foreach ($champs as $champ => $val) {
		$set[] = $champ . '=' . spip_mysqli_cite($val, $fields[$champ]);
	}
	return spip_mysqli_query(
			  calculer_mysqli_expression('UPDATE', $table, ',')
			. calculer_mysqli_expression('SET', $set, ',')
			. calculer_mysqli_expression('WHERE', $where),
			$serveur, $requeter);
}

/**
 * Supprime une ligne d'une table existente.
 *
 * <b>Exemple</b> :<br>
 * Supprimer la liaison entre des rubriques et un mot donné :
 * <code>
 * sql_delete("spip_mots_rubriques", "id_mot=$id_mot");
 * </code>
 * @link    http://doc.spip.org/@spip_mysql_delete
 * @param   string         $table      La table
 * @param   array|string   $where      Condition que doivent respecter les lignes supprimées
 * @param   string         $serveur    Identifiant du connecteur à utiliser
 * @param   bool           $requeter   Inutilisé
 * @return  mixed          Le nombre de lignes supprimées, FALSE en cas d'échec
 */
function spip_mysqli_delete($table, $where='', $serveur='',$requeter=true) {
	$res = spip_mysqli_query(
			  calculer_mysqli_expression('DELETE FROM', $table, ',')
			. calculer_mysqli_expression('WHERE', $where),
			$serveur, $requeter);
	if ($res){
		$link = $GLOBALS['connexions'][$serveur ? $serveur : 0]['link'];
		return $link->affected_rows;
	}
	else
		return false;
}

/**
 * insère une ligne dans une table existante.
 *
 * Si une vieille ligne a la même valeur pour un index UNIQUE ou une clef primaire,
 * la vieille ligne sera remplacée par la nouvelle.
 * Le nombre de lignes affectées sera alors de 2
 *
 * @link    http://doc.spip.org/@spip_mysql_replace
 * @param   string  $table     La table
 * @param   array   $couples   tableau associatif champ => valeur
 * @param   array   $desc      Inutilisé
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter
 * @return  bool    FALSE en cas d'échec
 */
function spip_mysqli_replace($table, $couples, $desc=array(), $serveur='',$requeter=true) {
	return spip_mysqli_query("REPLACE $table (" . join(',',array_keys($couples)) . ') VALUES (' .join(',',array_map('_q', $couples)) . ')', $serveur, $requeter);
}


/**
 * Remplace plusieures lignes d'une table existente
 *
 * @link    http://doc.spip.org/@spip_mysql_replace_multi
 * @param   string  $table         La table
 * @param   array   $tab_couples   tableau associatif champ => valeur
 * @param   array   $desc          Inutilisé
 * @param   string  $serveur       Identifiant du connecteur à utiliser
 * @param   bool    $requeter
 * @return  bool
 */
function spip_mysqli_replace_multi($table, $tab_couples, $desc=array(), $serveur='',$requeter=true) {
	$cles = "(" . join(',',array_keys($tab_couples[0])). ')';
	$valeurs = array();
	foreach ($tab_couples as $couples) {
		$valeurs[] = '(' .join(',',array_map('_q', $couples)) . ')';
	}
	$valeurs = implode(', ',$valeurs);
	return spip_mysqli_query("REPLACE $table $cles VALUES $valeurs", $serveur, $requeter);
}


/**
 * Construit une expression pour extraire un champ multi dans une requête.
 *
 * @link    http://doc.spip.org/@spip_mysql_multi
 * @param   string  $objet  champ MySQL contenant éventuellement un champ multi
 * @param   string  $lang   code langue qui determine le champ multi à récupérer
 * @return  string
 */
function spip_mysqli_multi ($objet, $lang) {
	$lengthlang = strlen("[$lang]");
	$posmulti = "INSTR(".$objet.", '<multi>')";
	$posfinmulti = "INSTR(".$objet.", '</multi>')";
	$debutchaine = "LEFT(".$objet.", $posmulti-1)";
	$finchaine = "RIGHT(".$objet.", CHAR_LENGTH(".$objet.") -(7+$posfinmulti))";
	$chainemulti = "TRIM(SUBSTRING(".$objet.", $posmulti+7, $posfinmulti -(7+$posmulti)))";
	$poslang = "INSTR($chainemulti,'[".$lang."]')";
	$poslang = "IF($poslang=0,INSTR($chainemulti,']')+1,$poslang+$lengthlang)";
	$chainelang = "TRIM(SUBSTRING(".$objet.", $posmulti+7+$poslang-1,$posfinmulti -($posmulti+7+$poslang-1) ))";
	$posfinlang = "INSTR(".$chainelang.", '[')";
	$chainelang = "IF($posfinlang>0,LEFT($chainelang,$posfinlang-1),$chainelang)";
	//$chainelang = "LEFT($chainelang,$posfinlang-1)";
	$retour = "(TRIM(IF($posmulti = 0 , ".
		"     TRIM(".$objet."), ".
		"     CONCAT( ".
		"          $debutchaine, ".
		"          IF( ".
		"               $poslang = 0, ".
		"                     $chainemulti, ".
		"               $chainelang".
		"          ), ". 
		"          $finchaine".
		"     ) ".
		"))) AS multi";

	return $retour;
}

/**
 * Contruire une chaîne permettant d'utiliser un code hexadécimal dans une requête.
 *
 * @link    http://doc.spip.org/@spip_mysql_hex
 * @param   int    $v
 * @return  string
 */
function spip_mysqli_hex($v)
{
	return "0x" . $v;
}

/**
 * Echappe une chaine pour la rendre utilisable par MySQL.
 *
 * @param   mixed   $v      valeur à traiter
 * @param   string  $type   type
 * @return  mixed
 */
function spip_mysqli_quote($v, $type='')
{
	return ($type === 'int' AND !$v) ? '0' :  spip_mysqli_q($v);
}

/**
 * Préparer un élément pour qu'il puisse être utilisé dans les requêtes plus tard.
 *
 * @link http://doc.spip.org/@_q
 * @internal Le traitement se fait de manière récursive sur les tableaux
 * @param   mixed   $a
 * @return  mixed
 */
function spip_mysqli_q($a) {
	$link = &$GLOBALS['connexions'][0]['link'];
	return (is_numeric($a)) ? strval($a) :
		(!is_array($a) ? ("'" . $link->real_escape_string($a) . "'")
		 : join(",", array_map('spip_mysqli_q', $a)));
}


/**
 * Calcul de date.
 *
 * @param   string   $champ
 * @param   int      $interval
 * @param   string   $unite
 * @return  string
 */
function spip_mysqli_date_proche($champ, $interval, $unite)
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

/**
 * IN (...) est limité à 255 elements, d'où cette fonction assistante.
 *
 * @link    http://doc.spip.org/@spip_mysql_in
 * @param   string  $val
 * @param   string  $valeurs
 * @param   string  $not
 * @param   string  $serveur   Identifiant du connecteur à utiliser
 * @param   bool    $requeter  Inutilisé
 * @return  string
 */
function spip_mysqli_in($val, $valeurs, $not='', $serveur='',$requeter=true) {
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

/**
 * Echappe une chaine pour l'utiliser dans une requête MySQL.
 *
 * @link    http://doc.spip.org/@spip_mysql_cite
 * @param   string  $v	    la valeur à traiter
 * @param   string  $type   son type
 * @return  string  la chaine préparée
 */
function spip_mysqli_cite($v, $type) {
	$link = &$GLOBALS['connexions'][0]['link'];
	if (sql_test_date($type) AND preg_match('/^\w+\(/', $v)
	OR (sql_test_int($type)
		 AND (is_numeric($v)
		      OR (ctype_xdigit(substr($v,2))
			  AND $v[0]=='0' AND $v[1]=='x'))))
		return $v;
	else return  ("'" . $link->real_escape_string($v) . "'");
}


?>