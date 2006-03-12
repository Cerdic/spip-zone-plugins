<?
function bd_externe_connect() {
	global $bd_externe;

	$message_erreur="<font face='Verdana,Arial,Sans,sans-serif' size='4' color='#970038'><B>Problème de connexion BD</b></font><p>Attention&nbsp;: un probl&egrave;me (Base de données authentification externe) emp&ecirc;che l'acc&egrave;s &agrave; cette partie du site. Merci de votre compr&eacute;hension.";
	$bd_externe_link = @mysql_connect($bd_externe['hostname'],$bd_externe['login'],$bd_externe['password']) or die($message_erreur);
	
	@mysql_select_db($bd_externe['database']) or die($message_erreur);
	
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
	
	return (mysql_fetch_array($result));
}
?>