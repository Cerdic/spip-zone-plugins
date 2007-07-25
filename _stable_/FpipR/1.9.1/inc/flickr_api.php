<?php

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
  spip_log("Flickr api call: $method.");
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
	$args .= '&'.urlencode($k).'='.urlencode($v);
  }

  $args = substr($args,1);

  include_spip('inc/distant');
  return unserialize(recuperer_page("http://www.flickr.com/services/rest/?$args"));
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
	  $args .= '&'.urlencode($k).'='.urlencode($v);
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
  var $perpage;
  var $total;
  var $pages;

  var $photos = array();
}

class FlickrPhotoDetails {
  var $id;
  var $secret;
  var $server;
  var $farm;
  var $isfavorite;
  var $license;
  var $rotation;
  var $originalformat;
  var $originalsecret;
  var $owner_nsid;
  var $owner_username;
  var $owner_realname;
  var $owner_location;
  var $title;
  var $description;
  var $visibility_ispublic;
  var $visibility_isfriend;
  var $visibility_isfamily;
  var $date_posted;
  var $date_taken;
  var $date_takengranularity;
  var $date_lastupdate;
  var $comments;
  var $tags;
  var $notes;
  var $urls;

  var $location_latitude;
  var $location_longitude;
  var $location_accuracy;
  
  /*
   s	small square 75x75
   t	thumbnail, 100 on longest side
   m	small, 240 on longest side
   medium, 500 on longest side
   b	large, 1024 on longest side (only exists for very large original images)
   o	original image, either a jpg, gif or png, depending on source format
  */
  function source($size='') {
	return "http://farm".$this->farm.".static.flickr.com/".$this->server."/".$this->id."_".(($size=='o')?$this->originalsecret:$this->secret).($size?"_$size":'').'.'.(($size=='o')?$this->originalformat:'jpg');
  }

}

class FlickrPhoto {
  var $id;
  var $owner;
  var $secret;
  var $server;
  var $farm;
  var $title;
  var $ispublic;
  var $isfriend;
  var $isfamily;
  var $originalformat;
  var $originalsecret;
  var $license='';
  var $dateupload='';
  var $datetaken='';
  var $ownername='';
  var $username = ''; //des fois, flickr envoi username et pas ownername
  var $iconserver='';
  var $lastupdate='';
  var $longitude='';
  var $latitude='';
  var $accuracy='';
  var $dateadded = ''; //seulement quand on vient d'un groupe

