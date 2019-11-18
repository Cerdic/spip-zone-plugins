<?php
/**
 * Générer (ou mettre à jour/supprimer) l'identifiant d'un objet
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     Tcharlss
 * @licence    GNU/GPL
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action : attribuer un identifiant à un objet
 *
 * @param string|null $arg
 *     identifiant-objet-id_objet
 * @return void
 */
function action_attribuer_identifiant_dist($arg = null) {

	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($identifiant, $objet, $id_objet) = explode('-', $arg);
	if (
		$identifiant
		and $objet
		and $id_objet = intval($id_objet)
	) {
		include_spip('action/editer_objet');
		include_spip('base/objets');
		$table_objet = table_objet_sql($objet);
		$tables_identifiables = identifiants_lister_tables_identifiables();
		$set = array('identifiant' => $identifiant);
		if (in_array($table_objet, $tables_identifiables)) {
			objet_modifier($objet, $id_objet, $set);
		}
	}
}
