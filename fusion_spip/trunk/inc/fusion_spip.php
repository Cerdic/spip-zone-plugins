<?php

/**
 * Définition des tables principales à importer
 *
 * @return array liste des tables
 */
function fusion_spip_lister_tables_principales($connect, $skip_non_existing = false) {

	// @todo : lire les descriptions des tables sources plutot que locales ?
	// comment dissocier principales/auxiliares/jointures de la base source ?
	$tables = lister_tables_principales();

	// ne pas importer certaines tables
	unset($tables['spip_jobs']);
	unset($tables['spip_fusion_spip']);
	unset($tables['spip_depots']);
	unset($tables['spip_plugins']);
	unset($tables['spip_paquets']);
	unset($tables['spip_types_documents']);

	// zapper les tables de l'hote qui ne sont pas dans la base importée
	if( $skip_non_existing ) {
		foreach($tables as $table => $shema){
			if( !sql_showtable($table, false, $connect)){
				unset($tables[$table]);
			}
		}
	}

	return $tables;
}

/**
 * Définition des tables auxiliaires à importer
 *
 * @param boolean $stats importer les tables visites
 * @param boolean $referers importer les tables referers
 * @param boolean $versions importer les versions
 *
 * @return array liste des tables
 */
function fusion_spip_lister_tables_auxiliaires($connect, $skip_non_existing = false, $stats = false, $referers = false, $versions = false) {

	// @todo : lire les descriptions des tables sources plutot que locales ?
	// comment dissocier principales/auxiliares/jointures de la base source ?
	$tables = lister_tables_auxiliaires();

	// ne pas importer certaines tables
	unset($tables['spip_meta']);
	unset($tables['spip_resultats']);
	unset($tables['spip_jobs_liens']);
	unset($tables['spip_depots_plugins']);

	if (!$stats) {
		unset($tables['spip_visites']);
		unset($tables['spip_visites_articles']);
	}
	if (!$referers) {
		unset($tables['spip_referers']);
		unset($tables['spip_referers_articles']);
	}
	if (!$versions) {
		unset($tables['spip_versions']);
		unset($tables['spip_versions_fragments']);
	}

	// zapper les tables de l'hote qui ne sont pas dans la base importée
	if( $skip_non_existing ) {
		foreach($tables as $table => $shema){
			if( !sql_showtable($table, false, $connect)){
				unset($tables[$table]);
			}
		}
	}

	return $tables;
}

/**
 * Retourne la liste des clés primaires de tables
 *
 * @param array $tables
 * @return array
 */
function fusion_spip_lister_cles_primaires($tables) {
	$cles_primaires = array();
	foreach ($tables as $nom_table => $shema) {
		$cles_primaires[$nom_table] = $shema['key']['PRIMARY KEY'];
	}
	return $cles_primaires;
}

/**
 * Compare le shéma de la base source et de la base locale
 * retourne les erreurs sur la base source :
 * - tables manquantes
 * - champs manquants
 *
 * Ne compare que la présence des tables et des champs, pas le type de champs
 *
 * @param integer $base identifiant de la connection
 * @return array liste des erreurs
 */
function fusion_spip_comparer_shemas($connect, $principales, $auxiliaires) {

	$tables = array_merge($principales, $auxiliaires);
	foreach ($tables as $nom_table => $shema_table) {
		// ne pas utiliser 'trouver_table' pour ne pas utiliser le cache
		if ($shema_source = sql_showtable($nom_table, false, $connect)) {
			if ($diff_colonnes = array_diff(array_keys($shema_table['field']), array_keys($shema_source['field']))) {
				$erreurs[] = _T('fusion_spip:manque_champs_source', array('table' => $nom_table, 'diff' => join(' - ', $diff_colonnes)));
			}
		} else {
			$erreurs[] = _T('fusion_spip:manque_table_source', array('table' => $nom_table));
		}
	}

	return $erreurs;
}

