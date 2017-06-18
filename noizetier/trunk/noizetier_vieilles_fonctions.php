<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -------------------------------------------------------------------
// ------------------------- API NOISETTES ---------------------------
// -------------------------------------------------------------------

/**
 * Lister les noisettes disponibles dans les dossiers noisettes/.
 *
 * @staticvar array $liste_noisettes
 *
 * @param string $type     renvoyer seulement un type de noisettes
 * @param string $noisette renvoyer spécifiquement une noisette données
 *
 * @return array
 */
function noizetier_lister_noisettes($type = 'tout') {
	static $liste_noisettes = array();
	if ($type == 'tout') {
		return noizetier_obtenir_infos_noisettes();
	}
	if (isset($liste_noisettes[$type])) {
		return $liste_noisettes[$type];
	}

	$noisettes = noizetier_obtenir_infos_noisettes();
	if ($type == '') {
		$match = '^[^-]*$';
	} else {
		$match = $type.'-[^-]*$';
	}

	foreach ($noisettes as $noisette => $description) {
		if (preg_match("/$match/", $noisette)) {
			$liste_noisettes[$type][$noisette] = $description;
		}
	}

	return isset($liste_noisettes[$type]) ? $liste_noisettes[$type]: '';
}

/**
 * Renvoie les info d'une seule noisette.
 *
 * @param string $noisette renvoyer spécifiquement une noisette données
 *
 * @return array
 */
function noizetier_info_noisette($noisette) {
	$noisettes = noizetier_obtenir_infos_noisettes();
	if (isset($noisettes[$noisette])) {
		$noisette = $noisettes[$noisette];
	} else {
		$noisette = array('nom' => _T('noizetier:formulaire_erreur_noisette_introuvable', array('noisette' => $noisette)));
	}

	return $noisette;
}

/**
 * Obtenir les infos de toutes les noisettes disponibles dans les dossiers noisettes/
 * On utilise un cache php pour alleger le calcul.
 *
 * @param
 *
 * @return
 **/
function noizetier_obtenir_infos_noisettes() {
	static $noisettes = false;

	// seulement 1 fois par appel, on lit ou calcule tous les contextes
	if ($noisettes === false) {
		// lire le cache des descriptions sauvees
		lire_fichier_securise(_CACHE_DESCRIPTIONS_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);
		// s'il en mode recalcul, on recalcule toutes les descriptions des noisettes trouvees.
		// ou si le cache est desactive
		if (!$noisettes or (_request('var_mode') == 'recalcul') or (defined('_NO_CACHE') and _NO_CACHE != 0)) {
			$noisettes = noizetier_obtenir_infos_noisettes_direct();
			ecrire_fichier_securise(_CACHE_DESCRIPTIONS_NOISETTES, serialize($noisettes));
		}
	}

	return $noisettes;
}

/**
 * Obtenir les infos de toutes les noisettes disponibles dans les dossiers noisettes/
 * C'est un GROS calcul lorsqu'il est a faire.
 *
 * @return array
 */
function noizetier_obtenir_infos_noisettes_direct() {
	$liste_noisettes = array();

	$match = '[^-]*[.]html$';
	$liste = find_all_in_path('noisettes/', $match);

	if (count($liste)) {
		foreach ($liste as $squelette => $chemin) {
			$noisette = preg_replace(',[.]html$,i', '', $squelette);
			$dossier = str_replace($squelette, '', $chemin);
			// On ne garde que les squelettes ayant un fichier YAML de config
			if (file_exists("$dossier$noisette.yaml")
				and ($infos_noisette = noizetier_charger_infos_noisette_yaml($dossier.$noisette))
			) {
				$liste_noisettes[$noisette] = $infos_noisette;
			}
		}
	}

	// supprimer de la liste les noisettes necessitant un plugin qui n'est pas actif.
	// On s'arrête au premier inactif.
	foreach ($liste_noisettes as $noisette => $infos_noisette) {
		if (!empty($infos_noisette['necessite'])) {
			foreach ($infos_noisette['necessite'] as $plugin) {
				if (!defined('_DIR_PLUGIN_'.strtoupper($plugin))) {
					unset($liste_noisettes[$noisette]);
					break;
				}
			}
		}
	}

	return $liste_noisettes;
}

/**
 * Charger les informations contenues dans le YAML d'une noisette.
 *
 * @param string $noisette
 * @param string $info
 *
 * @return array
 */
