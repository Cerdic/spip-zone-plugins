<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_fulltext_convert_engine_dist($table = null, $engine = null) {
	if (is_null($table)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$table = $securiser_action();
	}

	include_spip('inc/fulltext');
	if ($engine === null) {
		$engine = 'innodb';
	} else {
		$engine = strtolower((string)$engine);
	}
	$engines = fulltext_accepted_engines();
	if (!in_array($engine, array_keys($engines))) {
		include_spip('inc/minipres');
		minipres("Engine $engine non reconnu.");
		exit;
	}

	$oks = $erreurs = array();
	if (autoriser('webmestre')) {
		if ($table and $table !== 'all') {
			list($ok, $erreur) = fulltext_conversion_engine($table, $engine);
			$oks[] = $ok;
			$erreurs[] = $erreur;
		}

		if ($table === 'all') {
			$tables = fulltext_liste_des_tables();
			foreach ($tables as $table => $desc) {
				$my_engine = fulltext_trouver_engine_table($table);
				if (strtolower($my_engine) !== $engine) {
					list($ok, $erreur) = fulltext_conversion_engine($table, $engine);
					$oks[] = $ok;
					$erreurs[] = $erreur;
				}
			}
		}
	}


	$oks = array_filter($oks);
	$erreurs = array_filter($erreurs);
	$GLOBALS['redirect'] = _request('redirect');
	if ($oks) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'ok', $oks);
	}
	if ($erreurs) {
		$GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'], 'erreur', $erreurs);
	}
}



function fulltext_conversion_engine($table, $engine = 'innodb') {
	$engines = fulltext_accepted_engines();
	$engine = strtolower((string) $engine);
	$_engine = isset($engines[$engine]) ? $engines[$engine] : null;
	if (!$_engine) {
		return array('', "$engine inconnu");
	}

	if (!sql_alter('TABLE ' . table_objet_sql($table) . ' ENGINE=' . $_engine)) {
		return array('', "$table : "._T('spip:erreur') . ' ' . sql_errno() . ' ' . sql_error());
	} else {
		return array("$table : "._T('fulltext:table_convertie_engine', array('engine' => $_engine)), '');
	}
}
