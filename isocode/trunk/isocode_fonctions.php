<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin.
 *
 * @package SPIP\ISOCODE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Charge tout ou partie des tables de codes ISO dans la base de données.
 * Les tables sont regroupées par service.
 *
 * @api
 * @filtre
 * @uses iso_read_table()
 * @uses iana_read_table()
 *
 * @param array	$tables
 *      Tableau, par service, des tables à charger. Si le tableau est vide ou si la liste des tables
 *      d'un service donné est vide, l'ensemble des tables du ou des services concernés seront chargées.
 *      Les tables doivent être libellées sans le préfixe `spip_`.
 * 		Les services disponibles actuellement sont `iso` et `iana`.
 *
* @return bool
 *        `true` si le chargement a réussi, `false` sinon
 */
function isocode_charger_tables($tables = array()) {

	$retour = true;

	// Suivant le tableau fourni en argument, on détermine la liste exacte des tables à charger.
	// Le cas où le service existe mais que la liste de tables associées est vide est traité dans
	// la boucle des services ci-après.
	if (!$tables) {
		// Le tableau est vide : il faut charger toutes les tables de tous les services supportés.
		$tables_a_charger = isocode_lister_tables();
	} elseif (is_string($tables)) {
		// L'argument n'est pas un tableau mais une chaine, on considère que l'appelant a demandé
		// le chargement de toutes les tables du service identifié par cette chaine.
		$tables_a_charger = isocode_lister_tables($tables);
	} elseif (is_array($tables)) {
		$cles = array_keys($tables);
		if (is_numeric($cles[0])) {
			// On traite le cas où le tableau est de la forme ('iso', 'iana')
			// au lieu de ('iso' => array(...), 'iana' => array(...)).
			$tables_a_charger = isocode_lister_tables($tables);
		} else {
			$tables_a_charger = $tables;
		}
	} else {
		$tables_a_charger = array();
	}

	// On charge, service par service, chacune des tables spécifiées pour chaque service concerné.
	// -- Pour éviter d'avoir une mise à jour bancale, il faudrait inclure les requêtes SQL dans
	// une transaction ce qui n'est pas possible avec spip aujourd'hui.
	// De fait, on ne peut pas assurer que si une erreur se produit le résultat soit cohérent et
	// on renvoie juste l'erreur --
	if ($tables_a_charger) {
		include_spip('inc/config');
		include_spip('inc/isocode_outils');
		foreach ($tables_a_charger as $_service => $_tables) {
			// Si le service est bien disponible on charge les tables concernées.
			if (isocode_service_disponible($_service)) {
				// Vérification et finalisation de la liste des tables à charger
				if (!$_tables) {
					// La liste des tables est vide : il faut charger toutes les tables du service concerné.
					$_tables = isocode_lister_tables($_service);
				} elseif (is_string($_tables)) {
					// La liste des tables n'est pas un tableau mais une chaine, on considère la chaine est
					// le nom de table à charger.
					$_tables = array($_tables);
				} elseif (!is_array($_tables)) {
					// La liste des tables n'est ni une chaine ni un tableau : c'est une erreur.
					$_tables = array();
				}

				if ($_tables) {
					include_spip("services/${_service}/${_service}_api");
					foreach ($_tables as $_table) {
						// Détermination de la fonction de lecture de la table et lecture des données contenues
						// soit dans un fichier soit dans une page web.
						$lire_table = "isocode_read_{$GLOBALS['isocode'][$_service]['tables'][$_table]['populating']}";
						list($records, $sha) = $lire_table($_service, $_table);
						if ($records) {
							// Suppression des éléments éventuels déjà chargés.
							sql_delete("spip_${_table}");

							// Insertion dans la base de données des éléments extraits
							sql_insertq_multi("spip_${_table}", $records);
							// On stocke les informations de chargement de la table dans une meta.
							$meta = array(
								'sha' => $sha,
								'nbr' => count($records),
								'maj' => date('Y-m-d H:i:s'));
							ecrire_config("isocode/tables/${_table}", $meta);
						}
					}
				}
			}
		}
	}

	return $retour;
}


