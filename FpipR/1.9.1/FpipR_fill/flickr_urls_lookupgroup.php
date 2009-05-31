<?php

function FpipR_fill_flickr_urls_lookupgroup_dist($arguments) {
  include_spip('inc/flickr_api');
  $group = flickr_urls_lookupGroup($arguments['url'],$arguments['auth_token']);

  $query = "DELETE FROM spip_fpipr_groups";
  spip_query($query);
  if($group = $group['group']) {
	spip_abstract_insert('spip_fpipr_groups',
						 '(id_group,name)',
						 '('._q($group['id']).','._q($group['groupname']['_content']).')'
						 );
  }									 
}
?>
