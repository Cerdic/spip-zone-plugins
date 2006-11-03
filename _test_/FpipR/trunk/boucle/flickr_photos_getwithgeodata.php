<?php
function boucle_FLICKR_PHOTOS_GETWITHGEODATA_dist($id_boucle, &$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('privacy_filter');

  $possible_args = array('upload_date','taken_date');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $possible_sort = array('date_posted','date_taken','interestingness','relevance');

  $arguments = array_merge(FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle),
						   FpipR_utils_search_order($boucle,$possible_sort),
						   FpipR_utils_search_args_extras($boucle,$id_table,$possible_args,$possible_extras));

  $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.getWithGeoData',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
