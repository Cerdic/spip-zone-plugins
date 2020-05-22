<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
		if ($val == 'connect') {
			unset($bases[$key]);
		}
	}

	$valeurs = array(
		'bases' => $bases,
		'base' => '',
		'img_dir' => '',
		'secteur' => '',
		'stats' => '',
		'referers' => '',
		'versions' => '',
		'confirm_version' => '',
		'confirm_shema' => '',
		'traduire_documents_doublons' => '',
	);

	return $valeurs;
}

/**
 * Verifier
 * @return array
 */
function formulaires_fusion_spip_verifier_dist() {
	global $spip_version_base;
	$erreurs = array();

	// vérifier champs obligatoires
	if (!$base=_request('base')) {
		$erreurs['base'] = _T('info_obligatoire');
	} else {
		$traite_stats = (_request('stats') != 'on' ? true : false);
		$traite_referers = (_request('referers') != 'on' ? true : false);
		$bases = bases_referencees(_FILE_CONNECT_TMP);
		$connect = $bases[$base-1];
		/**
		 * S'assurer de pouvoir créer des sous répertoires et des fichiers dans IMG/
		 */
		if (_request('img_dir')) {
			$ok = ecrire_fichier(_DIR_IMG.'test_fusion.txt', 'Test de fusion', true);
			if ($ok) {
				$ok = sous_repertoire(_DIR_IMG, 'test_fusion');
				if (!$ok) {
					$erreurs['img_dir'] = _T('fusion_spip:erreur_img_accessible');
				}
			} else {
				$erreurs['img_dir'] = _T('fusion_spip:erreur_img_accessible');
			}
			if(!is_dir(_request('img_dir')) || !is_readable(_request('img_dir'))) {
				$erreurs['img_dir'] = _T('fusion_spip:erreur_source_inaccessible');
			}

			if (!isset($erreurs['img_dir']) && !_request('traduire_documents_doublons') && sql_showtable('spip_meta', false, $connect)) {
				include_spip('inc/config');
				$langue_base = sql_getfetsel('valeur', 'spip_meta', 'nom="langue_site"', '', '', '', '', $connect);
				$langue_site = lire_config('langue_site');
				if ($langue_base != $langue_site) {
					$erreurs['warning_traduction_document'] = _T('fusion_spip:erreur_traduction_document');
				}
			}
			supprimer_fichier(_DIR_IMG.'test_fusion.txt');
			supprimer_repertoire(_DIR_IMG.'test_fusion');
		}

		// vérifier la version de la base source
		if(!_request('confirm_version')) {
			if (!sql_showtable('spip_meta', true, $connect)) {
				$erreurs['versions_bases'] = _T('fusion_spip:erreur_versions_impossible');
			} else {
				$vsource = sql_fetsel('valeur', 'spip_meta', 'nom="version_installee"', '', '', '', '', $connect);
				if ($spip_version_base != $vsource['valeur']) {
					if(!$vsource['valeur']) {
						$vsource['valeur'] = _T('fusion_spip:erreur_version_indeterminee');
					}
					$erreurs['versions_bases'] = _T(
						'fusion_spip:erreur_versions',
						array(
							'vhote'   => $spip_version_base,
							'vsource' => $vsource['valeur']
						)
					);
				}
			}
		}
		// comparer les shémas des bases source et hote
		if ((empty($erreurs) or (count($erreurs) == 1 && isset($erreurs['warning_traduction_document']))) && !_request('confirm_shema')) {
			$comparer_shemas = charger_fonction('comparer_shemas', 'fusion_spip');
			$erreurs_shema = $comparer_shemas($connect, $traite_stats, $traite_referers);
			if (count($erreurs_shema)) {
				$erreurs['warning_shema'] = join('<hr>', $erreurs_shema).'<hr>';
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

	// préventif
	@ini_set('max_execution_time', 0);
	@ini_set('memory_limit', '-1');

	$base = _request('base')-1;
	$img_dir = _request('img_dir');
	$secteur = _request('secteur');
	$traite_stats = (_request('stats') != 'on' ? true : false);
	$traite_referers = (_request('referers') != 'on' ? true : false);

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
		@ini_set('zlib.output_compression', '0'); // pour permettre l'affichage au fur et a mesure

		$time_start = microtime(true);

		/**
		 * Pipeline de préfusion
		 */
		pipeline(
			'pre_fusion',
			array(
				'args' => array(
					'secteur' => $secteur,
				),
				'data' => array(
					'base' => $base,
					'img_dir' => $img_dir,
					'connect' => $connect,
					'traite_stats' => $traite_stats,
					'traite_referers' => $traite_referers
				)
			)
		);
		//commençons par vider la table de traitement fusion_spip pour pouvoir faire le comptage en fin de traiter
		sql_delete('spip_fusion_spip', 'site_origine='.sql_quote($connect));
		fusion_spip_log('Démarrage de la fusion', 'fusion_spip_'.$connect);

		$lister_tables_principales = charger_fonction('lister_tables_principales', 'fusion_spip');
		$principales = $lister_tables_principales($connect, false);
		$lister_tables_auxiliaires = charger_fonction('lister_tables_auxiliaires', 'fusion_spip');
		$auxiliaires = $lister_tables_auxiliaires($connect, false, $traite_stats, $traite_referers);
		$lister_cles_primaires = charger_fonction('lister_cles_primaires', 'fusion_spip');
		$cles_primaires = $lister_cles_primaires($principales);

		// insérer les objets principaux
		$inserer_table_principale = charger_fonction('inserer_table_principale', 'fusion_spip');
		foreach ($principales as $nom_table => $shema) {
			$inserer_table_principale($nom_table, $shema, $secteur, $connect);
		}

		// mettre à jour les liens entre objets principaux
		$liaisons_table_principale = charger_fonction('liaisons_table_principale', 'fusion_spip');
		foreach ($principales as $nom_table => $shema) {
			$liaisons_table_principale($nom_table, $shema, $cles_primaires, $connect);
		}

		// mise à jour des liaisons de vignettes de documents
		$vignettes_documents = charger_fonction('vignettes_documents', 'fusion_spip');
		$vignettes_documents($connect);

		// mise à jour des statuts des rubriques
		include_spip('inc/rubriques');
		calculer_rubriques();

		// insérer les tables auxiliaires
		$inserer_table_auxiliaire = charger_fonction('inserer_table_auxiliaire', 'fusion_spip');
		foreach ($auxiliaires as $nom_table => $shema) {
			$inserer_table_auxiliaire($nom_table, $shema, $cles_primaires, $connect);
		}

		// importer un par un les documents et logos de la source
		if ($img_dir) {
			$import_documents = charger_fonction('import_documents', 'fusion_spip');
			$options = array();
			if (_request('traduire_documents_doublons') == 'on') {
				$options['traduire_documents_doublons'] = 'on';
				$options['langue_base'] = sql_getfetsel('valeur', 'spip_meta', 'nom="langue_site"', '', '', '', '', $connect);
				include_spip('inc/config');
				$options['langue_origine'] = lire_config('langue_site');
			}
			$import_documents($img_dir, $connect, $options);
		}

		// mise à jour des liens internes [...->...]
		$maj_liens_internes = charger_fonction('maj_liens_internes', 'fusion_spip');
		$maj_liens_internes($principales, $connect);

		// mise à jour des modèles <docXX> <imgXX> <embXX> ...
		$maj_modeles = charger_fonction('maj_modeles', 'fusion_spip');
		$maj_modeles($principales, $connect);

		// déclarer les url uniques importées avec "perma=1"
		$maj_perma_urls = charger_fonction('maj_perma_urls', 'fusion_spip');
		$maj_perma_urls($connect);

		// appel d'une fonction de traitements perso (déclarée dans mes_options.php par exemple)
		// Voir aussi le pipeline post_fusion plus bas
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

		// Un résumé des objets importés
		$res = sql_select('objet, count(*) as count', 'spip_fusion_spip', 'site_origine='.sql_quote($connect), 'objet');
		$resume_imports = array();
		while ($ligne = sql_fetch($res)) {
			if ($ligne['count'] > 0) {
				$resume_imports[] = table_objet($ligne['objet']) . ' : ' . $ligne['count'];
			}
		}
		$resume_imports = join('<br />', $resume_imports);

		/**
		 * Pipeline de préfusion
		 */
		pipeline(
			'post_fusion',
			array(
				'args' => array(
					'secteur' => $secteur,
				),
				'data' => array(
					'base' => $base,
					'img_dir' => $img_dir,
					'connect' => $connect,
					'traite_stats' => $traite_stats,
					'traite_referers' => $traite_referers,
					'resume_imports' => $resume_imports
				)
			)
		);
		$retour = array(
			'message_ok' => _T('fusion_spip:message_import_ok') . $resume_imports
		);
	}
	return $retour;
}
