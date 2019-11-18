<?php
/**
 * Fonctions utiles au plugin identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Lister les identifiants utiles qui ne sont pas encore créés
 *
 * @return Array
 */
function identifiants_lister_utiles() {

	if (
		$identifiants_utiles = pipeline('identifiants_utiles', array())
		and is_array($identifiants_utiles)
	) {
		foreach ($identifiants_utiles as $objet => $identifiants) {
			// on retire les identifiants existants de la liste
			foreach ($identifiants as $identifiant) {
				if (sql_countsel(
					'spip_identifiants',
					'objet = '.sql_quote($objet).' AND identifiant = '.sql_quote($identifiant)
				)
				) {
					unset($identifiants_utiles[$objet][array_search($identifiant, $identifiants_utiles[$objet])]);
				}
			}
			// on retire l'objet de la liste s'il ne reste plus d'identifiant
			if (!count($identifiants_utiles[$objet])) {
				unset($identifiants_utiles[$objet]);
			}
		}
	}

	return $identifiants_utiles;
}


/**
 * Retourne la liste des tables des objets utiles non activés
 *
 * Le pipeline `identifiants_utiles` doit renvoyer un tableau de la forme suivante :
 *     objetA => [identifiantA, identifiantB, ...],
 *     objetB => [identifiantX, identifiantY, ...]
 *
 * @uses pipeline identifiants_utiles()
 * @return Array
 */
function identifiants_lister_tables_utiles_manquantes() {

	$tables_utiles_manquantes = array();
	$tables_identifiables = identifiants_lister_tables_identifiables();

	if (
		$identifiants_utiles = pipeline('identifiants_utiles')
		and is_array($identifiants_utiles)
		and $tables_utiles = array_map('table_objet_sql', array_keys($identifiants_utiles))
	) {
		$tables_utiles_manquantes = array_diff($tables_utiles, $tables_identifiables);
	}

	return $tables_utiles_manquantes;
}


/**
 * Répertorier les tables disposant nativement d’une colonne `identifiant`.
 *
 * On tient à jour dans la config une liste de *toutes* les tables.
 * Pour chacune on indique avec un booléen si elle a nativement la colonne ou pas.
 *
 * Cette fonction ne renvoie rien, utiliser identifiants_lister_tables_natives() pour avoir la liste.
 *
 * @return Void
 */
function identifiants_repertorier_tables_natives() {

	include_spip('inc/config');
	include_spip('base/objets');
	$tables_repertoriees = lire_config('identifiants/tables_repertoriees', array());
	$tables_toutes       = lister_tables_objets_sql();

	// Ajouts (plugins activés)
	if ($tables_non_repertoriees = array_diff(array_keys($tables_toutes), array_keys($tables_repertoriees))) {
		foreach ($tables_non_repertoriees as $table) {
			// On utilise show table pour éviter les soucis de cache bizarroïdes
			// avec lister_tables_objets_sql qui peuvent donner des faux positifs/négatifs.
			$showtable = sql_showtable($table);
			$has_identifiant = isset($showtable['field']['identifiant']);
			$tables_repertoriees[$table] = $has_identifiant;
		}
	}

	// Supressions (plugins désactivés)
	if ($tables_supprimees = array_diff(array_keys($tables_repertoriees), array_keys($tables_toutes))) {
		foreach ($tables_supprimees as $table) {
			unset($tables_repertoriees[$table]);
		}
	}

	ecrire_config('identifiants/tables_repertoriees', $tables_repertoriees);
}


/**
 * Adapter les tables selon les objets configurés :
 *
 * - Ajout de la colonne `identifiant` sur les tables des objets sélectionnés
 * - Suppression pour les autres
 *
 * La fonction ne renvoie pas de mise en garde en cas de perte de données,
 * il faut faire le test des éventuels identifiants qui vont être supprimés en amont.
 *
 * @note
 * Depuis le formulaire de config, appeler cette fonction **AVANT**
 * d'avoir écrit la nouvelle config avec les objets sélectionnés,
 * en utilisant le paramètre `$tables_selectionnees`.
 *
 * @param Array|Null $tables_selectionnees
 *     Si on la connait, liste des nouvelles tables sélectionnées pour économiser les traitements.
 *     Sinon, on passe sur toutes les tables en fonction de la config.
 * @return Array
 *     Tableau associatif :
 *     ok     => ajout   => tables où la colonne a été ajoutée.
 *               retrait => tables où elle a été supprimée.
 *     erreur => ajout   => tables où elle n'a pas pu être ajoutée.
 *               retrait => tables où elle n'a pas pu être supprimée.
 */
