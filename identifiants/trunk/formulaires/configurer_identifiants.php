<?php
/**
 * Formulaire de configuration du plugin identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     Tcharlss
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/cvt_configurer');
include_spip('inc/config');
include_spip('inc/identifiants');
include_spip('base/objets');

/**
 * Charger les valeurs
 *
 * Stratégie : il nous faut connaître les tables disposant nativement d'une colonne `identifiant`.
 * Pour ce faire, il faut les répertorier à chaque chargement du formulaire
 * en cas d'ajout ou suppression de plugins.
 *
 * @param array $flux
 * @return array
 */
function formulaires_configurer_identifiants_charger_dist() {

	// D'abord, chargement classique des valeurs via cvtconf
	$valeurs = cvtconf_formulaires_configurer_recense('configurer_identifiants');
	$valeurs['editable'] = true;
	// On sait pas à quoi ça sert mais c'est dans cvtconf_formulaire_charger()...
	if (_request('var_mode') == 'configurer' and autoriser('webmestre')) {
		if (!_AJAX) {
			var_dump($valeurs);
		}
		$valeurs['_hidden'] = "<input type='hidden' name='var_mode' value='configurer' />";
	}

	// Mettre à jour la liste des tables natives
	identifiants_repertorier_tables_natives();
	$valeurs['tables_natives'] = identifiants_lister_tables_natives();

	// Liste des tables utiles manquantes
	$valeurs['tables_utiles_manquantes'] = identifiants_lister_tables_utiles_manquantes();

	return $valeurs;
}

/**
 * Vérifier les valeurs postées
 *
 * Demander confirmation quand des identifiants vont être supprimés
 * suite à une déselection d'objets.
 *
 * @param array $flux
 * @return array
 */
function formulaires_configurer_identifiants_verifier_dist() {
	$erreurs = array();

	$confirmation = _request('confirmation');
	if (!$confirmation) {
		$tables_anciennes = identifiants_lister_tables_identifiables();
		$tables_selectionnees = _request('objets');
		if ($tables_deselectionnees = array_diff($tables_anciennes, $tables_selectionnees)) {
			$nb_supprimes = 0;
			foreach ($tables_deselectionnees as $table) {
				$nb_supprimes += intval(sql_countsel($table, 'identifiant!='.sql_quote('')));
			}
			if ($nb_supprimes) {
				set_request('confirmation', true);
				$erreurs['message_erreur'] = _T('identifiant:message_confirmer_suppression', array('nb' => $nb_supprimes));
			}
		}
	} else {
		set_request('confirmation', false);
	}

	return $erreurs;
}

/**
 * Traiter les valeurs postées
 *
 * @param array $flux
 * @return array
 */
function formulaires_configurer_identifiants_traiter_dist() {

	$retour = array();

	// D'abord, mise à jour des tables si la sélection a changé
	$tables_anciennes     = identifiants_lister_tables_identifiables();
	$tables_selectionnees = array_filter(_request('objets'));
	if ($tables_anciennes !== $tables_selectionnees) {
		include_spip('inc/identifiants');
		$adapter = identifiants_adapter_tables($tables_selectionnees);
	}
	// Messages de retour
	$message_ok_adapter     = '';
	$message_erreur_adapter = '';
	if (!empty($adapter)) {
		foreach ($adapter['ok'] as $action => $tables) {
			$message_ok_adapter .= '<br>'.ucfirst(_T('identifiant:message_ok_adapter_tables', array('action' => $action, 'tables' => join(', ', $tables))));
		}
		foreach ($adapter['erreur'] as $action => $tables) {
			$message_erreur_adapter .= '<br>'.ucfirst(_T('identifiant:message_erreur_adapter_tables', array('action' => $action, 'tables' => join(', ', $tables))));
		}
	}

	// Ensuite, traitement normal via cvtconf
	$trace      = cvtconf_formulaires_configurer_enregistre('configurer_identifiants', array());
	$message_ok = _T('config_info_enregistree') . $trace;

	$retour['message_ok']     = $message_ok . $message_ok_adapter;
	$retour['message_erreur'] = $message_erreur_adapter;
	$retour['editable']       = true;

	return $retour;
}
