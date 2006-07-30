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
	  if((strpos($k,'user') === 0) && preg_match('#nsid="(.*?)"#', $k, $matches)) 
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

class PhotoDetails {
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

	if(preg_match_all('#(page|pages|perpage|total)="([0-9]+)"#',$rez[0],$matches,PREG_SET_ORDER)) {
	  foreach($matches as $m) $resp->$m[1] = $m[2];
	}


	foreach($photos as $photo) {
	  $new_p = new Photo;
	  if(preg_match_all('#([a-zA-Z_]+)="(.*?)"#',$photo,$matches,PREG_SET_ORDER)) {
		foreach($matches as $m) $new_p->$m[1] = $m[2];
	  }

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
  if($photosets) {
	$rez = array_keys($photosets);
	$photosets = $photosets[$rez[0]][0];

	foreach($photosets as $set => $data) {
	  $new_p = new PhotoSet;
	  $new_p->owner = $user_id;
	  if(preg_match_all('#([a-zA-Z_]+)="(.*?)"#',$set,$matches,PREG_SET_ORDER)) {
		foreach($matches as $m) {
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

function flickr_photosets_getPhotos($photoset_id,$extras='',$privacy_filter='',$auth_token='') {

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
	  if(preg_match_all('#([a-zA-Z_]+)="(.*?)"#',$photo,$matches,PREG_SET_ORDER)) {
		foreach($matches as $m)
		  $new_p->$m[1] = $m[2];
	  }
	  $resp[] = $new_p;
	}
  }  
  return $resp;

}

function flickr_photos_getInfo($photo_id,$secret,$auth_token='') {
  $params = array('photo_id'=>$photo_id,'secret'=>$secret);

  $photo =  flickr_check_error(flickr_api_call('flickr.photos.getInfo',$params,$auth_token));
  $resp = new PhotoDetails;
  if($photo) {
	$rez = array_keys($photo);
	//	$details = array_keys($photo[$rez[0]][0]);


	if(preg_match_all('#(id|secret|server|isfavorite|license|rotation|originalformat)="(.*?)"#',$rez[0],$matches,PREG_SET_ORDER)) {
	  foreach($matches as $m) $resp->$m[1] = $m[2];
	}

	foreach($photo[$rez[0]][0] as $tag => $v) {
	  if(strpos($tag,'title') === 0 || strpos($tag,'description') === 0 || strpos($tag,'comments')===0) {
		$resp->$tag = $v[0];
	  } else if(strpos($tag,'owner') === 0) {
		if(preg_match_all('#([a-zA-Z_]+)="(.*?)"#',$tag,$matches,PREG_SET_ORDER)) {
		  foreach($matches as $m) {
			$name = 'owner_'.$m[1];
			$resp->$name = $m[2];
		  }
		}
	  } else if(strpos($tag,'dates') === 0) {
		if(preg_match_all('#([a-zA-Z_]+)="(.*?)"#',$tag,$matches,PREG_SET_ORDER)) {
		  foreach($matches as $m) {
			$name = 'date_'.$m[1];
			$resp->$name = $m[2];
		  }
		}
	  } else if(strpos($tag,'visibility') === 0) {
		if(preg_match_all('#([a-zA-Z_]+)="(.*?)"#',$tag,$matches,PREG_SET_ORDER)) {
		  foreach($matches as $m) {
			$name = $m[1];
			$resp->$name = $m[2];
		  }
		}
	  } else if(strpos($tag,'tags') === 0) {
		foreach($v[0] as $taginfo => $tag) { 
		  $resp->tags[] = $tag[0];
		}
	  } else if(strpos($tag,'urls') === 0) {
		foreach($v[0] as $urltype => $url) { 
		  if(preg_match('#type="(.*?)"#',$urltype,$matches)) {
			$resp->urls[$matches[1]] = $url[0];
		  }
		}
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

//======================================================================

function flickr_bookmarklet_info() {
	return  '<h3>Bookmarklet</h3><p>Vous pouvez ajouter n\'importe quel photo de Flickr &agrave; vos articles en plaçant ce <a href=\'javascript:var bookmarkletURL="'.$GLOBALS['meta']['adresse_site'].'/ecrire/'.find_in_path('fpipr_bookmarklet.js').'"; var script=document.createElement("script");script.type="text/javascript";script.src=bookmarkletURL;var head=document.getElementsByTagName("head")[0];head.appendChild(script);fpipr_add_photo("'.generer_url_ecrire('flickr_bookmarklet_photo').'")\'>lien</a> dans vos bookmarks.
</p>
<p>Quand vous visitez une photo, en cliquant sur ce bookmark, vous arriverez &agrave; une page pour choisir &agrave; quel article l\'ajouter.</p>';
}

?>
