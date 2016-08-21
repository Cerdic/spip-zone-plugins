<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin.
 *
 * @package SPIP\ISOCODE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * @return array
 */
function isocode_verifier_codes_spip() {

	$codes_verifies = array();

	include_spip('inc/lang_liste');
	if ($GLOBALS['codes_langues']) {
		foreach ($GLOBALS['codes_langues'] as $_code => $_nom) {
			$from = array('spip_iso639codes');
			$select = array('*');
			if (strlen($_code) == 2) {
				// Si le code a une taille de 2 caractères on recherche de suite dans la table iso639codes
				// un élément dont le code ISO639-1 est égal.
				$where = array('code_639_1=' . sql_quote($_code));
				$codes_iso = sql_allfetsel($select, $from, $where);
				if ($codes_iso) {
					$codes_verifies['ok']['iso6391'][$_code] = $codes_iso;
				} else {
					$codes_verifies['nok']['iso6391'][$_code] = array('nom_spip' => $_nom);
				}
			} elseif (strlen($_code) == 3) {
				// Si le code a une taille de 3 caractères on recherche :
				// - dans la table iso639codes un élément dont le code ISO639-3 est égal.
				// - et sinon dans la table iso639families un élément dont le code ISO639-5 est égal.
				$where = array('code_639_3=' . sql_quote($_code));
				$codes_iso = sql_allfetsel($select, $from, $where);
				if ($codes_iso) {
					$codes_verifies['ok']['iso6393'][$_code] = $codes_iso;
				} else {
					$where = array('code_639_5=' . sql_quote($_code));
					$code_famille = sql_fetsel($select, array('spip_iso639families'), $where);
					if ($code_famille) {
						$codes_verifies['ok']['iso6395'][$_code] = array('nom_spip' => $_nom);
					} else {
						$codes_verifies['nok']['iso6393'][$_code] = array('nom_spip' => $_nom);
					}
				}
			} else {
				$codes_verifies['nok']['spip'][$_code] = array('nom_spip' => $_nom);
			}
		}
	}

	return $codes_verifies;
}

/**
 * @return array
 */
function isocode_verifier_iso639_5() {

	$codes_verifies = array();

	$codes_639_5 = sql_allfetsel('*', 'spip_iso639families');
	if ($codes_639_5) {
		foreach ($codes_639_5 as $_code) {
			if (sql_countsel('spip_iso639macros', 'code_639_3=' . sql_quote($_code['code_639_5']))) {
				$codes_verifies['ok'][] = $_code['code_639_5'];
			} else {
				$codes_verifies['nok'][] = $_code['code_639_5'];
			}
		}
	}

	return $codes_verifies;
}
