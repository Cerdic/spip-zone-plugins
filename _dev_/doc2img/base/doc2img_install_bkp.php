<?php

include_spip('base/abstract_sql');	//fonctions d'acces sql
include_spip('inc/plugin');
include_spip('inc/utils');

include_spip('base/compat193');		//cr++ Ë la vol+ les fonctions sql pour 192


//configure la base spip et les metas
function doc2img_install($action){
	//version en cours
	//recupÚre les informations de plugin.xml
	$infos = plugin_get_infos('doc2img');
	$doc2img_version = $infos['version'];
	//Determine la version installÚe
	$infos_meta = unserialize($GLOBALS['meta']['plugin']);
    $doc2img_version_meta = $infos_meta['DOC2IMG']['version'];
    //sauve l'information
    spip_log('version_xml : '.$doc2img_version,'doc2img');
    spip_log('version_meta : '.$doc2img_version_meta,'doc2img');

    //liste les plugins actifs
    $plugins_actifs = unserialize($GLOBALS['meta']['plugin_installes']);
    spip_log('action : '.$action,'doc2img');

   switch ($action){
       case 'test':
           //Contr¶le du plugin Ó chaque chargement de la page d'administration
           // doit retourner true si le plugin est proprement installÚ et Ó jour, false sinon
		   if ((!isset($doc2img_version_meta)) || version_compare($doc2img_version_meta,$doc2img_version,"<")) {
			   //lance la mise Ó jour
			   spip_log('mise Ó jour lancÚe','doc2img');
			   return doc2img_installer($doc2img_version_meta);
		   } else {
			   //on est Ó jour
			   spip_log('mise Ó jour inutile','doc2img');
			   return true;
		   }
		   return true;
       break;
       case 'install':
           //Appel de la fonction d'installation. Lors du clic sur l'ic-ne depuis le panel.
           //quand le plugin est activÚ et test retourne false
           spip_log('installation','doc2img');
		   return doc2img_installer($archive_version_meta);
       break;
       case 'uninstall':
           //Appel de la fonction de suppression
           //quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
           spip_log('suppresion','doc2img');
		   return doc2img_uninstaller();
       break;
   }
}

//configure la base spip
function doc2img_installer($version_meta) {

    switch ($version_meta) {
        case NULL :
            spip_log('crÚation de la table','doc2img');
            spip_query("CREATE TABLE spip_doc2img (
                id_doc2img BIGINT (21) AUTO_INCREMENT, 
                id_document BIGINT (21), 
                page INT, 
                PRIMARY KEY (id_doc2img)
                );");
            spip_log('table spip_doc2img crÚÚe','doc2img');
        default :
            spip_log('tuti bene','doc2img');
            break;
    }

    
    return true;
}


//supprime les donn+es de la base spip
function doc2img_uninstaller() {

	//supprime la table doc2img
	spip_query("DROP TABLE spip_doc2img");
    
    spip_log('table spip_doc2img supprimÚe','doc2img');

    return true;
}
?>
