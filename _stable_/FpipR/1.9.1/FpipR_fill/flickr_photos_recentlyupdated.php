<?php
function FpipR_fill_flickr_photos_recentlyupdated_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photos_recentlyUpdated( $arguments['per_page'], $arguments['page'],
										   $arguments['min_date'],
										   $arguments['extras'],
										   $arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos);
  return $photos->total;
}
?>
