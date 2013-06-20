<?php

/* appeler adminer depuis spip */

$GLOBALS['adminer_racine'] = "";
if (!defined('_ECRIRE_INC_VERSION')){

	while (!file_exists("ecrire/inc_version.php")){
		$GLOBALS['adminer_racine'] .= "../";
		chdir ('..');
	}

	require_once 'ecrire/inc_version.php';

}

include_spip('inc/autoriser');
if (!autoriser('webmestre')) die('acces reserve aux webmestres');

if (!is_array($p = @unserialize($GLOBALS['meta']['plugin']))
OR !isset($p['ADMINER']))
	die('Plugin Adminer pas actif');

include_spip("inc/cookie");
spip_setcookie("spip_adminer","on");

// desactiver le logout et remplacer par un retour a l'admin spip
if (isset($_POST['logout'])){

	spip_setcookie("spip_adminer","",0);
	session_name("adminer_sid");
	session_start();
	session_destroy();

	unset($_POST['logout']);
	$redir = $GLOBALS['meta']['adresse_site'].'/ecrire';
	include_spip("inc/headers");
	redirige_par_entete($redir);
}

if (!isset($_COOKIE['adminer_sid'])
	OR (!_request('username') AND !_request('file'))) {

	// forcer un login sur les valeurs du site
	function adminer_connect_db($host, $port, $login, $pass, $db='', $type='mysql', $prefixe='', $auth='') {
		$drivers = array('mysql' => 'server', 'sqlite3' => 'sqlite', 'sqlite2' => 'sqlite2');
		if($type !== 'mysql') {
			if (defined('_ROOT_RACINE') AND defined('_DIR_DB'))
				$dir_base = _ROOT_RACINE . _DIR_DB;
			else
				$dir_base = $GLOBALS['adminer_racine'] . _NOM_PERMANENTS_INACCESSIBLES . 'bases/';
			$db = $dir_base . $db . '.sqlite';
		}
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

chdir (_DIR_PLUGIN_ADMINER);
require_once 'adminer.php';
