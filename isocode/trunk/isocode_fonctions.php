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
 * Charge en base de données une liste de tables de codes ISO donnée.
 * Si la liste est vide, la fonction charge toutes les tables disponibles.
 *
 * @api
 * @filtre
 *
 * @uses isocode_trouver_service()
 * @uses isocode_decharger_tables()
 *
 * @param array $tables
 *      Liste des tables à charger. Si le tableau est vide l'ensemble des tables
 *      seront chargées.
 *      Les tables doivent être libellées sans le préfixe `spip_`.
 *
 * @return array
 *      Tableau résultat de l'action de vidage:
 *      - index 0 : `true` si le vidage a réussi, `false` sinon.
 *      - index 1 : liste des tables en erreur ou tableau vide sinon.
 */
function isocode_charger_tables($tables = array()) {

	$retour = array(true, array());

	// Suivant le tableau fourni en argument, on détermine la liste exacte des tables à charger.
	// Le cas où le service existe mais que la liste de tables associées est vide est traité dans
	// la boucle des services ci-après.
	if (!$tables) {
		// Le tableau est vide : il faut charger toutes les tables de tous les services supportés.
		$tables = isocode_lister_tables();
	} elseif (is_string($tables)) {
		// L'argument n'est pas un tableau mais une chaine, on considère que l'appelant a demandé
		// le chargement d'une table identifiée par cette chaine.
		$tables = array($tables);
	} elseif (!is_array($tables)) {
		$tables = array();
	}

	// On charge chacune des tables spécifiées en identifiant au préalable le service concerné.
	// -- Pour éviter d'avoir une mise à jour bancale, il faudrait inclure les requêtes SQL dans
	// une transaction ce qui n'est pas possible avec spip aujourd'hui.
	// De fait, on ne peut pas assurer que si une erreur se produit le résultat soit cohérent et
	// on renvoie juste l'erreur --
	if ($tables) {
		include_spip('inc/config');
		foreach ($tables as $_table) {
			$erreur_table = false;

			// On détermine le service qui supporte le chargement de la table
			$service = isocode_trouver_service($_table);
			if ($service) {
				include_spip("services/${service}/${service}_api");
				// Détermination de la fonction de lecture de la table et lecture des données contenues
				// soit dans un fichier soit dans une page web.
				$lire_table = charger_fonction("isocode_read_{$GLOBALS['isocode'][$service]['tables'][$_table]['populating']}", 'inc');
				list($records, $sha) = $lire_table($service, $_table);
				if ($records) {
					// Suppression des éléments éventuels déjà chargés. On ne gère pas d'erreur
					// sur ce traitement car elle sera forcément détectée sur l'insert qui suit.
					isocode_decharger_tables($_table);

					// Insertion dans la base de données des éléments extraits
					$sql_ok = sql_insertq_multi("spip_${_table}", $records);
					if ($sql_ok !== false) {
						// On stocke les informations de chargement de la table dans une meta.
						$meta = array(
							'service' => $service,
							'sha'     => $sha,
							'nbr'     => count($records),
							'maj'     => date('Y-m-d H:i:s')
						);
						ecrire_config("isocode/tables/${_table}", $meta);
					} else {
						$erreur_table = true;
					}
				} else {
					$erreur_table = true;
				}
			} else {
				$erreur_table = true;
			}

			if ($erreur_table) {
				$retour[0] = false;
				$retour[1][] = $_table;
			}
		}
	}

	return $retour;
}


/**
 * Supprime en base de données, le contenu des tables de codes ISO choisies.
 * Si la liste des tables est vide la fonction considère que toutes les tables doivent être vidées.
 * La meta concernant les informations de chargement de chaque table est aussi effacée.
 *
 * @api
 * @filtre
 *
 * @param array $tables
 *        Liste des noms de table sans le préfixe `spip_`.
 *
 * @return array
 *         Tableau résultat de l'action de vidage:
 *         - index 0 : `true` si le vidage a réussi, `false` sinon.
 *         - index 1 : liste des tables en erreur ou tableau vide sinon.
 */
function isocode_decharger_tables($tables = array()) {

	$retour = array(true, array());

	// Suivant le tableau fourni en argument, on détermine la liste exacte des tables à charger.
	// Le cas où le service existe mais que la liste de tables associées est vide est traité dans
	// la boucle des services ci-après.
	if (!$tables) {
		// Le tableau est vide : il faut charger toutes les tables de tous les services supportés.
		$tables = isocode_lister_tables();
	} elseif (is_string($tables)) {
		// L'argument n'est pas un tableau mais une chaine, on considère que l'appelant a demandé
		// le chargement d'une table identifiée par cette chaine.
		$tables = array($tables);
	} elseif (!is_array($tables)) {
		$tables = array();
	}

	// On boucle sur la liste des tables et on vide chaque table référencée.
	if ($tables) {
		foreach ($tables as $_table) {
			$sql_ok = sql_delete("spip_${_table}");
			if ($sql_ok !== false) {
				// Supprimer la meta propre au règne.
				effacer_meta("isocode/tables/${_table}");
			} else {
				$retour[0] = false;
				$retour[1][] = $_table;
			}
		}
	}

	return $retour;
}


