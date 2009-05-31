<?php

//
// Si la page est backend, si le visiteur n'est pas feedburner
// et si on a precise une URL de redirection de flux, rediriger
// Attention pas compatible avec les flux specialises ?page=backend&id_mot=12
//
if (_request('page')=='backend'
AND !(_request('var_feedburner') == 'oui'
	OR strstr($_SERVER['HTTP_USER_AGENT'], 'FeedBurner'))
AND function_exists('lire_config')
AND lire_config('feedburner/url')) {
	include_spip('inc/headers');
	redirige_par_entete(lire_config('feedburner/url'));
}

?>