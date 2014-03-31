<?php

include_spip('inc/config'); 
include_spip('inc/utils');
include_spip('inc/lang');

$nompage='admin_lang';
$module = _request('module');
$mode = _request('mode');
$target_lang = _request('target_lang');
$master_lang = _request('master_lang');

$header = generer_url_ecrire($nompage);
$header = parametre_url($header, 'master_lang', $master_lang, '&');
$header = parametre_url($header, 'target_lang', $target_lang , '&');
$header = parametre_url($header, 'mode', $mode, '&');
$header = parametre_url($header, 'module', $module, '&' );
$header = "Location:".$header;

// echo $master_lang, "URL: ", $header;

header($header);

?>
