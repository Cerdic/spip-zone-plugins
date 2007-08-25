<?php
function boucle_FLICKR_PHOTOS_GETCONTACTSPUBLICPHOTOS_dist($id_boucle, &$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('nsid','just_friends','single_photo','include_self');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = array_merge(FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle),
						   FpipR_utils_search_extra($boucle,$id_table,$possible_extras));

  if($boucle->limit) {
	list($debut,$pas) = split(',',$boucle->limit);
	$arguments['count'] = $pas;
  } else if($boucle->total_parties) {
	$arguments['count'] = $boucle->total_parties;
  }
  
  $null = null;
  $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.getContactsPublicPhotos',$arguments,$null);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