/**
 * Importer une table principale
 *
 * @param string $nom_table nom de la table
 * @param string $shema shema de la table
 * @param int $secteur id du secteur dans lequel importer
 * @param string $connect nom du connecteur
 */
function fusion_spip_inserer_table_principale($nom_table, $shema, $secteur, $connect) {
	$time_start = microtime(true);

	// liste des champs à recopier
	$champs_select = array_keys($shema['field']);

	// Retrouve la clé primaire à partir du nom d'objet ou de table
	$nom_id_objet = id_table_objet($nom_table);
	// Retrouve le type d'objet à partir du nom d'objet ou de table
	$objet = objet_type($nom_table);

	// selectionner tous les objets d'une table à importer
	$res = sql_select($champs_select, $nom_table, '', '', '', '', '', $connect);
	$count = sql_count($res, $connect);
	$fusion_spips = array();
	while ($obj_import = sql_fetch($res, $connect)) {

		// garder l'id original
		$id_origine = $obj_import[$nom_id_objet];

		// mais ne pas l'insérer dans l'objet importé
		// (sinon doublon sur la clé primaire)
		unset($obj_import[$nom_id_objet]);

		// réaffecter les secteurs et mettre à jour la profondeur
		if ($secteur) {
			if (in_array('id_secteur', array_keys($shema['field']))) {
				$obj_import['id_secteur'] = $secteur;
			}
			if ($objet == 'rubrique' && $obj_import['id_parent'] == 0) {
				$obj_import['id_parent'] = $secteur;
			}
			if ($objet == 'rubrique') {
				$obj_import['profondeur']++;
			}
		}

		// inserer localement l'objet
		$id_final = sql_insertq($nom_table, $obj_import);

		$fusion_spips[] = array(
			'site_origine' => $connect,
			'objet' => $objet,
			'id_origine' => $id_origine,
			'id_final' => $id_final,
		);

	}
	// garder les traces id_origine / id_final
	if (count($fusion_spips)) {
		sql_insertq_multi('spip_fusion_spip', $fusion_spips);
	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	spip_log('Table '.$nom_table.' traitée ('.$count.') : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);

}

/**
 * Importer une table auxiliaire
 *
 * @param string $nom_table nom de la table
 * @param string $shema shema de la table
 * @param array $cles_primaires clés primaires des tables principales
 * @param string $connect nom du connecteur
 */
function fusion_spip_inserer_table_auxiliaire($nom_table, $shema, $cles_primaires, $connect) {
	$time_start = microtime(true);

	// liste des champs à recopier
	$champs_select = array_keys($shema['field']);

	// selectionner tous les objets d'une table à importer
	$res = sql_select($champs_select, $nom_table, '', '', '', '', '', $connect);
	$count = sql_count($res, $connect);
	while ($obj_import = sql_fetch($res, $connect)) {

		$skip_import_objet = false;

		// pour chaque champ de la table, et si ce champ est une clé primaire d'un objet principal,
		// retrouver l'id_final de l'objet lié
		foreach ($shema['field'] as $nom_champ => $valeur_champ) {
			if (in_array($nom_champ, $cles_primaires)) {
				$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'site_origine = '._q($connect).' and id_origine = '._q($obj_import[$nom_champ]).' and objet='._q(objet_type($nom_champ)));
				// mettre à jour l'id de l'objet lié
				if ($nouveau_id['id_final']) {
					$obj_import[$nom_champ] = $nouveau_id['id_final'];
				} else {
					// on n'a pas retrouvé l'objet initial ? l'enregistrement n'est plus cohérent, on le zappe
					$skip_import_objet = true;
				}
			}
		}

		// si la table utilise une liaison par id_objet / objet
		// retrouver l'id_final de l'objet lié
		if ($shema['field']['id_objet'] && $shema['field']['objet']) {
			$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'site_origine = '._q($connect).' and id_origine = '._q($obj_import['id_objet']).' and objet='._q($obj_import['objet']));
			// mettre à jour l'id de l'objet lié
			if ($nouveau_id['id_final']) {
				$obj_import['id_objet'] = $nouveau_id['id_final'];
			} else {
				// on n'a pas retrouvé l'objet initial ? l'enregistrement n'est plus cohérent, on le zappe
				$skip_import_objet = true;
			}
		}

		// cas particulier pour spip_urls (id_objet / type au lieu de id_objet / objet)
		if ($nom_table == 'spip_urls' && $shema['field']['id_objet'] && $shema['field']['type']) {
			$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'site_origine = '._q($connect).' and id_origine = '._q($obj_import['id_objet']).' and objet='._q($obj_import['type']));
			// mettre à jour l'id de l'objet lié
			if ($nouveau_id['id_final']) {
				$obj_import['id_objet'] = $nouveau_id['id_final'];
			} else {
				// on n'a pas retrouvé l'objet initial ? l'enregistrement n'est plus cohérent, on le zappe
				$skip_import_objet = true;
			}
		}

		if (!$skip_import_objet) {
			if ($nom_table == 'spip_visites') {
				// cas particulier pour la table spip_visites
				// il y a peut être déjà des visites pour cette date
				$res_visites = sql_fetsel('*', 'spip_visites', 'date='._q($obj_import['date']));
				if ($res_visites['date']) {
					sql_updateq('spip_visites', array('visites' => $res_visites['visites'] + $obj_import['visites']), 'date='._q($obj_import['date']));
				} else {
					sql_insertq($nom_table, $obj_import);
				}
			} else {
				sql_insertq($nom_table, $obj_import);
			}
		}

	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	spip_log('Table auxiliaire '.$nom_table.' traitée ('.$count.') : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);

}

/**
 * Mettre à jour les liaisons entre les tables
 *
 * @param string $nom_table nom de la table
 * @param string $shema shema de la table
 * @param array $cles_primaires clés primaires des tables principales
 * @param string $connect nom du connecteur
 */
function fusion_spip_liaisons_table_principale($nom_table, $shema, $cles_primaires, $connect) {
	$time_start = microtime(true);

	$objet = objet_type($nom_table);

	// cle primaire de la table concernée
	$cleprimaire = $shema['key']['PRIMARY KEY'];

	// supprimer des champs de l'objet sa propre primary key
	// (on ne met pas à jour id_auteur dans la table spip_auteurs)
	unset($shema['field'][$cleprimaire]);

	// pour chaque champ de la table, et si ce champ est une clé primaire d'un autre objet,
	// on met à jour les liaisons (par exemple mettre à jour id_rubrique dans spip_articles)
	foreach ($shema['field'] as $nom_champ => $valeur_champ) {

		if (in_array($nom_champ, $cles_primaires)) {
			$objet_liaison = objet_type($nom_champ);
			$cle_liaison = $nom_champ;
			fusion_spip_mettre_a_jour_liaisons($nom_table, $objet, $cleprimaire, $objet_liaison, $cle_liaison, $connect);
		}

	}

	// si la table utilise une liaison par id_objet / objet
	// mettre à jour les liaisons (par exemple spip_forum)
	if ($shema['field']['id_objet'] && $shema['field']['objet']) {
		fusion_spip_mettre_a_jour_liaisons_par_objet($nom_table, $objet, $cleprimaire, $connect);
	}

	// cas particulier : pour les rubriques, mettre à jour id_parent
	if ($objet == 'rubrique') {
		fusion_spip_mettre_a_jour_liaisons('spip_rubriques', 'rubrique', 'id_rubrique', 'rubrique', 'id_parent', $connect);
	}

	// cas particulier : pour les forums, mettre à jour id_parent et id_thread
	if ($objet == 'forum') {
		fusion_spip_mettre_a_jour_liaisons('spip_forum', 'forum', 'id_forum', 'forum', 'id_parent', $connect);
		fusion_spip_mettre_a_jour_liaisons('spip_forum', 'forum', 'id_forum', 'forum', 'id_thread', $connect);
	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	spip_log('Liaisons '.$nom_table.' traitées : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);

}

/**
 * pour tous les ($objet) importés,
 * retrouver et mettre à jour ($cle_liaison) dans ($table) par ($cle_primaire)
 * liaison avec l'objet ($objet_liaison)
 *
 * @param string $table
 * @param string $objet
 * @param string $cle_primaire
 * @param string $objet_liaison
 * @param string $cle_liaison
 * @param string $connect
 */
function fusion_spip_mettre_a_jour_liaisons($table, $objet, $cle_primaire, $objet_liaison, $cle_liaison, $connect) {

	$res = sql_select('id_origine,id_final', 'spip_fusion_spip', 'objet='._q($objet).' and site_origine='._q($connect));
	while ($obj_import = sql_fetch($res)) {
		// retrouver l'id_liaison original
		$ancien_id = sql_fetsel($cle_liaison, $table, $cle_primaire.' = '._q($obj_import['id_origine']), '', '', '', '', $connect);
		if ($ancien_id[$cle_liaison]) {
			// déterminer le nouveau lien
			$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'site_origine = '._q($connect).' and id_origine = '._q($ancien_id[$cle_liaison]).' and objet='._q($objet_liaison));
			// mettre à jour l'objet importé
			if ($nouveau_id['id_final']) {
				sql_updateq(
					$table,
					array($cle_liaison => $nouveau_id['id_final']),
					$cle_primaire.' = '._q($obj_import['id_final'])
				);
			}
		}
	}
}

/**
 * pour tous les ($objet) importés,
 * retrouver et mettre à jour id_objet dans ($table) par ($cle_primaire)
 *
 * @param string $table
 * @param string $objet
 * @param string $cle_primaire
 * @param string $objet_liaison
 * @param string $cle_liaison
 * @param string $connect
 */
function fusion_spip_mettre_a_jour_liaisons_par_objet($table, $objet, $cle_primaire, $connect) {

	$res = sql_select('id_origine,id_final', 'spip_fusion_spip', 'objet='._q($objet).' and site_origine='._q($connect));
	while ($obj_import = sql_fetch($res)) {
		// retrouver l'id_liaison original
		$ancien_id = sql_fetsel(array('id_objet', 'objet'), $table, $cle_primaire.' = '._q($obj_import['id_origine']), '', '', '', '', $connect);
		if ($ancien_id['id_objet']) {
			// déterminer le nouveau lien
			$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'site_origine = '._q($connect).' and id_origine = '._q($ancien_id['id_objet']).' and objet='._q($ancien_id['objet']));
			// mettre à jour l'objet importé
			if ($nouveau_id['id_final']) {
				sql_updateq(
					$table,
					array('id_objet' => $nouveau_id['id_final']),
					$cle_primaire.' = '._q($obj_import['id_final'])
				);
			}
		}
	}
}

/**
 * Ajouter le flag perma=1 sur toutes les urls uniques importées
 *
 * Les urls uniques de chaque objet sont flaggées "perma" pour éviter
 * que "voir en ligne" depuis le back office ne génère une nouvelle url
 *
 * @param string $connect base source
 */
function fusion_spip_maj_perma_urls($connect) {
	$res = sql_select('id_origine, objet, id_final', 'spip_fusion_spip', 'site_origine='._q($connect));
	while ($obj_import = sql_fetch($res)) {
		$urls = sql_allfetsel('*', 'spip_urls', 'id_objet='._q($obj_import['id_final']).' and type='._q($obj_import['objet']));
		if (count($urls) == 1) {
			sql_updateq(
				'spip_urls',
				array('perma' => 1),
				'id_objet='._q($obj_import['id_final']).' and type='._q($obj_import['objet'])
			);
		}
	}
}

/**
 * Mettre à jour les liens entre les documents et leurs vignettes
 * 
 * @param string $connect base source
 */
function fusion_spip_vignettes_documents($connect) {
	$time_start = microtime(true);

	$res = sql_select(
		'a.id_final, d.id_vignette',
		'spip_fusion_spip a join spip_documents d on (a.id_final = d.id_document)',
		'objet="document" and site_origine='._q($connect).' and id_vignette <> 0'
	);
	while ($obj_import = sql_fetch($res)) {
		$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'site_origine = '._q($connect).' and id_origine = '._q($obj_import['id_vignette']).' and objet="document"');
		if ($nouveau_id) {
			sql_updateq(
				'spip_documents',
				array('id_vignette' => $nouveau_id['id_final']),
				'id_document='._q($obj_import['id_final'])
			);
		} else {
			// on n'a pas retrouvé l'id original ? lien cassé, on le supprime
			sql_updateq(
				'spip_documents',
				array('id_vignette' => 0),
				'id_document='._q($obj_import['id_final'])
			);
		}
	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	spip_log('Vignettes documents mises à jour : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);
}

/**
 * Importer un par un les documents de la source
 *
 * @param string $img_dir répertoire IMG source
 * @param string $connect base source
 */
function fusion_spip_import_documents($img_dir, $connect) {
	include_spip('inc/documents');
	$time_start = microtime(true);

	if (substr($img_dir, -1, 1) != '/') {
		$img_dir = $img_dir.'/';
	}

	$documents_importes = 0;
	$logos_importes = 0;

	$res = sql_select(
		'a.id_final, d.fichier',
		'spip_fusion_spip a join spip_documents d on (a.id_final = d.id_document)',
		'objet="document" and site_origine='._q($connect)
	);
	while ($obj_import = sql_fetch($res)) {
		$source_doc = $img_dir.$obj_import['fichier'];
		$dest_doc = _DIR_IMG.$obj_import['fichier'];

		// créer répertoire si besoin
		$path_parts = pathinfo($source_doc);
		$ext = $path_parts['extension'];
		if ($ext) {
			creer_repertoire_documents($ext);
			// @todo: il existe surement mieux que copy() ?
			// @todo: traiter les fichiers déja existant (les renommer)
			if (file_exists($source_doc) && copy($source_doc, $dest_doc)) {
				$documents_importes++;
				//spip_log('Document copié : '.$source_doc.' > '.$dest_doc, 'fusion_spip_documents_'.$connect);
			} else {
				//spip_log('Document échec : '.$source_doc.' > '.$dest_doc, 'fusion_spip_documents_'.$connect);
			}
		}
	}

	$logos_racines = array(
		'arton' => 'article',
		'artoff' => 'article',
		'auton' => 'auteur',
		'autoff' => 'auteur',
		'breveon' => 'breve',
		'breveoff' => 'breve',
		'moton' => 'mot',
		'motoff' => 'mot',
		'rubon' => 'rubrique',
		'ruboff' => 'rubrique',
	);

	// lire tous les fichiers qui peuvent être des logos
	$liste_logos = glob($img_dir.'{'.join(',', array_keys($logos_racines)).'}*', GLOB_BRACE);
	foreach ($liste_logos as $logo) {
		$path_parts = pathinfo($logo);
		$ext_logo = strtolower($path_parts['extension']);
		$type_logo = preg_replace('#([0-9])+([\.a-z])+#i', '', basename($logo));
		$objet_logo = $logos_racines[$type_logo];
		$id_objet = preg_replace('#([^0-9])*#', '', basename($logo));

		$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'site_origine = '._q($connect).' and id_origine = '._q($id_objet).' and objet='._q($objet_logo));
		if ($nouveau_id) {
			$dest_logo = _DIR_IMG.$type_logo.$nouveau_id['id_final'].'.'.$ext_logo;
			// @todo: il existe surement mieux que copy() ?
			if (copy($logo, $dest_logo)) {
				$logos_importes++;
				//spip_log('Logo copié : '.$logo.' > '.$dest_logo, 'fusion_spip_documents_'.$connect);
			} else {
				//spip_log('Logo échec : '.$logo.' > '.$dest_logo, 'fusion_spip_documents_'.$connect);
			}
		} else {
			// objet lié pas trouvé ? logo obsolète, on ne fait rien
			//spip_log($logo.' : liaison pas trouvée', 'fusion_spip_documents_'.$connect);
		}
	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	spip_log('Documents importés ('.$documents_importes.' docs / '.$logos_importes.' logos) : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);
}


/** Mise à jour des liens internes [...->...]
 *
 * @param array $principales tables principales
 * @param string $connect base source
 */
function fusion_spip_maj_liens_internes($principales, $connect) {
	$time_start = microtime(true);
	$objets_mis_a_jour = 0;

	$objets_sources = fusion_spip_determiner_champs_texte($principales);

	// liens possibles et objets auxquels ils se rapportent
	$objets_liens = array(
		'rub' => 'rubrique',
		'rubrique' => 'rubrique',
		'aut' => 'auteur',
		'auteur' => 'auteur',
		'br' => 'breve',
		'breve' => 'breve',
		'brève' => 'breve',
		'doc' => 'document',
		'im' => 'document',
		'img' => 'document',
		'image' => 'document',
		'emb' => 'document',
		'document' => 'document',
		'art' => 'article',
		'article' => 'article',
		'' => 'article',
	);

	// pour tous les objets importés pouvant contenir des liens
	foreach ($objets_sources as $objet => $champs) {
		$table = table_objet_sql($objet);
		$cle_primaire = id_table_objet($objet);

		// selectionner les objets contenant des liens
		$select = array();
		$where = array();
		foreach ($champs as $champ) {
			$select[] = 'o.'.$champ;
			$where[] = 'o.'.$champ.' regexp "\[.*\->('.join('|', array_keys($objets_liens)).')*[0-9]+\]"';
		}
		$select = join(', ', $select);
		$where = join(' or ', $where);
		$objets_import = sql_allfetsel(
			'o.'.$cle_primaire.', '.$select,
			'spip_fusion_spip a join '.$table.' o on (a.id_final = o.'.$cle_primaire.' and a.objet="'.$objet.'" and a.site_origine='._q($connect).')',
			$where
		);

		foreach ($objets_import as $obj_import) {
			$update_array = array();
			foreach ($champs as $champ) {
				// recenser tous les liens dans le champ
				$nb_liens = preg_match_all('/\[([^][]*?([[]\w*[]][^][]*)*)->(>?)([^]]*)\]/msS', $obj_import[$champ], $liens_trouves, PREG_SET_ORDER);
				if ($nb_liens) {
					// pour chaque lien trouvé, remplacer id_origine par id_final
					foreach ($liens_trouves as $lien_trouve) {
						// extraire l'id du quatrieme motif du preg_matchall
						$id_origine_lien = preg_replace('#[a-z]*#', '', $lien_trouve[4]);
						$type_lien = preg_replace('#[0-9]*#', '', $lien_trouve[4]);
						$objet_lien = $objets_liens[$type_lien];
						$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'id_origine='._q($id_origine_lien).' and objet="'.$objet_lien.'" and site_origine="'.$connect.'"');
						if ($nouveau_id['id_final']) {
							$pattern_cherche = '#\[([^][]*?([[]\w*[]][^][]*)*)->'.$type_lien.$id_origine_lien.'\]#';
							// ajouter une signature pour éviter les remplacements en cascade
							$pattern_remplace = '[$1->__final__'.$type_lien.$nouveau_id['id_final'].']';
							$obj_import[$champ] = preg_replace($pattern_cherche, $pattern_remplace, $obj_import[$champ]);
						}
					}
					$obj_import[$champ] = str_replace('__final__', '', $obj_import[$champ]);
					$update_array[$champ] = $obj_import[$champ];
				}
			}
			if ($update_array) {
				sql_updateq($table, $update_array, $cle_primaire.'='._q($obj_import[$cle_primaire]));
				$objets_mis_a_jour++;
			}
		}
	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	spip_log('Liens internes mis à jour ('.$objets_mis_a_jour.' objets) : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);

}

/** Mise à jour des modèles <docXX> <imgXX> <embXX> ...
 *
 * @param array $principales tables principales
 * @param string $connect base source
 */
function fusion_spip_maj_modeles($principales, $connect) {
	$time_start = microtime(true);
	$objets_mis_a_jour = 0;

	$objets_sources = fusion_spip_determiner_champs_texte($principales);

	if (function_exists('medias_declarer_tables_objets_sql')) {
		// obtenir la liste des modeles dans la table spip_documents
		$spip_documents = medias_declarer_tables_objets_sql($principales);
	}
	if ($spip_documents['modeles']) {
		$modeles = $spip_documents['modeles'];
	} else {
		$modeles = array('document', 'doc', 'img', 'emb', 'image', 'video', 'text', 'audio', 'application');
	}


	// pour tous les objets importés pouvant contenir des modèles
	foreach ($objets_sources as $objet => $champs) {
		$table = table_objet_sql($objet);
		$cle_primaire = id_table_objet($objet);

		// selectionner les objets contenant les modèles recherchés
		$select = array();
		$where = array();
		foreach ($champs as $champ) {
			$select[] = 'o.'.$champ;
			$where[] = 'o.'.$champ.' regexp "<('.join('|', $modeles).')+[0-9]+"';
		}
		$select = join(', ', $select);
		$where = join(' or ', $where);

		$res = sql_select(
			'o.'.$cle_primaire.', '.$select,
			'spip_fusion_spip a join '.$table.' o on (a.id_final = o.'.$cle_primaire.' and a.objet="'.$objet.'" and a.site_origine='._q($connect).')',
			$where
		);
		while ($obj_import = sql_fetch($res)) {
			$update_array = array();
			foreach ($champs as $champ) {
				// recenser tous les modèles dans le champ
				$nb_liens = preg_match_all('#<('.join('|', $modeles).'){1}([0-9]+)#', $obj_import[$champ], $liens_trouves, PREG_SET_ORDER);
				if ($nb_liens) {
					// pour chaque lien trouvé, le remplacer id_origine par id_final
					foreach ($liens_trouves as $lien_trouve) {
						$id_origine_lien = $lien_trouve[2];
						$modele = $lien_trouve[1];
						$nouveau_id = sql_fetsel('id_final', 'spip_fusion_spip', 'id_origine='._q($id_origine_lien).' and objet="document" and site_origine="'.$connect.'"');
						if ($nouveau_id['id_final']) {
							$pattern_cherche = '#<'.$modele.$id_origine_lien.'#';
							$pattern_remplace = '<'.$modele.$nouveau_id['id_final'];
							$obj_import[$champ] = preg_replace($pattern_cherche, $pattern_remplace, $obj_import[$champ]);
						}
					}
					$update_array[$champ] = $obj_import[$champ];
				}
			}
			if ($update_array) {
				sql_updateq($table, $update_array, $cle_primaire.'='._q($obj_import[$cle_primaire]));
				$objets_mis_a_jour++;
			}
		}
	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	spip_log('Modèles mis à jour ('.$objets_mis_a_jour.' objets) : '.number_format($time, 2).' secondes)', 'fusion_spip_'.$connect);
}

/**
 * Retourne un tableau de tous les objets contenant des champs texte avec les noms des champs pour chaque objet
 *
 * @param array $tables liste des tables à examiner
 * @return array
 */
function fusion_spip_determiner_champs_texte($tables) {
	$objets = array();
	foreach ($tables as $nom_table => $shema_table) {
		$champs = array();
		foreach ($shema_table['field'] as $champ => $desc) {
			if (strpos($desc, 'text') !== false && strpos($desc, 'tinytext') === false
				&& strpos($champ, 'email') === false && strpos($champ, 'site') === false && strpos($champ, 'url') === false
			) {
				$champs[] = $champ;
			}
		}
		if ($champs) {
			$objets[objet_type($nom_table)] = $champs;
		}
	}
	return $objets;
}