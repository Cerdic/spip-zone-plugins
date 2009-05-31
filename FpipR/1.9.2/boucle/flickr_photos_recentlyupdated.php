<?php
function boucle_FLICKR_PHOTOS_RECENTLYUPDATED_dist($id_boucle, &$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('min_date');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = array_merge(FpipR_utils_search_extras($boucle,$id_table,$possible_extras),
						   FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle));

  $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.recentlyUpdated',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
