<?php
function boucle_FLICKR_PHOTOSETS_GETLIST_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photosets";

  $possible_args = array('user_id');

  $arguments = FpipR_utils_search_args($boucle,$id_table,$possible_args);

  $null = null;
  $boucle->hash = FpipR_utils_calculer_hash('flickr.photosets.getList',$arguments,$null);
  return calculer_boucle($id_boucle, $boucles); 
  }

?>