function noizetier_charger_infos_noisette_yaml($noisette, $info = '') {
	include_spip('inc/yaml');
	include_spip('inc/texte');

	// on peut appeler avec le nom du squelette
	$fichier = preg_replace(',[.]html$,i', '', $noisette).'.yaml';

	$infos_noisette = array();
	if ($infos_noisette = yaml_charger_inclusions(yaml_decode_file($fichier))) {
		if (isset($infos_noisette['nom'])) {
			$infos_noisette['nom'] = _T_ou_typo($infos_noisette['nom']);
		}
		if (isset($infos_noisette['description'])) {
			$infos_noisette['description'] = _T_ou_typo($infos_noisette['description']);
		}
		if (isset($infos_noisette['icon'])) {
			$infos_noisette['icon'] = $infos_noisette['icon'];
		}

		if (!isset($infos_noisette['parametres'])) {
			$infos_noisette['parametres'] = array();
		}

		// necessite de plugins : toujours renvoyer un array
		if (!isset($infos_noisette['necessite'])) {
			$infos_noisette['necessite'] = array();
		}

		if (is_string($infos_noisette['necessite'])) {
			$infos_noisette['necessite'] = array($infos_noisette['necessite']);
		}

		// contexte
		if (!isset($infos_noisette['contexte'])) {
			$infos_noisette['contexte'] = array();
		}

		if (is_string($infos_noisette['contexte'])) {
			$infos_noisette['contexte'] = array($infos_noisette['contexte']);
		}

		// ajax
		if (!isset($infos_noisette['ajax'])) {
			$infos_noisette['ajax'] = 'oui';
		}

		// inclusion
		if (!isset($infos_noisette['inclusion'])) {
			$infos_noisette['inclusion'] = 'statique';
		}
	}

	if (!$info) {
		return $infos_noisette;
	} else {
		return isset($infos_noisette[$info]) ? $infos_noisette[$info] : '';
	}
}

/**
 * Retourne true ou false pour indiquer si la noisette doit être inclue en ajax.
 *
 * @param
 *
 * @return
 **/
function noizetier_ajaxifier_noisette($noisette) {
	static $noisettes = false;

	// seulement 1 fois par appel, on lit ou calcule tous les contextes
	if ($noisettes === false) {
		// lire le cache des contextes sauves
		lire_fichier_securise(_CACHE_AJAX_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);

		// s'il en mode recalcul, on recalcule tous les contextes des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			$infos = noizetier_lister_noisettes();
			$noisettes = array();
			foreach ($infos as $cle_noisette => $infos) {
				$noisettes[$cle_noisette] = ($infos['ajax'] == 'non') ? false : true;
			}
			ecrire_fichier_securise(_CACHE_AJAX_NOISETTES, serialize($noisettes));
		}
	}

	if (isset($noisettes[$noisette])) {
		return $noisettes[$noisette];
	}

	return true;
}


// -------------------------------------------------------------------
// --------------------------- API ICONES ----------------------------
// -------------------------------------------------------------------


// -------------------------------------------------------------------
// ---------------------------- API BLOCS ----------------------------
// -------------------------------------------------------------------


// -------------------------------------------------------------------
// ---------------------------- API PAGES ----------------------------
// -------------------------------------------------------------------

/**
 * Retourne la liste des pages, des compositions explicites et des compositions virtuelles.
 * Chaque page est fournie avec l'ensemble de sa configuration.
 * Si le plugin Compositions n'est pas actif, les compositions explicites ou virtuelles ne sont
 * pas retournées.
 *
 * @package SPIP\NOIZETIER\API\PAGE
 * @uses noizetier_informer_page()
 * @api
 * @filtre
 *
 * @param array $filtres
 *
 * @return array|null
 * 		Tableau des pages, l'index est l'identifiant de la page.
 */
