<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_CACHE_AJAX_NOISETTES', 'noisettes_ajax.php');
define('_CACHE_CONTEXTE_NOISETTES', 'noisettes_contextes.php');
define('_CACHE_DESCRIPTIONS_NOISETTES', 'noisettes_descriptions.php');
define('_CACHE_INCLUSIONS_NOISETTES', 'noisettes_inclusions.php');

/**
 * Lister les noisettes disponibles dans les dossiers noisettes/.
 *
 * @staticvar array $liste_noisettes
 *
 * @param text $type     renvoyer seulement un type de noisettes
 * @param text $noisette renvoyer spécifiquement une noisette données
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
		lire_fichier_securise(_DIR_CACHE._CACHE_DESCRIPTIONS_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);
		// s'il en mode recalcul, on recalcule toutes les descriptions des noisettes trouvees.
		// ou si le cache est desactive
		if (!$noisettes or (_request('var_mode') == 'recalcul') or (defined('_NO_CACHE') and _NO_CACHE != 0)) {
			$noisettes = noizetier_obtenir_infos_noisettes_direct();
			ecrire_fichier_securise(_DIR_CACHE._CACHE_DESCRIPTIONS_NOISETTES, serialize($noisettes));
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
 * Charger les informations des contexte pour une noisette.
 *
 * @param string $noisette
 * @staticvar array $params_noisettes
 *
 * @return array
 */
function noizetier_charger_contexte_noisette($noisette) {
	static $contexte_noisettes = null;

	if (is_null($contexte_noisettes[$noisette])) {
		$noisettes = noizetier_lister_noisettes();
		$contexte_noisettes[$noisette] = $noisettes[$noisette]['contexte'];
	}

	return $contexte_noisettes[$noisette];
}

/**
 * Déterminer le répertoire dans lequel le NoiZetier peut lister les pages pouvant supporter
 * l'insertion de noisettes.
 *
 * @return string
 * 		Le répertoire des pages sous la forme dossier/.
 */
function noizetier_obtenir_dossier_pages() {
	$repertoire_pages = '';

	if (defined('_NOIZETIER_REPERTOIRE_PAGES')) {
		$repertoire_pages = _NOIZETIER_REPERTOIRE_PAGES;
	}
	elseif (isset($GLOBALS['z_blocs'])) {
		$premier_bloc = reset($GLOBALS['z_blocs']);
		$repertoire_pages = "$premier_bloc/";
	} else {
		$repertoire_pages = 'contenu/';
	}

	return $repertoire_pages;
}


/**
 * Lister les pages et les compositions spécifiques au noizetier pouvant recevoir des noisettes.
 * Le tableau de resultats peut-etre modifie via le pipeline noizetier_lister_pages.
 *
 * @param string $page_specifique
 * 		Id d'une page précise ou chaine vide.
 *
 * @return array
 * 		Si un id de page valide est fourni, on renvoie le tableau des éléments de cette page.
 * 		Sinon, on renvoie le tableau de toutes les pages, chaque index étant l'id de la page.
 */
