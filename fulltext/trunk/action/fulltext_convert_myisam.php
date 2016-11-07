<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_fulltext_convert_myisam_dist($table = null) {
	if (is_null($table)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$table = $securiser_action();
	}

	$ok = $erreur = '';
	if (autoriser('webmestre')) {
		if ($table and $table !== 'all') {
			list($ok,$erreur) = fulltext_conversion_myisam($table);
		}

		if ($table == 'all') {
			include_spip('inc/fulltext');
			$tables = fulltext_liste_des_tables();
			foreach ($tables as $table => $desc) {
				$engine = fulltext_trouver_engine_table($table);
				if (strtolower($engine) !== 'myisam') {
					list($ok,$erreur) = fulltext_conversion_myisam($table);
				}
			}
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



function fulltext_conversion_myisam($table) {
	if (!sql_alter('TABLE ' . table_objet_sql($table) . ' ENGINE=MyISAM')) {
		return array('', "$table : "._T('spip:erreur') . ' ' . sql_errno() . ' ' . sql_error());
	} else {
		return array("$table : "._T('fulltext:table_convertie'), '');
	}
}
