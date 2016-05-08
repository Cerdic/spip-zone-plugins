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
 * Charge toutes les tables SIL dans la base de données à partir des fichiers .tab issus du site de SIL.
 *
 * @api
 * @filtre
 * @uses sil_read_table()
 *
* @return bool
 *        `true` si le chargement a réussi, `false` sinon
 */
function codelang_charger_tables_sil() {

	$retour = true;
	
	// On récupère la liste des tables spip implémentant la base SIL
	include_spip('services/sil/sil_api');
	$tables_sil = array_keys($GLOBALS['sil_service']['fields']);
	
	// On charge chacune de ces tables avec le fichier .tab extrait du site SIL.
	// Pour éviter d'avoir une mise à jour bancale, il faudrait inclure les requêtes SQL dans
	// une transaction ce qui n'est pas possible avec spip aujourd'hui.
	// De fait, on ne peut pas assurer que si une erreur se produit le résultat soit cohérent et
	// on renvoie juste l'erreur.
	$meta = array();
	foreach ($tables_sil as $_table) {
		// Lecture du fichier SIL .tab pour la table en cours et extraction de ses
		// éléments.
		$records = sil_read_table($_table, $sha);
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
		ecrire_meta('codelang_sil', serialize($meta));
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
					$codes_verifies['iso6391'][$_code] = $codes_iso;
				}
			} elseif (strlen($_code) == 3) {
				// Si le code a une taille de 3 caractères on recherche de suite dans la table iso639codes
				// un élément dont le code ISO639-3 est égal.
				$where = array('code_639_3=' . sql_quote($_code));
				$codes_iso = sql_allfetsel($select, $from, $where);
				if ($codes_iso) {
					$codes_verifies['iso6393'][$_code] = $codes_iso;
				}
			} else {
				$codes_verifies['spip'][$_code] = array('nom_spip' => $_nom);
			}
		}
	}

	return $codes_verifies;
}