function identifiants_adapter_tables($tables_selectionnees = null) {

	include_spip('inc/config');
	include_spip('base/objets');

	$tables_toutes        = lister_tables_objets_sql();
	$tables_natives       = identifiants_lister_tables_natives();
	$tables_identifiables = identifiants_lister_tables_identifiables(); // celles en config
	$tables_selectionnees = is_array($tables_selectionnees) ? array_filter($tables_selectionnees) : $tables_selectionnees;
	$tables_ajouter       = array();
	$tables_supprimer     = array();
	$retour               = array();
	$ok                   = array();
	$erreur               = array();

	// Déterminer les tables où il faut ajouter ou retirer :
	// Soit on a une liste à l'avance, auquel cas on fait le delta avec la config.
	if (
		is_array($tables_selectionnees)
		and ($tables_selectionnees != $tables_identifiables) // il y a quelque chose à faire
	) {
		$tables_ajouter   = array_diff($tables_selectionnees, $tables_identifiables);
		$tables_supprimer = array_diff($tables_identifiables, $tables_selectionnees, $tables_natives);
	// Soit on passe sur toutes les tables : config → ajouter, le reste → supprimer.
	} elseif (is_null($tables_selectionnees)) {
		$tables_ajouter   = $tables_identifiables;
		$tables_supprimer = array_diff(array_keys($tables_toutes), $tables_ajouter, $tables_natives);
	}

	// Ajout de la colonne
	if ($tables_ajouter) {
		include_spip('base/create');
		foreach ($tables_ajouter as $table) {
			// On utilise show table pour éviter les soucis de cache bizarroïdes
			// avec lister_tables_objets_sql qui peuvent donner des faux positifs/négatifs.
			$showtable = sql_showtable($table);
			$has_identifiant = isset($showtable['field']['identifiant']);
			if (!$has_identifiant) {
				$desc                           = $tables_toutes[$table];
				$desc['field']['identifiant']   = "VARCHAR(255) NOT NULL DEFAULT ''";
				$desc['key']['KEY identifiant'] = 'identifiant';
				creer_ou_upgrader_table($table, $desc, false, true);
				$alter_col = true;
				// $alter_col = sql_alter("TABLE $table ADD COLUMN identifiant VARCHAR(255) NOT NULL DEFAULT ''");
				// $alter_key = sql_alter("TABLE $table ADD KEY identifiant (identifiant)");
				if ($alter_col) {
					$ok['ajout'][] = $table;
				} else {
					$erreur['ajout'][] = $table;
				}
			}
		}
	}

	// Suppression de la colonne
	if ($tables_supprimer) {
		foreach ($tables_supprimer as $table) {
			$has_colonne = isset($tables_toutes[$table]['field']['identifiant']);
			if ($has_colonne) {
				$alter_col = sql_alter("TABLE $table DROP COLUMN identifiant");
				$alter_key = sql_alter("TABLE $table DROP KEY identifiant");
				if ($alter_col) {
					$ok['retrait'][] = $table;
				} else {
					$erreur['retrait'][] = $table;
				}
			}
		}
	}

	// Vider le cache si changement
	// FIXME : nécessaire ?
	if (!empty($ok)) {
		$trouver_table = charger_fonction('trouver_table', 'base');
		$trouver_table('');
		include_spip('inc/invalideur');
		suivre_invalideur("id='identifiants/adapter_tables'");
	}

	// Logs
	foreach ($ok as $action => $tables) {
		$message = ucfirst(_T('identifiant:message_ok_adapter_tables', array('action' => $action, 'tables' => join(', ', $tables))));
		spip_log($message, 'identifiants');
	}
	foreach ($erreur as $action => $tables) {
		$message = ucfirst(_T('identifiant:message_erreur_adapter_tables', array('action' => $action, 'tables' => join(', ', $tables))));
		spip_log($message, 'identifiants'._LOG_ERREUR);
	}

	$retour['ok']     = $ok;
	$retour['erreur'] = $erreur;

	return $retour;
}


/**
 * Nettoyer les tables auxquelles on a ajouté une colonne `identifiant`
 *
 * @return void
 */
function identifiants_nettoyer_tables() {
	if ($tables = identifiants_lister_tables_identifiables()) {
		foreach ($tables as $table) {
			sql_alter("TABLE $table DROP COLUMN identifiant");
			sql_alter("TABLE $table DROP KEY identifiant");
		}
		include_spip('inc/invalideur');
		suivre_invalideur("id='identifiants/nettoyer_tables'");
	}
}
