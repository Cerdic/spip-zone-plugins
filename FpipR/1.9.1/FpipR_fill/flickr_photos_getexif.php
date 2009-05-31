<?php
function FpipR_fill_flickr_photos_getexif_dist($arguments) {
  include_spip('inc/flickr_api');
  $photo = flickr_photos_getExif($arguments['id_photo'],$arguments['secret'],$arguments['auth_token']);

  $query = "DELETE FROM spip_fpipr_exif";
  spip_query($query);
  if($photo = $photo['photo']) {
	$id = _q($photo['id']);
	$secret = _q($photo['secret']);
	$server = _q($photo['server']);
	$farm = _q($photo['farm']);
	foreach($photo['exif'] as $e) {
	  spip_abstract_insert('spip_fpipr_exif',
						   '(id_photo,secret,server,farm,tagspace,tagspaceid,tag,label,raw,clean)',
						   '('.$id.','.$secret.','.$server.','.$farm.','._q($e['tagspace']).','._q($e['tagspaceid']).','._q($e['tag']).','._q($e['label']).','._q($e['raw']['_content']).','._q($e['clean']['_content']).')'
						   );
	}
  }
}
?>
