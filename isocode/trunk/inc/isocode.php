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
 * @uses consigner_chargement()
 * @uses lire_source()
 * @uses isocode_decharger_tables()
 *
 * @param string $type Type de service. Prend les valeurs `nomenclature` ou `geometrie`.
 * @param array $services
 *      Liste des tables (nomenclature) ou des contours (geometrie) à charger.
 *      Si le tableau est vide l'ensemble des éléments du type choisi seront chargés.
 *
 * @return array
 *      Tableau associatif résultat de l'action de vidage:
 *      - index `ok`         : `true` si le vidage a réussi, `false` sinon.
 *      - index `elements_ok`  : liste des tables vidées avec succès ou tableau vide sinon.
 *      - index `elements_nok` : liste des tables en erreur ou tableau vide sinon.
 *      - index `elements_sha` : liste des tables inchangées (SHA identique) ou tableau vide sinon.
 */
function isocode_charger($type, $service, $table) {

	$retour = array(
		'erreur'  => '',
		'type'    => $type,
		'service' => $service,
		'table'   => $table,
	);

	// Lecture des données contenues soit dans un fichier, soit dans une page web, soit dans le retour d'une API REST
	// et renvoie une liste d'éléments prêts à être enregistrés dans la table concernée.
	// Si la table est déjà chargée et que le fichier ou la page source n'a pas changé, la fonction de
	// lecture ne renvoie aucun élément pour éviter des traitements inutiles mais renvoie un indicateur
	// sur le SHA.
	$erreur = true;

	spip_timer('lire');
	include_spip('inc/isocode_utils');
	list($enregistrements, $sha, $source_identique) = lire_source($type, $service, $table);
	$duree_lire = spip_timer('lire');
	if ($enregistrements) {
		spip_timer('ecrire');
		// Suppression des éléments éventuels déjà chargés. On ne gère pas d'erreur
		// sur ce traitement car elle sera forcément détectée lors de l'insertion qui suit.
		isocode_decharger($type, $service, $table);

		// Insertion dans la base de données des éléments extraits
		$sql_ok = sql_insertq_multi("spip_${table}", $enregistrements);
		if ($sql_ok !== false) {
			// On stocke les informations de chargement de la table dans une meta.
			$meta = array(
				'sha'     => $sha,
				'nbr'     => count($enregistrements),
				'maj'     => date('Y-m-d H:i:s')
			);
			consigner_chargement($meta, $type, $service, $table);
			$erreur = false;
		}
		$duree_ecrire = spip_timer('ecrire');
		spip_log("La table <${table}> a été chargée via le service <${service}> (lecture source: ${duree_lire} - insertion BD: ${duree_ecrire})", 'isocode' . _LOG_DEBUG);
	}

	// Si la table est en erreur, on passe l'indicateur global à erreur et on stocke la table en nok
	// ou en sha identique suivant la cas.
	// Si le traitement est ok on stocke juste la table.
	if ($erreur) {
		if ($source_identique) {
			$retour['erreur'] = 'sha';
		} else {
			$retour['erreur'] = 'nok';
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
 * @param string $type
 * @param string $service
 * @param string $table
 *      Liste des tables à vider. Si le tableau est vide l'ensemble des tables
 *      seront vidées.
 *      Les tables doivent être libellées sans le préfixe `spip_`.
 *
 * @return array
 *      Tableau associatif résultat de l'action de vidage:
 *      - index `ok`         : `true` si le vidage a réussi, `false` sinon.
 *      - index `elements_ok`  : liste des tables vidées avec succès ou tableau vide sinon.
 *      - index `elements_nok` : liste des tables en erreur ou tableau vide sinon.
 */
function isocode_decharger($type, $service, $table) {

	$retour = array(
		'erreur'  => '',
		'type'    => $type,
		'service' => $service,
		'table'   => $table,
	);

	$where = ($type === 'nomenclature')
		? array()
		: array('service=' . sql_quote($service));
	$sql_ok = sql_delete("spip_${table}", $where);
	if ($sql_ok !== false) {
		// Supprimer la meta propre à la table.
		include_spip('inc/isocode_utils');
		deconsigner_chargement($type, $service, $table);
	} else {
		$retour['erreur'] = 'nok';
	}

	return $retour;
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
function isocode_lister_types_service() {

	static $types = array();

	if (!$types) {
		if ($dossiers = glob(_DIR_PLUGIN_ISOCODE . 'services/*', GLOB_ONLYDIR)) {
			foreach ($dossiers as $_dossier) {
				$types[] = strtolower(basename($_dossier));
			}
		}
	}
	return $types;
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
function isocode_lister_services($type) {

	static $services = array();

	if (
		$type
		and !isset($services[$type])
	) {
		if ($type === 'nomenclature') {
			if ($dossiers = glob(_DIR_PLUGIN_ISOCODE . "services/${type}/*", GLOB_ONLYDIR)) {
				foreach ($dossiers as $_dossier) {
					$service = strtolower(basename($_dossier));
					if (file_exists($f = _DIR_PLUGIN_ISOCODE . "services/${type}/${service}/${service}_api.php")) {
						$services[$type][] = $service;
					}
				}
			}
		} elseif ($type === 'geometrie') {
			// Contours géométriques : les services sont décrits dans la configuration incluse dans le fichier
			// geometrie_api.php
			include_spip("services/${type}/${type}_api");
			$services[$type] = array_keys($GLOBALS['isocode']['geometrie']);
		}
	}

	return isset($services[$type]) ? $services[$type] : array();
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
 * @return string|array
 *      Le ou les services associés à la table fournie suivant le type de service.
 *      - nomenclature : service unique permettant le chargement de la table ou chaîne vide.
 *      - geometrie    : tableau des services permettant le chargement de la table ou tableau vide.
 */
function isocode_trouver_service($type, $table) {

	static $tables = array();
	static $services = array();
	$services_trouves = ($type === 'nomenclature') ? '' : array();

	if ($type) {
		if (!isset($services[$type])) {
			$services[$type] = isocode_lister_services($type);
		}
		if (!isset($tables[$type])) {
			$tables[$type] = isocode_lister_tables($type);
		}

		if (in_array($table, $tables[$type])) {
			if ($type === 'nomenclature') {
				foreach ($services[$type] as $_service) {
					include_spip("services/${type}/${_service}/${_service}_api");
					if (isset($GLOBALS['isocode'][$_service][$table])) {
						$services_trouves = $_service;
						break;
					}
				}
			} elseif ($type === 'geometrie') {
				// Détermination du ou des services pour le type géométrie
				include_spip("services/${type}/${type}_api");
				foreach ($services[$type] as $_service) {
					if ($GLOBALS['isocode'][$type][$_service]['table'] === $table) {
						$services_trouves[] = $_service;
					}
				}
			}
		}
	}

	return $services_trouves;
}


/**
 * Retourne la liste de toutes les tables gérées par le plugin ou de celles associées à un ou plusieurs
 * services donnés.
 *
 * @api
 * @filtre
 *
 * @uses isocode_lister_services()
 * @uses service_est_disponible()
 *
 * @param array $services
 *      Liste des services pour lesquels la liste des tables associées est demandée.
 *      Si la liste est vide la fonction renvoie les tables de tous les services disponibles.
 *
 * @return array
 *      Liste des tables sans le préfixe `spip_`.
 */
function isocode_lister_tables($type, $avec_groupes = false) {

	$tables = array();

	// On vérifie l'argument type de service qui est vide si tous les types sont requis.
	if ($type) {
		$services = isocode_lister_services($type);
		if ($type === 'nomenclature') {
			// On collecte pour chaque service, la liste des tables qu'il supporte.
			foreach ($services as $_service) {
				include_spip("services/${type}/${_service}/${_service}_api");
				if (!$avec_groupes) {
					$tables = array_merge($tables, array_keys($GLOBALS['isocode'][$_service]));
				} else {
					foreach ($GLOBALS['isocode'][$_service] as $_table => $_config) {
						$groupe = $_config['groupe'];
						$tables[$groupe][] = $_table;
					}
				}
			}
		} elseif ($type === 'geometrie') {
			// Un service ne concerne qu'une seule table
			include_spip("services/${type}/${type}_api");
			foreach ($services as $_service) {
				$tables[] = $GLOBALS['isocode'][$type][$_service]['table'];
			}
			$tables = array_unique($tables);
		}
	}

	return $tables;
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
 * @return string|array
 *      Le ou les tables associées au service fourni suivant le type de service.
 *      - nomenclature : tableau des tables pouvant être chargées par le service ou tableau vide.
 *      - geometrie    : table unique pouvant être chargée par le service ou chaine vide.
 */
function isocode_trouver_table($type, $service) {

	static $services = array();
	$tables_trouvees = ($type === 'nomenclature') ? array() : '';

	if ($type) {
		if (!isset($services[$type])) {
			$services[$type] = isocode_lister_services($type);
		}

		if (in_array($service, $services[$type])) {
			if ($type === 'nomenclature') {
				include_spip("services/${type}/${service}/${service}_api");
				$tables_trouvees = array_keys($GLOBALS['isocode'][$service]);
			} else {
				include_spip("services/${type}/${type}_api");
				$tables_trouvees = $GLOBALS['isocode'][$type][$service]['table'];
			}
		}
	}

	return $tables_trouvees;
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
function isocode_lire_consignation($type, $service, $table) {

	// Récupérer la meta propre au règne afin de la retourner.
	include_spip('inc/config');
	$meta = ($type === 'nomenclature')
		? lire_config("isocode/${type}/${table}")
		: lire_config("isocode/${type}/${service}");

	return $meta;
}
