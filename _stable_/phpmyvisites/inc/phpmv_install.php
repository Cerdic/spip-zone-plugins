<?php
	if (!defined('_DIR_PLUGIN_PHPMV')){
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
		define('_DIR_PLUGIN_PHPMV',(_DIR_PLUGINS.end($p)).'/');
	}
	include_spip('inc/meta');

	function phpmv_verif_install(){
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
		ecrire_meta('phpmv_dir_config',_PHPMV_DIR_CONFIG,'non');
		ecrire_meta('phpmv_dir_data',_PHPMV_DIR_DATA,'non');
		ecrire_meta('_PHPMV_DIR_CONFIG',realpath(_PHPMV_DIR_CONFIG));
		ecrire_meta('_PHPMV_DIR_DATA',realpath(_PHPMV_DIR_DATA));
		ecrire_meta('_DIR_PLUGIN_PHPMV',_DIR_PLUGIN_PHPMV);

		if (!isset($GLOBALS['meta']['PHPMyVisites_no_admin_stat']))
			ecrire_meta('PHPMyVisites_no_admin_stat','non');

		ecrire_metas();
		
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
		}
	}
	
	function phpmv_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				phpmv_verif_install();
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			ecrire_metas();
		}
	}
	
	function phpmv_vider_tables($nom_meta_base_version) {
		include_spip('phpmv_fonctions');
		// les tables
		$tables = array('phpmv_archives','phpmv_a_category','phpmv_a_config',
	'phpmv_a_file','phpmv_a_keyword','phpmv_a_newsletter','phpmv_a_page','phpmv_a_partner_name',
	'phpmv_a_partner_url','phpmv_a_provider','phpmv_a_resolution','phpmv_a_search_engine','phpmv_a_site',
	'phpmv_a_vars_name','phpmv_a_vars_value','phpmv_category','phpmv_groups','phpmv_ip_ignore','phpmv_link_vp',
	'phpmv_link_vpv','phpmv_newsletter','phpmv_page','phpmv_page_md5url','phpmv_page_url','phpmv_path','phpmv_query_log',
	'phpmv_site','phpmv_site_partner','phpmv_site_partner_url','phpmv_site_url','phpmv_users','phpmv_users_link_groups',
	'phpmv_vars','phpmv_version','phpmv_visit');
		foreach($tables as $table)
			spip_query("DROP TABLE $table");
		// les metas
		effacer_meta($nom_meta_base_version);
		effacer_meta('phpmv_dir_config');
		effacer_meta('phpmv_dir_data');
		effacer_meta('PHPMyVisites_no_admin_stat');
		effacer_meta('_PHPMV_DIR_CONFIG');
		effacer_meta('_PHPMV_DIR_DATA');
		effacer_meta('_DIR_PLUGIN_PHPMV');
		ecrire_metas();
		// la config
		$fichiers = preg_files(_PHPMV_DIR_CONFIG.'/',".*");
		foreach($fichiers as $f)
			@unlink($f);
		@unlink(_PHPMV_DIR_CONFIG);
	}

?>
