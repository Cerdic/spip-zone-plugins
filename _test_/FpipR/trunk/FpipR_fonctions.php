<?php
  /*
   * BOUCLEs Flickr API
   * 
   * Auteur: Mortimer (Pierre Andrews)
   * (c) 2006 - Distribue sous license GNU/GPL
   */

include_spip('base/FpipR_db');

function critere_tags_dist($idb, &$boucles, $crit) {
}

function critere_tag_mode_dist($idb, &$boucles, $crit) {
}

function critere_text_dist($idb, &$boucles, $crit) {
}

function critere_privacy_filter_dist($idb, &$boucles, $crit) {
}

function critere_bbox_dist($idb, &$boucles, $crit) {
}


function critere_accuracy_dist($idb, &$boucles, $crit) {
}


function balise_URL_PHOTO_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "FpipR_generer_url_photo($user_id,$id_photo)";
  return $p;
}


function balise_URL_OWNER_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $p->code = "FpipR_generer_url_owner($user_id)";
  return $p;
}

function balise_LOGO_PHOTO_dist($p) {
  $server = champ_sql('server',$p);
  $id_photo = champ_sql('id_photo',$p);
  $secret = champ_sql('secret',$p);
  $originalformat = champ_sql('originalformat',$p);
  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_logo_photo($id_photo,$server,$secret,$taille,$originalformat)";	
  return $p;
}

function balise_LOGO_OWNER_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $server = champ_sql('icon_server',$p);
  $p->code = "FpipR_logo_owner($user_id,$server)";	
  return $p;
}


/** boucle FLICKR_PHOTOS_SEARCH
 Voir la doc de l'API: http://flickr.com/services/api/flickr.photos.search.html
 user_id V
 tags V
 tag_mode V
 text V
 upload_date
 taken_date
 license: V
 <license id="4" name="Attribution License"
 url="http://creativecommons.org/licenses/by/2.0/" /> 
 <license id="6" name="Attribution-NoDerivs License"
 url="http://creativecommons.org/licenses/by-nd/2.0/" /> 
 <license id="3" name="Attribution-NonCommercial-NoDerivs License"
 url="http://creativecommons.org/licenses/by-nc-nd/2.0/" /> 
 <license id="2" name="Attribution-NonCommercial License"
 url="http://creativecommons.org/licenses/by-nc/2.0/" /> 
 <license id="1" name="Attribution-NonCommercial-ShareAlike License"
 url="http://creativecommons.org/licenses/by-nc-sa/2.0/" /> 
 <license id="5" name="Attribution-ShareAlike License"
 url="http://creativecommons.org/licenses/by-sa/2.0/" /> 
 privacy_filter X
 * 1 public photos
 * 2 private photos visible to friends
 * 3 private photos visible to family
 * 4 private photos visible to friends & family
 * 5 completely private photos
 bbox min_lon:min_lat:max_lon:max_lat V
 accuracy V
 * World level is 1
 * Country is ~3
 * Region is ~6
 * City is ~11
 * Street is ~16
 */
function boucle_FLICKR_PHOTOS_SEARCH_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_args = array('user_id','license','upload_date','taken_date');

  $possible_criteres = array('tags','tag_mode','text','privacy_filter',
							 'bbox','accuracy');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $possible_sort = array('date_posted','date_taken','interestingness','relevance');

  $arguments = '';


  FpipR_utils_search_criteres($boucle,$arguments,$possible_criteres);
  FpipR_utils_calcul_limit($boucle,$arguments);

  if(is_array($boucle->order)) {
	for($i=0;$i<count($boucle->order);$i++) {
	  list($sort,$desc) = split(' . ',str_replace("'",'',$boucle->order[$i]));
	  if(in_array($sort,$possible_sort)) {
		$sort = str_replace('_','-',$sort);
		if($sort != 'relevance' && isset($desc)) 
		  $sort .= '-desc';
		else
		  $sort .= '-asc';
		$boucle->order[$i] = "'fpipr_photos.rang'";
		$arguments['sort'] = "'".$sort."'";
	  }
	}
  }
	

  $extras = array();

  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if (in_array($key,$possible_args)){
	  if(in_array($key,$possible_extras)) $extras[] = $key; 
	  else if($key == 'upload_date') $extras[] = 'date_upload';
	  else if($key == 'taken_date') $extras[] ='date_taken';
	  switch($w[0]) {
		case "'='":
		  if($key == 'taken_date' || $key == 'upload_date') {
			$arguments['min_'.$key] = $val;
			$arguments['max_'.$key] = $val;
		  } else {
			$arguments[$key] = $val;
		  }
		  break;
		case "'<'":
		  if($key == 'taken_date' || $key == 'upload_date') {
			$arguments['max_'.$key] = $val;
		  }
		  break;
		case "'>'":
		  if($key == 'taken_date' || $key == 'upload_date') {
			$arguments['min_'.$key] = $val;
		  }
		  break;
		default:
		  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
	  }
	}
  }
  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  $bbox = '';
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photos.search',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function boucle_FLICKR_PHOTOS_GETINFO_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photo_details";

  $possible_args = array('id_photo','secret');

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] = "'='" && in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photos.getInfo',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}