function noizetier_lister_pages($page_specifique = '') {
	static $liste_pages = null;

	if (is_null($liste_pages)) {
		$liste_pages = array();

		// Choisir le bon répertoire des pages
		$repertoire_pages = noizetier_obtenir_dossier_pages();

		if ($repertoire_pages) {
			// Lister les fonds disponibles dans le repertoire des pages et ce pour tous les plugins activés
			$liste = find_all_in_path($repertoire_pages, '.+[.]html$');
			if (count($liste)) {
				foreach ($liste as $squelette => $chemin) {
					$page = basename($squelette, '.html');
					$dossier = str_replace($squelette, '', $chemin);
					// Exclure certaines pages :
					// -- celles du privé situes dans prive/contenu
					// -- page liée au plugin Zpip en v1
					// -- z_apl liée aux plugins Zpip v1 et Zcore
					if ((substr($dossier, -14) != 'prive/contenu/')
					and (($page != 'page') or !defined('_DIR_PLUGIN_Z'))
					and (($page != 'z_apl') or (!defined('_DIR_PLUGIN_Z') and !defined('_DIR_PLUGIN_ZCORE')))) {
						if (count($infos_page = noizetier_charger_infos_page($dossier, $page)) > 0) {
							// On n'inclue la page que si les plugins qu'elle nécessite explicitement dans son
							// fichier de configuration sont bien tous activés.
							// Rappel : si une page est incluse dans un plugin non actif elle ne sera pas détectée
							//          lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
							$page_a_garder = true;
							if (isset($infos_page['necessite'])) {
								foreach ($infos_page['necessite'] as $plugin) {
									if (!defined('_DIR_PLUGIN_'.strtoupper($plugin))) {
										$page_a_garder = false;
									}
								}
							}
							if ($page_a_garder) {
								$liste_pages[$page] = $infos_page;
							}
						}
					}
				}
			}

			$liste_pages = pipeline('noizetier_lister_pages', $liste_pages);

			// On ajoute les compositions du noizetier qui ne sont définies que dans une meta propre au NoiZetier.
			if (defined('_DIR_PLUGIN_COMPOSITIONS')) {

				$noizetier_compositions = isset($GLOBALS['meta']['noizetier_compositions'])
					? unserialize($GLOBALS['meta']['noizetier_compositions'])
					: array();
				// On doit transformer le tableau de [type][compo] en [type-compo]
				$liste_compos = array();
				if (is_array($noizetier_compositions) and $noizetier_compositions) {
					foreach ($noizetier_compositions as $type => $compos_type) {
						foreach ($compos_type as $compo => $infos_compo) {
							$infos_compo['nom'] = typo($infos_compo['nom']);
							$infos_compo['description'] = propre($infos_compo['description']);
							if ($infos_compo['icon'] == '') {
								$infos_compo['icon'] = (isset($liste_pages[$type]) && isset($liste_pages[$type]['icon']) && $liste_pages[$type]['icon'] != '') ? $liste_pages[$type]['icon'] : 'composition-24.png';
							}
							if (isset($liste_pages[$type])) {
								$infos_compo['blocs'] = $liste_pages[$type]['blocs'];
							} else {
								$infos_compo['blocs'] = noizetier_blocs_defaut();
							}
							$liste_compos[$type.'-'.$compo] = $infos_compo;
						}
					}
				}
				$liste_pages = $liste_pages + $liste_compos;
			}
		}
	}

	if ($page_specifique and isset($liste_pages[$page_specifique])) {
		return $liste_pages[$page_specifique];
	} else {
		return $liste_pages;
	}
}


/**
 * Lister les tables d'objets SPIP qui ne peuvent pas être configurées avec des noisettes
 * car elles ne possèdent pas de page compatible.
 *
 * @return array|null
 * 		Tableau des tables d'objets à exclure sous la forme spip_xxxx.
 */
function noizetier_lister_objets_exclus() {

	static $exclusions = null;

	if (is_null($exclusions)) {
		$exclusions = array();
		include_spip('base/objets');

		// On récupère les tables d'objets sous la forme spip_xxxx.
		$tables = lister_tables_objets_sql();
		$tables = array_keys($tables);

		// On récupère la liste des pages disponibles et on transforme le type d'objet en table SQL.
		$pages = noizetier_lister_pages();
		$pages = array_keys($pages);
		$pages = array_map('table_objet_sql', $pages);

		// On exclut donc les tables qui ne sont pas dans la liste issues des pages.
		$exclusions = array_diff($tables, $pages);
	}

	return $exclusions;
}


/**
 * Charger les informations d'une page, contenues dans un xml de config s'il existe.
 *
 * @param string $dossier
 * @param string $page
 * @param string $info
 *
 * @return array
 */
