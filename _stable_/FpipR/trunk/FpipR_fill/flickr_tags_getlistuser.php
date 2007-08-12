<?php
function FpipR_fill_flickr_tags_getlistuser_dist($arguments) {
  include_spip('inc/flickr_api');
  $who = flickr_tags_getListUser($arguments['author'],$arguments['auth_token']);

  $query = "DELETE FROM spip_fpipr_tags";
  spip_query($query);
  $fake_id = 0;
  if($who = $who['who']['tags']) {
	foreach($who['tag'] as $t) {
	  sql_insert('spip_fpipr_tags',
						   '(id_tag,author,safe)',
						   '('._q($fake_id++).','._q($arguments['author']).','._q($t['_content']).')'
						   );
	}
  }									 
}
?>
