<?php

function formulaires_configurer_traduiretexte_charger_dist() {
	include_spip('inc/config');
	$valeurs = lire_config('traduiretexte/', array());

	// cacher les clés que l’on ne saurait voir !
	foreach ($valeurs as $cle => $val) {
		if ($val and strpos($cle, 'cle_') === 0) {
			$valeurs[$cle] = traduiretexte_obscurify_cle_api($val);
		}
	}

	$valeurs['editable'] = true;
	return $valeurs;
}

function formulaires_configurer_traduiretexte_traiter_dist() {
	include_spip('inc/config');
	$valeurs = lire_config('traduiretexte/', array());

	// ne pas enregistrer la clé masquée
	foreach ($_POST as $cle => $val) {
		if (strpos($cle, 'cle_') === 0) {
			if (strpos($val, '****') === false) {
				$valeurs[$cle] = $val;
				set_request($cle, traduiretexte_obscurify_cle_api($val));
			}
		}
	}

	// On enregistre les nouvelles valeurs saisies
	include_spip('inc/config');
	ecrire_config('traduiretexte', $valeurs);
	$retour['message_ok'] = _T('config_info_enregistree');
	$retour['editable'] = true;
	return $retour;
}

function traduiretexte_obscurify_cle_api($cle) {
	$secret = str_pad(substr($cle, 0, 4), strlen($cle) - 4, '*') . substr($cle, -4);
	for($i=0;$i<strlen($cle);$i++) {
		if ($cle[$i] === '-') {
			$secret[$i] = '-';
		}
	}
	return $secret;
}