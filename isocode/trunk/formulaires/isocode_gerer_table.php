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
 * 		- `_tables`				: (affichage) noms des tables sans spip
 */
function formulaires_isocode_gerer_table_charger() {
	$valeurs = array();
	include_spip('isocode_fonctions');

	// Lister les actions sur les tables
	$valeurs['_actions_tables'] = array(
		'charger' => _T('isocode:label_action_charger_table'),
		'vider' => _T('isocode:label_action_vider_table')
	);

	// Acquérir la liste des tables par service et leur statut de chargement
	$tables = isocode_lister_tables();

	// Désactiver l'action vider si aucun table n'est chargée

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
 * Exécution du formulaire : le règne choisi est soit vidé, soit chargé jusqu'au rang minimal
 * choisi en y intégrant les traductions des noms communs sélectionnées.
 *
 * @uses taxonomie_regne_existe()
 * @uses taxonomie_vider_regne()
 * @uses taxonomie_charger_regne()
 *
 * @return array
 * 		Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 * 		d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_isocode_gerer_table_traiter() {
	$retour = array();

	$action = _request('action_table');
	$regne = _request('tables');

	$ok = true;
	$item = '';
	$table_existe = true;
	if ($action == 'vider') {
		if ($table_existe) {
//			$ok = taxonomie_vider_regne($regne);
//			$item = $ok ? 'isocode:succes_vider_table' : 'isocode:erreur_vider_table';
		}
		else {
			// Inutile de vider un règne non encore chargé
			$ok = false;
			$item = 'isocode:notice_vider_table_non_chargee';
		}
	}
	else {
		// La fonction de chargement du règne lance un vidage préalable si le règne
		// demandé est déjà chargé. Un mécanisme de sauvegarde interne permet aussi de
		// restituer les modifications manuelles des taxons
//		$ok = taxonomie_charger_regne($regne, $rang_feuille, $langues);
		$item = $ok ? 'isocode:succes_charger_table' : 'isocode:erreur_charger_table';
	}

	$message = $ok ? 'message_ok' : 'message_erreur';
	$retour[$message] = _T($item);
	$retour['editable'] = true;

	return $retour;
}

?>