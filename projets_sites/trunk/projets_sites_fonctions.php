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
	if (count($tableau) == 4) {
		$tableau['prefixe'] = trim($tableau[0]);
		$tableau['version'] = trim($tableau[1]);
		$tableau['version_base'] = '-';
		$tableau['titre'] = trim($tableau[2]);
		$tableau['statut'] = trim($tableau[3]);
	} elseif (count($tableau) == 5) {
		$tableau['prefixe'] = trim($tableau[0]);
		$tableau['version'] = trim($tableau[1]);
		$tableau['version_base'] = trim($tableau[2]);
		$tableau['titre'] = trim($tableau[3]);
		$tableau['statut'] = trim($tableau[4]);
	} else {
		foreach ($tableau as $key => $value) {
			$tableau[$key] = trim($value);
		}
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

function sp_lister_type_sites($from = 'bdd') {
	if (empty($from)) {
		$from = 'bdd';
	}
	$liste_types_sites = array();

	if ($from === 'objet') {
		// On reprend les valeurs passées dans #FORMULAIRE_EDITER_PROJETS_SITE
		$liste_types_sites = array(
			'01local' => _T('projets_site:type_site_01local_court'),
			'02dev' => _T('projets_site:type_site_02dev_court'),
			'03inte' => _T('projets_site:type_site_03inte_court'),
			'04test' => _T('projets_site:type_site_04test_court'),
			'05rec' => _T('projets_site:type_site_05rec_court'),
			'06prep' => _T('projets_site:type_site_06prep_court'),
			'07prod' => _T('projets_site:type_site_07prod_court'),
		);
	} else {
		// On va chercher les types de site enregistrés dans la base de données.
		include_spip('base/abstract_sql');
		$types_sites = sql_allfetsel("DISTINCT(type_site)", 'spip_projets_sites');
		if (is_array($types_sites) and count($types_sites) > 0) {
			foreach ($types_sites as $type_site) {
				$liste_types_sites[] = $type_site['type_site'];
			}
			$liste_types_sites = array_filter($liste_types_sites); // On enlève les valeurs vides
			$liste_types_sites = array_values($liste_types_sites); // On réindexe le tableau pour éviter des surprises
		}
	}

	return $liste_types_sites;
}

