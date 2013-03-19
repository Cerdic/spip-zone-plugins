<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// pour l'appel à bases_referencees()
include_spip('inc/install');

include_spip('inc/assemblage');

/**
 * Charger
 * @return array
 */
function formulaires_assemblage_charger_dist() {

	// liste des bases déclarées sauf la base "locale"
	$bases = bases_referencees(_FILE_CONNECT_TMP);
	foreach ($bases as $key => $val) {
		if ($val == 'connect') unset($bases[$key]);
	}

	$valeurs = array(
		'bases' => $bases,
		'base' => '',
		'img_dir' => '',
		'secteur' => '',
		'stats' => '',
		'referers' => '',
		'versions' => '',
	);

	return $valeurs;
}

/**
 * Verifier
 * @return array
 */
function formulaires_assemblage_verifier_dist() {
	$erreurs = array();

	$traite_stats = (_request('stats') == 'on' ? true : false);
	$traite_referers = (_request('referers') == 'on' ? true : false);
	$traite_versions = (_request('versions') == 'on' ? true : false);

	// vérifier champs obligatoires
	if (!_request('base')) {
		$erreurs['base'] = _T('info_obligatoire');
	} // vérifier la conformité du shéma de la base source
	else {
		$erreurs_shema = assemblage_comparer_shemas(_request('base'), $traite_stats, $traite_referers, $traite_versions);
		if (count($erreurs_shema)) {
			$erreurs['base'] = '- '.join('<br>- ', $erreurs_shema);
		}

	}

	return $erreurs;
}

/**
 * Traiter
 * @return array
 */
function formulaires_assemblage_traiter_dist() {
	$erreurs = array();

	// @todo: afficher une alerte sur formulaire_charger si max_execution_time ne peut pas être modifié
	ini_set('max_execution_time', 0);

	$base = _request('base');
	$img_dir = _request('img_dir');
	$secteur = _request('secteur');
	$traite_stats = (_request('stats') == 'on' ? true : false);
	$traite_referers = (_request('referers') == 'on' ? true : false);
	$traite_versions = (_request('versions') == 'on' ? true : false);

	$bases = bases_referencees(_FILE_CONNECT_TMP);
	$connect = $bases[$base];

	// vérifier que le répertoire donné existe et soit lisible
	if ($img_dir) {
		if (!file_exists($img_dir)) {
			$erreurs[] = _T('assemblage:dossier_existe_pas', array('dossier' => $img_dir));
		} else {
			if (!is_readable($img_dir)) {
				$erreurs[] = _T('assemblage:dossier_pas_lisible', array('dossier' => $img_dir));
			}
		}
	}

	if (count($erreurs)) {
		$retour = array(
			'message_erreur' =>
			_T('assemblage:message_import_nok')
				.'<br/>&bull;&nbsp;'.join('<br/>&bull;&nbsp;', $erreurs)
		);
	} else {

		$time_start = microtime(true);
		spip_log('Démarrage de l\'assemblage', 'assemblage_'.$connect);


		$principales = assemblage_lister_tables_principales();
		$auxiliaires = assemblage_lister_tables_auxiliaires($traite_stats, $traite_referers, $traite_versions);
		$cles_primaires = assemblage_lister_cles_primaires($principales);

		// insérer les objets principaux
		foreach ($principales as $nom_table => $shema) {
			assemblage_inserer_table_principale($nom_table, $shema, $secteur, $connect);
		}

		// mettre à jour les liens entre objets principaux
		foreach ($principales as $nom_table => $shema) {
			assemblage_liaisons_table_principale($nom_table, $shema, $cles_primaires, $connect);
		}

		// mise à jour des liaisons de vignettes de documents
		assemblage_vignettes_documents($connect);

		// mise à jour des statuts des rubriques
		include_spip('inc/rubriques');
		calculer_rubriques();

		// insérer les tables auxiliaires
		foreach ($auxiliaires as $nom_table => $shema) {
			assemblage_inserer_table_auxiliaire($nom_table, $shema, $cles_primaires, $connect);
		}

		// importer un par un les documents et logos de la source
		if ($img_dir) {
			assemblage_import_documents($img_dir, $connect);
		}

		// mise à jour des liens internes [...->...]
		assemblage_maj_liens_internes($principales, $auxiliaires, $connect);

		// mise à jour des modèles <docXX> <imgXX> <embXX> ...
		assemblage_maj_modeles($principales, $auxiliaires, $connect);

		// déclarer les url uniques importées avec "perma=1"
		assemblage_maj_perma_urls($connect);

		$time_end = microtime(true);
		$time = $time_end - $time_start;
		spip_log('Assemblage terminé : '.number_format($time, 2).' secondes)', 'assemblage_'.$connect);

		$retour = array(
			'message_ok' => _T('assemblage:message_import_ok')
		);
	}

	return $retour;
}

