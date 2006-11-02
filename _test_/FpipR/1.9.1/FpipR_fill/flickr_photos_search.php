<?php
function FpipR_fill_flickr_photos_search_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photos_search(
								 $arguments['per_page'],$arguments['page'],
								 $arguments['user_id'], $arguments['tags'], $arguments['tag_mode'],
								 $arguments['text'], $arguments['min_upload_date'],
								 $arguments['max_upload_date'], $arguments['min_taken_date'],
								 $arguments['max_taken_date'], $arguments['license'],
								 $arguments['sort'], $arguments['privacy_filter'],
								 $arguments['extras'],
								 $arguments['bbox'],$arguments['accuracy'],
								 $arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos);
}
?>
