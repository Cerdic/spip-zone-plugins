<?php
function boucle_FLICKR_PHOTOSETS_GETPHOTOS_dist($id_boucle, &$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('privacy_filter');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = array_merge(FpipR_utils_search_args_extras($boucle,$id_table,$possible_extras,array('id_photoset')),
						   FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle));
  $boucle->hash = FpipR_utils_calculer_hash('flickr.photosets.getPhotos',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
