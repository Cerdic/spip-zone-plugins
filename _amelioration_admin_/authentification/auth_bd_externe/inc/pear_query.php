<?
// Inclusion de la librairie Pear DB
require_once('DB.php');

function bd_externe_connect($afficherErreur=TRUE) {
	global $bd_externe;

	$dsn =$bd_externe['serveur']."://".$bd_externe['login'].":".$bd_externe['password']."@".$bd_externe['hostname']."/".$bd_externe['database'];
	$bd_externe_link = @DB::connect($dsn);	
	
	if (DB::isError($bd_externe_link)) {
		if ($afficherErreur) die("<font face='Verdana,Arial,Sans,sans-serif' size='4' color='#970038'><B>Probl&egrave;me de connexion BD externe</b></font><p>Attention : un probl&egrave;me (Base de donn&eacute;es authentification externe) emp&ecirc;che l'acc&egrave;s &agrave; cette partie du site. Merci de votre compr&eacute;hension.<p><tt>".$bd_externe_link->getMessage())."</tt></p>";			
		return FALSE;
	}
	
	$GLOBALS['bd_externe_link']=$bd_externe_link;
	
	return TRUE;
}

function bd_externe_query($query) {
	global $bd_externe_link;
	
	$result = @$bd_externe_link->query($query);
	return $result;
}

function bd_externe_fetch($result) {
	global $bd_externe_link;
	
	return ($result->fetchRow(DB_FETCHMODE_ASSOC));
}

function bd_externe_show_tables() {
	global $bd_externe_link;
		
	$info = @$bd_externe_link->getListOf('tables');
	
	$tables=array();
	foreach($info as $val) $tables[$val]=$val;
	return($tables);
}

function bd_externe_show_columns($table) {
	global $bd_externe_link;

	$info = @$bd_externe_link->tableInfo($table);

	$columns=array();
	foreach($info as $val) $columns[$val['name']]=$val['name'];
	
	return($columns);
}
?>