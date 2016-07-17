<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin Codes de Langues.
 *
 * @package SPIP\CODELANG\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Charge toutes les tables iso dans la base de données à partir des fichiers .tab issus du site de iso.
 *
 * @api
 * @filtre
 * @uses iso_read_table()
 *
 * @param array	$tables_iso
 * 		Tableau des tables ISO-639 à charger ou tableau vide sinon. Si le tableau est vide, l'ensemble des
 * 		tables ISO seront chargées. Les tables doivent être libellées sans le préfixe `spip_`.
 *
* @return bool
 *        `true` si le chargement a réussi, `false` sinon
 */
function codelang_charger_tables_iso($tables_iso = array()) {

	$retour = true;
	
	// On récupère la liste des tables spip implémentant la base iso
	include_spip('services/iso/iso_api');
	if (!$tables_iso) {
		$tables_iso = array_keys($GLOBALS['iso_service']);
	}

	// On charge chacune de ces tables avec le fichier .tab extrait du site iso.
	// Pour éviter d'avoir une mise à jour bancale, il faudrait inclure les requêtes SQL dans
	// une transaction ce qui n'est pas possible avec spip aujourd'hui.
	// De fait, on ne peut pas assurer que si une erreur se produit le résultat soit cohérent et
	// on renvoie juste l'erreur.
	$meta = array();
	foreach ($tables_iso as $_table) {
		// Lecture du fichier iso .tab pour la table en cours et extraction de ses
		// éléments.
		$records = iso_read_table($_table, $sha);
		if ($records) {
			// Suppression des éléments éventuels déjà chargés.
			sql_delete("spip_${_table}");

			// Insertion dans la base de données des éléments extraits du fichier .tab.
			sql_insertq_multi("spip_${_table}", $records);
			// On stocke les informations de la table dans une meta.
			$meta[$_table] = array(
				'sha' => $sha,
				'nbr' => count($records),
				'maj' => date('Y-m-d H:i:s'));
		}
	}

	if ($retour) {
		ecrire_meta('codelang_iso', serialize($meta));
	}

	return $retour;
}

function codelang_verifier_codes_spip() {

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

function codelang_verifier_iso639_5() {

	$codes_verifies = array();

	$codes_639_5 = sql_allfetsel('*', 'spip_iso639families');
	if ($codes_639_5) {
		foreach($codes_639_5 as $_code) {
			if (sql_countsel('spip_iso639macros', 'code_639_3=' . sql_quote($_code['code_639_5']))) {
				$codes_verifies['ok'][] = $_code['code_639_5'];
			} else {
				$codes_verifies['nok'][] = $_code['code_639_5'];
			}
		}
	}

	return $codes_verifies;
}