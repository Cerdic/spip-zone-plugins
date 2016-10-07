<?php
define('_AUTORISER_ACTION_ABS_REDIRECT', true);
// on ajoute la langue d'origine dans le contexte systematiquement.
if (!$langue = _request('lang')) {
	include_spip('inc/lang');
	include_spip('inc/config');
	$langues_du_site = explode(',', lire_config('langues_multilingue'));
	$langue = $GLOBALS['meta']['langue_site'];
	foreach ($langues_du_site as $vlang) {
		if ($t_domaines = lire_config('multilinguisme_par_domaines/domaines_'.$vlang)) {
			$domaines = explode("\n", $t_domaines);
			foreach ($domaines as $domaine) {
				if (stripos($domaine, $_SERVER['HTTP_HOST']) !== FALSE) {
					$langue = $vlang;
					break 2;
				}
			}
		}
	}
	changer_langue($langue);
	// stocker dans $_GET
	set_request('lang', $langue);
}
 
// stocker la langue en cookie...
if ($langue != $_COOKIE['spip_lang']) {
	include_spip('inc/cookie');
	spip_setcookie('spip_lang', $langue);
}