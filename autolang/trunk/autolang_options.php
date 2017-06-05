<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function autolang_changer_langue($lang) {

	$liste_langues = ',' . @$GLOBALS['meta']['langues_multilingue'] . ',';

	// Si la langue demandee n'existe pas, on essaie d'autres variantes
	// Exemple : 'pt-br' => 'pt_br' => 'pt'
	$lang = str_replace('-', '_', trim($lang));
	if (!$lang) {
		return false;
	}

	if (strpos($liste_langues, ",$lang,") !== false
		or ($lang = preg_replace(',_.*,', '', $lang)
			and strpos($liste_langues, ",$lang,") !== false)
	) {

		return $lang;
	} else {
		return false;
	}
}

function autolang_utiliser_langue_visiteur() {

	$l = (!test_espace_prive() ? 'spip_lang' : 'spip_lang_ecrire');
	if (isset($_COOKIE[$l])) {
		if (autolang_changer_langue($l = $_COOKIE[$l])) {
			return $l;
		}
	}

	if (isset($GLOBALS['visiteur_session']['lang'])) {
		if (autolang_changer_langue($l = $GLOBALS['visiteur_session']['lang'])) {
			return $l;
		}
	}

	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $s) {
			if (preg_match('#^([a-z]{2,3})(-[a-z]{2,3})?(;q=[0-9.]+)?$#i', trim($s), $r)) {
				if (autolang_changer_langue($l = strtolower($r[1]))) {
					return $l;
				}
			}
		}
	}

	return utiliser_langue_site();
}

function autolang_aiguiller(){
	$langue = (isset($_COOKIE['spip_lang']) ? $_COOKIE['spip_lang'] : '');
	if (!$langue) {
		include_spip('inc/lang');
		$langue = autolang_utiliser_langue_visiteur();
		include_spip('inc/cookie');
		spip_setcookie('spip_lang', $langue);
		include_spip('inc/headers');
		redirige_par_entete(self());
	}
}

autolang_aiguiller();