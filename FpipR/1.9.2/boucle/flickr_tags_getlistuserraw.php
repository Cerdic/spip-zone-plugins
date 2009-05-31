<?php

function boucle_FLICKR_TAGS_GETLISTUSERRAW_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_tags";

  $arguments =  FpipR_utils_search_criteres($boucle,array('tag'),$boucles,$id_boucle);

  $null = null;
  $boucle->hash = FpipR_utils_calculer_hash('flickr.tags.getListUserRaw',$arguments, $null);
  return calculer_boucle($id_boucle, $boucles); 
  }

?>
