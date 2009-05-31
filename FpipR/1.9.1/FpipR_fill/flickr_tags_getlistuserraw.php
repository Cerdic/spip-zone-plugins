<?php

function FpipR_fill_flickr_tags_getlistuserraw_dist($arguments) {
  include_spip('inc/flickr_api');
  $who = flickr_tags_getListUserRaw($arguments['tag'],$arguments['auth_token']);
  $query = "DELETE FROM spip_fpipr_tags";
  spip_query($query);
  $fake_id = 0;
  $user_id = $who['who']['id'];
  if($who = $who['who']['tags']) {
	foreach($who['tag'] as $t) {
	  foreach($t['raw'] as $r) {
		spip_abstract_insert('spip_fpipr_tags',
							 '(id_tag,author,raw,safe)',
							 '('._q($fake_id++).','._q($user_id).','._q($r['_content']).','._q($t['clean']).')'
							 );
	  }
	}
  }									 
}
?>