/**
 * Détermine le service associé au chargement de la table de codes ISO choisie.
 * Si la table est vide ou invalide, la fonction renvoie une chaine vide.
 *
 * @param $table
 *        Nom d'une table de codes ISO sans le préfixe `spip_`.
 *
 * @return string
 * 		Nom du service permettant le chargement de la table.
 */
function isocode_trouver_service($table) {

	static $services = array();
	static $tables = array();
	$service = '';

	if (is_string($table) and $table) {
		if (!$services) {
			$services = isocode_lister_services();
		}

		foreach ($services as $_service) {
			if (!isset($tables[$_service])) {
				include_spip("services/${_service}/${_service}_api");
				$tables[$_service] = array_keys($GLOBALS['isocode'][$_service]['tables']);
			}
			if (in_array(strtolower($table), $tables[$_service])) {
				$service = $_service;
				break;
			}
		}
	}

	return $service;
}


/**
 * Retourne la liste des services disponibles pour le chargement des tables de codes ISO.
 * La fonction lit les sous-répertoires du répertoire `services/` du plugin.
 *
 * @return array
 * 		La liste des services disponibles.
 */
function isocode_lister_services() {

	$services = array();

	if ($dossiers = glob(_DIR_PLUGIN_ISOCODE . '/services/*', GLOB_ONLYDIR)) {
		foreach ($dossiers as $_dossier) {
			$services[] = strtolower(basename($_dossier));
		}
	}

	return $services;
}


/**
 * Vérifie si le service demandé est fait bien partie de la liste des services disponibles.
 *
 * @param $service
 * 		Nom du service à vérifier.
 *
 * @return bool
 *      `true` si le service est disponible, `false` sinon.
 */
function isocode_service_disponible($service) {

	$disponible = false;
	if ($service and in_array(strtolower($service), isocode_lister_services())) {
		$disponible = true;
	}

	return $disponible;
}


/**
 * Retourne la liste de toutes les tables ou celle associée à un ou plusieurs services donnés.
 *
 * @param array $services
 * 		Liste des services pour lesquels la liste des tables associées est demandée.
 * 		Si la liste est vide la fonction renvoie les tables de tous les services dsponibles.
 *
 * @return array
 * 		Liste des tables de codes ISO sans le préfixe `spip_`.
 */
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
			$tables = array_merge($tables, array_keys($GLOBALS['isocode'][$_service]['tables']));
		}
	}

	return $tables;
}


/**
 * Indique si une table de codes ISO est déjà chargée ou pas en base de données.
 * La fonction scrute la table `spip_${table}` et non la meta propre à la table.
 *
 * @api
 * @filtre
 *
 * @param string $table
 *        Nom de la table sans le préfixe `spip_`.
 * @param array  $meta_table
 *        Meta propre à la table, créée lors du chargement de celle-ci et retournée si la table
 *        est déjà chargée.
 *
 * @return bool
 *        `true` si la table est chargée, `false` sinon.
 */
function isocode_table_chargee($table, &$meta_table) {
	$meta_table = array();
	$table_chargee = false;

	$retour = sql_countsel("spip_${table}");
	if ($retour) {
		// Récupérer la meta propre au règne afin de la retourner.
		include_spip('inc/config');
		$meta_table = lire_config("isocode/tables/${table}");
		$table_chargee = true;
	}

	return $table_chargee;
}


/**
 * Compare le sha passé en argument pour la table concernée avec le sha stocké dans la meta
 * pour cette même table.
 *
 * @api
 *
 * @param string $sha
 * 		Sha à comparer à celui de la table.
 * @param string $table
 * 		Nom de la table de code ISO (sans préfixe `spip_`) dont il faut comparer le sha
 * 		stoké dans sa meta de chargement.
 *
 * @return bool
 *      `true` si le sha passé en argument est identique au sha stocké pour la table choisie, `false` sinon.
 */
function isocode_comparer_sha($sha, $table) {

	$sha_identique = false;

	// On récupère le sha de la table dans les metas si il existe (ie. la table a été chargée)
	include_spip('inc/config');
	$sha_stocke = lire_config("isocode/tables/${table}/sha", '');

	if ($sha_stocke and ($sha == $sha_stocke)) {
		$sha_identique = true;
	}

	return $sha_identique;
}