function noizetier_charger_infos_page($dossier, $page, $info = '') {
	// on peut appeler avec le nom du squelette
	$page = preg_replace(',[.]html$,i', '', $page);
	
	// Choisir le bon répertoire des pages
	$repertoire_pages = noizetier_obtenir_dossier_pages();

	// On autorise le fait que le fichier xml ne soit pas dans le meme plugin que le fichier .html
	// Au cas ou le fichier .html soit surcharge sans que le fichier .xml ne le soit
	$fichier = find_in_path("$repertoire_pages$page.xml");

	include_spip('inc/xml');
	include_spip('inc/texte');
	$infos_page = array();
	
	// S'il existe un fichier xml de configuration (s'il s'agit d'une composition on utilise l'info de la composition)
	if (file_exists($fichier) and $xml = spip_xml_load($fichier, false) and !empty($xml['page'])) {
		$xml = reset($xml['page']);
	} elseif (file_exists($fichier) and $xml = spip_xml_load($fichier, false) and count($xml['composition'])) {
		$xml = reset($xml['composition']);
	} else {
		$xml = '';
	}
	
	if ($xml != '') {
		$infos_page['nom'] = _T_ou_typo(spip_xml_aplatit($xml['nom']));
		$infos_page['description'] = isset($xml['description']) ? _T_ou_typo(spip_xml_aplatit($xml['description'])) : '';
		$infos_page['icon'] = isset($xml['icon']) ? reset($xml['icon']) : 'page-24.png';

		// Decomposition des blocs
		if (spip_xml_match_nodes(',^bloc,', $xml, $blocs)) {
			$infos_page['blocs'] = array();
			foreach (array_keys($blocs) as $bloc) {
				list($balise, $attributs) = spip_xml_decompose_tag($bloc);
				$infos_page['blocs'][$attributs['id']] = array(
					'nom' => $attributs['nom'] ? _T($attributs['nom']) : $attributs['id'],
					'icon' => isset($attributs['icon']) ? $attributs['icon'] : '',
					'description' => _T($attributs['description']),
				);
			}
		}

		if (spip_xml_match_nodes(',^necessite,', $xml, $necessites)) {
			$infos_page['necessite'] = array();
			foreach (array_keys($necessites) as $necessite) {
				list($balise, $attributs) = spip_xml_decompose_tag($necessite);
				$infos_page['necessite'][] = $attributs['id'];
			}
		}
	} elseif (defined('_NOIZETIER_LISTER_PAGES_SANS_XML') ? _NOIZETIER_LISTER_PAGES_SANS_XML : true) {
		// S'il n'y a pas de fichier XML de configuration
		$infos_page['nom'] = $page;
		$infos_page['icon'] = 'img/ic_page.png';
	}

	// Si les blocs n'ont pas ete definis, on applique les blocs par defaut
	if (count($infos_page) > 0 and !isset($infos_page['blocs'])) {
		$infos_page['blocs'] = noizetier_blocs_defaut();
	}

	// On renvoie les infos
	if (!$info) {
		return $infos_page;
	} else {
		return isset($infos_page[$info]) ? $infos_page[$info] : '';
	}
}

/**
 * La liste des blocs par defaut d'une page peut etre modifiee via le pipeline noizetier_blocs_defaut.
 *
 * @staticvar array $blocs_defaut
 *
 * @return array
 */
function noizetier_blocs_defaut() {
	static $blocs_defaut = null;

	if (is_null($blocs_defaut)) {
		if (defined('_DIR_PLUGIN_ZCORE') and isset($GLOBALS['z_blocs']) and is_array($GLOBALS['z_blocs'])) {
			$blocs_defaut = array();
			
			foreach ($GLOBALS['z_blocs'] as $z_bloc) {
				$blocs_defaut[$z_bloc] = array(
					'nom' => ucfirst($z_bloc),
				);
			}
		}
		else {
			$blocs_defaut = array(
				'contenu' => array(
					'nom' => _T('noizetier:nom_bloc_contenu'),
					'description' => _T('noizetier:description_bloc_contenu'),
					'icon' => 'bloc-contenu-24.png',
					),
				'navigation' => array(
					'nom' => _T('noizetier:nom_bloc_navigation'),
					'description' => _T('noizetier:description_bloc_navigation'),
					'icon' => 'bloc-navigation-24.png',
					),
				'extra' => array(
					'nom' => _T('noizetier:nom_bloc_extra'),
					'description' => _T('noizetier:description_bloc_extra'),
					'icon' => 'bloc-extra-24.png',
					),
			);
		}
		
		$blocs_defaut = pipeline('noizetier_blocs_defaut', $blocs_defaut);
	}

	return $blocs_defaut;
}

