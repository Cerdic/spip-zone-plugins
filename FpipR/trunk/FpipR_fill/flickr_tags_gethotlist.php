<?php
function FpipR_fill_flickr_tags_gethotlist_dist($arguments) {
  include_spip('inc/flickr_api');
  $who = flickr_tags_getHotList($arguments['period'],$arguments['count'],$arguments['auth_token']);

  $query = "DELETE FROM spip_fpipr_tags";
  spip_query($query);
  $fake_id = 0;
  if($who = $who['hottags']) {
	foreach($who['tag'] as $t) {
	  sql_insert('spip_fpipr_tags',
						   '(id_tag,score,safe)',
						   '('._q($fake_id++).','._q($t['score']).','._q($t['_content']).')'
						   );
	}
  }									 
}
?>
