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
function flickr_api_call($method, $params=array(), $auth_token='', $force_sign=false) {
  global $FLICKR_API_KEY;
  spip_log("Flickr api call: $method");
  $params['api_key'] = $FLICKR_API_KEY;
  $params['format'] = 'php_serial';
  if($auth_token) {
	$params['auth_token'] = $auth_token;
  }
  $params['method'] = $method;
  if($auth_token || $force_sign)
	$params['api_sig'] = flickr_sign($params);

  
  $args = '';
  foreach($params as $k => $v) {
	$args .= '&'.$k.'='.$v;
  }

  $args = substr($args,1);

  return unserialize(file_get_contents("http://www.flickr.com/services/rest/?$args"));
}

function flickr_check_error($resp) {
  if($resp['stat'] == 'ok') {
	return $resp;
  } else if($resp['stat'] == 'fail') {
	  spip_log('Flickr Error: '.$resp['message'].'('.$resp['code'].')');
	return false;
  } else {
	spip_log('cannot understand response');
	spip_log($resp);
	return false;
  }
}

//======================================================================
// Authentification
//======================================================================

function flickr_authenticate_get_frob() {
  global $FLICKR_API_KEY;
  $frob = flickr_check_error(flickr_api_call('flickr.auth.getFrob',array(),false,true));
  if(isset($frob['frob'])) {
	$params['api_key'] = $FLICKR_API_KEY;
	$params['frob'] = $frob['frob']['_content'];
	$params['perms'] = 'read';
	$params['api_sig'] = flickr_sign($params);
  
	$args = '';
	foreach($params as $k => $v) {
	  $args .= '&'.$k.'='.$v;
	}
	$args = substr($args,1);
	return array('url'=>"http://flickr.com/services/auth/?$args",'frob'=>$frob['frob']['_content']);
  }
  return '';
}

function flickr_authenticate_end($id_auteur,$frob) {
  $resp = flickr_api_call('flickr.auth.getToken',array('frob'=>$frob),false,true);
  $frob = flickr_check_error($resp);
  $nsid='';
  $token = '';

  if(isset($frob['auth'])) {
	$nsid = $frob['auth']['user']['nsid'];
	$token = $frob['auth']['token']['_content'];

	if(isset($token) && isset($nsid))	{
	  include_spip('base/abstract_sql');
	  global $table_prefix;
	  spip_query("UPDATE ".$table_prefix."_auteurs SET flickr_nsid = '$nsid', flickr_token = '$token' WHERE id_auteur=$id_auteur");
	} 
  }
}

function flickr_auth_checkToken($token) {
  /*<rsp stat="fail">
	 <err code="98" msg="Invalid auth token"/>
	 </rsp>
	 OR
<auth>
	<token>976598454353455</token>
	<perms>read</perms>
	<user nsid="12037949754@N01" username="Bees" fullname="Cal H" />
</auth>*/
  $check = flickr_check_error(flickr_api_call('flickr.auth.checkToken',array('auth_token'=>$token),false,true));
  if(isset($check['auth'])) {
	$auth_info = array();
	foreach($check['auth'] as $t => $v) {
	  if($t == 'user') {		
		$auth_info['user_fullname'] = $t['fullname'];
		$auth_info['user_nsid'] = $t['nsid'];
		$auth_info['user_username'] = $t['username'];
	  } else {
		$auth_info[$t] = $v['_content'];
	  }
	}
	return $auth_info;
  } else return false;
}

//======================================================================

class FlickrPhotos {
  var $page;
  var $per_page;
  var $total;
  var $pages;

  var $photos = array();
}

class FlickrPhotoDetails {
  var $id;
  var $secret;
  var $server;
  var $isfavorite;
  var $license;
  var $rotation;
  var $originalformat;
  var $owner_nsid;
  var $owner_username;
  var $owner_realname;
  var $owner_location;
  var $title;
  var $description;
  var $ispublic;
  var $isfriend;
  var $isfamily;
  var $date_posted;
  var $date_taken;
  var $date_takengranularity;
  var $date_lastupdate;
  var $comments;
  var $tags;
  var $notes;
  var $urls;
  
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

}

