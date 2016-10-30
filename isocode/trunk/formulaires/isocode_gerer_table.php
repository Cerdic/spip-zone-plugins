<?php
/**
 * Gestion du formulaire de chargement ou de vidage des tables de codes ISO.
 *
 * @package    SPIP\ISOCODE\OBJET
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire propose les actions possibles sur les tables de codes ISO,
 * à savoir, charger ou vider et la liste des tables regroupées par service.
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro.
 * 		- `_actions_tables`		: (affichage) alias et libellés des actions possibles sur une table, `charger` et `vider`
 * 		- `_actions_disable`	: (affichage) liste des actions désactivées (`vider` si aucune table n`est chargée)
 * 		- `_action_defaut`		: (affichage) action sélectionnée par défaut, `charger`
 * 		- `_tables`				: (affichage) noms des tables sans le préfixe `spip_`
 */
function formulaires_isocode_gerer_table_charger() {
	$valeurs = array();
	include_spip('isocode_fonctions');

	// Lister les actions sur les tables
	$valeurs['_actions_tables'] = array(
		'charger' => _T('isocode:label_action_charger_table'),
		'vider' => _T('isocode:label_action_vider_table')
	);

	// Acquérir la liste des tables et leur statut de chargement
	$tables = isocode_lister_tables();
	$aucune_table_charge = true;
	if ($tables) {
		foreach ($tables as $_table) {
			$valeurs['_tables'][$_table] = "<em>${_table}</em>, " . _T("isocode:label_table_${_table}");
			if (isocode_table_chargee($_table, $meta_regne)) {
				$valeurs['_tables'][$_table] .= ' - [' . _T('isocode:info_table_chargee') . ']';
				$aucune_table_charge = false;
			}
		}
	}

	// Désactiver l'action vider si aucun table n'est chargée
	if ($aucune_table_charge) {
		$valeurs['_actions_disable'] = array('vider' => 'oui');
		$valeurs['_action_defaut'] = 'charger';
	}

	return $valeurs;
}

/**
 * Vérification des saisies : il est indispensable de choisir une action (`vider` ou `charger`) et
 * une table.
 *
 * @return array
 * 		Tableau des erreurs sur l'action et/ou la table ou tableau vide si aucune erreur.
 */
function formulaires_isocode_gerer_table_verifier() {
	$erreurs = array();

	$obligatoires = array('action_table', 'tables');
	foreach ($obligatoires as $_obligatoire) {
		if (!_request($_obligatoire))
			$erreurs[$_obligatoire] = _T('info_obligatoire');
	}

	return $erreurs;
}

/**
 * Exécution du formulaire : les tables choisies sont soit vidées, soit chargées.
 *
 * @uses isocode_charger_tables()
 * @uses isocode_decharger_tables()
 *
 * @return array
 * 		Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 * 		d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_isocode_gerer_table_traiter() {

	$retour = array();

	// Acquisition des saisies: comme elles sont obligatoires, il existe toujours une action et une
	// table.
	$action = _request('action_table');
	$tables = _request('tables');

	// Pour chaque table, on génère l'action demandée
	if ($action == 'vider') {
		list($action_ok, $tables_nok) = isocode_decharger_tables($tables);
		$message = $action_ok
			? _T('isocode:succes_vider_table')
			: _T('isocode:erreur_vider_table', array('tables' => implode(', ', $tables_nok)));
	}
	else {
		// La fonction de chargement de la table lance un vidage préalable si la table
		// demandé est déjà chargée.
		list($action_ok, $tables_nok, $tables_inchangees) = isocode_charger_tables($tables);
		if ($action_ok) {
			$message = _T('isocode:succes_charger_table');
		} else {
			$message = '';
			if ($tables_inchangees) {
				$message .= _T('isocode:notice_charger_table', array('tables' => implode(', ', $tables_inchangees)));
			}
			if ($tables_nok) {
				$message .= $message ? '<br/>' : '';
				$message .= _T('isocode:erreur_charger_table', array('tables' => implode(', ', $tables_nok)));
			}
		}
	}

	$type_message = $action_ok ? 'message_ok' : 'message_erreur';
	$retour[$type_message] = $message;
	$retour['editable'] = true;

	return $retour;
}
