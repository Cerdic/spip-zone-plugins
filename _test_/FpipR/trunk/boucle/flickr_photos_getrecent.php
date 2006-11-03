<?php
function boucle_FLICKR_PHOTOS_GETRECENT_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');
  $extras = array();

  $arguments = FpipR_utils_search_extras($boucle,$id_table,$possible_extras);
  $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.getRecent',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
