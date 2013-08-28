<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_configurer_boussole_serveur_verifier() {
	$erreurs = array();
	if (_request('actif')) {
		$nom = _request('nom');
		if (!$nom)
			$erreurs['nom'] = _T('info_obligatoire');
		else if ($nom == 'spip')
			$erreurs['nom'] = _T('boussole:message_nok_nom_serveur_spip');
	}
	return $erreurs;
}


?>
