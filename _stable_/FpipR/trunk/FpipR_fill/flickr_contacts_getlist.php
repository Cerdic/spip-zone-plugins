<?php

function FpipR_fill_flickr_contacts_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  $contacts = flickr_contacts_getList($arguments['nsid'],$arguments['filter'],$arguments['page'],$arguments['per_page'],$arguments['auth_token']);
  $query = "DELETE FROM spip_fpipr_people";
  spip_query($query);
  if($contacts = $contacts['contacts']) {
	foreach($contacts['contact'] as $c) {
	  sql_insert('spip_fpipr_people',
						   '(user_id,username,iconserver,ignored,friend,family,realname)',
						   '('._q($c['nsid']).','._q($c['username']).','._q($c['iconserver']).','._q($c['ignored']).
						   ','._q($c['friend']).','._q($c['family']).','._q($c['realname']).')'
						   );

	}
	return $contacts['total'];
  }	
  return 0;
}

?>