function noizetier_page_repertorier($filtres = array()) {
	static $pages = null;

	if (is_null($pages)) {
		// Choisir le bon répertoire des pages
		$options['repertoire_pages'] = noizetier_page_obtenir_dossier();

		if ($options['repertoire_pages']) {
			// Initialiser les blocs par défaut
			$options['blocs_defaut'] = noizetier_bloc_defaut();

			// On recherche en premier lieu les pages et les compositions explicites
			// -- on optimise la recherche si on a un filtre est_vrituelle à true inutile de récupérer les pages
			//    et compositions explicites
			if ($fichiers = find_all_in_path($options['repertoire_pages'], '.+[.]html$')) {
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
						if ($configuration = page_informer($page, '', $options)) {
							// On n'inclue la page que si les plugins qu'elle nécessite explicitement dans son
							// fichier de configuration sont bien tous activés.
							// Rappel : si une page est incluse dans un plugin non actif elle ne sera pas détectée
							//          lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
							$page_a_garder = true;
							if (isset($configuration['necessite'])) {
								foreach ($configuration['necessite'] as $plugin) {
									if (!defined('_DIR_PLUGIN_'.strtoupper($plugin))) {
										$page_a_garder = false;
										break;
									}
								}
							}

							if ($page_a_garder) {
								$pages[$page] = $configuration;
							}
						}
					}
				}
			}

			// Si le plugin Compositions est activé, on ajoute les compositions virtuelles
			// qui ne sont définies que dans une meta propre au noiZetier.
			// -- on optimise la recherche si on a un filtre est_virtuelle ou est_composition à false inutile de récupérer les
			//    compositions virtuelles du noiZetier
			if (defined('_DIR_PLUGIN_COMPOSITIONS')) {
				include_spip('inc/config');
				$options['compositions'] = lire_config('noizetier_compositions', array());
				if ($options['compositions']) {
					foreach ($options['compositions'] as $_composition => $_configuration) {
						if ($configuration = page_informer($_composition, '', $options)) {
								$pages[$_composition] = $configuration;
						}
					}
				}
			}

			// Appel du pipeline noizetier_lister_pages pour éventuellement compléter ou modifier la liste
			$pages = pipeline('noizetier_lister_pages', $pages);
		}
	}

	// Filtrage des pages et compositions récupérées:
	// - de façon systématique, si le plugin Compositions est activé, on ne retient que les compositions
	//   basées sur des types d'objets pour lesquels les compositions sont actives (configuration du plugin Compositions)
	// - a la demande, on applique les filtres éventuellement demandés en argument de la fonction
	$pages_repertoriees = $pages;
	foreach ($pages_repertoriees as $_page => $_configuration) {
		if (defined('_DIR_PLUGIN_COMPOSITIONS')
		and $_configuration['composition']
		and ($_configuration['est_page_objet'] == 'oui')
		and !in_array($_configuration['type'], compositions_objets_actives())) {
			unset($pages_repertoriees[$_page]);
		} else {
			if ($filtres) {
				foreach ($filtres as $_critere => $_valeur) {
					if ((($_critere == 'est_composition') and $_valeur and !$_configuration['composition'])
					or (($_critere == 'est_composition') and !$_valeur and $_configuration['composition'])
					or (isset($_configuration[$_critere]) and ($_configuration[$_critere] != $_valeur))) {
						unset($pages_repertoriees[$_page]);
						break;
					}
				}
			}
		}
	}

	return $pages_repertoriees;
}


/**
 * Retourne la configuration de la page, de la composition explicite ou de la composition virtuelle demandée.
 *
 * @uses noizetier_page_obtenir_dossier()
 * @uses noizetier_bloc_defaut()

 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 * @filtre
 *
 * @param string	$page
 * 		Identififant de la page ou de la composition.
 * @param string	$information
 * 		Information spécifique à retourner. Si vide, on retourne toute la configuration de la page
 * @param array		$options
 *      Options d'optimisation passées par l'appelant. Les options sont :
 * 		- `repertoire_pages` : répertoire où chercher les pages et les compositions explicites.
 * 		- `blocs_defaut` : liste des identifiants de blocs configurables par défaut pour toutes les pages.
 * 		- `compositions`: meta des compositions virtuelles créées par le noizetier.
 *
 * @return array|string
 */
