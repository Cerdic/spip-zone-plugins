<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/rechercher');

function verifier_conversion($table) {
	$charset = strtolower(str_replace('-', '', $GLOBALS['meta']['charset']));
	$necessite_conversion = false;

	// signaler les incoherences de charset site/tables qui plantent les requetes avec accents...
	// ?exec=convert_sql_utf8 => conversion base | ?exec=convert_utf8 => conversion site
	$data = sql_fetch(sql_query('SHOW CREATE TABLE ' . table_objet_sql($table)));
	preg_match(',DEFAULT CHARSET=([^\s]+),', $data['Create Table'], $match);
	$charset_table = strtolower(str_replace('-', '', $match[1]));
	$charset_table = preg_replace(',^latin1$,', 'iso88591', $charset_table);
	if ($charset_table != '' and $charset != $charset_table) {
		$modif = (substr($charset, 0, 3) == 'iso' ? 'convert_utf8' : 'convert_sql_utf8');
		$url = generer_url_ecrire($modif);
		echo _T('fulltext:incoherence_charset') . "<strong><a href='$url'>" . _T('fulltext:convertir_utf8') . '</a></strong>';
	}
}

function compter_elements($table) {
	$nb = sql_countsel(table_objet_sql($table));
	return $nb;
}

function fulltext_liste_des_tables() {
	$champs = liste_des_champs();
	$tables = array();
	$champs_interdits = array(
		'auteur'=>array('login')
	);

	foreach ($champs as $table => $fields) {
		$tables[$table] = array(
			'fields' => $fields,
			'engine' => fulltext_trouver_engine_table($table),
			'keys' => array(),
			'index_prop' => array(),
		);

		if (strtolower($tables[$table]['engine'])=='myisam') {
			if ($keys = fulltext_keys($table)) {
				$tables[$table]['keys'] = $keys;
			}

			// le champ de titre est celui qui a le poids le plus eleve
			$c = $fields;
			asort($c);
			$c = array_keys($c);
			$champ_titre = array_pop($c);

			if (!isset($tables[$table]['keys'][$champ_titre])) {
				$tables[$table]['index_prop'][$champ_titre] = array($champ_titre);
			}
			if (!isset($tables[$table]['keys']['tout'])) {
				$tables[$table]['index_prop']['tout'] = array_keys($fields);
				if (isset($champs_interdits[$table])) {
					$tables[$table]['index_prop']['tout'] = array_diff($tables[$table]['index_prop']['tout'], $champs_interdits[$table]);
				}
			}
		}
	}
	return $tables;
}

/**
 * Récupération de l'engine utilisé par une table sql
 *
 * Retourne MyISAM ou InnoDB
 *
 * @param string $table
 * 		La table à analyser
 * @return string
 * 		Le moteur utilisé
 */
function fulltext_trouver_engine_table($table) {
	if ($s = sql_query('SHOW CREATE TABLE ' . table_objet_sql($table)) and $t = sql_fetch($s) and $create = array_pop($t) and preg_match('/\bENGINE=([^\s]+)/', $create, $engine)) {
		return $engine[1];
	}
}

function fulltext_index($table, $champs, $nom = null) {
	if (!$nom) {
		list(, $nom) = each($champs);
	}

	if ($nom !== 'tout') {
		$champs = array($nom);
	}

	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table(table_objet($table));

	foreach ($champs as $i => $f) {
		if (preg_match(',^(tiny|long|medium)?text\b,i', $desc['field'][$f])) {
			$champs[$i] = "`$f`";
		} elseif (preg_match(',^varchar.*\b,i', $desc['field'][$f]) && !preg_match(',COLLATE utf8_bin.*\s,i', $desc['field'][$f])) {
			$champs[$i] = "`$f`";
		} else {
			unset($champs[$i]);
		}
	}
	return "`$nom` (" . join(',', $champs) . ')';
}

function fulltext_lien_creer_index($table, $champs, $nom = null) {
	$url = generer_action_auteur('fulltext_creer_index', "$table/$nom", generer_url_ecrire('fulltext'));
	return bouton_action(_T('fulltext:fulltext_creer', array('index' => fulltext_index($table, $champs, $nom))), $url);
}

function fulltext_reinitialiser_document() {
	sql_updateq('spip_documents', array('contenu' => '', 'extrait' => 'non'), "extrait='err'");
	return '<p><strong>' . _T('fulltext:index_reinitialise') . '</strong></p>';
}

function fulltext_reinitialiser_totalement_document() {
	sql_updateq('spip_documents', array('contenu' => '', 'extrait' => 'non'));
	return '<p><strong>' . _T('fulltext:index_reinitialise_totalement') . '</strong></p>';
}

function fulltext_reinitialiser_document_ptg() {
	sql_updateq('spip_documents', array('contenu' => '', 'extrait' => 'non'), "extrait='ptg'");
	return '<p><strong>' . _T('fulltext:index_reinitialise_ptg') . '</strong></p>';
}
