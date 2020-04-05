<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/fulltext');

function fulltext_liste_creer_index($arg = null) {

	list($table, $nom) = array_pad(explode('/', $arg), 2, null);

	$ok = $erreur = '';

	$tables = fulltext_liste_des_tables();
	if ($table and isset($tables[$table]) and isset($tables[$table]['index_prop'][$nom])) {
		list($ok, $erreur) = fulltext_creer_index($table, $nom, $tables[$table]['index_prop'][$nom]);
	} elseif ($table == 'all') {
		foreach ($tables as $table => $desc) {
			foreach ($desc['index_prop'] as $nom => $champs) {
				list($ok1, $erreur1) = fulltext_creer_index($table, $nom, $champs);
				if ($ok1) {
					$ok .= $ok1 . '<br />';
				}
				if ($erreur1) {
					$erreur .= $erreur1 . '<br />';
				}
			}
		}
	}

	return array($ok, $erreur);
}



function fulltext_creer_index($table, $nom, $vals) {
	$index = fulltext_index($table, $vals, $nom);

	if ($table == 'document' && $nom == 'tout') {
		// On initialise l'indexation du contenu des documents
		sql_updateq('spip_documents', array('contenu' => ''), "extrait='non'");
	}
	if (!$s = sql_alter($query = 'TABLE ' . table_objet_sql($table) . ' ADD FULLTEXT ' . $index)) {
		spip_log($query, 'fulltext'._LOG_ERREUR);
		return array('', "$table : " . _T('spip:erreur') . ' ' . sql_errno() . ' ' . sql_error());
	}
	sql_optimize(table_objet_sql($table));

	$keys = fulltext_keys($table);

	if (isset($keys[$nom])) {
		return array("$table : " . _T('fulltext:fulltext_cree') . " : $keys[$nom]", '');
	} else {
		return array('', "$table : "._T('spip:erreur'));
	}
}
