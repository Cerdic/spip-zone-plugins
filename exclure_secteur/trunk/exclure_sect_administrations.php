<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function exclure_sect_upgrade($nom_meta_version_base, $version_cible){
   include_spip('base/upgrade');
   $maj=array();
   $maj['create'] = array(array('exclure_sect_conf'));


    maj_plugin($nom_meta_version_base, $version_cible, $maj);
  }

function exclure_sect_conf(){
	include_spip('inc/config');
	if (!lire_config('secteur/exclure_sect')){
		ecrire_config('secteur/exclure_sect',array());
	}
}
function exclure_sect_vider_tables($nom_meta_version_base){
	if (lire_config('secteur')){
		effacer_config('secteur');
    }
    effacer_meta($nom_meta_version_base);
	return true;
}


?>
