<?php

$f = _request('field');
$q = _request('value');

// tester la securite
include_spip('inc/autoriser');
if (! autoriser('modifier', 'article', _request('id_article')) )
	die ('rien a faire la mon ami');

?>
