<?php
function boucle_FLICKR_URLS_LOOKUPUSER_dist($id_boucle, &$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_people";

  $possible_criteres = array('url');

  $arguments = FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle);

  $null = null;
  $boucle->hash = FpipR_utils_calculer_hash('flickr.urls.lookupUser',$arguments, $null);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
