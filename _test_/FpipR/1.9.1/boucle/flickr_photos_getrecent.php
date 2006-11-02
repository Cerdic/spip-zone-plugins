<?php
function boucle_FLICKR_PHOTOS_GETRECENT_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');
  $extras = array();

  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";

  $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.getRecent',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