function boucle_FLICKR_PHOTO_TAGS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_tags";

	if($boucles[$boucle->id_parent]->id_table != 'fpipr_photo_details') 
	  erreur_squelette(_T('fpipr:mauvaise_imbrication',array('boucle'=>'TAGS')), $id_boucle);
	  

	return calculer_boucle($id_boucle, $boucles); 
}

function boucle_FLICKR_PHOTO_NOTES_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_notes";

	if($boucles[$boucle->id_parent]->id_table != 'fpipr_photo_details') 
	  erreur_squelette(_T('fpipr:mauvaise_imbrication',array('boucle'=>'NOTES')), $id_boucle);

	return calculer_boucle($id_boucle, $boucles); 
}

function boucle_FLICKR_PHOTO_URLS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_urls";

	if($boucles[$boucle->id_parent]->id_table != 'fpipr_photo_details') 
	  erreur_squelette(_T('fpipr:mauvaise_imbrication',array('boucle'=>'URLS')), $id_boucle);

	return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function balise_LOGO_PHOTOSET_dist($p) {
  $server = champ_sql('server',$p);
  $id_photo = champ_sql('primary_photo',$p);
  $secret = champ_sql('secret',$p);
  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_logo_photo($id_photo,$server,$secret,$taille,'jpg')";	
  return $p;
}

function balise_URL_PHOTOSET_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $id_photoset = champ_sql('id_photoset',$p);
  $p->code = "FpipR_generer_url_photoset($user_id,$id_photoset)";
  return $p;
}

function boucle_FLICKR_PHOTOSETS_GETLIST_dist($id_boucle,&$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photosets";

  $possible_args = array('user_id');

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_photosets et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photosets.getList',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function boucle_FLICKR_PHOTOSETS_GETPHOTOS_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('privacy_filter');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = '';  
  $extras = array();

  FpipR_utils_search_criteres($boucle,$arguments,$possible_criteres);

  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	  if(in_array($key,$possible_extras)) $extras[] = $key; 
	  else if($key == 'upload_date') $extras[] = 'date_upload';
	  else if($key == 'taken_date') $extras[] ='date_taken';
	if ($w[0] == "'='" && $key == 'id_photoset'){
	  $arguments[$key] = $val;
	} else
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }

  FpipR_utils_calcul_limit($boucle,$arguments);

  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  $bbox = '';
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photosets.getPhotos',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================
// pour flickr.photos.getContext
// on ne fait que des balises
//======================================================================

/*<count>809</count>
<prevphoto id="268856369" secret="042e926016" server="113" farm="1" title="Thistle" url="/photos/mortimer/268856369/in/photostream/" thumb="http://static.flickr.com/113/268856369_042e926016_s.jpg"/>
<nextphoto id="269373997" secret="8c5632b520" server="84" farm="1" title="" url="/photos/mortimer/269373997/in/photostream/" thumb="http://static.flickr.com/84/269373997_8c5632b520_s.jpg"/>*/

function balise_PHOTOS_COUNT_dist($p) {
  $photo_id = champ_sql('id_photo',$p);
  
  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';
  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"count","_content")';
  return $p;
}

function balise_PREVPHOTO_ID_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"prevphoto","id")';
  return $p;  
}
function balise_PREVPHOTO_SECRET_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

   $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"prevphoto","secret")';
  return $p;  
}
function balise_PREVPHOTO_SERVER_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

    $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

$photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"prevphoto","server")';
  return $p;  
}
function balise_PREVPHOTO_TITLE_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"prevphoto","title")';
  return $p;  
}
function balise_URL_PREVPHOTO_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"prevphoto","url")';
  return $p;  
}
function balise_PREVPHOTO_THUMB_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"prevphoto","thumb")';
  return $p;  
}
function balise_LOGO_PREVPHOTO_dist($p) {
  $id_photo = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_logo_photo(FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'prevphoto','id'),FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'prevphoto','server'),FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'prevphoto','secret'),$taille,'jpg')";	
  return $p;
}

