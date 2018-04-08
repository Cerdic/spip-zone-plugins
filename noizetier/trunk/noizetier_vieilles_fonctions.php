<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -------------------------------------------------------------------
// ------------------------- API NOISETTES ---------------------------
// -------------------------------------------------------------------


function noizetier_noisette_ajax($noisette) {
	static $est_ajax = array();

	if (!isset($est_ajax[$noisette])) {
		// On détermine l'existence et le contenu du cache.
		if (lire_fichier_securise(_CACHE_AJAX_NOISETTES, $contenu)) {
			$est_ajax = unserialize($contenu);
		}

		// On doit recalculer le cache.
		if (!$est_ajax
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On détermine la valeur par défaut de l'ajax des noisettes
			include_spip('inc/config');
			$defaut_ajax = lire_config('noizetier/ajax_noisette') == 'on' ? true : false;

			// On repertorie toutes les noisettes disponibles et on compare
			// avec la valeur par défaut configurée pour le noiZetier.
			if ($noisettes = sql_allfetsel('type_noisette, ajax', 'spip_types_noisettes')) {
				$noisettes = array_column($noisettes, 'ajax', 'type_noisette');
				foreach ($noisettes as $_noisette => $_ajax) {
					$est_ajax[$_noisette] = ($_ajax == 'defaut')
						? $defaut_ajax
						: ($_ajax == 'non' ? false : true);
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant la valeur ajax par défaut afin de toujours renvoyer
			// quelque chose.
			if (!isset($est_ajax[$noisette])) {
				$est_ajax[$noisette] = $defaut_ajax;
			}

			// On met à jour in fine le cache
			if ($est_ajax) {
				ecrire_fichier_securise(_CACHE_AJAX_NOISETTES, serialize($est_ajax));
			}
		}
	}

	return $est_ajax[$noisette];
}

function noizetier_noisette_dynamique($noisette) {
	static $est_dynamique = array();

	if (!isset($est_dynamique[$noisette])) {
		// On détermine l'existence et le contenu du cache.
		if (lire_fichier_securise(_CACHE_INCLUSIONS_NOISETTES, $contenu)) {
			$est_dynamique = unserialize($contenu);
		}

		// On doit recalculer le cache.
		if (!$est_dynamique
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On repertorie toutes les types de noisettes disponibles et on compare la valeur
			// du champ inclusion.
			if ($noisettes = sql_allfetsel('type_noisette, inclusion', 'spip_types_noisettes')) {
				$noisettes = array_column($noisettes, 'inclusion', 'type_noisette');
				foreach ($noisettes as $_noisette => $_inclusion) {
					$est_dynamique[$_noisette] = ($_inclusion == 'dynamique') ? true : false;
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant l'inclusion dynamique à false.
			if (!isset($est_dynamique[$noisette])) {
				$est_dynamique[$noisette] = false;
			}

			// On met à jour in fine le cache
			if ($est_dynamique) {
				ecrire_fichier_securise(_CACHE_INCLUSIONS_NOISETTES, serialize($est_dynamique));
			}
		}
	}

	return $est_dynamique[$noisette];
}


function noizetier_noisette_contexte($noisette) {
	static $contexte = array();

	if (!isset($contexte[$noisette])) {
		// On détermine l'existence et le contenu du cache.
		if (lire_fichier_securise(_CACHE_CONTEXTE_NOISETTES, $contenu)) {
			$contexte = unserialize($contenu);
		}

		// On doit recalculer le cache.
		if (!$contexte
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On repertorie toutes les types de noisettes disponibles et on compare la valeur
			// du champ inclusion.
			if ($noisettes = sql_allfetsel('type_noisette, contexte', 'spip_types_noisettes')) {
				$noisettes = array_column($noisettes, 'contexte', 'type_noisette');
				$contexte = array_map('unserialize', $noisettes);
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant le contexte à tableau vide.
			if (!isset($contexte[$noisette])) {
				$contexte[$noisette] = array();
			}

			// On met à jour in fine le cache
			if ($contexte) {
				ecrire_fichier_securise(_CACHE_CONTEXTE_NOISETTES, serialize($contexte));
			}
		}
	}

	return $contexte[$noisette];
}



// API à traiter
// -------------



/**
 * Retourne le tableau des noisettes et des compositions du noizetier pour les exports.
 *
 * @return
 **/
function noizetier_tableau_export() {
	$data = array();

	// On calcule le tableau des noisettes
	$data['noisettes'] = sql_allfetsel(
		'type, composition, bloc, type_noisette, parametres, css',
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
					$rang = sql_getfetsel('rang_noisette', 'spip_noisettes', 'type='.sql_quote($type).' AND composition='.sql_quote($composition), '', 'rang DESC') + 1;
				}
			} else {
				$rang = $rang + 1;
			}
			$noisette['rang_noisette'] = $rang;
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
 // ENCORE UTILISEE DANS LA BALISE DU MEME NOM
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
// ENCORE UTILISEE DANS LA BALISE DU MEME NOM
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


// -------------------------------------------------------------------
// --------------------------- API ICONES ----------------------------
// -------------------------------------------------------------------


// -------------------------------------------------------------------
// ---------------------------- API BLOCS ----------------------------
// -------------------------------------------------------------------


// -------------------------------------------------------------------
// ---------------------------- API PAGES ----------------------------
// -------------------------------------------------------------------


// --------------------------------------------------------------------
// ---------------------------- API OBJETS ----------------------------
// --------------------------------------------------------------------

