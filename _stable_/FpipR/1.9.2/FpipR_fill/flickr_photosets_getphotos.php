<?php

function FpipR_fill_flickr_photosets_getphotos_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photosets_getPhotos($arguments['id_photoset'],
									   $arguments['extras'],
									   $arguments['per_page'],
									   $arguments['page'],
									   $arguments['privacy_filter'],$arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos,array(
										'id_photoset' => $arguments['id_photoset']
										));
  return $photos->total;
}
?>
