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
 * Charge tous les taxons d'un règne donné, du règne lui-même aux taxons de genre au maximum.
 * La fonction permet aussi de choisir un rang taxonomique feuille différent du genre.
 * Les nom communs anglais, français ou espagnols peuvent aussi être chargés en complément mais
 * ne couvrent pas l'ensemble des taxons.
 *
 * @api
 * @filtre
 * @uses sil_read_table()
 *
 * @param string $regne
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 * @param string $rang
 *        Rang taxonomique minimal jusqu'où charger le règne. Ce rang est fourni en anglais, en minuscules et
 *        correspond à : `phylum`, `class`, `order`, `family`, `genus`.
 * @param array  $codes_langue
 *        Tableau des codes (au sens SPIP) des langues à charger pour les noms communs des taxons.
 *
 * @return bool
 *        `true` si le chargement a réussi, `false` sinon
 */
function codelang_charger_tables_sil() {

	$retour = false;
	
	// On récupère la liste des tables spip implémentant la base SIL
	include_spip('services/sil/sil_api');
	$tables_sil = array_keys($GLOBALS['sil_service']['fields']);
	
	// On charge chacune de ces tables
	$meta = array();
	foreach ($tables_sil as $_table) {
		// Lecture du fichier SIL .tab de la table en cours et extraction de ses
		// éléments
		$records = sil_read_table($_table, $sha);

		// Suppression des éléments éventuels déjà chargés
		sql_delete("spip_${_table}");

		// Insertion dans la base de données
		$retour = sql_insertq_multi("spip_${_table}", $records);
		if ($retour) {
			// Insérer les sha dans une meta propre au règne.
			// Ca permettra de tester l'utilité ou pas d'un rechargement du règne
			$meta[$_table] = array(
				'sha' => $sha,
				'maj' => date('Y-m-d H:i:s'));
		}
	}

	if ($retour) {
		ecrire_meta('codelang_sil', serialize($meta));
	} else {
		effacer_meta('codelang_sil');
	}

	return $retour;
}
