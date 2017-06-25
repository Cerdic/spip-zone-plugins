<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function phraser_noisette($noisette, $options = array()) {

	// Initialisation de la description
	$description = array();

	// Initialiser le contexte de chargement
	if (!isset($options['recharger'])) {
		$options['recharger'] = false;
	}
	if (!isset($options['md5']) or $options['recharger']) {
		$options['md5'] = '';
	}

	// Initialiser les composants de l'identifiant de la noisette:
	// - type-noisette si la noisette est dédiée uniquement à une page
	// - type-composition-noisette si la noisette est dédiée uniquement à une composition
	// - noisette sinon
	$type = '';
	$composition = '';
	$identifiants = explode('-', $noisette);
	if (isset($identifiants[1])) {
		$type = $identifiants[0];
	}
	if (isset($identifiants[2])) {
		$composition = $identifiants[1];
	}

	// Initialisation de la description par défaut de la page
	$description_defaut = array(
		'noisette'       => $noisette,
		'type'           => $type,
		'composition'    => $composition,
		'nom'            => $noisette,
		'description'    => '',
		'icon'           => 'noisette-24.png',
		'necessite'      => array(),
		'contexte'       => array(),
		'ajax'           => 'defaut',
		'inclusion'      => 'statique',
		'parametres'     => array(),
		'signature'      => '',
	);

	// Recherche des noisettes par leur fichier YAML uniquement.
	$md5 = '';
	$fichier = isset($options['yaml']) ? $options['yaml'] : find_in_path("noisettes/${noisette}.yaml");
	if ($fichier) {
		// il y a un fichier YAML de configuration, on vérifie le md5 avant de charger le contenu.
		$md5 = md5_file($fichier);
		if ($md5 != $options['md5']) {
			include_spip('inc/yaml');
			$description = yaml_charger_inclusions(yaml_decode_file($fichier));
			// Traitements des champs pouvant être soit une chaine soit un tableau
			if (!empty($description['necessite']) and is_string($description['necessite'])) {
				$description['necessite'] = array($description['necessite']);
			}
			if (!empty($description['contexte']) and is_string($description['contexte'])) {
				$description['contexte'] = array($description['contexte']);
			}
		}
	}

	// Si la description est remplie c'est que le chargement a correctement eu lieu.
	// Sinon, si la noisette n'a pas changée on renvoie une description limitée à un indicateur d'identité pour
	// distinguer ce cas avec une erreur de chargement qui renvoie une description vide.
	if ($description) {
		// Mise à jour du md5
		$description['signature'] = $md5;
		// Complétude de la description avec les valeurs par défaut
		$description = array_merge($description_defaut, $description);
		// Sérialisation des champs necessite, contexte et parametres qui sont des tableaux
		$description['necessite'] = serialize($description['necessite']);
		$description['contexte'] = serialize($description['contexte']);
		$description['parametres'] = serialize($description['parametres']);
	} elseif ($md5 == $options['md5']) {
		$description['identique'] = true;
	}

	return $description;
}


