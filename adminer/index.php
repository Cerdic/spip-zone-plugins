<?php

/* appeler adminer depuis spip */

chdir ('../..');
require_once 'ecrire/inc_version.php';

include_spip('inc/autoriser');
if (!autoriser('webmestre')) die('acces reserve aux webmestres');

if (!is_array($p = @unserialize($GLOBALS['meta']['plugin']))
OR !isset($p['ADMINER']))
	die('Plugin Adminer pas actif');

if (!isset($_COOKIE['adminer_sid'])) {

	// forcer un login sur les valeurs du site
	function adminer_connect_db($host, $port, $login, $pass, $db='', $type='mysql', $prefixe='', $auth='') {
		if($type !== 'mysql') die ('MySQL seulement');
		if ($port) $host.=':'.$port;
		$_POST['auth'] = array(
			"driver" => "server",
			"server"=> $host,
			"username"=> $login,
			"password"=>$pass,
			"db"=>$db
		);
	}
	lire_fichier(_FILE_CONNECT, $connect);
	$connect = str_replace('spip_connect_db(', 'adminer_connect_db(', $connect);
	eval('?'.'>'.$connect);
}

require_once 'adminer.php';

