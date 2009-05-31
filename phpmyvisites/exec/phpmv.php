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
	if (!isset($GLOBALS['meta']['_DIR_PLUGIN_PHPMV']) 
	OR $GLOBALS['meta']['_DIR_PLUGIN_PHPMV']!=_DIR_PLUGIN_PHPMV
	OR !file_exists(_PHPMV_DIR_CONFIG."config.php") ){
		include_spip("inc/phpmv_install");
		phpmv_verif_install();
	}

	@define('_PHPMV_DIR_CONFIG',$GLOBALS['meta']['phpmv_dir_config']);
	@define('_PHPMV_DIR_DATA',$GLOBALS['meta']['phpmv_dir_data']);
	
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
