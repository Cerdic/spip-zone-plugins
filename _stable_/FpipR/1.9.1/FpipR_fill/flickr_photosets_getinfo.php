<?php
function FpipR_fill_flickr_photosets_getinfo_dist($arguments) {
  include_spip('inc/flickr_api');
  //on vide les tables
  $query = "DELETE FROM spip_fpipr_photosets";
  spip_query($query);
  
  $set = flickr_photosets_getInfo($arguments['id_photoset'],$arguments['auth_token']);

  spip_abstract_insert('spip_fpipr_photosets',
					   '(id_photoset,user_id,primary_photo,secret,server,farm,photos,title,description)',
					   '('._q($set->id).','._q($set->owner).','._q($set->primary).','._q($set->secret).','._q($set->server).','._q($set->farm).','._q($set->photos).','._q($set->title).','._q($set->description).')'
					   );	
}
?>
