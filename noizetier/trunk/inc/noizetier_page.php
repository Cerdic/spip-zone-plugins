<?php
/**
 * Ce fichier contient l'API de gestion des pages et compositions configurables par le noiZetier.
 *
 * @package SPIP\NOIZETIER\PAGE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 *
 * @api
 *
 * @param bool $recharger
 *
 * @return bool
 */
function noizetier_page_charger($recharger = false) {

	// Retour de la fonction
	$retour = false;

	// Initialiser les blocs par défaut
	include_spip('inc/noizetier_bloc');
	$options['blocs_defaut'] = noizetier_bloc_defaut();

	// Choisir le bon répertoire des pages
	$options['repertoire_pages'] = noizetier_page_repertoire();

	// Initialiser le contexte de rechargement
	// TODO : en attente de voir si on rajoute un var_mode=vider_noizetier
	$forcer_chargement = $recharger;

	// Initaliser la table et le where des pages non virtuelles qui sont utilisés plusieurs fois.
	$from ='spip_noizetier_pages';
	$where = array('est_virtuelle=' . sql_quote('non'));

	// On recherche les pages et les compositions explicites par le fichier HTML en premier
	// Si on le trouve, on récupère la configuration du fichier XML ou YAML.
	if ($fichiers = find_all_in_path($options['repertoire_pages'], '.+[.]html$')) {
		$pages_nouvelles = $pages_modifiees = $pages_obsoletes = array();
		// Récupération des signatures md5 des pages déjà enregistrées.
		// Si on force le rechargement il est inutile de gérer les signatures et les pages modifiées ou obsolètes.
		$signatures = array();
		if (!$forcer_chargement) {
			$select = array('page', 'signature');
			if ($signatures = sql_allfetsel($select, $from, $where)) {
				$signatures = array_column($signatures, 'signature', 'page');
			}
			// On initialise la liste des pages à supprimer avec l'ensemble des pages non virtuelles
			$pages_obsoletes = $signatures ? array_keys($signatures) : array();
		}

		foreach ($fichiers as $_squelette => $_chemin) {
			$page = basename($_squelette, '.html');
			$dossier = dirname($_chemin);
			$est_composition = noizetier_page_est_composition($page);
			// Exclure certaines pages :
			// -- celles du privé situes dans prive/contenu
			// -- page liée au plugin Zpip en v1
			// -- z_apl liée aux plugins Zpip v1 et Zcore
			// -- les compositions explicites si le plugin Compositions n'est pas activé
			if ((substr($dossier, -13) != 'prive/contenu')
			and (($page != 'page') or !defined('_DIR_PLUGIN_Z'))
			and (($page != 'z_apl') or (!defined('_DIR_PLUGIN_Z') and !defined('_DIR_PLUGIN_ZCORE')))
			and (!$est_composition or ($est_composition	and defined('_DIR_PLUGIN_COMPOSITIONS')))) {
				// On passe le md5 de la page si il existe sinon la chaine vide. Cela permet de déterminer
				// si on doit ajouter la page ou la mettre à jour.
				// Si le md5 est le même et qu'il n'est donc pas utile de recharger la page, la configuration
				// retournée est vide.
				$options['md5'] = isset($signatures[$page]) ? $signatures[$page] : '';
				$options['recharger'] = $forcer_chargement;
				if ($configuration = page_phraser_fichier($page, $options)) {
					if (empty($configuration['identique'])) {
						// La page a été chargée (nouvelle) ou rechargée (modifiée).
						// Néanmoins, on n'inclue cette page que si les plugins qu'elle nécessite explicitement dans son
						// fichier de configuration sont bien tous activés.
						// Rappel: si une page est incluse dans un plugin non actif elle ne sera pas détectée
						//         lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
						//         Ce n'est pas ce cas qui est traité ici.
						$page_a_garder = true;
						$necessite = unserialize($configuration['necessite']);
						if (!empty($necessite)) {
							foreach ($necessite as $plugin) {
								if (!defined('_DIR_PLUGIN_'.strtoupper($plugin))) {
									$page_a_garder = false;
									break;
								}
							}
						}

						// Si la page est à garder on détermine si elle est nouvelle ou modifiée.
						// En mode rechargement forcé toute page est considérée comme nouvelle.
						// Sinon, la page doit être retirée de la base car un plugin qu'elle nécessite a été désactivée:
						// => il suffit pour cela de la laisser dans la liste des pages obsolètes.
						if ($page_a_garder) {
							if (!$options['md5'] or $forcer_chargement) {
								// La page est soit nouvelle soit on est en mode rechargement forcé:
								// => il faut la rajouter dans la table.
								$pages_nouvelles[] = $configuration;
							} else {
								// La configuration stockée dans la table a été modifiée et le mode ne force pas le rechargement:
								// => il faut mettre à jour la page dans la table.
								$pages_modifiees[] = $configuration;
								// => il faut donc la supprimer de la liste des pages obsolètes
								$pages_obsoletes = array_diff($pages_obsoletes, array($page));
							}
						}
					} else {
						// La page n'a pas changée et n'a donc pas été réchargée:
						// => Il faut donc juste indiquer qu'elle n'est pas obsolète.
						$pages_obsoletes = array_diff($pages_obsoletes, array($page));
					}
				} else {
					// Il y a eu une erreur sur lors du rechargement de la page.
					// Ce peut être en particulier le cas où une page HTML sans XML n'est plus détectée car le
					// paramètre _NOIZETIER_LISTER_PAGES_SANS_XML a été positionné de true à false.
					// => il faut donc ne rien faire pour laisser la page dans les obsolètes
					continue;
				}
			}
		}

		// Mise à jour de la table des pages
		// -- Suppression des pages obsolètes ou de toute les pages non virtuelles si on est en mode
		//    rechargement forcé.
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}
		if ($pages_obsoletes) {
			sql_delete($from, sql_in('page', $pages_obsoletes));
		} elseif ($forcer_chargement) {
			sql_delete($from, $where);
		}
		// -- Update des pages modifiées
		if ($pages_modifiees) {
			sql_replace_multi($from, $pages_modifiees);
		}
		// -- Insertion des nouvelles pages
		if ($pages_nouvelles) {
			sql_insertq_multi($from, $pages_nouvelles);
		}
		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}

		$retour = true;
	}

	return $retour;
}

