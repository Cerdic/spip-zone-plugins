<?
function bd_externe_connect($afficherErreur=TRUE) {
	global $bd_externe;

	$message_erreur="<font face='Verdana,Arial,Sans,sans-serif' size='4' color='#970038'><B>Problème de connexion BD</b></font><p>Attention&nbsp;: un probl&egrave;me (Base de données authentification externe) emp&ecirc;che l'acc&egrave;s &agrave; cette partie du site. Merci de votre compr&eacute;hension.";
	if ((!($bd_externe_link = @mysql_connect($bd_externe['hostname'],$bd_externe['login'],$bd_externe['password']))) AND ($afficherErreur) ) die($message_erreur);
	
	if ((!@mysql_select_db($bd_externe['database'])) AND ($afficherErreur)) die($message_erreur);
	
	$GLOBALS['bd_externe_link']=$bd_externe_link;	
	if ($bd_externe_link!=NULL) return TRUE;
	else return FALSE;
}


function bd_externe_query($query) {
	global $bd_externe_link;
	
	$result = @mysql_query($query,$bd_externe_link);
	return $result;
}

function bd_externe_fetch($result) {
	global $bd_externe_link;
	
	return (mysql_fetch_array($result));
}

function bd_externe_show_tables() {
	global $bd_externe_link;
		
	$result=bd_externe_query("SHOW TABLES");
		
	$tables=array();
	while($row=bd_externe_fetch($result)) $tables[$row[0]]=$row[0];
	return($tables);
}

function bd_externe_show_columns($table) {
	global $bd_externe_link;
		
	$result=bd_externe_query("SHOW COLUMNS FROM $table");
		
	$columns=array();
	while($row=bd_externe_fetch($result)) $columns[$row[0]]=$row[0];
	return($columns);
}
?>