<?php
$GLOBALS['spip2spip_base_version'] = 0.1;
    
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
			  ecrire_meta('spip2spip_base_version',$current_version=$version_base,'non');
				
			}
			ecrire_metas();
		}
}
	
function spip2spip_vider_tables() {
		spip_query("DROP TABLE spip_spip2spip");
		effacer_meta('spip2spip_base_version');
		ecrire_metas();
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