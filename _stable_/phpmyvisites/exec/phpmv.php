<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: index.php,v 1.49 2005/12/21 00:54:18 matthieu_ Exp $

/**
 * index de l'application
 *
 *
 * @author  xavier Lembo <xav@elix-dev.com>
 * @since Thu Sep 01 21:03:50 CEST 2005
 * @version $Id: index.php,v 1.49 2005/12/21 00:54:18 matthieu_ Exp $
 * @package phpmyvisites_v2
 * 
 */

if (!defined('_DIR_PLUGIN_PHPMV')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_PHPMV',(_DIR_PLUGINS.end($p)).'/');
}

function verif_install(){
	// gestion de l'install et repertoires, selon les versions
	if (defined('_DIR_TMP')){
		define('_PHPMV_DIR_DATA',realpath(_DIR_TMP . "phpmvdatas"));
		if (!is_dir(_DIR_TMP."phpmvdatas"))
			sous_repertoire(_DIR_TMP, "phpmvdatas");
	}
	else {
		define('_PHPMV_DIR_DATA',realpath(_DIR_SESSIONS . "phpmvdatas"));
		if (!is_dir(_DIR_SESSIONS."phpmvdatas"))
			sous_repertoire(_DIR_SESSIONS, "phpmvdatas");
	}
	if (defined('_DIR_ETC')){
		if (!is_dir(_DIR_ETC."phpmvconfig"))
			sous_repertoire(_DIR_ETC, "phpmvconfig");
		if (is_dir(_DIR_ETC."phpmvconfig"))
			define('_PHPMV_DIR_CONFIG',realpath(_DIR_ETC . "phpmvconfig"));
	}
	if (!defined('_PHPMV_DIR_CONFIG')){
		define('_PHPMV_DIR_CONFIG',realpath(_DIR_SESSIONS . "phpmvconfig"));
		if (!is_dir(_DIR_SESSIONS."phpmvconfig"))
			sous_repertoire(_DIR_SESSIONS, "phpmvconfig");
	}

	if (@file_exists(_PHPMV_DIR_CONFIG."config.php"))
		return;

	if (!defined('_FILE_CONNECT')){
		define('_FILE_CONNECT',
		  (@is_readable($f = _DIR_RESTREINT . 'inc_connect.php') ? $f
		:	(@is_readable($f = _DIR_RESTREINT . 'inc_connect.php3') ? $f
		:	false)));
	}
	if (lire_fichier(_FILE_CONNECT,$connect) && preg_match(',spip_connect_db\(([^\)]*)\),i',$connect,$r)){
		$pars = explode(',',$r[1]);
		$host = substr($pars[0],1,strlen($pars[0])-2);
		$port = substr($pars[1],1,strlen($pars[1])-2);
		$login = substr($pars[2],1,strlen($pars[2])-2);
		$pass = substr($pars[3],1,strlen($pars[3])-2);
		$db = substr($pars[4],1,strlen($pars[4])-2);
		$url = url_de_base()._DIR_PLUGIN_PHPMV;
		
		define('DB_LOGIN',$login);
		define('DB_PASSWORD',$pass);
		define('DB_HOST',$host);
		define('DB_NAME',$db);

		$conf = '<'.'?php 
$siteInfo = array ('."
  1 => 
  array (
    'idsite' => '1',
    'name' => '".$GLOBALS['meta']['nom_site']."',
    'logo' => 'pixel.gif',
    'params_choice' => 'all',
    'params_names' => '',
  ),
);
?".'>';
		ecrire_fichier(_PHPMV_DIR_CONFIG."/site_info.php",$conf);
		$conf = '<'.'?php 
$siteUrls = array ('."
  1 => 
  array (
    0 => '".$GLOBALS['meta']['adresse_site']."',
  ),
);
?".'>';
		ecrire_fichier(_PHPMV_DIR_CONFIG."/site_urls.php",$conf);
		
		return;

		/*define('_PHPMV_DIR_CONFIG',realpath(_DIR_SESSIONS . "phpmvconfig"));
		define('_PHPMV_DIR_DATA',realpath(_DIR_SESSIONS . "phpmvdatas"));
		chdir(_DIR_PLUGIN_PHPMV);
		require_once INCLUDE_PATH . '/core/include/PmvConfig.class.php';
		require_once INCLUDE_PATH . '/core/include/ApplicationController.php';
		require_once INCLUDE_PATH . '/core/include/Request.class.php';
		require_once INCLUDE_PATH . '/core/include/Module.class.php';
		require_once INCLUDE_PATH . '/core/include/global.php';
		require_once INCLUDE_PATH . '/core/include/Lang.class.php';
		require_once INCLUDE_PATH . '/core/include/User.class.php';
		
		$configPhpFileContent = array(
			'db_login' => $login,
			'db_password' => $pass,
			'db_host' => $host,
			'db_name' => $db,
			'db_tables_prefix' => 'phpmv_',
		);
		
		$db =& Db::getInstance();
		
		// try to connect with new values
		$db->host = $configPhpFileContent['db_host'];
		$db->login = $configPhpFileContent['db_login'];
		$db->password = $configPhpFileContent['db_password'];
		$db->name = $configPhpFileContent['db_name'];
		$db->init();
		
		if($db->isReady())
		{
			$c =& PmvConfig::getInstance();
			$c->update( $configPhpFileContent );
			
			$c->write();
			$c->defineAsConstant( $c->content );
			$c->defineTables();			
			$db->createAllTables();
		}*/
					
		//$db->connect();
		//$db->createAllTables();
	}
	
}


