<?php
function boucle_FLICKR_INTERESTINGNESS_GETLIST_dist($id_boucle, &$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('date');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = FpipR_utils_search_extras($boucle,$id_table,$possible_extras);
  $arguments = array_merge($arguments,FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle));
  $boucle->hash = FpipR_utils_calculer_hash('flickr.interestingness.getList',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
