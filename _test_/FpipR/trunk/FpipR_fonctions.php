<?php
  /*
   * BOUCLEs Flickr API
   * 
   * Auteur: Mortimer (Pierre Andrews)
   * (c) 2006 - Distribue sous license GNU/GPL
   */

include_spip('base/FpipR_db');

function critere_tags($idb, &$boucles, $crit) {
}

function critere_tag_mode($idb, &$boucles, $crit) {
}

function critere_text($idb, &$boucles, $crit) {
}

function critere_privacy_filter($idb, &$boucles, $crit) {
}

function critere_bbox($idb, &$boucles, $crit) {
}


function critere_accuracy($idb, &$boucles, $crit) {
}


function balise_URL_PHOTO($p) {
  $user_id = champ_sql('user_id',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "'http://www.flickr.com/photos/'.$user_id.'/'.$id_photo.'/'";
  return $p;
}


function balise_URL_OWNER($p) {
  $user_id = champ_sql('user_id',$p);
  $p->code = "'http://www.flickr.com/photos/'.$user_id.'/'";
  return $p;
}

function balise_LOGO_PHOTO($p) {
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

function balise_LOGO_OWNER($p) {
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


  foreach($boucle->criteres as $crit) {
	if (in_array($crit->op,$possible_criteres)){
	  $val = !isset($crit->param[0]) ? "" : calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	  $arguments[$crit->op] = $val;
	}
  }

  //on calcul le nombre de page d'apres {0,10}
  list($debut,$pas) = split(',',$boucle->limit);
  $page = $debut/$pas;
  if($page <= 0) $page = 1;
  $arguments['page'] = intval($page);
  $arguments['per_page'] = $pas>0?$pas:100;
  $boucle->limit = NULL;

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
	if (in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	}
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

	return calculer_boucle($id_boucle, $boucles); 
}

function boucle_FLICKR_PHOTO_NOTES_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_notes";

	return calculer_boucle($id_boucle, $boucles); 
}

function boucle_FLICKR_PHOTO_URLS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fpipr_urls";

	return calculer_boucle($id_boucle, $boucles); 
}

//======================================================================

function balise_LOGO_PHOTOSET($p) {
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

function balise_URL_PHOTOSET($p) {
  $user_id = champ_sql('user_id',$p);
  $id_photoset = champ_sql('id_photoset',$p);
  $p->code = "'http://www.flickr.com/photos/'.$user_id.'/'.$id_photoset.'/'";
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
	if (in_array($key,$possible_args)){
	  $arguments[$key] = $val;
	}
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

  foreach($boucle->criteres as $crit) {
	if (in_array($crit->op,$possible_criteres)){
	  $val = !isset($crit->param[0]) ? "" : calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	  $arguments[$crit->op] = $val;
	}
  }
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
	}
  }

  //on calcul le nombre de page d'apres {0,10}
  list($debut,$pas) = split(',',$boucle->limit);
  $page = $debut/$pas;
  if($page <= 0) $page = 1;
  $arguments['page'] = intval($page);
  $arguments['per_page'] = $pas>0?$pas:100;
  $boucle->limit = NULL;

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


?>
