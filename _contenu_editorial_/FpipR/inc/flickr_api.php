<?php

$GLOBALS['FLICKR_API_KEY'] = "7356b4277d182c151e676903c064edec";
$GLOBALS['FLICKR_SECRET'] = "8dd0abb691ff659d";

function flickr_sign($params) {
  global $FLICKR_SECRET;
  ksort($params);
  $ret = $FLICKR_SECRET;
  foreach($params as $k => $v) {
	$ret .= $k.$v;
  }

  return md5($ret);
}

function flickr_api_call($method, $params=array()) {
  global $FLICKR_API_KEY;
  $params['api_key'] = $FLICKR_API_KEY;
  $params['method'] = $method;
  $params['api_sig'] = flickr_sign($params);
  
  $args = '';
  foreach($params as $k => $v) {
	$args .= '&'.$k.'='.$v;
  }

  $args = substr($args,1);

  return file_get_contents("http://www.flickr.com/services/rest/?$args");
}

function flickr_authenticate_get_frob() {
  global $FLICKR_API_KEY;
  $frob = flickr_api_call('flickr.auth.getFrob');
  if(preg_match('#<frob>(.*?)</frob>#', $frob, $matches)) {
	$params['api_key'] = $FLICKR_API_KEY;
	$params['frob'] = $matches[1];
	$params['perms'] = 'read';
	$params['api_sig'] = flickr_sign($params);
  
	$args = '';
	foreach($params as $k => $v) {
	  $args .= '&'.$k.'='.$v;
	}
	$args = substr($args,1);
	return array('url'=>"http://flickr.com/services/auth/?$args",'frob'=>$matches[1]);
  } else spip_log($frob);
}

function flickr_authenticate_end($id_auteur,$frob) {
  $frob = flickr_api_call('flickr.auth.getToken',array('frob'=>$frob));
  $nsid='';
  $token = '';
  if(preg_match('#<token>(.*?)</token>#', $frob, $matches)) {
	$token = $matches[1];
  }
  if(preg_match('#<user nsid="(.*?)"#', $frob, $matches)) {
	$nsid = $matches[1];
  } 
  if(isset($token) && isset($nsid))	{
	include_spip('base/abstract_sql');
	global $table_prefix;
	spip_query("UPDATE ".$table_prefix."_auteurs SET flickr_nsid = '$nsid', flickr_token = '$token' WHERE id_auteur=$id_auteur");
  } else spip_log($frob);
}

?>
