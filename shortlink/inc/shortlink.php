<?php

	$api = 'http://api.bit.ly/v3/shorten';
	$conf = array('login'=>'xxxxxxx',
		'apiKey'=>'R_XXXXXXXXXXXXX',
		'format'=>'json',
		'longUrl'=>$url
	);


function shortlink($url) {
	## utiliser la memoization pour ne pas taper trop souvent chez bit.ly

	$urp = str_replace('&amp;', '&', url_absolue($url));

	$cfg = @unserialize($GLOBALS['meta']['shortlink']);

	if (!is_array($cfg)
	OR !$api = $cfg['api']
	OR !strlen($login = $cfg['login']))
	OR !$key = $cfg['key']) {
		spip_log('Erreur de parametrage : '.$GLOBALS['meta']['shortlink'], 'shortlink');
		return quote_amp($urp);
	}

	foreach (array(
		'login'=> $login,
		'apiKey'=> $key
		'format'=>'json',
		'longUrl'=>$urp
	) as $k=>$v) 
		$api = parametre_url($api, $k, $v, '&');

	if (is_object($r = json_decode(recuperer_page($api)))
	AND $r->status_code == 200
	AND isset($r->data->url))
		return quote_amp($r->data->url);

	return quote_amp($urp);
}


echo shortlink('http://rezo.net/');
