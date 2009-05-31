<?php
function FpipR_fill_flickr_photos_getinfo_dist($arguments) {
  include_spip('inc/flickr_api');
  $details = flickr_photos_getInfo($arguments['id_photo'],$arguments['secret'],$arguments['auth_token']);
  $id_photo = intval($details->id);
  if($id_photo) {
	//on vide les tables
	$query = "DELETE FROM spip_fpipr_photo_details";
	spip_query($query);
	$query = "DELETE FROM spip_fpipr_tags";
	spip_query($query);
	$query = "DELETE FROM spip_fpipr_notes";
	spip_query($query);
	$query = "DELETE FROM spip_fpipr_urls";
	spip_query($query);
	//on insere la ligne unique de detail
	sql_insert('spip_fpipr_photo_details',
						 '(id_photo,secret,server,isfavorite,license,rotation,originalformat,user_id,owner_username,owner_realname,owner_location,title,description,ispublic,isfriend,isfamily,date_posted,date_taken,date_lastupdate,comments,latitude,longitude,accuracy)',						   
						 '('._q($details->id).','._q($details->secret).','._q($details->server).','._q($details->isfavorite).','._q($details->license).','._q($details->rotation).','._q($details->originalformat).','._q($details->owner_nsid).','._q($details->owner_username).','._q($details->owner_realname).','._q($details->owner_location).','._q($details->title).','._q($details->description).','._q($details->visibility_ispublic).','._q($details->visibility_isfriend).','._q($details->visibility_isfamily).','._q(date('Y-m-d H:i:s',$details->date_posted+0)).','._q($details->date_taken).','._q(date('Y-m-d H:i:s',$details->date_lastupdate+0)).','._q($details->comments).','._q($details->location_latitude).','._q($details->location_longitude).','._q($details->location_accuracy).')'
						 );	  
	//on insere les tags
	foreach($details->tags as $tag) {
	  sql_insert('spip_fpipr_tags',
						   '(id_tag,author,raw,safe,id_photo)',
						   '('._q($tag->id).','._q($tag->author).','._q($tag->raw).','._q($tag->safe).','._q($id_photo).')'
						   );
	}
	//on insere les notes
	foreach($details->notes as $n) {
	  sql_insert('spip_fpipr_notes',
						   '(id_note,id_photo,author,authorname,x,y,width,height,texte)',
						   '('._q($n['id']).','._q($id_photo).','._q($n['author']).','._q($n['authorname']).','._q($n['x']).','._q($n['y']).','._q($n['w']).','._q($n['h']).','._q($n['_content']).')'
						   );
	}
	//on insere les urls
	foreach($details->urls as $k=>$u) {
	  sql_insert('spip_fpipr_urls',
						   '(type,id_photo,url)',
						   '('._q($k).','._q($id_photo).','._q($u).')'
						   );
	}
  }
}
?>
