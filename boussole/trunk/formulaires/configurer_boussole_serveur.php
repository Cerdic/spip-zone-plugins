<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_configurer_boussole_serveur_verifier() {
	$erreurs = array();
	include_spip('inc/config');
	if (_request('actif')) {
		$nom = _request('nom');
		if (!$nom)
			$erreurs['nom'] = _T('info_obligatoire');
		else if (($nom == 'spip')
			AND (rtrim(lire_config('adresse_site'), '/') != 'http://boussole.spip.net'))
			$erreurs['nom'] = _T('boussole:message_nok_nom_serveur_spip');
	}
	return $erreurs;
}

?>