function exec_phpmv(){
	global $connect_statut;
	include_spip('inc/presentation');

	if (_request('mod')!='view_graph'){
		if ($connect_statut != '0minirezo') {
			debut_page(_L("PHPMyVisites"), "statistiques", "phpmv");
			echo "<strong>"._T('avis_acces_interdit')."</strong>";
			fin_page();
			exit;
		}
		else{
			// TODO : si pas les tampons dispo, afficher l'en tete direct
			// les redirect de phpmv s'afficheront avec un lien a cliquer
			$GLOBALS['spip_debut_page']="";
			ob_start();
			debut_page(_L("PHPMyVisites"), "statistiques", "phpmv");
			$GLOBALS['spip_debut_page']=ob_get_contents();
			ob_end_clean();
		}
	}
	else if ($connect_statut != '0minirezo') {
		exit;
	}
	
	define('INCLUDE_PATH', '.');
	
	verif_install();
	
	if (!isset($GLOBALS['meta']['PHPMyVisites_no_admin_stat'])){
		ecrire_meta('PHPMyVisites_no_admin_stat','non');
		ecrire_metas();
	}
	$PHPMyVisites_no_admin_stat = $GLOBALS['meta']['PHPMyVisites_no_admin_stat'];
	
	$cwd = getcwd();
	chdir(_DIR_PLUGIN_PHPMV);
	
	define('PROFILING', false );
	@set_time_limit(0);
	@error_reporting(E_ALL);
	
	if(PROFILING)
		xdebug_start_profiling();
		
	require_once INCLUDE_PATH . '/core/include/PmvConfig.class.php';
	require_once INCLUDE_PATH . '/core/include/ApplicationController.php';
	
	ApplicationController::init();
	
	if(	Request::moduleIsNotAStrangeModule() )
		printTime('EOF', true);
	
	if(PROFILING)
		xdebug_dump_function_profile(1);

	chdir($cwd);
	fin_page();
	if ($PHPMyVisites_no_admin_stat != $GLOBALS['meta']['PHPMyVisites_no_admin_stat']){
		ecrire_meta('PHPMyVisites_no_admin_stat',$GLOBALS['meta']['PHPMyVisites_no_admin_stat']);
		ecrire_metas();
	}
	
}

?>
