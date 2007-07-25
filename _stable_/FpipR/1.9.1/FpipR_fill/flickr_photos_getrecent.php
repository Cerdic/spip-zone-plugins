<?php
function FpipR_fill_flickr_photos_getrecent_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photos_getRecent(
								 $arguments['per_page'],$arguments['page'],
								 $arguments['extras'],
								 $arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos);
  return $photos->total;
}
?>
