<?php
function boucle_FLICKR_URLS_LOOKUPGROUP_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_groups";

  $possible_criteres = array('url');

  $arguments = FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle);

  $null = null;
  $boucle->hash = FpipR_utils_calculer_hash('flickr.urls.lookupGroup',$arguments, $null);
  return calculer_boucle($id_boucle, $boucles); 
}
?>
