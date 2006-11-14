<?php
function FpipR_fill_flickr_groups_pools_getphotos_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_groups_pools_getPhotos($arguments['id_group'],
										   $arguments['tags'],
										   $arguments['user_id'],
										   $arguments['extras'],
										   $arguments['per_page'],
										   $arguments['page'],$arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos,array('id_group'=>$arguments['id_group']));
  return $photos->total;
}

?>