/**
 * Supprime de spip_noisettes les noisettes liees a une page.
 *
 * @param string|array $page
 */
function noizetier_supprimer_noisettes_page($page) {
	$objet = '';
	$id_objet = 0;
	$type = '';
	$composition = '';
	
	if (is_array($page)) {
		$objet = $page['objet'];
		$id_objet = $page['id_objet'];
	}
	else {
		$type_compo = explode('-', $page, 2);
		$type = $type_compo[0];
		
		if (isset($type_compo[1])) {
			$composition = $type_compo[1];
		}
	}
	
	if (autoriser('configurer', 'noizetier')) {
		sql_delete(
			'spip_noisettes',
			'type = '.sql_quote($type)
			.' AND composition = '.sql_quote($composition)
			.' AND objet = '.sql_quote($objet)
			.' AND id_objet = '.intval($id_objet)
		);
		
		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur("id='page/$page'");
	}
}

/**
 * Supprime de spip_noisettes une noisette particuliere.
 *
 * @param text $id_noisette
 */
function noizetier_supprimer_noisette($id_noisette) {
	if (autoriser('configurer', 'noizetier') and intval($id_noisette)) {
		sql_delete('spip_noisettes', 'id_noisette='.intval($id_noisette));
		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur("id='noisette/$id_noisette'");
	}
}

/**
 * Ajoute une noisette à un bloc d'une page
 *
 * @param string $noisette
 * 		Nom de la noisette à ajouter
 * @param string|array $page
 * 		Nom de la page-composition OU tableau contenant l'objet et l'id_objet
 * @param string $bloc
 * 		Nom du bloc où ajouter la noisette
 *
 * @return int
 * 		Retourne l'identifiant de la nouvelle noisette
 **/
function noizetier_ajouter_noisette($noisette, $page, $bloc) {
	$objet = '';
	$id_objet = 0;
	if (is_array($page)) {
		$objet = $page['objet'];
		$id_objet = $page['id_objet'];
		$page = null;
	}
	
	if (autoriser('configurer', 'noizetier') && $noisette) {
		include_spip('inc/saisies');
		$info_noisette = noizetier_info_noisette($noisette);
		$parametres = saisies_lister_valeurs_defaut($info_noisette['parametres']);
		
		// On construit le where pour savoir quelles noisettes chercher
		$where = array();
		if ($page) {
			$where[] = 'type = '.sql_quote(noizetier_page_type($page));
			$where[] = 'composition = '.sql_quote(noizetier_page_composition($page));
		}
		else {
			$where[] = 'objet = '.sql_quote($objet);
			$where[] = 'id_objet = '.intval($id_objet);
		}
		$where[] = 'bloc = '.sql_quote($bloc);
		
		// On cherche le rang suivant
		$rang = intval(sql_getfetsel(
			'rang',
			'spip_noisettes',
			$where,
			'',
			'rang DESC',
			'0,1'
		)) + 1;
		
		$id_noisette = sql_insertq(
			'spip_noisettes',
			array(
				'type' => noizetier_page_type($page),
				'composition' => noizetier_page_composition($page),
				'objet' => $objet,
				'id_objet' => $id_objet,
				'bloc' => $bloc,
				'noisette' => $noisette,
				'rang' => $rang,
				'parametres' => serialize($parametres),
			)
		);

		if ($id_noisette) {
			// On invalide le cache
			include_spip('inc/invalideur');
			suivre_invalideur("id='noisette/$id_noisette'");

			return $id_noisette;
		}
	}
	
	return 0;
}

/**
 * Tri les noisettes d'une page
 * Attention : parfois la page est transmise dans $ordre (et peu éventuellement changer en cours, cas de la page-dist de Zpip-vide).
 *
 * @param text  $page
 * @param array $ordre
 *
 * @return bool
 */
