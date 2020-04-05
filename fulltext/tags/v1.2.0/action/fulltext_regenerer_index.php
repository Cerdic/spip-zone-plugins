<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/fulltext');
function action_fulltext_regenerer_index_dist($table = null) {

	if (is_null($table)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$table = $securiser_action();
	}

	$ok = $erreur = '';
	if (autoriser('webmestre')) {
		$tables = fulltext_liste_des_tables();
		if ($table and isset($tables[$table])) {
			list($ok,$erreur) = fulltext_regenerer_index($table, $tables[$table]['keys']);
		} elseif ($table == 'all') {
			foreach ($tables as $table => $desc) {
				fulltext_regenerer_index($table, $tables[$table]['keys']);
			}
			$ok = _T('fulltext:index_regenere');
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

function fulltext_regenerer_index($table, $keys) {
	if (count($keys) > 0) {
		foreach ($keys as $key => $vals) {
			if (!sql_alter($query = 'TABLE '.table_objet_sql($table).' DROP INDEX '.$key)) {
				spip_log($query, 'fulltext'._LOG_ERREUR);
				return array('', "$table :" . _T('spip:erreur') . ' ' . sql_errno() . ' ' . sql_error());
			}
			if (!sql_alter($query = 'TABLE '.table_objet_sql($table).' ADD FULLTEXT '.$key.' ('.$vals.')')) {
				spip_log($query, 'fulltext'._LOG_ERREUR);
				return array('', "$table :" . _T('spip:erreur') . ' ' . sql_errno() . ' ' . sql_error());
			}
			sql_optimize(table_objet_sql($table));
		}
		return array("$table :" . _T('fulltext:index_regenere'),'');
	}
}
