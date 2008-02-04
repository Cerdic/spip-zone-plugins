<?php
$nouvelleAdresse='spip.php?page=rubrique'; 
if (isset($_SERVER['QUERY_STRING'])&&($_SERVER['QUERY_STRING']!=''))
 	$nouvelleAdresse.='&'.substr($_SERVER['QUERY_STRING'],0,2048); //Récupère les paramètres
header('Location: '.$nouvelleAdresse); //Redirection HTTP
exit;
?>