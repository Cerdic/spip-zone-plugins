<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
    function archive_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	//recupére les informations de plugin.xml
	$infos = plugin_get_infos('archive');
	$version_base = $infos['version_base'];    
    spip_log("upgrade","archive");
    spip_log("meta base version".$GLOBALS['meta'][$nom_meta_base_version],'archive');
    spip_log("version cible".$version_cible,'archive');
    spip_log("nom meta".$nom_meta_base_version,'archive');    

    //Changement de version ou premiere installation    
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			OR (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		spip_log("maj","archive");
		include_spip('base/archive');
		include_spip('base/create');
		maj_tables(array('spip_articles','spip_rubriques'));

		ecrire_meta($nom_meta_base_version,$current_version=$version_base,'non');
	}
	
	ecrire_metas();
}

function archive_vider_tables($nom_meta_base_version) {
    sql_alter('TABLE spip_articles DROP archive');
    sql_alter('TABLE spip_articles DROP archive_date');
    sql_alter('TABLE spip_rubriques DROP archive');
    sql_alter('TABLE spip_rubriques DROP archive_date');        
	effacer_meta($nom_meta_base_version);
}

/*
//configure la base spip et les metas
function archive_install($action){
	//version en cours
	//recupére les informations de plugin.xml
	$infos = plugin_get_infos('archive');
	$archive_version = $infos['version'];
	
    switch ($action){
        case 'test':
           //Contrôle du plugin à chaque chargement de la page d'administration
           // doit retourner true si le plugin est proprement installé et à jour, false sinon
           if ((!isset($GLOBALS['meta']['archive_version'])) || version_compare($GLOBALS['meta']['archive_version'],$archive_version,"<")) {
	           //lance la mise à jour
	            //on sauve en meta la version spip
	            ecrire_meta('archive_version',$archive_version);
           } else {
	           //on est à jour
	           return true;
           }
        break;
        case 'install':
           //Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
           //quand le plugin est activé et test retourne false
           //return archive_installer($archive_version);
        break;
        case 'uninstall':
           //Appel de la fonction de suppression
           //quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
           //return archive_uninstaller();
        break;
    }
}
*/
?>
