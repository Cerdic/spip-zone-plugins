<?php

function FpipR_fill_flickr_tags_getrelated_dist($arguments) {
  include_spip('inc/flickr_api');
  $who = flickr_tags_getRelated($arguments['tag'],$arguments['auth_token']);
  $query = "DELETE FROM spip_fpipr_tags";
  spip_query($query);
  $fake_id = 0;
  if($who = $who['tags']) {
	foreach($who['tag'] as $r) {
	  sql_insert('spip_fpipr_tags',
						   '(id_tag,author,safe)',
						   '('._q($fake_id++).','._q($user_id).','._q($r['_content']).')'
						   );
	}
  }									 
}
?>