function noizetier_trier_noisette($page, $ordre) {
	// Vérifications
	if (
		!autoriser('configurer', 'noizetier')
		or !is_array($ordre)
		or substr($ordre[0], 0, 4) != 'bloc'
	) {
		return false;
	}
	
	$objet = '';
	$id_objet = 0;
	if (is_array($page)) {
		$objet = $page['objet'];
		$id_objet = $page['id_objet'];
		$type = '';
		$composition = '';
	}
	elseif ($page) {
		$type = noizetier_page_type($page);
		$composition = noizetier_page_composition($page);
	}
	
	$modifs = array();
	foreach ($ordre as $entree) {
		$entree = explode('-', $entree, 2);
		
		if ($entree[0] == 'bloc') {
			$bloc = $entree[1];
			$rang = 1;
		}
		if ($entree[0] == 'page') {
			$page = $entree[1];
			$type = noizetier_page_type($page);
			$composition = noizetier_page_composition($page);
		}
		if ($entree[0] == 'objet') {
			$objet = $entree[1];
		}
		if ($entree[0] == 'id_objet') {
			$id_objet = intval($entree[1]);
		}
		if ($entree[0] == 'noisette') {
			$modifs[$entree[1]] = array(
				'bloc' => $bloc,
				'type' => $type,
				'composition' => $composition,
				'objet' => $objet,
				'id_objet' => $id_objet,
				'rang' => $rang,
			);
			$rang += 1;
		}
		if ($entree[0] == 'ajouter') {
			$id_noisette = noizetier_ajouter_noisette($entree[1], $page, $bloc);
			$modifs[$id_noisette] = array(
				'bloc' => $bloc,
				'type' => $type,
				'composition' => $composition,
				'objet' => $objet,
				'id_objet' => $id_objet,
				'rang' => $rang,
			);
			$rang += 1;
		}
	}

	foreach ($modifs as $id_noisette => $valeurs) {
		sql_updateq('spip_noisettes', $valeurs, 'id_noisette='.intval($id_noisette));
	}

	return true;
}

/**
 * Déplace une noisette au sein d'un bloc.
 *
 * @param text $id_noisette
 * @param text $sens
 */
function noizetier_deplacer_noisette($id_noisette, $sens) {
	$id_noisette = intval($id_noisette);
	if ($sens != 'bas') {
		$sens = 'haut';
	}
	
	if (autoriser('configurer', 'noizetier') and $id_noisette) {
		// On récupère des infos sur le placement actuel
		$noisette = sql_fetsel(
			'bloc, type, composition, objet, id_objet, rang',
			'spip_noisettes',
			'id_noisette = '.$id_noisette
		);
		$bloc = $noisette['bloc'];
		$type = $noisette['type'];
		$composition = $noisette['composition'];
		$objet = $noisette['objet'];
		$id_objet = intval($noisette['id_objet']);
		$rang_actuel = intval($noisette['rang']);

		// On teste si y a une noisette suivante
		$dernier_rang = intval(sql_getfetsel(
			'rang',
			'spip_noisettes',
			array(
				'bloc = '.sql_quote($bloc),
				'type = '.sql_quote($type),
				'composition = '.sql_quote($composition),
				'objet = '.sql_quote($objet),
				'id_objet = '.$id_objet,
			),
			'',
			'rang desc',
			'0,1'
		));

		// Tant qu'on ne veut pas faire de tour complet
		if (!($sens == 'bas' and $rang_actuel == $dernier_rang) and !($sens == 'haut' and $rang_actuel == 1)) {
			// Alors on ne fait qu'échanger deux noisettes
			$rang_echange = ($sens == 'bas') ? ($rang_actuel + 1) : ($rang_actuel - 1);
			$ok = sql_updateq(
				'spip_noisettes',
				array(
					'rang' => $rang_actuel,
				),
				array(
					'bloc = '.sql_quote($bloc),
					'type = '.sql_quote($type),
					'composition = '.sql_quote($composition),
					'objet = '.sql_quote($objet),
					'id_objet = '.$id_objet,
					'rang = '.$rang_echange,
				)
			);
			if ($ok) {
				$ok = sql_updateq(
					'spip_noisettes',
					array(
						'rang' => $rang_echange,
					),
					'id_noisette = '.$id_noisette
				);
			}
		}
		// Sinon on fait un tour complet en déplaçant tout
		else {
			if ($sens == 'bas') {
				// Tout le monde descend d'un rang
				$ok = sql_update(
					'spip_noisettes',
					array(
						'rang' => 'rang + 1',
					),
					array(
						'bloc = '.sql_quote($bloc),
						'type = '.sql_quote($type),
						'composition = '.sql_quote($composition),
						'objet = '.sql_quote($objet),
						'id_objet = '.$id_objet,
					)
				);
				// La noisette passe tout en haut
				if ($ok) {
					$ok = sql_updateq(
						'spip_noisettes',
						array(
							'rang' => 1,
						),
						'id_noisette = '.$id_noisette
					);
				}
			} else {
				// Tout le monde monte d'un rang
				$ok = sql_update(
					'spip_noisettes',
					array(
						'rang' => 'rang - 1',
					),
					array(
						'bloc = '.sql_quote($bloc),
						'type = '.sql_quote($type),
						'composition = '.sql_quote($composition),
						'objet = '.sql_quote($objet),
						'id_objet = '.$id_objet,
					)
				);
				// La noisette passe tout en bas
				if ($ok) {
					$ok = sql_updateq(
						'spip_noisettes',
						array(
							'rang' => $dernier_rang,
						),
						'id_noisette = '.$id_noisette
					);
				}
			}
		}
		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur("id='noisette/$id_noisette'");
	}
}

