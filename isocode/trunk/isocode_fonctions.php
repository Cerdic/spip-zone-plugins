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
 * @uses isocode_lister_tables()
 * @uses isocode_trouver_service()
 * @uses lire_source()
 * @uses isocode_decharger_tables()
 *
 * @param array $tables
 *      Liste des tables à charger. Si le tableau est vide l'ensemble des tables
 *      seront chargées.
 *      Les tables doivent être libellées sans le préfixe `spip_`.
 *
 * @return array
 *      Tableau associatif résultat de l'action de vidage:
 *      - index `ok`         : `true` si le vidage a réussi, `false` sinon.
 *      - index `tables_ok`  : liste des tables vidées avec succès ou tableau vide sinon.
 *      - index `tables_nok` : liste des tables en erreur ou tableau vide sinon.
 *      - index `tables_sha` : liste des tables inchangées (SHA identique) ou tableau vide sinon.
 */
function isocode_charger_tables($tables = array()) {

	$retour = array(
		'ok'         => true,
		'tables_ok'  => array(),
		'tables_nok' => array(),
		'tables_sha' => array()
	);

	// Suivant le tableau fourni en argument, on détermine la liste exacte des tables à charger.
	// Le cas où le service existe mais que la liste de tables associées est vide est traité dans
	// la boucle des services ci-après.
	if (!$tables) {
		// Le tableau est vide : il faut charger toutes les tables de tous les services supportés.
		$tables = isocode_lister_tables();
	} elseif (is_string($tables)) {
		// L'argument n'est pas un tableau mais une chaîne, on considère que l'appelant a demandé
		// le chargement d'une table identifiée par cette chaîne.
		$tables = array($tables);
	} elseif (!is_array($tables)) {
		// L'argument n'est pas compréhensible, on met la liste à vide pour sortir sans traitement
		// et on enregistre l'erreur.
		$retour['ok'] = false;
		$retour['tables_nok'][] = $tables;
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
			$erreur_table = true;
			$source_identique = false;

			// On détermine le service qui supporte le chargement de la table
			$service = isocode_trouver_service($_table);
			if ($service) {
				// Lecture des données contenues soit dans un fichier soit dans une page web et renvoie d'une liste
				// d'éléments prêts à être enregistrés dans la table concernée.
				// Si la table est déjà chargée et que le fichier ou la page source n'a pas changé, la fonction de
				// lecture ne renvoie aucun élément pour éviter des traitements inutiles mais renvoie un indicateur
				// sur le SHA.
				spip_timer('lire');
				include_spip('inc/isocode_sourcer');
				list($enregistrements, $sha, $source_identique) = lire_source($service, $_table);
				$duree_lire = spip_timer('lire');
				if ($enregistrements) {
					spip_timer('ecrire');
					// Suppression des éléments éventuels déjà chargés. On ne gère pas d'erreur
					// sur ce traitement car elle sera forcément détectée lors de l'insertion qui suit.
					isocode_decharger_tables($_table);

					// Insertion dans la base de données des éléments extraits
					$sql_ok = sql_insertq_multi("spip_${_table}", $enregistrements);
					if ($sql_ok !== false) {
						// On stocke les informations de chargement de la table dans une meta.
						$meta = array(
							'service' => $service,
							'sha'     => $sha,
							'nbr'     => count($enregistrements),
							'maj'     => date('Y-m-d H:i:s')
						);
						ecrire_config("isocode/tables/${_table}", $meta);
						$erreur_table = false;
					}
					$duree_ecrire = spip_timer('ecrire');
					spip_log("La table <${_table}> a été chargée (lecture source: ${duree_lire} - insertion BD: ${duree_ecrire})", 'isocode' . _LOG_DEBUG);
				}
			}

			// Si la table est en erreur, on passe l'indicateur global à erreur et on stocke la table en nok
			// ou en sha identique suivant la cas.
			// Si le traitement est ok on stocke juste la table.
			if ($erreur_table) {
				$retour['ok'] = false;
				if ($source_identique) {
					$retour['tables_sha'][] = $_table;
				} else {
					$retour['tables_nok'][] = $_table;
				}
			} else {
				$retour['tables_ok'][] = $_table;
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
 * @uses isocode_lister_tables()
 *
 * @param array $tables
 *      Liste des tables à vider. Si le tableau est vide l'ensemble des tables
 *      seront vidées.
 *      Les tables doivent être libellées sans le préfixe `spip_`.
 *
 * @return array
 *      Tableau associatif résultat de l'action de vidage:
 *      - index `ok`         : `true` si le vidage a réussi, `false` sinon.
 *      - index `tables_ok`  : liste des tables vidées avec succès ou tableau vide sinon.
 *      - index `tables_nok` : liste des tables en erreur ou tableau vide sinon.
 */
function isocode_decharger_tables($tables = array()) {

	$retour = array(
		'ok'         => true,
		'tables_ok'  => array(),
		'tables_nok' => array()
	);

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
		// L'argument n'est pas compréhensible, on met la liste à vide pour sortir sans traitement
		// et on enregistre l'erreur.
		$retour['ok'] = false;
		$retour['tables_nok'][] = $tables;
		$tables = array();
	}

	// On boucle sur la liste des tables et on vide chaque table référencée.
	if ($tables) {
		include_spip('inc/config');
		foreach ($tables as $_table) {
			$sql_ok = sql_delete("spip_${_table}");
			if ($sql_ok !== false) {
				// Supprimer la meta propre à la table.
				effacer_config("isocode/tables/${_table}");
				// Enregistrer le succès du déchargement de la table
				$retour['tables_ok'][] = $_table;
			} else {
				$retour['ok'] = false;
				$retour['tables_nok'][] = $_table;
			}
		}
	}

	return $retour;
}


/**
 * Détermine le service associé au chargement de la table de codes ISO choisie.
 * Si la table est vide ou invalide, la fonction renvoie une chaîne vide.
 *
 * @api
 * @filtre
 *
 * @uses isocode_lister_services()
 *
 * @param $table
 *      Nom d'une table sans le préfixe `spip_`.
 *
 * @return string
 *      Nom du service permettant le chargement de la table ou chaîne vide si aucun service n'est trouvé.
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
 * La fonction lit les sous-répertoires du répertoire `services/` du plugin et vérifie qu'un fichier
 * d'API existe.
 *
 * @api
 * @filtre
 *
 * @return array
 *      La liste des services disponibles ou tableau vide aucun service n'est détecté.
 */
function isocode_lister_services() {

	$services = array();

	if ($dossiers = glob(_DIR_PLUGIN_ISOCODE . 'services/*', GLOB_ONLYDIR)) {
		foreach ($dossiers as $_dossier) {
			$service = strtolower(basename($_dossier));
			if (file_exists($f = _DIR_PLUGIN_ISOCODE . "services/${service}/${service}_api.php")) {
				$services[] = $service;
			}
		}
	}

	return $services;
}


/**
 * Vérifie si le service demandé fait bien partie de la liste des services disponibles.
 *
 * @api
 * @filtre
 *
 * @uses isocode_lister_services()
 *
 * @param string $service
 *      Nom du service à vérifier.
 *
 * @return bool
 *      `true` si le service est disponible, `false` sinon.
 */
function isocode_service_disponible($service) {

	$disponible = false;
	if (
		$service
		and in_array(strtolower($service), isocode_lister_services())
	) {
		$disponible = true;
	}

	return $disponible;
}


/**
 * Retourne la liste de toutes les tables gérées par le plugin ou de celles associées à un ou plusieurs
 * services donnés.
 *
 * @api
 * @filtre
 *
 * @uses isocode_lister_services()
 * @uses isocode_service_disponible()
 *
 * @param array $services
 *      Liste des services pour lesquels la liste des tables associées est demandée.
 *      Si la liste est vide la fonction renvoie les tables de tous les services disponibles.
 *
 * @return array
 *      Liste des tables sans le préfixe `spip_`.
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
 * Informe sur la liste des tables déjà chagées en base de données.
 * Les informations de la meta de chaque table sont complétées et renvoyées.
 *
 * @api
 * @filtre
 *
 * @return array
 *      Liste des tables de codes ISO sans le préfixe `spip_` et leurs informations de chargement.
 */
function isocode_informer_tables_chargees() {

	// On initialise la liste des tables en lisant la meta idoine.
	include_spip('inc/config');
	$tables = lire_config('isocode/tables', array());

	// On complète chaque bloc d'informations par le nom de la table et son libéllé.
	if ($tables) {
		foreach ($tables as $_table => $_informations) {
			$tables[$_table]['nom'] = $_table;
			$tables[$_table]['libelle'] = _T("isocode:label_table_${_table}");
		}
	}

	return $tables;
}


/**
 * Indique si une table est déjà chargée ou pas en base de données.
 * La fonction scrute la table `spip_${table}` et non la meta propre à la table.
 *
 * @api
 * @filtre
 *
 * @param string $table
 *      Nom de la table sans le préfixe `spip_`.
 * @param array  $meta_table
 *      Meta propre à la table, créée lors du chargement de celle-ci et retournée si la table
 *      est déjà chargée.
 *
 * @return bool
 *      `true` si la table est chargée, `false` sinon.
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
