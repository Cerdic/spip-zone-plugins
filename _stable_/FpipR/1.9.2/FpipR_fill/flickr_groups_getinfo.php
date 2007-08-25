<?php
function FpipR_fill_flickr_groups_getinfo_dist($arguments) {
  include_spip('inc/flickr_api');
  $group = flickr_groups_getInfo($arguments['id_group'],$arguments['auth_token']);
  $query = "DELETE FROM spip_fpipr_groups";
  spip_query($query);
  if($group = $group['group']) {
	spip_abstract_insert('spip_fpipr_groups',
						 '(id_group,iconserver,name,description,members,privacy,throttle_count,throttle_mode,throttle_remaining)',
						 '('._q($group['id']).','._q($group['iconserver']).','._q($group['name']['_content']).','._q($group['description']['_content']).','._q($group['members']['_content']).','._q($group['privacy']['_content']).','._q($group['throttle']['count']).','._q($group['throttle']['mode']).','._q($group['throttle']['remaining']).')'
						 );
  }									 
}
?>
