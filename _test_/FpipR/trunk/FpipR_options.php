<?php


function generer_url_document_flickr($id_document, $statut='') {
  if (intval($id_document) <= 0) 
	return '';
  $row = @spip_fetch_array(spip_query("SELECT fichier,distant	FROM spip_documents WHERE id_document = $id_document"));
  if ($row) {
	if ($row['distant'] == 'oui') {
	  if(preg_match('#http://static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$row['fichier'],$matches)) {
		$id = $matches[2];
		$secret = $matches[3];
		include_spip('inc/flickr_api');
		$details = flickr_photos_getInfo($id,$secret);
		if($details->urls['photopage']) return $details->urls['photopage'];
		if($details->owner_nsid) 
		  return "http://www.flickr.com/photos/".$details->owner_nsid."/$id/";
		else return $row['fichier'];
	  } else 
		return $row['fichier'];
	} else {
	  if (($GLOBALS['meta']["creer_htaccess"]) != 'oui')
		return _DIR_RACINE . ($row['fichier']);
	  else 	return generer_url_action('autoriser', "arg=$id_document");
	}
  }

}


if (!_DIR_RESTREINT) {
  if (!function_exists('generer_url_document')) {
	function generer_url_document($id, $stat='')
	  { return generer_url_document_flickr($id, $stat);}
  }
}

function balise_URL_DOCUMENT($p) {
  $_id_document = '';
  if ($p->param && !$p->param[0][0]){
	$_id_document =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  }
  if (!$_id_document)
	$_id_document = champ_sql('id_document',$p);
  $p->code = "generer_url_document_flickr($_id_document)";

  $p->interdire_scripts = false;
  return $p;
}

/***********************************************************************
 *                       Pour les boucles Flickr                        *
 ***********************************************************************/

/*Copie du plugin boucles_xml*/
function FpipR_fill_table_boucle($method,$arguments){
  include_spip('base/FpipR_db');
  FpipR_creer_tables($method);
  FpipR_fill_table($method,$arguments);
}

function FpipR_traiter_argument($key, $val){
  $val = str_replace("'",'',$val);
  if(!$val) $val = '0';
  if($key == 'bbox')
	$val = str_replace(':',',',$val);
  else if(strpos($key,'upload_date') !== false) {
	return strtotime($val);
  }
  return $val;
}


function FpipR_logo_owner($user_id,$server = '') {
  $url = 'http://www.flickr.com/images/buddyicon.jpg';
  if($server) {
	$url ="http://static.flickr.com/$server/buddyicons/$user_id.jpg";
  }
  return '<img src="'.$url.'" width="48" height="48"/>';
}


function FpipR_logo_photo($id_photo,$server,$secret,$taille='',$originalformat='jpg') {
  if($id_photo && $server)
	return '<img src="http://static.flickr.com/'.$server."/".$id_photo."_".$secret.($taille?"_$taille":'').'.'.(($taille=='o')?$originalformat:'jpg').'" />';
  return '';
}

function FpipR_generer_url_photo($user_id,$id_photo) {
  if($user_id && $id_photo)
	return "http://www.flickr.com/photos/$user_id/$id_photo/";
  else if($id_photo)
	return "http://www.flickr.com/photo.gne?id=$id_photo";
  return '';
}

function FpipR_generer_url_owner($user_id,$type) {
  if($user_id) {
	switch($type) {
	  case 1: //photos
		include_spip('inc/flickr_api');
		$url = flickr_urls_getUserPhotos($user_id);
		if($url) return $url['user']['url'];
	  case 2: //profile
		include_spip('inc/flickr_api');
		$url = flickr_urls_getUserProfile($user_id);
		include_spip('inc/flickr_api');
		if($url) return $url['user']['url'];
	  case 0:
	  default:
		return 'http://www.flickr.com/photos/'.$user_id.'/';
		
	}
  } 
  return '';
}
function FpipR_generer_url_photoset($user_id,$id_photoset) {
  if($user_id && $id_photoset)
	return 'http://www.flickr.com/photos/'.$user_id.'/sets/'.$id_photoset.'/';
  return '';
}

function FpipR_generer_url_group($id) {
  if($id) {
	include_spip('inc/flickr_api');
	$url = flickr_urls_getGroup($id);
	if($url)return $url['group']['url'];
	return 'http://www.flickr.com/groups/'.$id;
  }
  return '';
}

function FpipR_photos_getContext($id_photo,$id_photoset='',$id_group='',$tag,$attr) {
  static $contexts;
  if($id_photoset) {
	if(!$contexts["$id_photo-$id_photoset"]) { 
	  include_spip('inc/flickr_api');
	  $contexts["$id_photo-$id_photoset"] = flickr_photosets_getContext($id_photo,$id_photoset);
	}
	return $contexts["$id_photo-$id_photoset"][$tag][$attr];
  } else if($id_group) {
	if(!$contexts["$id_photo-$id_group"]) { 
	  include_spip('inc/flickr_api');
	  $contexts["$id_photo-$id_group"] = flickr_groups_pools_getContext($id_photo,$id_group);
	}
	return $contexts["$id_photo-$id_group"][$tag][$attr];
	} else {
	if(!$contexts[$id_photo]) { 
	  include_spip('inc/flickr_api');
	  $contexts[$id_photo] = flickr_photos_getContext($id_photo);
	}
	return $contexts[$id_photo][$tag][$attr];
	}
}

/*function FpipR_photos_getPerms($id_photo,$perm) {
  static $perms;
  if(!$perms[$id_photo]) {
	  include_spip('inc/flickr_api');
	  $perms[$id_photo] = flickr_photos_getPerms($id_photo);
	var_dump($perms[$id_photo]);
  } 
  return $perms[$id_photo]['perms'][$perm];
}*/

function FpipR_photos_geo_getLocation($id_photo,$location) {
  static $locations;
  if(!$locations[$id_photo]) {
	  include_spip('inc/flickr_api');
	  $locations[$id_photo] = flickr_photos_geo_getLocation($id_photo);
  } 
  return $locations[$id_photo]['location'][$location];
}

function FpipR_get_flickr_photo_id($fichier) {
  if(preg_match('#http://static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$fichier,$matches))
		return $matches[2];
	return '';
}

function FpipR_get_flickr_photo_secret($fichier) {
  if(preg_match('#http://static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$fichier,$matches))
		return $matches[3];
  return '';
	
}


?>
