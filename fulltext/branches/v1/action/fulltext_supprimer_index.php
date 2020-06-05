<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/fulltext');
function action_fulltext_supprimer_index_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($table,$nom) = explode('/', $arg);

	$ok = $erreur = '';
	if (autoriser('webmestre')) {
		$tables = fulltext_liste_des_tables();
		if ($table and isset($tables[$table])) {
			list($ok,$erreur) = fulltext_supprimer_index($table, $nom);
		}
	}

	$GLOBALS['redirect'] = _request('redirect');
	if ($ok) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'ok', $ok);
	}
	if ($erreur) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'erreur', $erreur);
	}
}

function fulltext_supprimer_index($table, $nom = 'tout') {
	if (!sql_alter($query = 'TABLE ' . table_objet_sql($table) . ' DROP INDEX ' . $nom)) {
		spip_log($query, 'fulltext'._LOG_ERREUR);
		return array('', "$table : ". _T('spip:erreur') . ' ' . sql_errno() . ' ' . sql_error());
	} else {
		if ($table == 'document' && $nom == 'tout') {
			// Plus besoin des donnees extraites des fichiers
			sql_updateq('spip_documents', array('contenu' => ''), "extrait='n/a'");
		}
		return array("$table : ". _T('fulltext:index_supprime'), '');
	}
}
