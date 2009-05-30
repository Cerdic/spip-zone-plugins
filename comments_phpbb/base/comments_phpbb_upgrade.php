<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');

function comments_phpbb_upgrade($nom_meta_base_version,$version_cible){
include_spip('base/create');
// installation des tables du plugin et mises à jour
        $current_version = 0.0;
        if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
                        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
                if ($current_version==0.0){
			include_spip('base/comments_phpbb');
			creer_base();
                        ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
                } 
                
                ecrire_metas();
        } 
}



function comments_phpbb_vider_tables($nom_meta_base_version) {
     sql_drop_table(''.ARTICLES_PHPBB_TABLE.'');
     effacer_meta($nom_meta_base_version);
}
?>