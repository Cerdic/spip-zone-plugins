<?php
function FpipR_fill_flickr_tags_getlistuserpopular_dist($arguments) {
  include_spip('inc/flickr_api');
  $who = flickr_tags_getListUserPopular($arguments['author'],$arguments['count'],$arguments['auth_token']);

  $query = "DELETE FROM spip_fpipr_tags";
  spip_query($query);
  $fake_id = 0;
  if($who = $who['who']['tags']) {
	foreach($who['tag'] as $t) {
	  spip_abstract_insert('spip_fpipr_tags',
						   '(id_tag,author,safe,count)',
						   '('._q($fake_id++).','._q($arguments['author']).','._q($t['_content']).','._q($t['count']).')'
						   );
	}
  }									 
}
?>
