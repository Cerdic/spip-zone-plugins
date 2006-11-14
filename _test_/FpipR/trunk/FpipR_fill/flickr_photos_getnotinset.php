<?php
function FpipR_fill_flickr_photos_getnotinset_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photos_getNotInSet(
								 $arguments['per_page'],$arguments['page'],
								 $arguments['min_upload_date'],
								 $arguments['max_upload_date'], $arguments['min_taken_date'],
								 $arguments['max_taken_date'], $arguments['privacy_filter'],
								 $arguments['extras'],
								 $arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos);
  return $photos->total;
}
?>
