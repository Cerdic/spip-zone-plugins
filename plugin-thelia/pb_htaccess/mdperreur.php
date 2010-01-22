<?php
$nouvelleAdresse='spip.php?page=mdperreur'; 
if (isset($_SERVER['QUERY_STRING'])&&($_SERVER['QUERY_STRING']!=''))
 	$nouvelleAdresse.='&'.substr($_SERVER['QUERY_STRING'],0,2048); //R�cup�re les param�tres
header('Location: '.$nouvelleAdresse); //Redirection HTTP
exit;
?>