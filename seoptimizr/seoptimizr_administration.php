<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');
include_spip('base/seoptimizr');
	
function seoptimizr_upgrade($nom_meta_base_version,$version_cible) {
  $maj = array();
  cextras_api_upgrade(seoptimizr_declarer_champs_extras(), $maj['create']);	
  include_spip('base/upgrade');
  maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function seoptimizr_vider_tables($nom_meta_base_version) {
  cextras_api_vider_tables(srp_declarer_champs_extras());
  effacer_meta($nom_meta_base_version);
}