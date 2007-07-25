<?php

function boucle_FLICKR_PHOTOS_COMMENTS_GETLIST_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_comments";

  $arguments = FpipR_utils_search_args($boucle,$id_table,array('id_photo'));  
  $null = null;
  $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.comments.getList',$arguments,$null);
  return calculer_boucle($id_boucle, $boucles); 
  }

?>