function phraser_page($page, $options = array()) {

	// Initialisation de la description
	$description = array();

	// Choisir le bon répertoire des pages
	if (empty($options['repertoire_pages'])) {
		$options['repertoire_pages'] = noizetier_page_repertoire();
	}

	// Initialiser les blocs par défaut
	if (empty($options['blocs_defaut'])) {
		$options['blocs_defaut'] = noizetier_bloc_defaut();
	}

	// Initialiser le contexte de chargment
	if (!isset($options['recharger'])) {
		$options['recharger'] = false;
	}
	if (!isset($options['md5']) or $options['recharger']) {
		$options['md5'] = '';
	}

	// Initialiser les composants de l'identifiant de la page:
	// - type-composition si la page est une composition
	// - type sinon
	// On gère aussi le cas de Zpip v1 où page-xxxx désigne une page et non une composition.
	// Dans ce cas, on doit donc obtenir type = xxxx et composition vide.
	$identifiants = explode('-', $page);
	if (!isset($identifiants[1])) {
		$identifiants[1] = '';
	} elseif ($identifiants[0] == 'page') {
		$identifiants[0] = $identifiants[1];
		$identifiants[1] = '';
	}

	// Initialisation de la description par défaut de la page
	$description_defaut = array(
		'page'           => $page,
		'type'           => $identifiants[0],
		'composition'    => $identifiants[1],
		'nom'            => $page,
		'description'    => '',
		'icon'           => 'page-24.png',
		'blocs_exclus'   => array(),
		'necessite'      => array(),
		'branche'        => array(),
		'est_virtuelle'  => 'non',
		'est_page_objet' => 'non',
		'signature'      => '',
	);

	// Recherche des pages ou compositions explicites suivant le processus :
	// a- Le fichier YAML est recherché en premier,
	// b- ensuite le fichier XML pour compatibilité ascendante.
	// c- enfin, si il n'y a ni YAML, ni XML et que le mode le permet, on renvoie une description standard minimale
	//    basée sur le fichier HTML uniquement
	$md5 = '';
	if ($fichier = find_in_path("{$options['repertoire_pages']}${page}.yaml")) {
		// 1a- il y a un fichier YAML de configuration, on vérifie le md5 avant de charger le contenu.
		$md5 = md5_file($fichier);
		if ($md5 != $options['md5']) {
			include_spip('inc/yaml');
			$description = yaml_charger_inclusions(yaml_decode_file($fichier));
		}
	} elseif ($fichier = find_in_path("{$options['repertoire_pages']}${page}.xml")) {
		// 1b- il y a un fichier XML de configuration, on vérifie le md5 avant de charger le contenu.
		//     on extrait et on parse le XML de configuration en tenant compte que ce peut être
		//     celui d'une page ou d'une composition, ce qui change la balise englobante.
		$md5 = md5_file($fichier);
		if ($md5 != $options['md5']) {
			include_spip('inc/xml');
			if ($xml = spip_xml_load($fichier, false)
			and (isset($xml['page']) or isset($xml['composition']))) {
				$xml = isset($xml['page']) ? reset($xml['page']) : reset($xml['composition']);
				// Titre (nom), description et icone
				if (isset($xml['nom'])) {
					$description['nom'] = spip_xml_aplatit($xml['nom']);
				}
				if (isset($xml['description'])) {
					$description['description'] = spip_xml_aplatit($xml['description']);
				}
				if (isset($xml['icon'])) {
					$description['icon'] = reset($xml['icon']);
				}

				// Liste des blocs autorisés pour la page. On vérifie que les blocs configurés sont bien dans
				// la liste des blocs par défaut et on calcule les blocs exclus qui sont les seuls insérés en base.
				$blocs_inclus = array();
				if (spip_xml_match_nodes(',^bloc,', $xml, $blocs)) {
					foreach (array_keys($blocs) as $_bloc) {
						list(, $attributs) = spip_xml_decompose_tag($_bloc);
						$blocs_inclus[] = $attributs['id'];
					}
				}
				if ($blocs_inclus) {
					$description['blocs_exclus'] = array_diff($options['blocs_defaut'], array_intersect($options['blocs_defaut'], $blocs_inclus));
				}

				// Liste des plugins nécessaires pour utiliser la page
				if (spip_xml_match_nodes(',^necessite,', $xml, $necessites)) {
					$description['necessite'] = array();
					foreach (array_keys($necessites) as $_necessite) {
						list(, $attributs) = spip_xml_decompose_tag($_necessite);
						$description['necessite'][] = $attributs['id'];
					}
				}

				// Liste des héritages
				if (spip_xml_match_nodes(',^branche,', $xml, $branches)) {
					$description['branche'] = array();
					foreach (array_keys($branches) as $_branche) {
						list(, $attributs) = spip_xml_decompose_tag($_branche);
						$description['branche'][$attributs['type']] = $attributs['composition'];
					}
				}
			}
		}
	} elseif (defined('_NOIZETIER_LISTER_PAGES_SANS_XML') ? _NOIZETIER_LISTER_PAGES_SANS_XML : false) {
		// 1c- il est autorisé de ne pas avoir de fichier XML de configuration.
		// Ces pages sans XML ne sont chargées qu'une fois, la première. Ensuite, aucune mise à jour n'est nécessaire.
		if (!$options['md5']) {
			$description['icon'] = 'page_noxml-24.png';
			$md5 = md5('_NOIZETIER_LISTER_PAGES_SANS_XML');
		}
	}

	// Si la description est remplie c'est que le chargement a correctement eu lieu.
	// Sinon, si la page n'a pas changée on renvoie une description limitée à un indicateur d'identité pour
	// distinguer ce cas avec une erreur de chargement qui renvoie une description vide.
	if ($description) {
		// Mise à jour du md5
		$description['signature'] = $md5;
		// Identifie si la page est celle d'un objet SPIP
		include_spip('base/objets');
		$tables_objets = array_keys(lister_tables_objets_sql());
		$description['est_page_objet'] = in_array(table_objet_sql($description_defaut['type']), $tables_objets) ? 'oui' : 'non';
		// Complétude de la description avec les valeurs par défaut
		$description = array_merge($description_defaut, $description);
		// Sérialisation des champs blocs_exclus, necessite et branche qui sont des tableaux
		$description['blocs_exclus'] = serialize($description['blocs_exclus']);
		$description['necessite'] = serialize($description['necessite']);
		$description['branche'] = serialize($description['branche']);
	} elseif ($md5 == $options['md5']) {
		$description['identique'] = true;
	}

	return $description;
}
