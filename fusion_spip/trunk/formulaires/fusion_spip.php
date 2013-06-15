<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// pour l'appel à bases_referencees()
include_spip('inc/install');

include_spip('inc/fusion_spip');

/**
 * Charger
 * @return array
 */
function formulaires_fusion_spip_charger_dist() {

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
function formulaires_fusion_spip_verifier_dist() {
	$erreurs = array();

	$base = _request('base');
	$traite_stats = (_request('stats') == 'on' ? true : false);
	$traite_referers = (_request('referers') == 'on' ? true : false);
	$traite_versions = (_request('versions') == 'on' ? true : false);

	$bases = bases_referencees(_FILE_CONNECT_TMP);
	$connect = $bases[$base];

	$principales = fusion_spip_lister_tables_principales($connect, false);
	$auxiliaires = fusion_spip_lister_tables_auxiliaires($connect, false, $traite_stats, $traite_referers, $traite_versions);

	// vérifier champs obligatoires
	if (!_request('base')) {
		$erreurs['base'] = _T('info_obligatoire');
	}
	else {
		// vérifier la version de la base source
		$vhote = sql_fetsel('valeur', 'spip_meta', 'nom="version_installee"');
		$vsource = sql_fetsel('valeur', 'spip_meta', 'nom="version_installee"', '', '', '', '', $connect);
		if($vhote['valeur'] > $vsource['valeur']){
			$erreurs['versions_bases'] = _T('fusion_spip:erreur_versions', array('vhote'=>$vhote['valeur'], 'vsource'=>$vsource['valeur']));
		}
		// vérifier la conformité du shéma de la base source
		if( empty($erreurs) && _request('confirme_warning') != 'on' ){
			$erreurs_shema = fusion_spip_comparer_shemas($connect, $principales, $auxiliaires);
			if (count($erreurs_shema)) {
				$erreurs['warning_shema'] = '- '.join('<br>- ', $erreurs_shema);
			}
		}
	}

	return $erreurs;
}

/**
 * Traiter
 * @return array
 */
function formulaires_fusion_spip_traiter_dist() {
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
			$erreurs[] = _T('fusion_spip:dossier_existe_pas', array('dossier' => $img_dir));
		} else {
			if (!is_readable($img_dir)) {
				$erreurs[] = _T('fusion_spip:dossier_pas_lisible', array('dossier' => $img_dir));
			}
		}
	}

	if (count($erreurs)) {
		$retour = array(
			'message_erreur' =>
			_T('fusion_spip:message_import_nok')
				.'<br/>&bull;&nbsp;'.join('<br/>&bull;&nbsp;', $erreurs)
		);
	} else {

		@ini_set("zlib.output_compression","0"); // pour permettre l'affichage au fur et a mesure

		$time_start = microtime(true);
		fusion_spip_log('Démarrage de la fusion', 'fusion_spip_'.$connect);

		$principales = fusion_spip_lister_tables_principales($connect, true);
		$auxiliaires = fusion_spip_lister_tables_auxiliaires($connect, true, $traite_stats, $traite_referers, $traite_versions);
		$cles_primaires = fusion_spip_lister_cles_primaires($principales);

		// insérer les objets principaux
		foreach ($principales as $nom_table => $shema) {
			fusion_spip_inserer_table_principale($nom_table, $shema, $secteur, $connect);
		}

		// mettre à jour les liens entre objets principaux
		foreach ($principales as $nom_table => $shema) {
			fusion_spip_liaisons_table_principale($nom_table, $shema, $cles_primaires, $connect);
		}

		// mise à jour des liaisons de vignettes de documents
		fusion_spip_vignettes_documents($connect);

		// mise à jour des statuts des rubriques
		include_spip('inc/rubriques');
		calculer_rubriques();

		// insérer les tables auxiliaires
		foreach ($auxiliaires as $nom_table => $shema) {
			fusion_spip_inserer_table_auxiliaire($nom_table, $shema, $cles_primaires, $connect);
		}

		// importer un par un les documents et logos de la source
		if ($img_dir) {
			fusion_spip_import_documents($img_dir, $connect);
		}

		// mise à jour des liens internes [...->...]
		fusion_spip_maj_liens_internes($principales, $connect);

		// mise à jour des modèles <docXX> <imgXX> <embXX> ...
		fusion_spip_maj_modeles($principales, $connect);

		// déclarer les url uniques importées avec "perma=1"
		fusion_spip_maj_perma_urls($connect);

		// appel d'une fonction de traitements perso (déclarée dans mes_options.php par exemple)
		if (function_exists('fusion_spip_extra_action')) {
			fusion_spip_extra_action($connect);
		}

		// recalculer les secteurs et les statuts des rubriques et des articles
		include_spip('inc/rubriques');
		calculer_rubriques();
		propager_les_secteurs();

		$time_end = microtime(true);
		$time = $time_end - $time_start;
		fusion_spip_log('Fusion terminée : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);

		$retour = array(
			'message_ok' => _T('fusion_spip:message_import_ok')
		);
	}

	return $retour;
}

