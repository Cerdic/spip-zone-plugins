<?php
/**
 * Gestion du formulaire de chargement des taxons d'un règne.
 *
 * @package    SPIP\TAXONOMIE\TAXON
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire propose de charger ou vider un des 3 règnes gérés par Taxonomie. Pour le
 * chargement d'un règne, le formulaire propose de choisir les langues vernaculaires à utiliser parmi celles
 * supportées par le plugin.
 *
 * @uses taxonomie_regne_existe()
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro.
 * 		- `_actions_regnes`		: (affichage) alias et libellés des actions possibles sur un règne, `charger` et `vider`
 * 		- `_actions_disable`	: (affichage) liste des actions désactivées (`vider` si le règne n`est pas chargé)
 * 		- `_action_defaut`		: (affichage) action sélectionnée par défaut, `charger`
 * 		- `_regnes`				: (affichage) noms scientifiques et libellés des règnes supportés par le plugin
 * 		- `_langues_regne`		: (affichage) codes de langue SPIP et libellés des langues utilisées (configuration)
 * 		- `_langue_defaut`		: (affichage) la première langue de la liste des langues utilisées
 */
function formulaires_charger_regne_charger() {
	$valeurs = array();
	include_spip('inc/taxonomie');

	// Lister les actions sur les règnes
	$valeurs['_actions_regne'] = array(
		'charger' => _T('taxonomie:label_action_charger_regne'),
		'vider' => _T('taxonomie:label_action_vider_regne')
	);

	// Acquérir la liste des règnes gérer par le plugin et leur statut de chargement
	// Désactiver l'action vider si aucun règne n'est chargé
	$aucun_regne_charge = true;
	$regnes = regne_lister();
	foreach ($regnes as $_regne) {
		$valeurs['_regnes'][$_regne] = '<span class="nom_scientifique_inline">' . $_regne . '</span>, ' . _T("taxonomie:regne_${_regne}");
		if (taxonomie_regne_existe($_regne, $meta_regne)) {
			$valeurs['_regnes'][$_regne] .= ' [' . _T("taxonomie:info_regne_charge") . ']';
			$aucun_regne_charge = false;
		}
	}
	if ($aucun_regne_charge) {
		$valeurs['_actions_disable'] = array('vider' => 'oui');
		$valeurs['_action_defaut'] = 'charger';
	}

	// Acquérir la liste des langues utilisables par le plugin et stockées dans la configuration.
	$langues_utilisees = lire_config('taxonomie/langues_utilisees');
	foreach ($langues_utilisees as $_code_langue) {
		$valeurs['_langues_regne'][$_code_langue] = traduire_nom_langue($_code_langue);
	}
	$valeurs['_langues_defaut'] = reset($langues_utilisees);

	return $valeurs;
}

/**
 * Vérification des saisies : il est indispensable de choisir une action (`vider` ou `charger`) et
 * un règne.
 * Un rang minimal est toujours sélectionné. La saisie des langues des noms communs est optionnelle.
 *
 * @return array
 * 		Tableau des erreurs sur l'action et/ou le règne ou tableau vide si aucune erreur.
 */
function formulaires_charger_regne_verifier() {
	$erreurs = array();

	$obligatoires = array('action_regne', 'regne');
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
 * @uses taxonomie_regne_vider()
 * @uses taxonomie_regne_charger()
 *
 * @return array
 * 		Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 * 		d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_charger_regne_traiter() {
	$retour = array();

	$action = _request('action_regne');
	$regne = _request('regne');
	$regne_existe = taxonomie_regne_existe($regne, $meta_regne);

	if ($action == 'vider') {
		if ($regne_existe) {
			$ok = taxonomie_regne_vider($regne);
			$item = $ok ? 'taxonomie:succes_vider_regne' : 'taxonomie:erreur_vider_regne';
		}
		else {
			// Inutile de vider un règne non encore chargé
			$ok = false;
			$item = 'taxonomie:notice_vider_regne_inexistant';
		}
	}
	else {
		// La fonction de chargement du règne lance un vidage préalable si le règne
		// demandé est déjà chargé. Un mécanisme de sauvegarde interne permet aussi de
		// restituer les modifications manuelles des taxons
		$langues = _request('langues_regne');
		$ok = taxonomie_regne_charger($regne, $langues);
		$item = $ok ? 'taxonomie:succes_charger_regne' : 'taxonomie:erreur_charger_regne';
	}

	$message = $ok ? 'message_ok' : 'message_erreur';
	$retour[$message] = _T($item, array('regne' => '<i>' . ucfirst($regne) . '</i>'));
	$retour['editable'] = true;

	return $retour;
}
