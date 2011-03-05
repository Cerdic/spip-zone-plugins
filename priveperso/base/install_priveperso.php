<?php
    
$GLOBALS['priveperso_version'] = 0.1;    
    

	
function priveperso_install($action){

		switch ($action){

                case 'test':
								return (isset($GLOBALS['meta']['priveperso_version']) AND ($GLOBALS['meta']['priveperso_version']>=$GLOBALS['priveperso_version']));
								priveperso_mettre_a_jour();
                        break;
        
// Lancement de l'installation          
                case 'install':
                        priveperso_installer();
                        break;
        
// Lancement de la désinstallation              
                case 'uninstall':
                        priveperso_desinstaller();
                        break;
        }

}


function priveperso_mettre_a_jour(){

				include_spip('base/pipelines_priveperso');
				include_spip('base/create');
				include_spip('base/abstract_sql');

				priveperso_init_tables_principales($tables_principales);				
				
				creer_base();
				maj_tables('spip_priveperso');
				
		ecrire_meta('priveperso_version', $GLOBALS['priveperso_version']);
      ecrire_metas();

}


function priveperso_installer(){

				include_spip('base/pipelines_priveperso');
				include_spip('base/create');
				include_spip('base/abstract_sql');

				priveperso_init_tables_principales($tables_principales);				
				
				creer_base();
				maj_tables('spip_priveperso');
				maj_tables('spip_priveperso_texte');
				
		ecrire_meta('priveperso_version', $GLOBALS['priveperso_version']);
      ecrire_metas();

}

	
function priveperso_desinstaller() {

		sql_drop_table('spip_priveperso');
		sql_drop_table('spip_priveperso_texte');
		effacer_meta('priveperso_version');
		ecrire_metas();
}

?>