/**
 * Retourne la configuration de la page, de la composition explicite ou de la composition virtuelle demandée.
 * La configuration est stockée en base de données, certains champs sont recalculés avant d'être fournis.
 *
 * @api
 *
 * @uses noizetier_bloc_defaut()
 *
 * @param string	$page
 * 		Identifiant de la page ou de la composition.
 * @param boolean	$traitement_typo
 *      Indique si les données textuelles doivent être retournées brutes ou si elles doivent être traitées
 *      en utilisant la fonction _T_ou_typo.
 * 		Les champs sérialisés sont toujours désérialisés.
 *
 * @return array
 */
function noizetier_page_lire($page, $traitement_typo = true) {

	static $description_page = array();

	if (!isset($description_page[$traitement_typo][$page])) {
		// Chargement de toute la configuration de la page en base de données.
		$description = sql_fetsel('*', 'spip_noizetier_pages', array('page=' . sql_quote($page)));

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Traitements des champs textuels
			if ($traitement_typo) {
				$description['nom'] = _T_ou_typo($description['nom']);
				if (isset($description['description'])) {
					$description['description'] = _T_ou_typo($description['description']);
				}
			}
			// Traitements des champs tableaux sérialisés
			$description['blocs_exclus'] = unserialize($description['blocs_exclus']);
			$description['necessite'] = unserialize($description['necessite']);
			$description['branche'] = unserialize($description['branche']);
			// Calcul des blocs
			$description['blocs'] = noizetier_page_lister_blocs($page, $description['blocs_exclus']);
			$description_page[$traitement_typo][$page] = $description;
		} else {
			$description_page[$traitement_typo][$page] = array();
		}
	}

	return $description_page[$traitement_typo][$page];
}



/**
 *
 * @api
 *
 * @param       $page
 * @param array $blocs_exclus
 *
 * @return array
 */
