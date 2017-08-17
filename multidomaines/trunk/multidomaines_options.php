<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
$options                                    = lire_config('multidomaines');
$GLOBALS['multidomaine_id_secteur_courant'] = null;
$GLOBALS["multidomaine_site_principal"]     = true;
if (!defined('_MULTIDOMAINE_RUBRIQUE')) {
	define('_MULTIDOMAINE_RUBRIQUE', '0');
}
if (!defined('_SECTEUR_URL')) {
	define('_SECTEUR_URL', '0');
}

if (is_array($options)) {
	foreach ($options as $cle => $valeur) {
		if (strpos($cle, 'editer_url_') === 0) {
			if (empty($valeur)) {
				$valeur = $options['editer_url'];
			}
			list(, , $id_secteur) = explode('_', $cle);
			$partie_url = parse_url($valeur);
			if (!isset($partie_url['port'])) {
				$partie_url['port'] = $partie_url['scheme'] == 'https' ? 443 : 80;
			}
			if ($partie_url['host'] == $_SERVER['HTTP_HOST'] AND $partie_url['port'] == $_SERVER['SERVER_PORT']) {
				if ($options['squelette_' . $id_secteur]) {
					$GLOBALS['multidomaine_id_secteur_courant'] = $id_secteur;
					$GLOBALS['dossier_squelettes']              = trim($GLOBALS['dossier_squelettes'] . ':' . $options['squelette_' . $id_secteur], ':');
					$GLOBALS["multidomaine_site_principal"]     = false;
				}
			}
		}
	}
	if (!$GLOBALS['dossier_squelettes']) {
		multidomaines_squelettespardefaut_dist();
	}
}

function multidomaines_squelettespardefaut_dist() {

	if (function_exists('multidomaines_squelettespardefaut')) {
		return multidomaines_squelettespardefaut();
	}
	$dossiers_port = '';
	$dossiers      = '';

	if (strpos($_SERVER['HTTP_HOST'], '.') === false) {
		// ex: localhost
		$dossiers = ':' . lire_config('multidomaines/squelette') . '/' . $_SERVER['HTTP_HOST'];
	} else {
		$parties_domaine = explode('.', $_SERVER['HTTP_HOST']);
		$extention       = array_pop($parties_domaine);
		do {
			$base = ':' . lire_config('multidomaines/squelette') . '/' . implode('.', $parties_domaine);
			$dossiers_port .= $base . '.' . $extention . '.' . $_SERVER['SERVER_PORT'];
			$dossiers_port .= $base . '.' . $_SERVER['SERVER_PORT'];
			$dossiers      .= $base . '.' . $extention;
			$dossiers      .= $base;
			array_shift($parties_domaine);
		} while (count($parties_domaine) > 0);
	}

	$GLOBALS['dossier_squelettes'] = trim($GLOBALS['dossier_squelettes'] . $dossiers_port . $dossiers, ':');
}

