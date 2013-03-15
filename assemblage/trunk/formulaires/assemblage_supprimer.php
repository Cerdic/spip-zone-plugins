<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// pour l'appel à bases_referencees()
include_spip('inc/install');

include_spip('inc/assemblage');

/**
 * Charger
 * @return array
 */
function formulaires_assemblage_supprimer_charger_dist() {

	// liste des bases déclarées sauf la base "locale"
	$bases = bases_referencees(_FILE_CONNECT_TMP);
	foreach ($bases as $key => $val) {
		if ($val == 'connect') unset($bases[$key]);
	}

	$valeurs = array(
		'bases' => $bases,
	);

	return $valeurs;
}

/**
 * Verifier
 * @return array
 */
function formulaires_assemblage_supprimer_verifier_dist() {
	$erreurs = array();

	// champs obligatoires
	foreach (array('base') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = _T('info_obligatoire');
		}
	}

	return $erreurs;
}

/**
 * Traiter
 * @return array
 */
function formulaires_assemblage_supprimer_traiter_dist() {
	$erreurs = array();

	$base = _request('base');
	$bases = bases_referencees(_FILE_CONNECT_TMP);
	$connect = $bases[$base];

	$principales = assemblage_lister_tables_principales();
	foreach ($principales as $nom_table => $val) {
		// Retrouve la clé primaire à partir du nom d'objet ou de table
		$nom_id_objet = id_table_objet($nom_table);
		// Retrouve le type d'objet à partir du nom d'objet ou de table
		$type_objet = objet_type($nom_table);

		spip_log('Suppression des objets '.$type_objet.' importés de la base '.$connect, 'assemblage_suppression_'.$connect.'.'._LOG_AVERTISSEMENT);

		$res = sql_select('id_final', 'spip_assemblage', 'objet='._q($type_objet).' and site_origine="'.$connect.'"');
		while ($obj = sql_fetch($res)) {
			sql_delete(
				$nom_table,
				$nom_id_objet.' = '._q($obj['id_final'])
			);
		}
		sql_delete('spip_assemblage', 'objet='._q($type_objet).' and site_origine="'.$connect.'"');
	}

	$retour['message_ok'] = _T('assemblage:message_suppression_ok');

	return $retour;
}

