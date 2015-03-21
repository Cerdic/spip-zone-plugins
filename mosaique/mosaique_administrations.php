<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');
include_spip('base/mosaique');
	
function mosaique_upgrade($nom_meta_base_version,$version_cible) {

  $maj = array();
  cextras_api_upgrade(mosaique_declarer_champs_extras(), $maj['create']);	
  cextras_api_upgrade(mosaique_declarer_champs_extras(), $maj['0.0.2']);

  include_spip('base/upgrade');
  maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

function mosaique_vider_tables($nom_meta_base_version) {
  cextras_api_vider_tables(mosaique_declarer_champs_extras());
  effacer_meta($nom_meta_base_version);
}
?>