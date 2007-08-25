<?php
function FpipR_fill_flickr_interestingness_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_interestingness_getList($arguments['date'],
										   $arguments['extras'],
										   $arguments['per_page'],
										   $arguments['page'],$arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos);
  return $photos->total;
}
?>