function page_informer($page, $information = '', $options =array()) {

	static $description_page = array();

	if (!isset($description_page[$page])) {
		// Initialisation de la description
		$description = array();

		// Choisir le bon répertoire des pages
		if (empty($options['repertoire_pages'])) {
			$options['repertoire_pages'] = noizetier_page_obtenir_dossier();
		}

		// Initialiser les blocs par défaut
		if (empty($options['blocs_defaut'])) {
			$options['blocs_defaut'] = noizetier_bloc_defaut();
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
		$est_virtuelle = 'non';

		// La recherche de la page est basée sur l'heuristique suivante:
		//  1- Les pages ou compositions explicites sont les plus fréquentes et on les recherche
		//     en premier.
		//     a- Le fichier YAML est recherché en premier,
		//     b- ensuite le fichier XML pour compatibilité ascendante.
		//     c- enfin, si il n'y a ni YAML, ni XML et que le mode le permet, on renvoie une description standard minimale
		//  2- Si cette recherche n'aboutit pas et que le plugin Compositions est actif,
		//     on scrute les compositions du noiZetier stockées en meta.
		if ($fichier = find_in_path("{$options['repertoire_pages']}${page}.yaml")) {
			// 1a- il y a un fichier YAML de configuration
			include_spip('inc/yaml');
			if ($description = yaml_charger_inclusions(yaml_decode_file($fichier))) {
				$description['nom'] = isset($description['nom']) ? _T_ou_typo($description['nom']) : $page;
				if (isset($description['description'])) {
					$description['description'] = _T_ou_typo($description['description']);
				}
				if (!isset($description['icon'])) {
					$description['icon'] = 'page-24.png';
				}
				if (!isset($description['blocs_exclus'])) {
					$description['blocs'] = $options['blocs_defaut'];
				} else {
					$description['blocs'] = array_diff($options['blocs_defaut'], $description['blocs_exclus']);
				}
			}
		} elseif ($fichier = find_in_path("{$options['repertoire_pages']}${page}.xml")) {
			// 1b- il y a un fichier XML de configuration.
			//     on extrait et on parse le XML de configuration en tenant compte que ce peut être
			//     celui d'une page ou d'une composition, ce qui change la balise englobante.
			include_spip('inc/xml');
			if ($xml = spip_xml_load($fichier, false)
			and (isset($xml['page']) or isset($xml['composition']))) {
				$xml = isset($xml['page']) ? reset($xml['page']) : reset($xml['composition']);
				// Titre (nom), description et icone
				$description['nom'] = isset($xml['nom']) ? _T_ou_typo(spip_xml_aplatit($xml['nom'])) : $page;
				if (isset($xml['description'])) {
					$description['description'] = _T_ou_typo(spip_xml_aplatit($xml['description']));
				}
				$description['icon'] = isset($xml['icon']) ? reset($xml['icon']) : 'page-24.png';

				// Liste des blocs autorisés pour la page. On vérifie que les blocs configurés sont bien dans
				// la liste des blocs par défaut.
				$description['blocs'] = array();
				if (spip_xml_match_nodes(',^bloc,', $xml, $blocs)) {
					foreach (array_keys($blocs) as $_bloc) {
						list(, $attributs) = spip_xml_decompose_tag($_bloc);
						$description['blocs'][] = $attributs['id'];
					}
				}
				$description['blocs'] = $description['blocs'] ? array_intersect($options['blocs_defaut'], $description['blocs']) : $options['blocs_defaut'];

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
		} elseif (defined('_NOIZETIER_LISTER_PAGES_SANS_XML') ? _NOIZETIER_LISTER_PAGES_SANS_XML : false) {
			// 1c- il est autorisé de ne pas avoir de fichier XML de configuration
			$description['nom'] = $page;
			$description['icon'] = 'img/ic_page.png';
			$description['blocs'] = $options['blocs_defaut'];
		} else {
			// 2- la page est une composition virtuelle
			if (empty($options['compositions'])) {
				include_spip('inc/config');
				// TODO : ne peut-on pas limiter à la page ?
				$options['compositions'] = lire_config('noizetier_compositions', array());
			}
			if (isset($options['compositions'][$page])) {
				$description = $options['compositions'][$page];
				$description['nom'] = !empty($description['nom'])
					? typo($description['nom'])
					: $page;
				if (isset($description['description'])) {
					$description['description'] = typo($description['description']);
				}
				if (empty($description['icon'])) {
					$description['icon'] = 'composition-24.png';
				}
				if (!isset($description['blocs_exclus'])) {
					$description['blocs'] = $options['blocs_defaut'];
				} else {
					$description['blocs'] = array_diff($options['blocs_defaut'], $description['blocs_exclus']);
				}
				$est_virtuelle = 'oui';
			}
		}

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Renuméroter les blocs à cause des exclusions
			sort($description['blocs']);
			// Stockage des identifiants séparément
			$description['type'] = $identifiants[0];
			$description['composition'] = $identifiants[1];
			// Identifie si la page est celle d'un objet SPIP
			include_spip('base/objets');
			$tables_objets = array_keys(lister_tables_objets_sql());
			$description['est_page_objet'] = in_array(table_objet_sql($description['type']), $tables_objets) ? 'oui' : 'non';
			// Stockage de l'information indiquant si la page ou la composition est virtuelle ou pas
			$description['est_virtuelle'] = $est_virtuelle;
			$description_page[$page] = $description;
		} else {
			$description_page[$page] = array();
		}
	}

	if (!$information) {
		return $description_page[$page];
	} elseif (isset($description_page[$page][$information])) {
		return $description_page[$page][$information];
	} else {
		return '';
	}
}


// --------------------------------------------------------------------
// ---------------------------- API OBJETS ----------------------------
// --------------------------------------------------------------------

