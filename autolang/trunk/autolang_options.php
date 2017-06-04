<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function autolang_aiguiller(){
	$langue = (isset($_COOKIE['spip_lang']) ? $_COOKIE['spip_lang'] : '');
	if (!$langue) {
		include_spip('inc/lang');
		$langue = utiliser_langue_visiteur();
		include_spip('inc/cookie');
		spip_setcookie('spip_lang', $langue);
		include_spip('inc/headers');
		redirige_par_entete(self());
	}
}

autolang_aiguiller();