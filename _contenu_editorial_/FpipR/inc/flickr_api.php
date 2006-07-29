<?php


  //Attention, cette api_key est seulement pour les utilisations non commerciale. Si vous avez besoin d'utiliser ce plugin avec un site commercial, vous devrez demander votre propre clef (et secret) à Flickr.
$GLOBALS['FLICKR_API_KEY'] = "7356b4277d182c151e676903c064edec";
$GLOBALS['FLICKR_SECRET'] = "8dd0abb691ff659d";

//calcule la signature de la requette
function flickr_sign($params) {
  global $FLICKR_SECRET;
  ksort($params);
  $ret = $FLICKR_SECRET;
  foreach($params as $k => $v) {
	$ret .= $k.$v;
  }

  return md5($ret);
}

//lance une requette à flickr. $method est le nom de la méthode à appeler (flickr.auth.getToken par exemple) $params est un tableau de paramétres nom => valeur. Retourne le xml renvoye par Flickr 
function flickr_api_call($method, $params=array(), $auth_token='') {
  global $FLICKR_API_KEY;
  $params['api_key'] = $FLICKR_API_KEY;
  if($auth_token) $params['auth_token'] = $auth_token;
  $params['method'] = $method;
  $params['api_sig'] = flickr_sign($params);
  
  $args = '';
  foreach($params as $k => $v) {
	$args .= '&'.$k.'='.$v;
  }

  $args = substr($args,1);

  return file_get_contents("http://www.flickr.com/services/rest/?$args");
}

function flickr_check_error($resp) {
  include_spip('inc/plugin');
  $xml = parse_plugin_xml($resp);
  if(isset($xml['rsp stat="ok"'])) {
	return $xml['rsp stat="ok"'][0];
  } else if(isset($xml['rsp stat="fail"'])) {
	foreach(array_keys($xml['rsp stat="fail"'][0]) as $k) {
	  spip_log('Flickr Error: '.preg_replace('/err/','',$k));
	}
	return false;
  } else return 'cannot understand response';
}

//======================================================================
// Authentification
//======================================================================

function flickr_authenticate_get_frob() {
  global $FLICKR_API_KEY;
  $frob = flickr_check_error(flickr_api_call('flickr.auth.getFrob'));
  if(isset($frob['frob'])) {
	$params['api_key'] = $FLICKR_API_KEY;
	$params['frob'] = $frob['frob'][0];
	$params['perms'] = 'read';
	$params['api_sig'] = flickr_sign($params);
  
	$args = '';
	foreach($params as $k => $v) {
	  $args .= '&'.$k.'='.$v;
	}
	$args = substr($args,1);
	return array('url'=>"http://flickr.com/services/auth/?$args",'frob'=>$frob['frob'][0]);
  }
  return '';
}

function flickr_authenticate_end($id_auteur,$frob) {
  $frob = flickr_check_error(flickr_api_call('flickr.auth.getToken',array('frob'=>$frob)));
  $nsid='';
  $token = '';
  if(isset($frob['auth'])) {
	foreach ($frob['auth'][0] as $k => $v) {
	  if((strpos('user',$k) == 0) && preg_match('#nsid="(.*?)"#', $k, $matches)) 
		$nsid = $matches[1];
	  else if($k == 'token') $token = $v[0];
	}
	if(isset($token) && isset($nsid))	{
	  include_spip('base/abstract_sql');
	  global $table_prefix;
	  spip_query("UPDATE ".$table_prefix."_auteurs SET flickr_nsid = '$nsid', flickr_token = '$token' WHERE id_auteur=$id_auteur");
	} 
  }
}

//======================================================================

class Photos {
  var $page;
  var $per_page;
  var $total;
  var $pages;

  var $photos = array();
}

class Photo {
  var $id;
  var $owner;
  var $secret;
  var $server;
  var $title;
  var $ispublic;
  var $isfriend;
  var $isfamilly;
  var $originalformat;
  
  /*
   s	small square 75x75
   t	thumbnail, 100 on longest side
   m	small, 240 on longest side
   medium, 500 on longest side
   b	large, 1024 on longest side (only exists for very large original images)
   o	original image, either a jpg, gif or png, depending on source format
  */
  function source($size='') {
	return "http://static.flickr.com/".$this->server."/".$this->id."_".$this->secret.($size?"_$size":'').'.'.(($size=='o')?$this->originalformat:'jpg');
  }

  function url() {
	return "http://www.flickr.com/photos/".$this->owner.'/'.$this->id;
  }
}