function noizetier_page_lister_blocs($page, $blocs_exclus = array()) {

	// Initialisation des blocs avec la liste des blocs par défaut
	include_spip('inc/noizetier_bloc');
	$blocs = noizetier_bloc_defaut();

	// Si la liste des blocs exclus n'a pas été passé en argument on les cherche dans la configuration
	// de la page
	if (!$blocs_exclus) {
		$where = array('page=' . sql_quote($page));
		$blocs_exclus = sql_getfetsel('blocs_exclus', 'spip_noizetier_pages', $where);
		$blocs_exclus = unserialize($blocs_exclus);
	}

	if ($blocs_exclus) {
		$blocs = array_diff($blocs, $blocs_exclus);
		sort($blocs);
	}

	return $blocs;
}


/**
 * Renvoie le type d'une page à partir de son identifiant.
 *
 * @api
 *
 * @param string $page
 * 		L'identifiant de la page.
 *
 * @return string
 * 		Le type de la page choisie, c'est-à-dire:
 * 		- soit l'identifiant complet de la page,
 * 		- soit le mot précédent le tiret dans le cas d'une composition.
 */
function noizetier_page_type($page) {
	$type = explode('-', $page, 2);

	return $type[0];
}

/**
 * Détermine, à partir de son identifiant, la composition d'une page si elle existe.
 *
 * @api
 *
 * @param string $page
 * 		L'identifiant de la page.
 *
 * @return string
 *      La composition de la page choisie, à savoir, le mot suivant le tiret,
 * 		ou la chaine vide sinon.
 */
function noizetier_page_composition($page) {
	$composition = explode('-', $page, 2);
	$composition = isset($composition[1]) ? $composition[1] : '';

	return $composition;
}

/**
 * Détermine, à partir de son identifiant, si la page est une composition.
 *
 * @api
 *
 * @param string $page
 * 		L'identifiant de la page.
 *
 * @return boolean
 *      `true` si la page est une composition, `false` sinon.
 */
function noizetier_page_est_composition($page) {
	$est_composition = false;
	if (strpos($page, '-') !== false) {
		$est_composition = true;
	}

	return $est_composition;
}

/**
 * Détermine si les compositions sont possibles sur un type de page.
 *
 * @api
 *
 * @param string $type
 * 		Identifiant du type de page.
 *
 * @return boolean
 * 		True si les compositions sont autorisées, false sinon.
 */
function noizetier_page_composition_activee($type) {

	$est_activee = false;

	if (defined('_DIR_PLUGIN_COMPOSITIONS')) {
		include_spip('compositions_fonctions');
		if (in_array($type, compositions_objets_actives())) {
			$est_activee = true;
		}
	}

	return $est_activee;
}

/**
 * Déterminer le répertoire dans lequel le NoiZetier peut lister les pages pouvant supporter
 * l'insertion de noisettes.
 *
 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 *
 * @return string
 * 		Le répertoire des pages sous la forme dossier/.
 */
function noizetier_page_repertoire() {

	if (defined('_NOIZETIER_REPERTOIRE_PAGES')) {
		$repertoire_pages = _NOIZETIER_REPERTOIRE_PAGES;
	} elseif (isset($GLOBALS['z_blocs'])) {
		$premier_bloc = reset($GLOBALS['z_blocs']);
		$repertoire_pages = "$premier_bloc/";
	} else {
		$repertoire_pages = 'contenu/';
	}

	return $repertoire_pages;
}

/**
 * Phrase le fichier XML ou YAML des pages et compositions configurables par le noiZetier et renvoie
 * un tableau des caractéristiques complètes.
 *
 * @internal
 *
 * @uses noizetier_page_repertoire()
 * @uses noizetier_bloc_defaut()
 *
 * @param       $page
 * @param array $options
 *
 * @return array
 */
function page_phraser_fichier($page, $options = array()) {

	// Initialisation de la description
	$description = array();

	// Choisir le bon répertoire des pages
	if (empty($options['repertoire_pages'])) {
		$options['repertoire_pages'] = noizetier_page_repertoire();
	}

	// Initialiser les blocs par défaut
	if (empty($options['blocs_defaut'])) {
		include_spip('inc/noizetier_bloc');
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