/**
 * Supprime une composition du noizetier.
 *
 * @param text $page
 *
 * @return text
 */
function noizetier_supprimer_composition($page) {
	$type_page = noizetier_page_type($page);
	$composition = noizetier_page_composition($page);
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	unset($noizetier_compositions[$type_page][$composition]);
	if (count($noizetier_compositions[$type_page]) == 0) {
		unset($noizetier_compositions[$type_page]);
	}
	ecrire_meta('noizetier_compositions', serialize($noizetier_compositions));
}

/**
 * Renvoie le type d'une page.
 *
 * @param text $page
 *
 * @return text
 */
function noizetier_page_type($page) {
	$type_compo = explode('-', $page, 2);

	return $type_compo[0];
}

/**
 * Renvoie la composition d'une page.
 *
 * @param text $page
 *
 * @return text
 */
function noizetier_page_composition($page) {
	$type_compo = explode('-', $page, 2);
	$type_compo = isset($type_compo[1]) ? $type_compo[1] : '';

	return $type_compo;
}

/**
 * Liste les blocs pour lesquels il y a des noisettes a inserer.
 *
 * @staticvar array $liste_blocs
 *
 * @return array
 */
function noizetier_lister_blocs_avec_noisettes() {
	static $liste_blocs = null;

	if (is_null($liste_blocs)) {
		include_spip('base/abstract_sql');
		
		$liste_blocs = array();
		$resultats = sql_allfetsel(
			array('bloc', 'type', 'composition'),
			'spip_noisettes',
			'1',
			array('bloc', 'type', 'composition')
		);
		foreach ($resultats as $res) {
			if ($res['composition']) {
				$liste_blocs[] = $res['bloc'].'/'.$res['type'].'-'.$res['composition'];
			} else {
				$liste_blocs[] = $res['bloc'].'/'.$res['type'];
			}
		}
	}

	return $liste_blocs;
}

/**
 * Liste les blocs pour lesquels il y a des noisettes a inserer POUR UN OBJET
 *
 * @staticvar array $liste_blocs
 *
 * @return array
 */
function noizetier_lister_blocs_avec_noisettes_objet($objet, $id_objet) {
	static $liste_blocs = null;

	if (is_null($liste_blocs[$objet][$id_objet])) {
		include_spip('base/abstract_sql');
		
		$liste_blocs[$objet][$id_objet] = array();
		$resultats = sql_allfetsel(
			array('bloc'),
			'spip_noisettes',
			array(
				'objet = '.sql_quote($objet),
				'id_objet = '.intval($id_objet),
			),
			array('bloc')
		);
		foreach ($resultats as $res) {
			$liste_blocs[$objet][$id_objet][] = $res['bloc'].'/'.$objet;
		}
	}

	return $liste_blocs[$objet][$id_objet];
}

/**
 * Liste d'icones en 24px obtenues en fouillant le theme.
 *
 * @staticvar array $liste_icones
 *
 * @return array
 */
