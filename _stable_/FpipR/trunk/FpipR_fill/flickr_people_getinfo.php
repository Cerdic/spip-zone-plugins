<?php
function FpipR_fill_flickr_people_getinfo_dist($arguments) {
  include_spip('inc/flickr_api');
  $person = flickr_people_getInfo($arguments['user_id'],$arguments['auth_token']);

  $query = "DELETE FROM spip_fpipr_people";
  spip_query($query);
  if($person = $person['person']) {
	sql_insert('spip_fpipr_people',
						 '(user_id,isadmin,ispro,iconserver,username,realname,location,url_photos,url_profile,date_firstphoto,date_taken_firstphoto,photos_count)',
						 '('._q($person['nsid']).','._q($person['isadmin']).','._q($person['ispro']).','._q($person['iconserver']).','._q($person['username']['_content']).','._q($person['realname']['_content']).','._q($person['location']['_content']).','._q($person['photosurl']['_content']).','._q($person['profileurl']['_content']).','._q(date('Y-m-d H:i:s',$person['photos']['firstdate']['_content'])).','._q($person['photos']['firstdatetaken']['_content']).','._q($person['photos']['count']['_content']).')'
						 );
  }									 
}
?>
