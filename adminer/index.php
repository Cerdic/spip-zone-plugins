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
		$drivers = array('mysql' => 'server', 'sqlite3' => 'sqlite', 'sqlite2' => 'sqlite2');
		if($type !== 'mysql') {$db = '../../'._NOM_PERMANENTS_INACCESSIBLES . 'bases/' . $db . '.sqlite';}
		if(!isset($drivers[$type])) die ('Type de base de donn&eacute;es '.$type.' non reconnu');
		if ($port) $host.=':'.$port;
		$_POST['auth'] = array(
			"driver" => $drivers[$type],
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

chdir ('plugins/adminer/');
require_once 'adminer.php';