class PhotoSet {
  var $owner;
  var $id;
  var $primary;
  var $secret;
  var $server;
  var $photos;
  var $title;
  var $description;

    /*
   s	small square 75x75
   t	thumbnail, 100 on longest side
   m	small, 240 on longest side
   medium, 500 on longest side
   b	large, 1024 on longest side (only exists for very large original images)
  */
  function logo($size='') {
	return "http://static.flickr.com/".$this->server."/".$this->primary."_".$this->secret."_$size.jpg";
  }

  function url() {
	return "http://www.flickr.com/photos/".$this->owner.'/sets/'.$this->id;
  }

}

//======================================================================

function flickr_photos_search(
							  $per_page='',$page='',
							  $user_id = '', 
							  $tags = '', $tag_mode='',
							  $text = '',
							  $min_upload_date = '',$max_upload_date = '',
							  $min_taken_date = '',$max_taken_date = '',
							  $license = '',
							  $sort = '',
							  $privacy_filter = '',
							  $extras = '',
							  $auth_token = ''
							  ) {
  $params = array();

  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;
  if($user_id) $params['user_id'] = $user_id;
  if($tags) $params['tags'] = $tags;
  if($tag_mode) $params['tag_mode'] = $tag_mode;
  if($text) $params['text'] = $text;
  if($min_upload_date) $params['min_upload_date'] = $min_upload_date;
  if($max_upload_date) $params['max_upload_date'] = $max_upload_date;
  if($min_taken_date) $params['min_taken_date'] = $min_taken_date;
  if($max_taken_date) $params['max_taken_date'] = $max_taken_date;
  if($license) $params['license'] = $license;
  if($sort) $params['sort'] = $sort;
  if($privacy_filter ) $params['privacy_filter '] = $privacy_filter ;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.search',$params,$auth_token));
  $resp = new Photos;
  if($photos) {
	$rez = array_keys($photos);
	$photos = array_keys($photos[$rez[0]][0]);
	$rez = split(' ',$rez[0]);

	foreach($rez as $attr){
	  if(preg_match('#(page|pages|perpage|total)="([0-9]+)"#',$attr,$matches)) {
		$resp->$matches[1] = $matches[2];
	  }
	}

	foreach($photos as $photo) {
	  $new_p = new Photo;
	  foreach(split(' ',$photo) as $attr){
		if(preg_match('#([a-zA-Z_]+)="(.*?)"#',$attr,$matches)) {
		  $new_p->$matches[1] = $matches[2];
		}
	  }
	  $resp->photos[] = $new_p;
	}
  }  
  return $resp;
}

function flickr_photosets_getList($user_id,$auth_token) {
  /*<photosets cancreate="1">
	<photoset id="5" primary="2483" secret="abcdef"
		server="8" photos="4">
		<title>Test</title>
		<description>foo</description>
	</photoset>
	<photoset id="4" primary="1234" secret="832659"
		server="3" photos="12">
		<title>My Set</title>
		<description>bar</description>
	</photoset>
	</photosets>*/


  $photosets =  flickr_check_error(flickr_api_call('flickr.photosets.getList',array('user_id'=>$user_id),$auth_token));
  $resp = array();
  if($photosets) {

	$rez = array_keys($photosets);
	$photosets = $photosets[$rez[0]][0];

	foreach($photosets as $set => $data) {
	  $new_p = new PhotoSet;
	  $new_p->owner = $user_id;
	  foreach(split(' ',$set) as $attr){
		if(preg_match('#([a-zA-Z_]+)="(.*?)"#',$attr,$matches)) {
		  $new_p->$matches[1] = $matches[2];
		}
	  }
	  foreach($data[0] as $k => $v) {
		$new_p->$k = $v[0];
	  }
	  $resp[] = $new_p;
	}
  }  
  return $resp;
}

function flickr_photosets_getPhotos($photoset_id,$extras='',$privacy_filter='',$auth_token) {

  $params = array();
  $params['photoset_id'] = $photoset_id;
  if($privacy_filter) $params['privacy_filter'] = $privacy_filter;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photosets.getPhotos',$params,$auth_token));
  $resp = array();
  if($photos) {
	$rez = array_keys($photos);
	$photos = array_keys($photos[$rez[0]][0]);

	foreach($photos as $photo) {
	  $new_p = new Photo;
	  foreach(split(' ',$photo) as $attr){
		if(preg_match('#([a-zA-Z_]+)="(.*?)"#',$attr,$matches)) {
		  $new_p->$matches[1] = $matches[2];
		}
	  }
	  $resp[] = $new_p;
	}
  }  
  return $resp;

}

?>
