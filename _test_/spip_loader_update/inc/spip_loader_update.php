<?php

# redefinissables dans ecrire/mes_options ; si on veut en mettre
# plusieurs separer par des virgules
define('_SPIP_LOADER_UPDATE_AUTEURS', '1');

# fichier source
define('_SPIP_LOADER_UPDATE_FILE', find_in_path('spip_loader_update_list.txt'));

function spip_loader_liste($fichier = '') {
	$spip_loader_liste = array();
	if(!$fichier) $fichier = _SPIP_LOADER_UPDATE_FILE;

	if (!file_exists($fichier)) {
		die ("Fichier de configuration ".$fichier." absent");
	}
	$config = file($fichier);
	foreach ($config as $l) {
		$l = trim($l);
		if ($l AND substr($l,0,1) != "#") {
			list($paquet,$url) = explode(' ', $l);
			$spip_loader_liste[$paquet] = $url;				
		}
	}
	return $spip_loader_liste;
}

?>
