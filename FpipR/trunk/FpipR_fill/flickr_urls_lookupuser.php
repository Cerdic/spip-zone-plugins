<?php

function FpipR_fill_flickr_urls_lookupuser_dist($arguments) {
  include_spip('inc/flickr_api');
  $user = flickr_urls_lookupUser($arguments['url'],$arguments['auth_token']);

  $query = "DELETE FROM spip_fpipr_people";
  spip_query($query);
  if($user = $user['user']) {
	sql_insert('spip_fpipr_groups',
						 '(user_id,username)',
						 '('._q($group['id']).','._q($group['username']['_content']).')'
						 );
  }									 
}
?>
