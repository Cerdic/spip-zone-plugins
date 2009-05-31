<?php
function FpipR_fill_flickr_tags_getlistphoto_dist($arguments) {
  include_spip('inc/flickr_api');
  $tags = flickr_tags_getListPhoto($arguments['id_photo'],$arguments['auth_token']);
  foreach($tags as $tag) {
	sql_insert('spip_fpipr_tags',
						 '(id_tag,author,raw,safe,id_photo)',
						 '('._q($tag->id).','._q($tag->author).','._q($tag->raw).','._q($tag->safe).','._q($arguments['id_photo']).')'
						 );
  }
}
?>
