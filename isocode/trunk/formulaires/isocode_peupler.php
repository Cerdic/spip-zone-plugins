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
 * L'action vider s'appelle décharger car il existe dékà une fonction d'administration de vidage des tables.
 *
 * @return array
 *      Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 *      champ de saisie, celle-ci sont systématiquement remises à zéro.
 *      - `_actions_tables`  : (affichage) alias et libellés des actions possibles sur une table, `charger` et
 *        `decharger`
 *      - `_actions_disable` : (affichage) liste des actions désactivées (`decharger` si aucune table n`est chargée)
 *      - `_action_defaut`   : (affichage) action sélectionnée par défaut, `charger`
 *      - `_tables`          : (affichage) noms des tables sans le préfixe `spip_`
 */
function formulaires_isocode_peupler_charger($type) {
	$valeurs = array();

	// Lister les actions sur les tables
	include_spip('inc/isocode');
	$valeurs['_action_explication'] = _T("isocode:explication_action_${type}");
	$valeurs['_actions'] = array(
		'charger' => _T('isocode:label_action_charger'),
		'decharger'   => _T('isocode:label_action_decharger')
	);

	// Construire la liste des éléments et leur statut de chargement
	$valeurs['_label_element'] = _T("isocode:label_element_${type}");
	$aucune_element_charge = true;
	if ($type === 'nomenclature') {
		$tables = isocode_lister_tables($type, true);
		if ($tables) {
			foreach ($tables as $_groupe => $_tables) {
				$groupe = _T("isocode:label_groupe_${_groupe}");
				foreach ($_tables as $_table) {
					$valeurs['_elements'][$groupe][$_table] = "<em>${_table}</em>, " . _T("isocode:${type}_${_table}");
					if (isocode_lire_consignation($type, '', $_table)) {
						$valeurs['_elements'][$groupe][$_table] .= ' - [' . _T('isocode:info_charge') . ']';
						$aucune_element_charge = false;
					}
				}
			}
		}
	} else {
		$services = isocode_lister_services($type);
		foreach ($services as $_service) {
			$valeurs['_elements'][$_service] = _T("isocode:${type}_${_service}");
			if (isocode_lire_consignation($type, $_service, '')) {
				$valeurs['_elements'][$_service] .= ' - [' . _T('isocode:info_charge') . ']';
				$aucune_element_charge = false;
			}
		}
	}

	// Désactiver l'action vider si aucun table n'est chargée
	if ($aucune_element_charge) {
		$valeurs['_actions_disable'] = array('decharger' => 'oui');
		$valeurs['_action_defaut'] = 'charger';
	}

	return $valeurs;
}

/**
 * Vérification des saisies : il est indispensable de choisir une action (`decharger` ou `charger`) et
 * une table.
 *
 * @return array
 *      Tableau des erreurs sur l'action et/ou la table ou tableau vide si aucune erreur.
 */
function formulaires_isocode_peupler_verifier($type) {
	$erreurs = array();

	$obligatoires = array('action_peuplement', 'elements');
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
 * @uses isocode_decharger_tables()
 * @uses formater_message()
  *
 * @return array
 *      Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 *      d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_isocode_peupler_traiter($type) {

	// Acquisition des saisies: comme elles sont obligatoires, il existe toujours une action et une
	// table.
	$action = _request('action_peuplement');
	$elements = _request('elements');

	// On construit le tableau services/tables à fournir aux actions charger ou décharger.
	// -- tableau de service => [tables]
	include_spip('inc/isocode');
	$services = $tables = array();
	if ($type === 'nomenclature') {
		$tables = $elements;
		foreach ($tables as $_table) {
			$service = isocode_trouver_service($type, $_table);
			$services[$service][] = $_table;
		}
	} else {
		foreach ($elements as $_service) {
			$table = isocode_trouver_table($type, $_service);
			$services[$_service][] = $table;
		}
	}

	// Pour chaque couple (service, table) on génère l'action demandée.
	// (La fonction de chargement de la table lance un vidage préalable si la table demandé est déjà chargée)
	foreach ($services as $_service => $_tables) {
		foreach ($_tables as $_table) {
			$actionner = "isocode_${action}";
			$statut[] = $actionner($type, $_service, $_table);
		}
	}

	// Formatage du message avant renvoie au formulaire
	$retour = isocode_peupler_notifier($type, $action, $statut);
	$retour['editable'] = true;

	return $retour;
}


/**
 * Formate les messages de succès et d'erreur résultant des actions de chargement ou de vidage
 * des tables de codes ISO.
 *
 * @param string $action
 *      Action venant d'être appliquée à certaines tables. Peut prendre les valeurs `charger` et
 *      `decharger`.
 * @param array  $statut
 * 		Tableau résultant de l'action sur les tables choisies:
 *      - `ok`         : `true` si le vidage a réussi, `false` sinon.
 *      - `elements_ok`  : liste des tables vidées avec succès ou tableau vide sinon.
 *      - `elements_nok` : liste des tables en erreur ou tableau vide sinon.
 *      - `elements_sha` : liste des tables inchangées (SHA identique) ou tableau vide sinon.
 *                       Uniquement disponible pour l'action `charger`.
 *
 * @return array
 *      Tableau des messages à afficher sur le formulaire:
 *      - `message_ok`     : message sur les tables ayant été traitées avec succès ou tableau vide sinon.
 *      - `message_erreur` : message sur les tables en erreur ou tableau vide sinon.
 */
function isocode_peupler_notifier($type, $action, $statuts) {

	$messages = array(
		'message_ok'     => '',
		'message_erreur' => ''
	);
	$variables = array(
		'ok'  => array(),
		'nok' => array(),
		'sha' => array(),
	);
	$statut_global = array(
		'ok'  => false,
		'nok' => false,
		'sha' => false,
	);

	// On compile la liste des pays traités et un indicateur global pour chaque cas d'erreur.
	foreach ($statuts as $_statut) {
		// Traitement des succès
		if ($_statut['erreur'] === 'sha') {
			$variables['sha'][] = ($type === 'nomenclature') ? $_statut['table'] : $_statut['service'];
			$statut_global['sha'] = true;
		} elseif ($_statut['erreur'] === 'nok') {
			$variables['nok'][] = ($type === 'nomenclature') ? $_statut['table'] : $_statut['service'];
			$statut_global['nok'] = true;
		} else {
			$variables['ok'][] = ($type === 'nomenclature') ? $_statut['table'] : $_statut['service'];
			$statut_global['ok'] = true;
		}
	}

	// Traitement des succès
	if ($statut_global['ok']) {
		$messages['message_ok'] .= _T("isocode:succes_${action}", array('elements' => implode(', ', $variables['ok'])));
	}

	// Traitement des erreurs
	if ($statut_global['nok']) {
		$messages['message_erreur'] .= _T("isocode:erreur_${action}", array('elements' => implode(', ', $variables['nok'])));
	}
	if ($statut_global['sha']) {
		$messages['message_erreur'] .= $messages['message_erreur'] ? '<br />' : '';
		$messages['message_erreur'] .= _T("isocode:notice_${action}", array('elements' => implode(', ', $variables['sha'])));
	}

	return $messages;
}
