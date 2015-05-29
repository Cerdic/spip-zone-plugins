<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['rssarticle_base_version'] = 0.3;

function rssarticle_upgrade(){
	$version_base = $GLOBALS['rssarticle_base_version'];
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta']['rssarticle_base_version']) )
			|| (($current_version = $GLOBALS['meta']['rssarticle_base_version'])!=$version_base)){
		include_spip('base/rssarticle');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();	
			maj_tables('spip_syndic');
			echo "<p>"._T('rssarticle:install_rssarticle')."</p>"; 
			ecrire_meta('rssarticle_base_version',$current_version=$version_base,'non');
		}
		
		if (version_compare($current_version,"0.3","<")){
			include_spip('base/create');
			maj_tables('spip_syndic');
			echo "<p>"._T('rssarticle:mise_a_jour_v03')."</p>"; 
			ecrire_meta('rssarticle_base_version',$current_version="0.3",'non');
		}
		ecrire_metas();
	}
}


	
function rssarticle_install($action){
		$version_base = $GLOBALS['rssarticle_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['rssarticle_base_version']) AND ($GLOBALS['meta']['rssarticle_base_version']>=$version_base));
				break;
			case 'install':
				rssarticle_upgrade();
				break;
			case 'uninstall':
				rssarticle_vider_tables();
				break;
		}
}

/**
 * Desinstallation du plugin
 *
 * @param string $nom_meta_base_version
 */
	
function rssarticle_vider_tables() {
	sql_alter("TABLE spip_syndic DROP rssarticle");
	spip_query("DROP TABLE spip_articles_syndic");
	effacer_meta('rssarticle_base_version');
	ecrire_metas();
}
?>
