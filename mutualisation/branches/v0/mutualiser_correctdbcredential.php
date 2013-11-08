<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function mutualiser_correctdbcredential() {
	define('_DOCTYPE_ECRIRE', ''); # on n'a pas lance spip_initialisation_suite() donc cette constante n'est pas definie
	include_spip('inc/minipres');

	// verif securite
	if (_request('secret')
	!= md5(
	$GLOBALS['meta']['version_installee'].'-'.$GLOBALS['meta']['popularite_total']
	)) {
		include_spip('inc/headers');
		redirige_par_entete($GLOBALS['meta']['adresse_site'].'/'._DIR_RESTREINT_ABS);
		exit;
	}

	// faire l'upgrade
	lire_metas();
	echo minipres(_T('titre_page_upgrade'),
		_L('Correction des acc&egrave;s &agrave; la base')
	);
	
	$file = file_get_contents(_DIR_SITE.'/config/connect.php');
	if (strpos($file, _INSTALL_USER_DB_ROOT)) {
	
		preg_match("/.*^spip_connect_db\((.*\));$/m", $file, $conn_string);
		$var_connect = explode(',',str_replace("'",'',$conn_string[1]));
	
		define('_PRIVILEGES_MYSQL_USER_BASE','Alter, Select, Insert, Update, Delete, Create, Drop');
		$_INSTALL_NAME_DB = $var_connect[4];
		$_INSTALL_USER_DB = substr($var_connect[4],strpos($var_connect[4],'_')+1);
		$_INSTALL_PASS_DB =
			substr(md5(
				_INSTALL_PASS_DB_ROOT   # secret du site
				. $_SERVER['HTTP_HOST'] # un truc variable, mais reutilisable site par site
				. $_INSTALL_USER_DB # un autre truc variable
			), 0, 8);
		$_INSTALL_HOST_DB_LOCALNAME = $var_connect[0];
		$_INSTALL_SERVER_DB = _INSTALL_SERVER_DB;
				
		include_spip('base/abstract_sql');
		include_once(dirname(__FILE__).'/base/abstract_mutu.php');
		
		$link = mutu_connect_db(_INSTALL_HOST_DB, 0,  _INSTALL_USER_DB_ROOT, _INSTALL_PASS_DB_ROOT, '', $_INSTALL_SERVER_DB);
		
		$req = $err = array();
		// creer user
		$req[] = "CREATE user '" . $_INSTALL_USER_DB. "'@'" . $_INSTALL_HOST_DB_LOCALNAME . "' IDENTIFIED BY '" . $_INSTALL_PASS_DB . "'";
		$err[] = "CREATE user '" . $_INSTALL_USER_DB. "'@'" . $_INSTALL_HOST_DB_LOCALNAME . "' IDENTIFIED BY 'xxx'";
		// affecter a sa base
		$req[] = "GRANT " . _PRIVILEGES_MYSQL_USER_BASE . " ON "
			. $_INSTALL_NAME_DB.".* TO '"
			. $_INSTALL_USER_DB."'@'"._INSTALL_HOST_DB_LOCALNAME
			. "' IDENTIFIED BY '" . $_INSTALL_PASS_DB . "'";
		$err[] = "GRANT " . _PRIVILEGES_MYSQL_USER_BASE . " ON "
			. $_INSTALL_NAME_DB.".* TO '"
			. $_INSTALL_USER_DB."'@'"._INSTALL_HOST_DB_LOCALNAME
			. "' IDENTIFIED BY 'xxx'";

		foreach ($req as $n=>$sql){
			sql_query($sql, $_INSTALL_SERVER_DB);
		}

		$link = mutu_connect_db(_INSTALL_HOST_DB,'',  $_INSTALL_USER_DB, $_INSTALL_PASS_DB, '', _INSTALL_SERVER_DB);
		
		$file = str_replace("'"._INSTALL_USER_DB_ROOT."','"._INSTALL_PASS_DB_ROOT."'", "'".$_INSTALL_USER_DB."','".$_INSTALL_PASS_DB."'", $file);
		
		file_put_contents(_DIR_SITE.'/config/connect.php', $file);
		
	}
	
	echo minipres(_T('titre_page_upgrade'),
		_L('Aller dans <a href="@ecrire@">ecrire/</a>',
			array('ecrire' => $GLOBALS['meta']['adresse_site'].'/'._DIR_RESTREINT_ABS))
	);
	exit;
}

?>
