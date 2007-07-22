<?php
  //
	//on crée la colonne pour stoquer les frobs

$installe = unserialize(lire_meta('FpipR:installe'));

	if(!$installe) {
	  spip_query("ALTER TABLE `".$GLOBALS['table_prefix']."_auteurs` ADD (`flickr_token` TINYTEXT NULL, `flickr_nsid` TINYTEXT NULL);");
	  ecrire_meta('FpipR:installe',serialize(true)); //histoire de pas faire une recherche dans la base à chaque coup
	  ecrire_metas();
	}

  //
function generer_url_document_flickr($id_document, $statut='') {
  if (intval($id_document) <= 0) 
	return '';
  $row = @spip_fetch_array(spip_query("SELECT fichier,distant	FROM spip_documents WHERE id_document = $id_document"));
  if ($row) {
	if ($row['distant'] == 'oui') {
	  if(preg_match('#http://(farm[0-9].)?static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$row['fichier'],$matches)) {
		$id = $matches[3];
		$secret = $matches[4];
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

//on ne peut plus utiliser ce truc pour avoir l'url de la page flickr comme url_document
// parce que le modele emb utilise url_document pour inclure l'image.
/*if (!_DIR_RESTREINT) {
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
  }*/

/***********************************************************************
 *                       Pour les boucles Flickr                        *
 ***********************************************************************/

/*Copie du plugin boucles_xml*/
function FpipR_fill_table_boucle($method,$arguments){
  include_spip('base/FpipR_db');
  FpipR_creer_tables($method);
  $arguments['auth_token'] = FpipR_getAuthToken();
  return FpipR_fill_table($method,$arguments);
}

function FpipR_traiter_argument($key, $val){
  $val = str_replace("'",'',$val);
  if(!$val) $val = '0';
  if($key == 'bbox')
	$val = str_replace(':',',',$val);
  else if(strpos($key,'min_date') !== false || strpos($key,'upload_date') !== false) {
	return strtotime($val);
  }
  return $val;
}


function FpipR_logo_owner($user_id,$server = '') {
  $url = 'http://www.flickr.com/images/buddyicon.jpg';
  if($server) {
	$url ="http://static.flickr.com/$server/buddyicons/$user_id.jpg";
  }
  return '<img src="'.$url.'" width="48" height="48" />';
}


function FpipR_logo_photo($id_photo,$server,$secret,$taille='',$originalformat='jpg',$farm='') {
  if($id_photo) {
	$w = ($taille=='s')?75:FpipR_taille_photo($id_photo,$taille,'width');
	$h = ($taille=='s')?75:FpipR_taille_photo($id_photo,$taille,'height');
	if($server) {
	  return '<img src="http://'.($farm?('farm'.$farm.'.'):'').'static.flickr.com/'.$server."/".$id_photo."_".$secret.($taille?"_$taille":'').'.'.(($taille=='o')?$originalformat:'jpg').'" width="'.$w.'" height="'.$h.'" style="width:'.$w.';height:'.$h.'" />';
	} else {
	  $src = FpipR_taille_photo($id_photo,$taille,'source');
	  return '<img src="'.$src.'" width="'.$w.'" height="'.$h.'" style="width:'.$w.';height:'.$h.'" />';
	}
  }
  return NULL;
}

function FpipR_generer_url_photo($user_id,$id_photo) {
  if($user_id && $id_photo)
	return "http://www.flickr.com/photos/$user_id/$id_photo/";
  else if($id_photo)
	return "http://www.flickr.com/photo.gne?id=$id_photo";
  return NULL;
}

function FpipR_generer_url_owner($user_id,$type) {
  if($user_id) {  
	$auth_token = FpipR_getAuthToken();
	switch($type) {
	  case 1: //photos
		include_spip('inc/flickr_api');
		$url = flickr_urls_getUserPhotos($user_id,$auth_token);
		if($url) return $url['user']['url'];
	  case 2: //profile
		include_spip('inc/flickr_api');
		$url = flickr_urls_getUserProfile($user_id,$auth_token);
		include_spip('inc/flickr_api');
		if($url) return $url['user']['url'];
	  case 0:
	  default:
		return 'http://www.flickr.com/photos/'.$user_id.'/';
		
	}
  } 
  return NULL;
}
function FpipR_generer_url_photoset($user_id,$id_photoset) {
  if($user_id && $id_photoset)
	return 'http://www.flickr.com/photos/'.$user_id.'/sets/'.$id_photoset.'/';
  return NULL;
}

function FpipR_generer_url_group($id) {
  if($id) {
	$auth_token = FpipR_getAuthToken();
	include_spip('inc/flickr_api');
	$url = flickr_urls_getGroup($id,$auth_token);
	if($url)return $url['group']['url'];
	return 'http://www.flickr.com/groups/'.$id;
  }
  return NULL;
}

function FpipR_taille_photo($id_photo,$taille='',$type) {
  static $tailles = array();
  if(!$tailles[$id_photo]) {
	$auth_token = FpipR_getAuthToken();
	$tailles[$id_photo] = flickr_photos_getSizes($id_photo,$auth_token);
  } 
  /*
   s	small square 75x75
   t	thumbnail, 100 on longest side
   m	small, 240 on longest side
   -	medium, 500 on longest side
   b	large, 1024 on longest side (only exists for very large original images)
   o	original image, either a jpg, gif or png, depending on source format
  */
  switch($taille) {
	case 's':
	  $t = 'Square';
	  break;
	case 't':
	  $t = 'Thumbnail';
	  break;
	case 'm':
	  $t = 'Small';
	  break;
	case 'b':
	  $t = 'Large';
	  break;
	case 'o':
	  $t = 'Original';
	  break;
	default:
	  $t = 'Medium';
  }
  if(is_array($tailles[$id_photo]))
	foreach($tailles[$id_photo]['sizes']['size'] as $size) {
	  if($size['label'] == $t) {
		return $size[$type];
	  }
	}
  return '';
}

function FpipR_photos_getContext($id_photo,$id_photoset='',$id_group='',$tag,$attr) {
  static $contexts = array();
  if($id_photoset) {
	if(!$contexts["$id_photo-$id_photoset"]) { 
	  include_spip('inc/flickr_api');
	  $auth_token = FpipR_getAuthToken();
	  $contexts["$id_photo-$id_photoset"] = flickr_photosets_getContext($id_photo,$id_photoset,$auth_token);
	}
	return $contexts["$id_photo-$id_photoset"][$tag][$attr];
  } else if($id_group) {
	if(!$contexts["$id_photo-$id_group"]) { 
	  include_spip('inc/flickr_api');
	  $auth_token = FpipR_getAuthToken();
	  $contexts["$id_photo-$id_group"] = flickr_groups_pools_getContext($id_photo,$id_group,$auth_token);
	}
	return $contexts["$id_photo-$id_group"][$tag][$attr];
	} else {
	if(!$contexts[$id_photo]) { 
	  include_spip('inc/flickr_api');
	  $auth_token = FpipR_getAuthToken();
	  $contexts[$id_photo] = flickr_photos_getContext($id_photo,$auth_token);
	}
	return $contexts[$id_photo][$tag][$attr];
	}
}

function FpipR_photos_getPerms($id_photo,$perm) {
  static $perms;
  if(!$perms[$id_photo]) {
	  include_spip('inc/flickr_api');
	  $auth_token = FpipR_getAuthToken();
	  $perms[$id_photo] = flickr_photos_getPerms($id_photo,$auth_token);
  } 
  return $perms[$id_photo]['perms'][$perm];
}

function FpipR_photos_geo_getLocation($id_photo,$location) {
  static $locations;
  if(!$locations[$id_photo]) {
	include_spip('inc/flickr_api');
	$auth_token = FpipR_getAuthToken();
	$locations[$id_photo] = flickr_photos_geo_getLocation($id_photo,$auth_token);
  } 
  return $locations[$id_photo]['location'][$location];
}

function FpipR_get_flickr_photo_id($fichier) {
  if(preg_match('#http://(farm[0-9].)?static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$fichier,$matches))
		return $matches[3];
	return NULL;
}

function FpipR_get_flickr_photo_secret($fichier) {
  if(preg_match('#http://(farm[0-9].)?static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$fichier,$matches))
		return $matches[4];
  return NULL;
	
}

function FpipR_calcul_argument_page($debut,$pas) {
  $page = intval($debut/$pas)+1;
  if($page == 1)  {
	$pas = $pas+$debut;
  } else
	$pas = $pas+$debut-($page-1)*$pas;
  return array($page,$pas>0?intval($pas):100);
}

//----------------------------------------------------------------------
// fonction d'affichage pour exec/flickr_bookmarklet_photo.php
//----------------------------------------------------------------------
function flickr_afficher_articles_boucle($row)
{
	global $connect_id_auteur, $dir_lang, $spip_lang_right;
  $vals = '';

  $id_article = intval($row['id_article']);
  $tous_id[] = $id_article;
  $titre = sinon($row['titre'], _T('ecrire:info_sans_titre'));
  $id_rubrique = $row['id_rubrique'];
  $date = $row['date'];
  $statut = $row['statut'];
  if ($lang = $row['lang']) changer_typo($lang);

  // La petite puce de changement de statut
  $vals[] = puce_statut_article($id_article, $statut, $id_rubrique);

  // Le titre (et la langue)
  $s = "<div>";

  if (acces_restreint_rubrique($id_rubrique))
	$s .= http_img_pack("admin-12.gif", "", "width='12' height='12'", _T('titre_image_admin_article'));

  $s .= "<a href='" .generer_url_ecrire('articles',"id_article=$id_article")."'$descriptif$dir_lang style=\"display:block;\">";
	
  $s .= typo($titre);
  $s .= "</a>";
  $s .= "</div>";
	
  $vals[] = $s;

  // La date
  $vals[] = affdate_jourcourt($date);
	$vals[]='';

  $input .= '<input type="radio" name="id" value="'.$id_article.'"/>';

  $vals[] = $input;

  
  if ($options == "avancees") { // Afficher le numero (JMB)
	$largeurs = array(11, '', 80, 100, 50);
	$styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
  } else {
	$largeurs = array(11, '', 100, 100);
	$styles = array('', 'arial2', 'arial1', 'arial1');
  }
  
  return ($spip_display != 4)
	? afficher_liste_display_neq4($largeurs, $vals, $styles)
	: afficher_liste_display_eq4($largeurs, $vals, $styles);
  
}
//----------------------------------------------------------------------



//======================================================================
// Histoire d'authentification
//======================================================================

if (isset($auteur_session['id_auteur'])) {
  $combins = $auteur_session['id_auteur'];
  if (!isset($GLOBALS['marqueur'])) {
	$GLOBALS['marqueur'] = "";
  }
  $GLOBALS['marqueur'] .= ":FpipR $combins";
 }

function FpipR_getAuthToken() {
  global $auteur_session;
  if(isset($auteur_session['id_auteur']) && (strpos($GLOBALS['marqueur'],':FpipR') >= 0)) {
	$row = spip_fetch_array(spip_query("SELECT flickr_token FROM spip_auteurs WHERE id_auteur=".intval($auteur_session['id_auteur'])));
	if ($row) {
	  return $row['flickr_token'];
	}
  }
  return NULL;
}

?>
