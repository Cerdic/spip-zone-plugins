<?php
/**
 * Gestion du formulaire de chargement ou de vidage des tables de codes ISO.
 *
 * @package    SPIP\ISOCODE\OBJET
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement des données : le formulaire propose les actions possibles sur les tables de codes ISO,
 * à savoir, charger ou vider et la liste des tables regroupées par service.
 *
 * @return array
 *      Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 *      champ de saisie, celle-ci sont systématiquement remises à zéro.
 *      - `_actions_tables`        : (affichage) alias et libellés des actions possibles sur une table, `charger` et
 *        `vider`
 *      - `_actions_disable`    : (affichage) liste des actions désactivées (`vider` si aucune table n`est chargée)
 *      - `_action_defaut`        : (affichage) action sélectionnée par défaut, `charger`
 *      - `_tables`                : (affichage) noms des tables sans le préfixe `spip_`
 */
function formulaires_isocode_gerer_table_charger() {
	$valeurs = array();
	include_spip('isocode_fonctions');

	// Lister les actions sur les tables
	$valeurs['_actions_tables'] = array(
		'charger' => _T('isocode:label_action_charger_table'),
		'vider'   => _T('isocode:label_action_vider_table')
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
 *      Tableau des erreurs sur l'action et/ou la table ou tableau vide si aucune erreur.
 */
function formulaires_isocode_gerer_table_verifier() {
	$erreurs = array();

	$obligatoires = array('action_table', 'tables');
	foreach ($obligatoires as $_obligatoire) {
		if (!_request($_obligatoire)) {
			$erreurs[$_obligatoire] = _T('info_obligatoire');
		}
	}

	return $erreurs;
}

/**
 * Exécution du formulaire : les tables choisies sont soit vidées, soit chargées.
 *
 * @uses isocode_charger_tables()
 * @uses isocode_vider_tables()
 * @uses formater_message()
  *
 * @return array
 *      Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 *      d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_isocode_gerer_table_traiter() {

	$retour = array();

	// Acquisition des saisies: comme elles sont obligatoires, il existe toujours une action et une
	// table.
	$action = _request('action_table');
	$tables = _request('tables');

	// Pour chaque table, on génère l'action demandée.
	// (La fonction de chargement de la table lance un vidage préalable si la table demandé est déjà chargée)
	$actionner = "isocode_${action}_tables";
	$statut = $actionner($tables);

	// Formatage du message avant renvoie au formulaire
	$retour = formater_message($action, $statut);
	$retour['editable'] = true;

	return $retour;
}


/**
 * Formate les messages de succès et d'erreur résultant des actions de chargement ou de vidage
 * des tables de codes ISO.
 *
 * @param string $action
 *      Action venant d'être appliquée à certaines tables. Peut prendre les valeurs `charger` et
 *      `vider`.
 * @param array  $statut
 * 		Tableau résultant de l'action sur les tables choisies:
 *      - `ok`         : `true` si le vidage a réussi, `false` sinon.
 *      - `tables_ok`  : liste des tables vidées avec succès ou tableau vide sinon.
 *      - `tables_nok` : liste des tables en erreur ou tableau vide sinon.
 *      - `tables_sha` : liste des tables inchangées (SHA identique) ou tableau vide sinon.
 *                       Uniquement disponible pour l'action `charger`.
 *
 * @return array
 *      Tableau des messages à afficher sur le formulaire:
 *      - `message_ok`     : message sur les tables ayant été traitées avec succès ou tableau vide sinon.
 *      - `message_erreur` : message sur les tables en erreur ou tableau vide sinon.
 */
function formater_message($action, $statut) {

	$message = array(
		'message_ok'     => '',
		'message_erreur' => ''
	);

	// Traitement des succès
	if (!empty($statut['tables_ok'])) {
		$message['message_ok'] .= _T("isocode:succes_${action}_table", array('tables' => implode(', ', $statut['tables_ok'])));
	}

	// Traitement des erreurs
	if (!empty($statut['tables_nok'])) {
		$message['message_erreur'] .= _T("isocode:erreur_${action}_table", array('tables' => implode(', ', $statut['tables_nok'])));
	}
	if (!empty($statut['tables_sha'])) {
		$message['message_erreur'] .= $message['message_erreur'] ? '<br />' : '';
		$message['message_erreur'] .= _T("isocode:notice_${action}_table", array('tables' => implode(', ', $statut['tables_sha'])));
	}

	return $message;
}