<?php
function boucle_FLICKR_GROUPS_POOLS_GETGROUPS_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_groups";


  $boucle->hash = FpipR_utils_calculer_hash('flickr.groups.pools.getGroups',array(),$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
