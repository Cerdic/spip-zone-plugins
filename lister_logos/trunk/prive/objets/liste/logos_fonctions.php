<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function logo_on_existe($id_objet, $_id_objet) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$logo = $chercher_logo($id_objet, $_id_objet);
	if (count($logo) == 0) {
		spip_log($logo, 'test.'._LOG_ERREUR);
		return false;
	}
	return $logo;
}
