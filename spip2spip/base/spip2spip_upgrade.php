<?php
/**
 * Plugin spip2spip pour Spip 2.0
 * Licence GPL
 * 
 *
 */

$GLOBALS['spip2spip_base_version'] = 0.2;
    
function spip2spip_upgrade(){
		$version_base = $GLOBALS['spip2spip_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['spip2spip_base_version']) )
				|| (($current_version = $GLOBALS['meta']['spip2spip_base_version'])!=$version_base)){
			include_spip('base/spip2spip');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();	
        echo _T('spiptospip:install_spip2spip')."<br />";        		
				sql_insertq("spip_groupes_mots", array(
                                         'titre' => '- spip2spip -' , 
                                         'descriptif' => _T('spiptospip:install_spip2spip_4'),
                                         'texte' =>  _T('spiptospip:install_spip2spip_5'),
                                         'unseul' => 'non',
                                         'obligatoire' => 'non',
                                         'tables_liees' => 'articles,rubriques',
                                         'minirezo' => 'oui',
                                         'comite' => 'oui' ,
                                         'forum' => 'non' ,
                                         'maj' => 'NOW()'));
        echo _T('spiptospip:install_spip2spip_groupe_mot');                                 
			  ecrire_meta('spip2spip_base_version',$current_version=0.1,'non');
				
			}
			if (version_compare($current_version,"0.2","<")){
				sql_alter("TABLE spip_articles ADD s2s_url VARCHAR(255) DEFAULT '' NOT NULL");
				sql_alter("TABLE spip_articles ADD s2s_url_trad VARCHAR(255) DEFAULT '' NOT NULL");
				ecrire_meta('spip2spip_base_version',$current_version=0.2,'non');
			}
			
			
		}
}
	
function spip2spip_vider_tables() {
		spip_query("DROP TABLE spip_spip2spip");
		sql_alter("TABLE spip_articles DROP COLUMN s2s_url");
		sql_alter("TABLE spip_articles DROP COLUMN s2s_url_trad");
		effacer_meta('spip2spip_base_version');
}

	
function spip2spip_install($action){
		$version_base = $GLOBALS['spip2spip_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spip2spip_base_version']) AND ($GLOBALS['meta']['spip2spip_base_version']>=$version_base));
				break;
			case 'install':
				spip2spip_upgrade();
				break;
			case 'uninstall':
				spip2spip_vider_tables();
				// desinstaller aussi le groupe de mots-clés tags ? 				
				break;
		}
}
?>