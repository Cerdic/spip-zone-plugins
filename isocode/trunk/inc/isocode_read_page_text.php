<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin Codes de Langues.
 *
 * @package SPIP\ISOCODE\OUTILS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * @param string $service
 * @param string $table
 *
 * @return array
 */
function inc_isocode_read_page_text($service, $table) {

	// Initialisations
	$records = array();
	$sha_file = false;
	$f_complete_record = "${table}_complete_by_record";
	$f_complete_table = "${table}_complete_by_table";

	// Inclusion des configurations et des fonctions spécifiques au service qui fournit les données
	// de la table à remplir.
	include_spip("services/${service}/${service}_api");

	// Initialisations des données de configuration propre au service et à la table
	$url = $GLOBALS['isocode'][$service]['tables'][$table]['url'];
	$parsing_config = $GLOBALS['isocode'][$service]['tables'][$table]['parsing'];
	$fields_config = $GLOBALS['isocode'][$service]['tables'][$table]['basic_fields'];

	// Acquisition de la page sur le site de l'IANA
	include_spip('inc/distant');
	$options = array();
	$flux = recuperer_url($url, $options);

	if (!empty($flux['page'])) {
		$elements = array();
		// Chaque élément est identifié soit par un délimiteur, soit par une regexp suivant la méthode configurée.
		if ($parsing_config['element']['method'] == 'explode') {
			// On récupére donc un tableau des éléments à lire en utilisant la fonction explode
			$elements = explode($parsing_config['element']['delimiter'], $flux['page']);
		} else {
			// C'est une regexp... à compléter
		}

		// Initialisation d'un enregistrement vide (tous les champs sont des chaines ou des dates)
		$empty_record = array();
		foreach ($fields_config as $_cle => $_field) {
			$empty_record[$_field] = '';
		}

		// Chaque champ est identifié par une regexp qui permet d'extraire le nom du champ et sa valeur dans deux tableaux
		// distincts indexés de façon concomittante.
		// C'est pour l'instant la seule méthode permise sur un élément.
		foreach ($elements as $_element) {
			if (preg_match_all($parsing_config['field']['regexp'], $_element, $matches)) {
				$fields = $empty_record;
				$invalid_field = false;
				foreach ($matches[1] as $_cle => $_header) {
					if (isset($fields_config[trim($_header)])) {
						$fields[$fields_config[trim($_header)]] = isset($matches[2][$_cle]) ? $matches[2][$_cle] : '';
					} else {
						$invalid_field =true;
						break;
					}
				}

				// Si on a pas détecté un champ invalide on peut finaliser l'élémént et l'enregistrer
				if (!$invalid_field) {
					// Si besoin on appelle une fonction pour chaque enregistrement afin de le compléter
					if (function_exists($f_complete_record)) {
						$fields = $f_complete_record($fields);
					}
					$records[] = $fields;
				}
			}
		}

		// Si besoin on appelle une fonction pour toute la table
		if (function_exists($f_complete_table)) {
			$records = $f_complete_table($records);
		}
	}

	return array($records, $sha_file);
}
