<?php
$GLOBALS['rssarticle_base_version'] = 0.2;
    
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
        echo "<p>"._T('rssarticle:install_rssarticle')."</p>"; 
			  ecrire_meta('rssarticle_base_version',$current_version=$version_base,'non');
				
			}
			ecrire_metas();
		}
}
	
function rssarticle_vider_tables() {
		spip_query("DROP TABLE spip_articles_syndic");
		effacer_meta('rssarticle_base_version');
		ecrire_metas();
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
?>