function isocode_lister_services() {

	$services = array();

	if ($dossiers = glob(_DIR_PLUGIN_ISOCODE . '/services/*', GLOB_ONLYDIR)) {
		foreach ($dossiers as $_dossier) {
			$services[] = strtolower(basename($_dossier));
		}
	}

	return $services;
}


function isocode_service_disponible($service) {

	$autorisation = false;
	if ($service and in_array(strtolower($service), isocode_lister_services())) {
		$autorisation = true;
	}

	return $autorisation;
}


function isocode_lister_tables($services = array()) {

	$tables = array();

	// Si le tableau des services est vide c'est que l'on veut toutes les tables de tous
	// les services disponibles.
	// On accepte tous les cas où l'argument est vide sans être un tableau.
	if (!$services) {
		$services = isocode_lister_services();
	}

	// Si l'argument est non vide et est une chaine on considère que c'est un service et on le
	// transforme en un tableau à un élément.
	// Sinon c'est une erreur.
	if (!is_array($services)) {
		$services = is_string($services) ? array($services) : array();
	}

	// On collecte pour chaque service, la liste des tables qu'il supporte.
	foreach ($services as $_service) {
		if (isocode_service_disponible($_service)) {
			include_spip("services/${_service}/${_service}_api");
			$tables[$_service] = array_keys($GLOBALS['isocode'][$_service]['tables']);
		}
	}

	return $tables;
}


function isocode_verifier_codes_spip() {

	$codes_verifies = array();
	
	include_spip('inc/lang_liste');
	if ($GLOBALS['codes_langues']) {
		foreach ($GLOBALS['codes_langues'] as $_code => $_nom) {
			$from = array('spip_iso639codes');
			$select = array('*');
			if (strlen($_code) == 2) {
				// Si le code a une taille de 2 caractères on recherche de suite dans la table iso639codes
				// un élément dont le code ISO639-1 est égal.
				$where = array('code_639_1=' . sql_quote($_code));
				$codes_iso = sql_allfetsel($select, $from, $where);
				if ($codes_iso) {
					$codes_verifies['ok']['iso6391'][$_code] = $codes_iso;
				} else {
					$codes_verifies['nok']['iso6391'][$_code] = array('nom_spip' => $_nom);
				}
			} elseif (strlen($_code) == 3) {
				// Si le code a une taille de 3 caractères on recherche :
				// - dans la table iso639codes un élément dont le code ISO639-3 est égal.
				// - et sinon dans la table iso639families un élément dont le code ISO639-5 est égal.
				$where = array('code_639_3=' . sql_quote($_code));
				$codes_iso = sql_allfetsel($select, $from, $where);
				if ($codes_iso) {
					$codes_verifies['ok']['iso6393'][$_code] = $codes_iso;
				} else {
					$where = array('code_639_5=' . sql_quote($_code));
					$code_famille = sql_fetsel($select, array('spip_iso639families'), $where);
					if ($code_famille) {
						$codes_verifies['ok']['iso6395'][$_code] = array('nom_spip' => $_nom);
					} else {
						$codes_verifies['nok']['iso6393'][$_code] = array('nom_spip' => $_nom);
					}
				}
			} else {
				$codes_verifies['nok']['spip'][$_code] = array('nom_spip' => $_nom);
			}
		}
	}

	return $codes_verifies;
}

function isocode_verifier_iso639_5() {

	$codes_verifies = array();

	$codes_639_5 = sql_allfetsel('*', 'spip_iso639families');
	if ($codes_639_5) {
		foreach($codes_639_5 as $_code) {
			if (sql_countsel('spip_iso639macros', 'code_639_3=' . sql_quote($_code['code_639_5']))) {
				$codes_verifies['ok'][] = $_code['code_639_5'];
			} else {
				$codes_verifies['nok'][] = $_code['code_639_5'];
			}
		}
	}

	return $codes_verifies;
}