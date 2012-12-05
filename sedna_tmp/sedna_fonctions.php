<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (_request('rss')
AND _request('page')=='sedna') {
	include_spip('inc/headers');
	redirige_par_entete(parametre_url(
		parametre_url(self(), 'rss', ''),
		'page','sedna-rss',
		'&'));
}
?>