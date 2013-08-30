<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');
$options = lire_config('multidomaines');
$GLOBALS['multidomaine_id_secteur_courant'] = NULL;
if (!defined('_MULTIDOMAINE_RUBRIQUE')) define('_MULTIDOMAINE_RUBRIQUE', '0');

if (is_array($options)) {
	foreach ($options as $cle => $valeur) {
		if (strpos($cle, 'editer_url_') === 0) {
			if (empty($valeur)) {
				$valeur = $options['editer_url'];
			}
			list(,,$id_secteur) = explode('_', $cle);
			$partie_url = parse_url($valeur);
			if (!isset($partie_url['port'])) {
				$partie_url['port'] = $partie_url['scheme'] == 'https'? 443:80;
			}
			if ($partie_url['host'] == $_SERVER['HTTP_HOST'] AND $partie_url['port'] == $_SERVER['SERVER_PORT']) {
				$GLOBALS['multidomaine_id_secteur_courant'] = $id_secteur;
				if ($options['squelette_' .$id_secteur]) {
					$GLOBALS['dossier_squelettes'] = trim($GLOBALS['dossier_squelettes'] .':'. $options['squelette_' .$id_secteur], ':');
				}
				else {multidomaines_squelettespardefaut_dist();}
			}
		}
	}
}

function multidomaines_squelettespardefaut_dist() {

	if (function_exists('multidomaines_squelettespardefaut')) {
		return multidomaines_squelettespardefaut();
	}
	$dossiers_port = '';
	$dossiers = '';

	if (strpos($_SERVER['HTTP_HOST'], '.') === FALSE) {
	  // ex: localhost
	  $dossiers = ':' . lire_config('multidomaines/squelette') . '/' . $_SERVER['HTTP_HOST'];
	}
	else {
	  $parties_domaine = explode('.', $_SERVER['HTTP_HOST']);
	  $extention = array_pop($parties_domaine);
	  do {
		$dossiers_port .=  ':' . lire_config('multidomaines/squelette') . '/' . implode('.', $parties_domaine) . '.' . $extention . '.' . $_SERVER['SERVER_PORT'];
		$dossiers_port .=  ':' . lire_config('multidomaines/squelette') . '/' . implode('.', $parties_domaine) . '.' . $_SERVER['SERVER_PORT'];
		$dossiers .=  ':' . lire_config('multidomaines/squelette') . '/' . implode('.', $parties_domaine) . '.' . $extention;
		$dossiers .=  ':' . lire_config('multidomaines/squelette') . '/' . implode('.', $parties_domaine);
		array_shift($parties_domaine);
	  } while (count($parties_domaine) > 0);
	}

	$GLOBALS['dossier_squelettes'] = trim($GLOBALS['dossier_squelettes'] . $dossiers_port . $dossiers, ':');
}
?>
