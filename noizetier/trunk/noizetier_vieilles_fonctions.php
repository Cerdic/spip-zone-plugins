<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -------------------------------------------------------------------
// ------------------------- API NOISETTES ---------------------------
// -------------------------------------------------------------------


// API à traiter
// -------------
/**
 * Charger les informations des contexte pour une noisette.
 *
 * @param string $noisette
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
 * @param string  $page
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
 * @param int $id_noisette
 * @param string $sens
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
		lire_fichier_securise(_CACHE_CONTEXTE_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);

		// s'il en mode recalcul, on recalcule tous les contextes des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			$infos = noizetier_lister_noisettes();
			$noisettes = array();
			foreach ($infos as $cle_noisette => $infos) {
				$noisettes[$cle_noisette] = ($infos['contexte'] ? $infos['contexte'] : array());
			}
			ecrire_fichier_securise(_CACHE_CONTEXTE_NOISETTES, serialize($noisettes));
		}
	}

	if (isset($noisettes[$noisette])) {
		return $noisettes[$noisette];
	}

	return array();
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
		lire_fichier_securise(_CACHE_INCLUSIONS_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);

		// s'il en mode recalcul, on recalcule tous les contextes des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			$infos = noizetier_lister_noisettes();
			$noisettes = array();
			foreach ($infos as $cle_noisette => $infos) {
				$noisettes[$cle_noisette] = ($infos['inclusion'] == 'dynamique') ? true : false;
			}
			ecrire_fichier_securise(_CACHE_INCLUSIONS_NOISETTES, serialize($noisettes));
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
 * @param string  $type_import
 * @param string  $import_compos
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

// API en cours de traitement
// --------------------------
/**
 * @param array $filtres
 *
 * @return array|mixed|null
 */
function noizetier_noisette_repertorier($filtres = array()) {
	static $noisettes = null;

	if (is_null($noisettes)) {
		// On détermine l'existence et le contenu du cache.
		$cache_noisettes = array();
		if (lire_fichier_securise(_CACHE_DESCRIPTIONS_NOISETTES, $contenu)) {
			$cache_noisettes = unserialize($contenu);
		}

		if (!$cache_noisettes
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On doit recalculer le cache
			if ($fichiers = find_all_in_path('noisettes/', '.+[.]yaml$')) {
				foreach ($fichiers as $_fichier => $_chemin) {
					$noisette = basename($_fichier, '.yaml');
					$options = array('recharger' => true, 'yaml' => $_chemin);
					if ($configuration = noisette_informer($noisette, '', $options)) {
						// On n'inclue la noisette que si les plugins qu'elle nécessite explicitement dans son
						// fichier de configuration sont bien tous activés.
						// Rappel : si une noisette est incluse dans un plugin non actif elle ne sera pas détectée
						//          lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
						$noisette_a_garder = true;
						if (isset($configuration['necessite'])) {
							foreach ($configuration['necessite'] as $plugin) {
								if (!defined('_DIR_PLUGIN_'.strtoupper($plugin))) {
									$noisette_a_garder = false;
									break;
								}
							}
						}

						if ($noisette_a_garder) {
							$noisettes[$noisette] = $configuration;
						}
					}
				}
				// Mise à jour du cache des descriptions
				if ($noisettes) {
					ecrire_fichier_securise(_CACHE_DESCRIPTIONS_NOISETTES, serialize($noisettes));
				}
			}
		} else {
			// On renvoie le cache des descriptions.
			$noisettes = $cache_noisettes;
		}
	}

	// Application des filtres éventuellement demandés en argument de la fonction
	$noisettes_repertoriees = $noisettes;
	if ($filtres) {
		foreach ($noisettes_repertoriees as $_noisette => $_configuration) {
			foreach ($filtres as $_critere => $_valeur) {
				if (isset($_configuration[$_critere]) and ($_configuration[$_critere] != $_valeur)) {
					unset($noisettes_repertoriees[$_noisette]);
					break;
				}
			}
		}
	}

	return $noisettes_repertoriees;
}



/**
 * @param        $noisette
 * @param string $information
 * @param array  $options
 *
 * @return mixed|string
 */
function noisette_informer($noisette, $information = '', $options = array()) {

	static $description_noisette = array();

	if (!isset($description_noisette[$noisette])) {
		// On essaye d'abord de récupérer la description dans le cache sauf si l'option recharger est activée
		if (empty($options['recharger'])) {
			if (lire_fichier_securise(_CACHE_DESCRIPTIONS_NOISETTES, $contenu)) {
				$cache_noisettes = unserialize($contenu);
				if (isset($cache_noisettes[$noisette])) {
					$description_noisette[$noisette] = $cache_noisettes[$noisette];
				} else {
					// On a pas trouvé la noisette dans le cache, on essaye de la charger directement
					$options['recharger'] = true;
				}
			}
		}

		if (!empty($options['recharger'])) {
			// Initialisation de la description et d'une description par défaut
			$description = array();
			$defaut = array(
				'nom' => $noisette,
				'description' => '',
				'icon' => 'noisette-24.png',
				'parametres' => array(),
				'necessite' => array(),
				'contexte' => array(),
				'ajax' => 'defaut',
				'inclusion' => 'statique',
			);

			// Le fichier YAML de la noisette est soit passé en argument soit à déterminer à partir de
			// l'identifiant de la noisette.
			$fichier = isset($options['yaml']) ? $options['yaml'] : find_in_path("noisettes/${noisette}.yaml");
			if ($fichier) {
				include_spip('inc/yaml');
				if ($description = yaml_charger_inclusions(yaml_decode_file($fichier))) {
					if (isset($description['nom'])) {
						$description['nom'] = _T_ou_typo($description['nom']);
					}
					if (isset($description['description'])) {
						$description['description'] = _T_ou_typo($description['description']);
					}
					if (!empty($description['necessite']) and is_string($description['necessite'])) {
						$description['necessite'] = array($description['necessite']);
					}
					if (!empty($description['contexte']) and is_string($description['contexte'])) {
						$description['contexte'] = array($description['contexte']);
					}

					// Merge pour obtenir une description complète
					$description = array_merge($defaut, $description);
				}
			}

			// Sauvegarde de la description de la noisette pour une consultation ultérieure dans le même hit.
			if ($description) {
				// Ajout du type de noisette
				$identifiants = explode('-', $noisette, 2);
				$description['type'] = isset($identifiants[1]) ? $identifiants[0] : '';
				$description_noisette[$noisette] = $description;
			} else {
				$description_noisette[$noisette] = array();
			}
		}
	}

	if (!$information) {
		return $description_noisette[$noisette];
	} elseif (isset($description_noisette[$noisette][$information])) {
		return $description_noisette[$noisette][$information];
	} else {
		return '';
	}
}

// API traitées
// ------------
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

// API traitées
// ------------
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

