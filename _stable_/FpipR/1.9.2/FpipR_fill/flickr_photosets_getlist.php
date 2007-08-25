<?php
function FpipR_fill_flickr_photosets_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  //on vide les tables
  $query = "DELETE FROM spip_fpipr_photosets";
  spip_query($query);
  
  $photosets = flickr_photosets_getList($arguments['user_id'],$arguments['auth_token']);
  foreach($photosets as $set) {
	spip_abstract_insert('spip_fpipr_photosets',
						 '(id_photoset,user_id,primary_photo,secret,server,photos,title,description,farm)',
						 '('._q($set->id).','._q($set->owner).','._q($set->primary).','._q($set->secret).','._q($set->server).','._q($set->photos).','._q($set->title).','._q($set->description).','.intval($set->farm).')'
						 );	
  }
}
?>
