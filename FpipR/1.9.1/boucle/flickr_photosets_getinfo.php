<?php
function boucle_FLICKR_PHOTOSETS_GETINFO_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photosets";

  $possible_args = array('id_photoset');

  $arguments = FpipR_utils_search_args($boucle,$id_table,$possible_args);

  $boucle->hash = FpipR_utils_calculer_hash('flickr.photosets.getinfo',$arguments);
  return calculer_boucle($id_boucle, $boucles); 
  }

?>
