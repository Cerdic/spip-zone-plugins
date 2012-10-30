<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('base/upgrade');
function moderation_upgrade($nom_meta_version_base, $version_cible){
    $maj = array();
    var_dump($maj);
    $maj['create'] = array(array("moderation_create"));
    maj_plugin($nom_meta_version_base, $version_cible, $maj);
}
function moderation_create(){
    ecrire_config('moderation/visit', 'on');
    ecrire_config('moderation/redac', 'on');
    ecrire_config('moderation/admin', 'on');
    return true;
}
function moderation_vider_tables($nom_meta_version_base){
      effacer_config("moderation");
      effacer_meta($nom_meta_version_base);  
}
?>