function balise_NEXTPHOTO_ID_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"nextphoto","id")';
  return $p;  
}
function balise_NEXTPHOTO_SECRET_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"nextphoto","secret")';
  return $p;  
}
function balise_NEXTPHOTO_SERVER_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"nextphoto","server")';
  return $p;  
}
function balise_NEXTPHOTO_TITLE_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"nextphoto","title")';
  return $p;  
}
function balise_URL_NEXTPHOTO_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"nextphoto","url")';
  return $p;  
}
function balise_NEXTPHOTO_THUMB_dist($p) {
  $photo_id = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"nextphoto","thumb")';
  return $p;  
}
function balise_LOGO_NEXTPHOTO_dist($p) {
  $id_photo = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_logo_photo(FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'nextphoto','id'),FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'nextphoto','server'),FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'nextphoto','secret'),$taille,'jpg')";	
  return $p;
}

//======================================================================

function boucle_FLICKR_PHOTOS_GETALLCONTEXTS_dist($id_boucle,&$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_contextes";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'id_photo'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_contextes et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photos.getAllContexts',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

function balise_ID_GROUP_dist($p) {
	$_type = $p->type_requete;
	if($_type == 'flickr_photos_getallcontexts') {
	  $t = champ_sql('type',$p);
	  $id = champ_sql('id_contexte',$p);
	  $p->code = "($t == 'pool')?$id:''";
	} else $p->code = champ_sql('id_group',$p);
	return $p;
}

function balise_ID_PHOTOSET_dist($p) {
	$_type = $p->type_requete;
	if($_type == 'flickr_photos_getallcontexts') {
	  $t = champ_sql('type',$p);
	  $id = champ_sql('id_contexte',$p);
	  $p->code = "($t == 'set')?$id:''";
	} else $p->code = champ_sql('id_photoset',$p);
	return $p;
}

//======================================================================

function boucle_FLICKR_INTERESTINGNESS_GETLIST_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('date');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = '';  
  $extras = array();

  FpipR_utils_search_criteres($boucle,$arguments,$possible_criteres);

  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';

  }

  FpipR_utils_calcul_limit($boucle,$arguments);

  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  $bbox = '';
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.interestingness.getList',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}
//======================================================================

function boucle_FLICKR_GROUPS_POOLS_GETPHOTOS_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_args = array('id_group','user_id');

  $possible_criteres = array('tags');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = '';  
  $extras = array();

  FpipR_utils_search_criteres($boucle,$arguments,$possible_criteres);

  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] = "'='" && in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);

	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';

  }

  FpipR_utils_calcul_limit($boucle,$arguments);

  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  $bbox = '';
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.groups.pools.getPhotos',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function boucle_FLICKR_TAGS_GETLISTPHOTO_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_tags";

	foreach($boucle->where as $w) {
	  if($w[0] == "'?'") {
		$w = $w[2];
	  } 
	  $key = str_replace("'",'',$w[1]);
	  $val = $w[2];
	  $key = str_replace("$id_table.",'',$key);
	  if ($w[0] = "'='" && $key == 'id_photo'){
		$arguments[$key] = $val;
	  } else 
		erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
	}

	$boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
	$bbox = '';
	foreach($arguments as $key => $val) {
	  if($val) {
		$boucle->hash .= "\$v=$val;\n";
		$boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	  }}
	
	$boucle->hash .= "FpipR_fill_table_boucle('flickr.tags.getListPhoto',\$arguments);";

	return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

//ici, on utilise un critere nsid different pour specifier de qui on veut les contacts
function critere_nsid_dist($idb, &$boucles, $crit) {
}

function critere_just_friends_dist($idb, &$boucles, $crit) {
}

function critere_single_photo_dist($idb, &$boucles, $crit) {
}

function critere_include_self_dist($idb, &$boucles, $crit) {
}

function boucle_FLICKR_PHOTOS_GETCONTACTSPUBLICPHOTOS_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('nsid','just_friends','single_photo','include_self');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = '';  
  $extras = array();

  FpipR_utils_search_criteres($boucle,$arguments,$possible_criteres);

  if($boucle->limit) {
	list($debut,$pas) = split(',',$boucle->limit);
	$arguments['count'] = $pas;
  }
  

  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] = "'='" && in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);

	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';

  }

  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  $bbox = '';
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photos.getContactsPublicPhotos',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

function boucle_FLICKR_FAVORITES_GETPUBLICLIST_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_photos";

  $possible_criteres = array('nsid');

  $possible_extras = array('license', 'owner_name', 'icon_server', 'original_format', 'last_update');

  $arguments = '';  
  $extras = array();

  FpipR_utils_search_criteres($boucle,$arguments,$possible_criteres);

  FpipR_utils_calcul_limit($boucle,$arguments);

  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] = "'='" && in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);

	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';

  }

  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->select as $w) {
	$key = str_replace("'",'',$w);
	$key = str_replace("$id_table.",'',$key);
	if(in_array($key,$possible_extras)) $extras[] = $key; 
	else if($key == 'upload_date') $extras[] = 'date_upload';
	else if($key == 'taken_date') $extras[] ='date_taken';
	else if($key == 'longitude' || $key == 'latitude') $extras[] = 'geo';
  }
  $arguments['extras'] = "'".join(',',$extras)."'";
  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  $bbox = '';
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.favorites.getPublicList',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function boucle_FLICKR_PHOTOS_COMMENTS_GETLIST_dist($id_boucle,&$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_comments";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'id_photo'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_contextes et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photos.comments.getList',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