function noizetier_lister_icones() {
	static $liste_icones = null;

	if (is_null($liste_icones)) {
		$match = '.+-24[.](jpg|jpeg|png|gif)$';
		$liste_icones = find_all_in_path('prive/themes/spip/images/', $match);
	}

	return $liste_icones;
}

/**
 * Teste si une page fait partie des compositions du noizetier.
 *
 * @param text $page
 *
 * @return text
 */
function noizetier_test_compo_noizetier($page) {
	$compos = isset($GLOBALS['meta']['noizetier_compositions']) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();
	$type = noizetier_page_type($page);
	$composition = noizetier_page_composition($page);

	return (isset($compos[$type][$composition]) and is_array($compos[$type][$composition])) ? 'on' : '';
}

/**
 * Retourne les elements du contexte uniquement
 * utiles a la noisette demande.
 *
 * @param
 *
 * @return
 **/
function noizetier_choisir_contexte($noisette, $contexte_entrant, $id_noisette) {
	$contexte_noisette = array_flip(noizetier_obtenir_contexte($noisette));

	// On transmet toujours l'id_noisette et les variables se terminant par _$id_noisette (utilisees par exemple par Aveline pour la pagination)
	$contexte_min = array('id_noisette' => $id_noisette);

	if (isset($contexte_noisette['env'])) {
		return array_merge($contexte_entrant, $contexte_min);
	}

	$l = -1 * (strlen($id_noisette) + 1);
	foreach ($contexte_entrant as $variable => $valeur) {
		if (substr($variable, $l) == '_'.$id_noisette) {
			$contexte_min[$variable] = $valeur;
		}
	}

	if (isset($contexte_noisette['aucun'])) {
		return $contexte_min;
	}
	if ($contexte_noisette) {
		return array_merge(array_intersect_key($contexte_entrant, $contexte_noisette), $contexte_min);
	}

	return $contexte_entrant;
}

/**
 * Retourne la liste des contextes donc peut avoir besoin une noisette.
 *
 * @param
 *
 * @return
 **/
function noizetier_obtenir_contexte($noisette) {
	static $noisettes = false;

	// seulement 1 fois par appel, on lit ou calcule tous les contextes
	if ($noisettes === false) {
		// lire le cache des contextes sauves
		lire_fichier_securise(_DIR_CACHE._CACHE_CONTEXTE_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);

		// s'il en mode recalcul, on recalcule tous les contextes des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			$infos = noizetier_lister_noisettes();
			$noisettes = array();
			foreach ($infos as $cle_noisette => $infos) {
				$noisettes[$cle_noisette] = ($infos['contexte'] ? $infos['contexte'] : array());
			}
			ecrire_fichier_securise(_DIR_CACHE._CACHE_CONTEXTE_NOISETTES, serialize($noisettes));
		}
	}

	if (isset($noisettes[$noisette])) {
		return $noisettes[$noisette];
	}

	return array();
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
		lire_fichier_securise(_DIR_CACHE._CACHE_AJAX_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);

		// s'il en mode recalcul, on recalcule tous les contextes des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			$infos = noizetier_lister_noisettes();
			$noisettes = array();
			foreach ($infos as $cle_noisette => $infos) {
				$noisettes[$cle_noisette] = ($infos['ajax'] == 'non') ? false : true;
			}
			ecrire_fichier_securise(_DIR_CACHE._CACHE_AJAX_NOISETTES, serialize($noisettes));
		}
	}

	if (isset($noisettes[$noisette])) {
		return $noisettes[$noisette];
	}

	return true;
}

/**
 * Retourne true ou false pour indiquer si la noisette doit être inclue dynamiquement.
 *
 * @param
 *
 * @return
 **/
