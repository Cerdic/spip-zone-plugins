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

function FpipR_generer_url_owner($user_id) {
  if($user_id) return 'http://www.flickr.com/photos/'.$user_id.'/';
  return '';
}
function FpipR_generer_url_photoset($user_id,$id_photoset) {
  if($user_id && $id_photoset)
	return 'http://www.flickr.com/photos/'.$user_id.'/sets/'.$id_photoset.'/';
  return '';
}


function FpipR_photos_getContext($id_photo,$tag,$attr) {
  static $contexts;
  if(!$contexts[$id_photo]) { 
	include_spip('inc/flickr_api');
	$contexts[$id_photo] = flickr_photos_getContext($id_photo);
  }
  return $contexts[$id_photo][$tag][$attr];
}


?>