class FlickrPhoto {
  var $id;
  var $owner;
  var $secret;
  var $server;
  var $title;
  var $ispublic;
  var $isfriend;
  var $isfamily;
  var $originalformat;
  var $license='';
  var $dateupload='';
  var $datetaken='';
  var $ownername='';
  var $iconserver='';
  var $lastupdate='';
  var $longitude='';
  var $latitude='';
  var $accuracy='';

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

class FlickrPhotoSet {
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

class FlickrLicense {
  var $url;
  var $name;
  var $id;
}

class FlickrTag {
  var $id;
  var $author;
  var $raw;
  var $safe;
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
  $resp = new FlickrPhotos;
  if($photos = $photos['photos']) {
	$resp->page = $photos['page'];
	$resp->pages = $photos['pages'];
	$resp->perpage = $photos['perpage'];
	$resp->total = $photos['total'];

	foreach($photos['photo'] as $photo) {
	  $new_p = new FlickrPhoto;
	  foreach($photo as $key => $value) $new_p->$key = $value;
	  $resp->photos[] = $new_p;
	}
  }  
  return $resp;
}

function flickr_photosets_getList($user_id,$auth_token='') {
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
  if($photosets = $photosets['photosets']) {
	foreach($photosets['photoset'] as $set) {
	  $new_p = new FlickrPhotoSet;
	  $new_p->owner = $user_id;

	  foreach($set as $key => $value) {
		if(is_array($value))
		  $new_p->$key = $value['_content'];
		else
		  $new_p->$key = $value;
	  }

	  $resp[] = $new_p;
	}
  }  
  return $resp;
}

function flickr_photosets_getPhotos($photoset_id,$extras='',$privacy_filter='',$auth_token='') {

  $params = array();
  $params['photoset_id'] = $photoset_id;
  if($privacy_filter) $params['privacy_filter'] = $privacy_filter;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photosets.getPhotos',$params,$auth_token));
  $resp = array();
  if($photos = $photos['photoset']) {
	foreach($photos['photo'] as $photo) {
	  $new_p = new FlickrPhoto;
	  
	  foreach($photo as $key => $value) {
		if(is_array($value))
		  $new_p->$key = $value['_content'];
		else
		  $new_p->$key = $value;
	  }

	  $resp[] = $new_p;
	}
  }  
  return $resp;

}

function flickr_photos_getInfo($photo_id,$secret,$auth_token='') {
  $params = array('photo_id'=>$photo_id,'secret'=>$secret);

  $photo =  flickr_check_error(flickr_api_call('flickr.photos.getInfo',$params,$auth_token));
  $resp = new FlickrPhotoDetails;
  if($photo) {
	foreach($photo['photo'] as $key => $value) {
	  if($key == 'tags') {
		foreach($value['tag'] as $tag) { 
		  $t = new FlickrTag;
		  $t->safe = $tag['_content'];
		  $t->id = $tag['id'];
		  $t->raw = $tag['raw'];
		  $t->author = $tag['author'];
		  
		  $resp->tags[] = $t;
		}
	  } else if($key == 'notes') {
		$resp->notes = $value['note'];
	  } else if($key == 'urls') {
		foreach($value['url'] as $urldata)
		  $resp->urls[$urldate['type']] = $urldata['_content'];
	  } else if(is_array($value)) {
		foreach($value as $attr => $content) {
		  if(is_array($content)) 
			$resp->$key = $content['_content'];
		  else {
			$k = $key.'_'.$attr;
			$resp->$k = $content;
		  }			  
		}
	  } else {
		$resp->$key = $value;

	  }
	}		
  }

  return $resp;

  /*
   <photo id="2733" secret="123456" server="12"
	isfavorite="0" license="3" rotation="90" originalformat="png">
	<owner nsid="12037949754@N01" username="Bees"
		realname="Cal Henderson" location="Bedford, UK" />
	<title>orford_castle_taster</title>
	<description>hello!</description>
	<visibility ispublic="1" isfriend="0" isfamily="0" />
	<dates posted="1100897479" taken="2004-11-19 12:51:19"
		takengranularity="0" lastupdate="1093022469" />
	<permissions permcomment="3" permaddmeta="2" />
	<editability cancomment="1" canaddmeta="1" />
	<comments>1</comments>
	<notes>
		<note id="313" author="12037949754@N01"
			authorname="Bees" x="10" y="10"
			w="50" h="50">foo</note>
	</notes>
	<tags>
		<tag id="1234" author="12037949754@N01" raw="woo yay">wooyay</tag>
		<tag id="1235" author="12037949754@N01" raw="hoopla">hoopla</tag>
	</tags>
	<urls>
		<url type="photopage">http://www.flickr.com/photos/bees/2733/</url> 
	</urls>
  */
}

function flickr_photos_licenses_getInfo() {
  $licenses =  flickr_check_error(flickr_api_call('flickr.photos.licenses.getInfo',array()));
  $larray = array();
  if($licenses = $licenses['licenses']) {
	foreach($licenses as $l) {
	  $lic = new FlickrLicense;
	  foreach($l as $k => $v) {
		$lic->$k = $v;
	  }
	  $larray[$lic->id] = $lic;
	}	
  }
  return $larray;
  /*<licenses>
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
		</licenses>*/
}

//======================================================================

function flickr_bookmarklet_info() {
  return  '<h3>'._T('fpipr:bookmarklet').'</h3>'._T('fpipr:bookmarklet_info',array('url'=>'javascript:var bookmarkletURL="'.$GLOBALS['meta']['adresse_site'].'/'._DIR_RESTREINT_ABS.find_in_path('fpipr_bookmarklet.js').'"; var script=document.createElement("script");script.type="text/javascript";script.src=bookmarkletURL;var head=document.getElementsByTagName("head")[0];head.appendChild(script);fpipr_add_photo("'.generer_url_ecrire('flickr_bookmarklet_photo').'")'));
}

?>
