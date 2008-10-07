<?php

if (_request('page')=='backend' AND _request('var_feedburner')!=='oui' AND lire_config('feedburner/feedId')) {
	include_spip('inc/headers');
	$url=lire_config('feedburner/url');
	if(!$url) $url='http://feeds.feedburner.com/~e?ffid='.lire_config('feedburner/feedId');
	redirige_par_entete($url);
}

?>