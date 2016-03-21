<?php
/**
 * Fonctions utiles au plugin Sites pour projets
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formater_tableau($chaine) {
	$listing = array();
	if (preg_match("/\n/", $chaine)) {
		$listing = explode("\n", $chaine);
		if (is_array($listing) and count($listing) > 0) {
			foreach ($listing as $cle => $valeur) {
				$listing[$cle] = formater_valeur($valeur);
			}
		}
	} else {
		if (preg_match("/\|/", $chaine)) {
			$listing[] = formater_valeur($chaine);
		}
	}

	return $listing;
}

function formater_valeur($valeur) {
	$tableau = explode("|", $valeur);
	foreach ($tableau as $key => $value) {
		$tableau[$key] = trim($value);
	}

	return $tableau;
}

function url_webservice_array($url) {
	$recuperer_flux = charger_fonction('recuperer_flux', 'inc');
	$convertir = charger_fonction('xml_to_array', 'inc');

	$page = $recuperer_flux($url);
	$xml = $convertir($page['content']);
	ksort($xml);

	return $xml;
}

function url_webservice_xml($url, $login = '', $password = '') {
	$recuperer_flux = charger_fonction('recuperer_flux', 'inc');

	$page = $recuperer_flux($url, $login, $password);

	return $page['content'];
}

function version2branche($version) {
	if (preg_match("/\./", $version)) {
		$numeros = explode(".", $version);
		if (count($numeros) >= 3) {
			$version = $numeros[0] . "." . $numeros[1];
		} elseif (count($numeros) <= 2) {
			$version = $numeros[0];
		}
	}

	return $version;
}

?>
