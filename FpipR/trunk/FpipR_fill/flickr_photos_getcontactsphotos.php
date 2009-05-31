<?php
function FpipR_fill_flickr_photos_getcontactsphotos_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photos_getContactsPhotos(
										   $arguments['count'],
										   $arguments['just_friends'],
										   $arguments['single_photo'],
										   $arguments['include_self'],
										   $arguments['extras'],$arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos);
}
?>
