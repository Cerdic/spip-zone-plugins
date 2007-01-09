<?php

# redefinissables dans ecrire/mes_options ; si on veut en mettre
# plusieurs separer par des virgules
define('_SPIP_LOADER_UPDATE_AUTEURS', '1');

# source script
define('_SPIP_LOADER_SOURCE_SCRIPT', 'http://www.spip.net/spip-dev/INSTALL/spip_loader.php.txt');
define('_SPIP_LOADER_LOCAL_SCRIPT', _DIR_RACINE.'spip_loader.php');

# fichier source
define('_SPIP_LOADER_UPDATE_FILE',
	(@is_readable($f = _DIR_ETC.'spip_loader_update_list.txt') ? $f :
	(@is_readable($f = find_in_path('spip_loader_update_list.txt')) ? $f :
	false))
);

function spip_loader_liste() {
	$spip_loader_liste = array();
	if (!_SPIP_LOADER_UPDATE_FILE) {
		die ("Fichier de configuration absent");
	}
	$config = file(_SPIP_LOADER_UPDATE_FILE);
	foreach ($config as $l) {
		$l = trim($l);
		if ($l AND substr($l,0,1) != "#") {
			list($paquet,$url, $dest) = explode(' ', $l);
			$spip_loader_liste[$paquet] = array($url, $dest);
		}
	}
	return $spip_loader_liste;
}

function spip_loader_meta_paquets($paquet='', $date='') {
	return $vide;
}

?>