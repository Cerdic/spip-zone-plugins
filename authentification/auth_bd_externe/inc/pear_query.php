<?
// Inclusion de la librairie Pear DB
require_once('DB.php');

function bd_externe_connect() {
	global $bd_externe;

	$dsn =$bd_externe['serveur']."://".$bd_externe['login'].":".$bd_externe['password']."@".$bd_externe['hostname']."/".$bd_externe['database'];
	$bd_externe_link = @DB::connect($dsn);	
	
	if (DB::isError($bd_externe_link)) echo ("<font face='Verdana,Arial,Sans,sans-serif' size='4' color='#970038'><B>Problème de connexion BD</b></font><p>Attention&nbsp;: un probl&egrave;me (Base de données authentification externe) emp&ecirc;che l'acc&egrave;s &agrave; cette partie du site. Merci de votre compr&eacute;hension.<p><tt>".$bd_externe_link->getMessage())."</tt></p>";	
	
	$GLOBALS['bd_externe_link']=$bd_externe_link;
	
	return $GLOBALS['bd_externe_link'];
}

function bd_externe_query($query) {
	global $bd_externe_link;
	
	$result = @$bd_externe_link->query($query);
	return $result;
}

function bd_externe_fetch($result) {
	
	return ($result->fetchRow(DB_FETCHMODE_ASSOC));
}

?>