<?php

function FpipR_fill_flickr_photos_comments_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  $comments = flickr_photos_comments_getList($arguments['id_photo'],$arguments['auth_token']);
  FpipR_fill_comments_table($comments);
}
?>
