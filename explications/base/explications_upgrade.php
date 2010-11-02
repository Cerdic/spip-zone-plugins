<?php
include_spip('inc/meta');
include_spip('base/create');

function explications_upgrade($nom_meta_base_version, $version_cible){
  $current_version = "0.0";
  if (isset($GLOBALS['meta'][$nom_meta_base_version]))
    $current_version = $GLOBALS['meta'][$nom_meta_base_version];
  if ($current_version=="0.0") {
    creer_base();
    ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
  }
  if (version_compare($current_version,"0.1","<")){
    maj_tables('spip_explications');
    ecrire_meta($nom_meta_base_version,$current_version="0.1");
  }
}

function explications_vider_tables($nom_meta_base_version) {
  sql_drop_table("spip_explications");
  effacer_meta($nom_meta_base_version);
}