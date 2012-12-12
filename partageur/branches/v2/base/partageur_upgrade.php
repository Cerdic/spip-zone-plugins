<?php
$GLOBALS['partageur_base_version'] = 0.1;
    
function partageur_upgrade(){

		$version_base = $GLOBALS['partageur_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['partageur_base_version']) )
				|| (($current_version = $GLOBALS['meta']['partageur_base_version'])!=$version_base)){
			include_spip('base/partageur');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();				
        echo _T('partageur:install_table'); 
			  ecrire_meta('partageur_base_version',$current_version=$version_base,'non');	
        
        // commun à spip2spip
        sql_alter("TABLE spip_articles ADD s2s_url VARCHAR(255) DEFAULT '' NOT NULL");
				sql_alter("TABLE spip_articles ADD s2s_url_trad VARCHAR(255) DEFAULT '' NOT NULL");			
			}
			
			/* exemple upd.
			if (version_compare($current_version,"0.3","<")){
			   ....
		  }
		  */
		
			ecrire_metas();
		}
}


	
function partageur_install($action){
		$version_base = $GLOBALS['partageur_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['partageur_base_version']) AND ($GLOBALS['meta']['partageur_base_version']>=$version_base));
				break;
			case 'install':
				partageur_upgrade();
				break;
			case 'uninstall':
				partageur_vider_tables();								
				break;
		}
}

/**
 * Desinstallation du plugin
 *
 * @param string $nom_meta_base_version
 */
	
function partageur_vider_tables() {
		spip_query("DROP TABLE spip_partageurs");
		sql_alter("TABLE spip_articles DROP COLUMN s2s_url");
		sql_alter("TABLE spip_articles DROP COLUMN s2s_url_trad");		
		effacer_meta('partageur_base_version');
		ecrire_metas();
}
?>