function boucle_FLICKR_PHOTOSETS_COMMENTS_GETLIST_dist($id_boucle,&$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_comments";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'id_photoset'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_contextes et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.photosets.comments.getList',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

/*
Etrangement, on ne peut pas faire ca sans auth.
function balise_ISPUBLIC_dist($p) {
  $ispublic = champ_sql('ispublic',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($ispublic)?$ispublic:FpipR_photos_getPerms($id_photo,'ispublic'))";
  return $p;
}
function balise_ISFAMILY_dist($p) {
  $isfamily = champ_sql('isfamily',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($isfamily)?$isfamily:FpipR_photos_getPerms($id_photo,'isfamily'))";
  return $p;
}
function balise_ISFRIEND_dist($p) {
  $isfriend = champ_sql('isfriend',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($isfriend)?$isfriend:FpipR_photos_getPerms($id_photo,'isfriend'))";
  return $p;
}*/

//======================================================================

function balise_LATITUDE_dist($p) {
  $latitude = champ_sql('latitude',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($latitude)?$latitude:FpipR_photos_geo_getLocation($id_photo,'latitude'))";
  return $p;
}
function balise_LONGITUDE_dist($p) {
  $longitude = champ_sql('longitude',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($longitude)?$longitude:FpipR_photos_geo_getLocation($id_photo,'longitude'))";
  return $p;
}
function balise_ACCURACY_dist($p) {
  $accuracy = champ_sql('accuracy',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($accuracy)?$accuracy:FpipR_photos_geo_getLocation($id_photo,'accuracy'))";
  return $p;
}

//======================================================================

function boucle_FLICKR_GROUPS_GETINFO_dist($id_boucle,&$boucles) {
 $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_groups";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'id_group'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_groups et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.groups.getInfo',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

function balise_LOGO_GROUP_dist($p) {
  $id_group = champ_sql('id_group',$p);
  $server = champ_sql('iconserver',$p);
  $p->code = "FpipR_logo_owner($id_group,$server)";	
  return $p;
}

function balise_URL_GROUP_dist($p) {
  $id = champ_sql('id_group',$p);
  $p->code = "FpipR_generer_url_group($id)";	
  return $p;
}

function boucle_FLICKR_URLS_LOOKUPGROUP_dist($id_boucle, &$boucles) {
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_groups";

  $possible_criteres = array('url');

  $arguments = '';  
  $extras = array();

  FpipR_utils_search_criteres($boucle,$arguments,$possible_criteres);


  $boucle->hash = "// CREER la table flickr_photos et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  $bbox = '';
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.urls.lookupGroup',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

function critere_url_dist($idb, &$boucles, $crit) {
}
//======================================================================

function boucle_FLICKR_PEOPLE_GETINFO_dist($id_boucle,&$boucles) {
 $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_people";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'user_id'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_people et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.people.getInfo',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function boucle_FLICKR_PEOPLE_GETPUBLICGROUPS_dist($id_boucle,&$boucles) {
 $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_groups";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'user_id'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = "// CREER la table flickr_groups et la peupler avec le resultat de la query
	  \$arguments = '';\n";
  foreach($arguments as $key => $val) {
	if($val) {
	  $boucle->hash .= "\$v=$val;\n";
	  $boucle->hash .= "\$arguments['$key']=FpipR_traiter_argument('$key',\$v);\n";
	}}

  $boucle->hash .= "FpipR_fill_table_boucle('flickr.people.getPublicGroups',\$arguments);";
  return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function FpipR_utils_calcul_limit(&$boucle,&$arguments) {
  //on calcul le nombre de page d'apres {0,10}
  list($debut,$pas) = split(',',$boucle->limit);
  $page = $debut/$pas;
  if($page <= 0) $page = 1;
  $arguments['page'] = intval($page);
  $arguments['per_page'] = $pas>0?($pas+$debut):100;
  //  $boucle->limit = NULL;
}

function FpipR_utils_search_criteres(&$boucle,&$arguments,$possible_criteres) {
  foreach($boucle->criteres as $crit) {
	if (in_array($crit->op,$possible_criteres)){
	  $val = !isset($crit->param[0]) ? "1" : calculer_liste($crit->param[0], array(), $boucles, $boucles[$id_boucle]->id_parent);
	  $arguments[$crit->op] = $val;
	}
  }
}





?>