function noizetier_inclusion_dynamique($noisette) {
	static $noisettes = false;

	// seulement 1 fois par appel, on lit ou calcule tous les contextes
	if ($noisettes === false) {
		// lire le cache des contextes sauves
		lire_fichier_securise(_DIR_CACHE._CACHE_INCLUSIONS_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);

		// s'il en mode recalcul, on recalcule tous les contextes des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			$infos = noizetier_lister_noisettes();
			$noisettes = array();
			foreach ($infos as $cle_noisette => $infos) {
				$noisettes[$cle_noisette] = ($infos['inclusion'] == 'dynamique') ? true : false;
			}
			ecrire_fichier_securise(_DIR_CACHE._CACHE_INCLUSIONS_NOISETTES, serialize($noisettes));
		}
	}

	if (isset($noisettes[$noisette])) {
		return $noisettes[$noisette];
	}

	return false;
}

/**
 * Retourne le tableau des noisettes et des compositions du noizetier pour les exports.
 *
 * @return
 **/
function noizetier_tableau_export() {
	$data = array();

	// On calcule le tableau des noisettes
	$data['noisettes'] = sql_allfetsel(
		'type, composition, bloc, noisette, parametres, css',
		'spip_noisettes',
		'1',
		'',
		'type, composition, bloc, rang'
	);

	// On remet au propre les parametres
	foreach ($data['noisettes'] as $cle => $noisette) {
		$data['noisettes'][$cle]['parametres'] = unserialize($noisette['parametres']);
	}

	// On recupere les compositions du noizetier
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	if (is_array($noizetier_compositions) and count($noizetier_compositions) > 0) {
		$data['noizetier_compositions'] = $noizetier_compositions;
	}

	$data = pipeline('noizetier_config_export', $data);

	return $data;
}

/**
 * Importe une configuration de noisettes et de compositions.
 *
 * @param text  $type_import
 * @param text  $import_compos
 * @param array $config
 *
 * @return bool
 */
function noizetier_importer_configuration($type_import, $import_compos, $config) {
	if ($type_import != 'remplacer') {
		$type_import = 'fusion';
	}
	if ($import_compos != 'oui') {
		$import_compos = 'non';
	}

	$config = pipeline('noizetier_config_import', $config);

	// On s'occupe deja des noisettes
	$noisettes = $config['noisettes'];
	include_spip('base/abstract_sql');
	if (is_array($noisettes) and count($noisettes) > 0) {
		$noisettes_insert = array();
		$rang = 1;
		$page = '';
		
		if ($type_import == 'remplacer') {
			sql_delete('spip_noisettes', '1');
		}
		
		foreach ($noisettes as $noisette) {
			$type = $noisette['type'];
			$composition = $noisette['composition'];
			if ($type.'-'.$composition != $page) {
				$page = $type.'-'.$composition;
				$rang = 1;
				if ($type_import == 'fusion') {
					$rang = sql_getfetsel('rang', 'spip_noisettes', 'type='.sql_quote($type).' AND composition='.sql_quote($composition), '', 'rang DESC') + 1;
				}
			} else {
				$rang = $rang + 1;
			}
			$noisette['rang'] = $rang;
			$noisette['parametres'] = serialize($noisette['parametres']);
			$noisettes_insert[] = $noisette;
		}
		
		$ok = sql_insertq_multi('spip_noisettes', $noisettes_insert);
	}

	// On s'occupe des compositions du noizetier
	if ($import_compos == 'oui') {
		include_spip('inc/meta');
		$compos_importees = $config['noizetier_compositions'];
		if (is_array($compos_importees) and count($compos_importees) > 0) {
			if ($type_import == 'remplacer') {
				effacer_meta('noizetier_compositions');
			} else {
				$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
			}

			if (!is_array($noizetier_compositions)) {
				$noizetier_compositions = array();
			}

			foreach ($compos_importees as $type => $compos_type) {
				foreach ($compos_type as $composition => $info_compo) {
					$noizetier_compositions[$type][$composition] = $info_compo;
				}
			}

			ecrire_meta('noizetier_compositions', serialize($noizetier_compositions));
			ecrire_metas();
		}
	}

	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur('noizetier-import-config');

	return $ok;
}

/**
 * Retourne le chemin complet d'une icone, vérifie d'abord chemin_image, sinon passe par find_in_path.
 *
 * @param text $icone
 *
 * @return text
 */
function noizetier_chemin_icone($icone){
	if ($i = chemin_image($icone)) {
		return $i;
	} else {
		return find_in_path($icone);
	}
}