  /*
   s	small square 75x75
   t	thumbnail, 100 on longest side
   m	small, 240 on longest side
   medium, 500 on longest side
   b	large, 1024 on longest side (only exists for very large original images)
   o	original image, either a jpg, gif or png, depending on source format
  */
  function source($size='') {
	return "http://farm".$this->farm.".static.flickr.com/".$this->server."/".$this->id."_".(($size=='o')?$this->originalsecret:$this->secret).($size?"_$size":'').'.'.(($size=='o')?$this->originalformat:'jpg');
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
  var $farm;
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
	return "http://farm".$this->farm.".static.flickr.com/".$this->server."/".$this->primary."_".$this->secret."_$size.jpg";
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
							  $per_page=NULL,$page=NULL,
							  $user_id = NULL, 
							  $tags = NULL, $tag_mode=NULL,
							  $text = NULL,
							  $min_upload_date = NULL,$max_upload_date = NULL,
							  $min_taken_date = NULL,$max_taken_date = NULL,
							  $license = NULL,
							  $sort = NULL,
							  $privacy_filter = NULL,
							  $extras = NULL,
							  $bbox = NULL,
							  $accuracy = NULL,
							  $auth_token = NULL
							  ) {
  $params = array();

  if($per_page!= NULL) $params['per_page'] = $per_page;
  if($page!= NULL) $params['page'] = $page;
  if($user_id!= NULL) $params['user_id'] = $user_id;
  if($tags!= NULL) $params['tags'] = $tags;
  if($tag_mode!= NULL) $params['tag_mode'] = $tag_mode;
  if($text!= NULL) $params['text'] = $text;
  if($min_upload_date!= NULL) $params['min_upload_date'] = $min_upload_date;
  if($max_upload_date!= NULL) $params['max_upload_date'] = $max_upload_date;
  if($min_taken_date!= NULL) $params['min_taken_date'] = $min_taken_date;
  if($max_taken_date!= NULL) $params['max_taken_date'] = $max_taken_date;
  if($license != NULL) $params['license'] = $license;
  if($sort!= NULL) $params['sort'] = $sort;
  if($privacy_filter != NULL) $params['privacy_filter'] = $privacy_filter ;

  if($extras!= NULL) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  if($bbox != NULL) $params['bbox'] = $bbox ;
  if($accuracy != NULL) $params['accuracy'] = $accuracy ;

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.search',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}

//======================================================================

function flickr_photos_getWithGeoData(
							  $per_page=NULL,$page=NULL,
							  $min_upload_date = NULL,$max_upload_date = NULL,
							  $min_taken_date = NULL,$max_taken_date = NULL,
							  $sort = NULL,
							  $privacy_filter = NULL,
							  $extras = NULL,
							  $auth_token = NULL
							  ) {
  $params = array();

  if($per_page!= NULL) $params['per_page'] = $per_page;
  if($page!= NULL) $params['page'] = $page;
  if($min_upload_date!= NULL) $params['min_upload_date'] = $min_upload_date;
  if($max_upload_date!= NULL) $params['max_upload_date'] = $max_upload_date;
  if($min_taken_date!= NULL) $params['min_taken_date'] = $min_taken_date;
  if($max_taken_date!= NULL) $params['max_taken_date'] = $max_taken_date;
  if($sort!= NULL) $params['sort'] = $sort;
  if($privacy_filter != NULL) $params['privacy_filter'] = $privacy_filter ;

  if($extras!= NULL) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getWithGeoData',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}

function flickr_photos_getWithoutGeoData(
							  $per_page=NULL,$page=NULL,
							  $min_upload_date = NULL,$max_upload_date = NULL,
							  $min_taken_date = NULL,$max_taken_date = NULL,
							  $sort = NULL,
							  $privacy_filter = NULL,
							  $extras = NULL,
							  $auth_token = NULL
							  ) {
  $params = array();

  if($per_page!= NULL) $params['per_page'] = $per_page;
  if($page!= NULL) $params['page'] = $page;
  if($min_upload_date!= NULL) $params['min_upload_date'] = $min_upload_date;
  if($max_upload_date!= NULL) $params['max_upload_date'] = $max_upload_date;
  if($min_taken_date!= NULL) $params['min_taken_date'] = $min_taken_date;
  if($max_taken_date!= NULL) $params['max_taken_date'] = $max_taken_date;
  if($sort!= NULL) $params['sort'] = $sort;
  if($privacy_filter != NULL) $params['privacy_filter'] = $privacy_filter ;

  if($extras!= NULL) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getWithoutGeoData',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}

function flickr_photos_recentlyUpdated(
							  $per_page=NULL,$page=NULL,
							  $min_date = NULL,
							  $extras = NULL,
							  $auth_token = NULL
							  ) {
  $params = array();

  if($per_page!= NULL) $params['per_page'] = $per_page;
  if($page!= NULL) $params['page'] = $page;
  if($min_date!= NULL) $params['min_date'] = $min_date;

  if($extras!= NULL) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.recentlyUpdated',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}



function flickr_photos_getUntagged(
							  $per_page=NULL,$page=NULL,
							  $min_upload_date = NULL,$max_upload_date = NULL,
							  $min_taken_date = NULL,$max_taken_date = NULL,
							  $privacy_filter = NULL,
							  $extras = NULL,
							  $auth_token = NULL
							  ) {
  $params = array();

  if($per_page!= NULL) $params['per_page'] = $per_page;
  if($page!= NULL) $params['page'] = $page;
  if($min_upload_date!= NULL) $params['min_upload_date'] = $min_upload_date;
  if($max_upload_date!= NULL) $params['max_upload_date'] = $max_upload_date;
  if($min_taken_date!= NULL) $params['min_taken_date'] = $min_taken_date;
  if($max_taken_date!= NULL) $params['max_taken_date'] = $max_taken_date;
  if($privacy_filter != NULL) $params['privacy_filter'] = $privacy_filter ;

  if($extras!= NULL) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getUntagged',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}


function flickr_photos_getNotInSet(
							  $per_page=NULL,$page=NULL,
							  $min_upload_date = NULL,$max_upload_date = NULL,
							  $min_taken_date = NULL,$max_taken_date = NULL,
							  $privacy_filter = NULL,
							  $extras = NULL,
							  $auth_token = NULL
							  ) {
  $params = array();

  if($per_page!= NULL) $params['per_page'] = $per_page;
  if($page!= NULL) $params['page'] = $page;
  if($min_upload_date!= NULL) $params['min_upload_date'] = $min_upload_date;
  if($max_upload_date!= NULL) $params['max_upload_date'] = $max_upload_date;
  if($min_taken_date!= NULL) $params['min_taken_date'] = $min_taken_date;
  if($max_taken_date!= NULL) $params['max_taken_date'] = $max_taken_date;
  if($privacy_filter != NULL) $params['privacy_filter'] = $privacy_filter ;

  if($extras!= NULL) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getNotInSet',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}

function flickr_photos_getRecent(
							  $per_page=NULL,$page=NULL,
							  $extras = NULL,
							  $auth_token = NULL
							  ) {
  $params = array();

  if($per_page!= NULL) $params['per_page'] = $per_page;
  if($page!= NULL) $params['page'] = $page;
  if($extras!= NULL) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getRecent',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
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
  return flickr_utils_createPhotoSets($photosets['photosets'],$user_id);
}

function flickr_utils_createPhotoSets($photosets,$user_id='') {
  $resp = array();
	foreach($photosets['photoset'] as $set) {	
	  $resp[] = flickr_utils_createOnePhotoSet($set,$user_id);
	}
	return $resp;
}

function flickr_utils_createOnePhotoSet($photoset,$user_id='') {
	  $new_p = new FlickrPhotoSet;
	  $new_p->owner = $user_id;
	  foreach($photoset as $key => $value) {
		if(is_array($value))
		  $new_p->$key = $value['_content'];
		else
		  $new_p->$key = $value;
	  }
	return $new_p;
}

function flickr_photosets_getInfo($photoset_id,$auth_token='') {
  if(!$photoset_id) return false;
  $photoset =  flickr_check_error(flickr_api_call('flickr.photosets.getInfo',array('photoset_id'=>$photoset_id),$auth_token));
  return  flickr_utils_createOnePhotoSet($photoset['photoset']);
}

function flickr_photosets_getPhotos($photoset_id,$extras='',$per_page='',$page='',$privacy_filter='',$auth_token='') {
  $params = array();
  $params['photoset_id'] = $photoset_id;
  if($privacy_filter) $params['privacy_filter'] = $privacy_filter;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  $photos =  flickr_check_error(flickr_api_call('flickr.photosets.getPhotos',$params,$auth_token));
  return flickr_utils_createPhotos($photos,'photoset');
}

function flickr_utils_createPhotos($photos,$key='photos') {
  $resp = new FlickrPhotos;
  if($photos = $photos[$key]) {
	$resp->page = $photos['page'];
	$resp->pages = $photos['pages'];
	$resp->perpage = $photos['perpage'];
	$resp->total = $photos['total'];
	
	foreach($photos['photo'] as $photo) {
	  $new_p = new FlickrPhoto;
	  foreach($photo as $key => $value) {
		if(is_array($value))
		  $new_p->$key = $value['_content'];
		else
		  $new_p->$key = $value;
	  }
	  $resp->photos[] = $new_p;
	}
  }  
  return $resp;
}

function flickr_photos_getInfo($photo_id,$secret='',$auth_token='') {
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
		  $resp->urls[$urldata['type']] = $urldata['_content'];
	  } else if ($key == 'dates'){
		foreach($value as $attr => $content) {
		  $k = 'date_'.$attr;
		  $resp->$k = $content;
		}
	  } else if ($key == 'title' || $key == 'description' || $key == 'comments'){
		$resp->$key = $value['_content'];
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

//retourne le tableau php de base Flickr
function flickr_photos_getContext($photo_id,$auth_token='') {
  $params= array(
				 'photo_id' => $photo_id
				 );
  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getContext',$params,$auth_token));
  return $photos;
}
//retourne un tableau php:
function flickr_photos_getAllContexts($photo_id,$auth_token='') {
    $params= array(
				 'photo_id' => $photo_id
				 );
  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getAllContexts',$params,$auth_token));
  return $photos;
}


//retourne le tableau php de base Flickr
function flickr_photosets_getContext($photo_id,$photoset_id,$auth_token='') {
  $params= array(
				 'photo_id' => $photo_id,
				 'photoset_id' => $photoset_id
				 );
  $photos =  flickr_check_error(flickr_api_call('flickr.photosets.getContext',$params,$auth_token));
  return $photos;
}

//retourne le tableau php de base Flickr
function flickr_groups_pools_getContext($photo_id,$group_id,$auth_token='') {
  $params= array(
				 'photo_id' => $photo_id,
				 'group_id' => $group_id
				 );
  $photos =  flickr_check_error(flickr_api_call('flickr.groups.pools.getContext',$params,$auth_token));
  return $photos;
}

//http://www.flickr.com/services/api/flickr.interestingness.getList.html
//retourne un FlickrPhotos
function flickr_interestingness_getList($date,$extras,$per_page,$page,$auth_token='') {
  $params = array();
  if($date) $params['date'] = $date;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  $photos =  flickr_check_error(flickr_api_call('flickr.interestingness.getList',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}

function flickr_groups_pools_getPhotos($group_id, $tags, $user_id, $extras, $per_page, $page, $auth_token='') {
  $params = array();
  if($group_id) $params['group_id'] = $group_id;
  else return false;
  if($tags) $params['tags'] = $tags;
  if($user_id) $params['user_id'] = $user_id;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  $photos =  flickr_check_error(flickr_api_call('flickr.groups.pools.getPhotos',$params,$auth_token));
  return flickr_utils_createPhotos($photos);
}

function flickr_tags_getListPhoto($photo_id,$auth_token='') {
  $params = array();
  if($photo_id) $params['photo_id'] = $photo_id;
  else return false;
  $p = flickr_check_error(flickr_api_call('flickr.tags.getListPhoto',$params,$auth_token));
  
  $tags = array();
  foreach($p['photo']['tags']['tag'] as $tag) { 
	$t = new FlickrTag;
	$t->safe = $tag['_content'];
	$t->id = $tag['id'];
	$t->raw = $tag['raw'];
	$t->author = $tag['author'];
		  
	$tags[] = $t;
  }
  return $tags;
}

function flickr_photos_getContactsPublicPhotos($user_id,$count,$just_friend,$single_photo,$include_self,$extras,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;
  if($count) $params['count'] = ($count>50)?50:$count;
  if($just_friend) $params['just_friend'] = 1;
  if($single_photo) $params['single_photo'] = 1;
  if($include_self) $params['include_self'] = 1;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getContactsPublicPhotos',$params,$auth_token));
  return flickr_utils_createPhotos($photos);  
}

function flickr_photos_getContactsPhotos($count,$just_friend,$single_photo,$include_self,$extras,$auth_token='') {
  $params = array();
  if($count) $params['count'] = ($count>50)?50:$count;
  if($just_friend) $params['just_friend'] = 1;
  if($single_photo) $params['single_photo'] = 1;
  if($include_self) $params['include_self'] = 1;
  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";

  $photos =  flickr_check_error(flickr_api_call('flickr.photos.getContactsPhotos',$params,$auth_token));
  return flickr_utils_createPhotos($photos);  
}


function flickr_favorites_getPublicList($user_id,$extras,$per_page,$page,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";
  $photos =  flickr_check_error(flickr_api_call('flickr.favorites.getPublicList',$params,$auth_token));
  return flickr_utils_createPhotos($photos);  
}

function flickr_favorites_getList($user_id,$extras,$per_page,$page,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  if($extras) $params['extras'] = "original_format,$extras"; 
  else $params['extras'] = "original_format";
  $photos =  flickr_check_error(flickr_api_call('flickr.favorites.getList',$params,$auth_token));
  return flickr_utils_createPhotos($photos);  
}


function flickr_photos_comments_getList($photo_id,$auth_token='') {
  $params = array();
  if($photo_id) $params['photo_id'] = $photo_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.photos.comments.getList',$params,$auth_token));
}

function flickr_photosets_comments_getList($photoset_id,$auth_token='') {
  $params = array();
  if($photoset_id) $params['photoset_id'] = $photoset_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.photosets.comments.getList',$params,$auth_token));
}

function flickr_photos_getPerms($photo_id,$auth_token='') {
  $params = array();
  if($photo_id) $params['photo_id'] = $photo_id;
  else return false;
  return flickr_check_error(flickr_api_call('flickr.photos.getPerms',$params,$auth_token));
}

function flickr_photos_geo_getLocation($photo_id,$auth_token='') {
  $params = array();
  if($photo_id) $params['photo_id'] = $photo_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.photos.geo.getLocation',$params,$auth_token));
}

function flickr_groups_getInfo($group_id,$auth_token='') {
  $params = array();
  if($group_id) $params['group_id'] = $group_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.groups.getInfo',$params,$auth_token));
}

function flickr_urls_lookupGroup($url,$auth_token='') {
  $params = array();
  if($url) $params['url'] = $url;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.urls.lookupGroup',$params,$auth_token));
}

function flickr_urls_lookupUser($url,$auth_token='') {
  $params = array();
  if($url) $params['url'] = $url;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.urls.lookupUser',$params,$auth_token));
}


function flickr_people_getPublicGroups($user_id,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.people.getPublicGroups',$params,$auth_token));
}

function flickr_groups_pools_getGroups($page,$per_page,$auth_token='') {
  $params = array();
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  return flickr_check_error(flickr_api_call('flickr.groups.pools.getGroups',$params,$auth_token));
}

function flickr_people_getInfo($user_id,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.people.getInfo',$params,$auth_token));
}


function flickr_contacts_getPublicList($user_id,$page,$per_page,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  return flickr_check_error(flickr_api_call('flickr.contacts.getPublicList',$params,$auth_token));
}

function flickr_contacts_getList($user_id,$filter,$page,$per_page,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;
  if($filter) $params['filter'] = $filter;
  if($per_page) $params['per_page'] = $per_page;
  if($page) $params['page'] = $page;

  return flickr_check_error(flickr_api_call('flickr.contacts.getList',$params,$auth_token));
}


function flickr_urls_getGroup($group_id,$auth_token='') {
  $params = array();
  if($group_id) $params['group_id'] = $group_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.urls.getGroup',$params,$auth_token));
}

function flickr_urls_getUserPhotos($user_id,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.urls.getUserPhotos',$params,$auth_token));
}

function flickr_urls_getUserProfile($user_id,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.urls.getUserProfile',$params,$auth_token));
}

function flickr_tags_getListUser($user_id,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.tags.getListUser',$params,$auth_token));
}

function flickr_tags_getListUserRaw($tag,$auth_token='') {
  $params = array();
  if($tag) $params['tag'] = $tag;

  return flickr_check_error(flickr_api_call('flickr.tags.getListUserRaw',$params,$auth_token));
}

function flickr_tags_getRelated($tag,$auth_token='') {
  $params = array();
  if($tag) $params['tag'] = $tag;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.tags.getRelated',$params,$auth_token));
}

function flickr_tags_getListUserPopular($user_id,$count,$auth_token='') {
  $params = array();
  if($user_id) $params['user_id'] = $user_id;
  else return false;
  if($count) $params['count'] = $count;

  return flickr_check_error(flickr_api_call('flickr.tags.getListUserPopular',$params,$auth_token));
}


function flickr_tags_getHotList($period,$count,$auth_token='') {
  $params = array();
  if($period) $params['period'] = $period;
  if($count) $params['count'] = $count;

  return flickr_check_error(flickr_api_call('flickr.tags.getHotList',$params,$auth_token));
}

function flickr_photos_getExif($photo_id,$secret='',$auth_token='') {
  $params = array();
  if($photo_id) $params['photo_id'] = $photo_id;
  else return false;
  if($secret) $params['secret'] = $secret;

  return flickr_check_error(flickr_api_call('flickr.photos.getExif',$params,$auth_token));

}

function flickr_photos_getSizes($photo_id,$auth_token=''){
  $params = array();
  if($photo_id) $params['photo_id'] = $photo_id;
  else return false;

  return flickr_check_error(flickr_api_call('flickr.photos.getSizes',$params,$auth_token));
}

//======================================================================

function flickr_bookmarklet_info() {
  return  '<h3>'._T('fpipr:bookmarklet').'</h3>'._T('fpipr:bookmarklet_info',array('url'=>'javascript:var fpipr_retour="'.generer_url_ecrire('flickr_bookmarklet_photo').'";var bookmarkletURL="'.$GLOBALS['meta']['adresse_site'].'/'._DIR_RESTREINT_ABS.find_in_path('fpipr_bookmarklet.js').'"; var script=document.createElement("script");script.type="text/javascript";script.src=bookmarkletURL;var head=document.getElementsByTagName("head")[0];head.appendChild(script);void 0;'));
}

?>
