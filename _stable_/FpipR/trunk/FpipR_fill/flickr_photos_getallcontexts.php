 <?php
function FpipR_fill_flickr_photos_getallcontexts_dist($arguments) {
  include_spip('inc/flickr_api');
  $query = "DELETE FROM spip_fpipr_contextes";
  spip_query($query);
  $id_photo = $arguments['id_photo'];
  $contextes = flickr_photos_getAllContexts($id_photo,$arguments['auth_token']);
  foreach($contextes as $type => $cont) {
	if(($type == 'set' || $type == 'pool') && is_array($cont)) 
	  foreach ($cont as $c) {
		sql_insert('spip_fpipr_contextes',
							 '(id_contexte,title,type,id_photo)',
							 '('._q($c['id']).','._q($c['title']).','._q($type).','._q($id_photo).')'
							 );  
	  }
  } 
}
 ?>
