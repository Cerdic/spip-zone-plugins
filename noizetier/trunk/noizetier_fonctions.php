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
 * @return array|null
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
 * Supprime une composition du noizetier.
 *
 * @param string $page
 *
 * @return void
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

/**
 * Retourne le chemin complet d'une icone, vérifie d'abord chemin_image, sinon passe par find_in_path.
 *
 * @param string $icone
 *
 * @return string
 */
function noizetier_chemin_icone($icone){
	if ($i = chemin_image($icone)) {
		return $i;
	} else {
		return find_in_path($icone);
	}
}


// -------------------------------------------------------------------
// ---------------------------- API BLOCS ----------------------------
// -------------------------------------------------------------------

/**
 * La liste des blocs par defaut d'une page peut etre modifiee via le pipeline noizetier_blocs_defaut.
 *
 *
 * @return array
 */
function noizetier_bloc_defaut() {
	static $blocs_defaut = null;

	if (is_null($blocs_defaut)) {
		if (defined('_DIR_PLUGIN_ZCORE') and !empty($GLOBALS['z_blocs'])) {
			$blocs_defaut = $GLOBALS['z_blocs'];
		}
		else {
			$blocs_defaut = array('contenu', 'navigation', 'extra');
		}

		// Changer la liste au travers du pipeline. A priori le but est de supprimer
		// certains blocs comme head ou head_js si on ne veut pas les configurer.
		$blocs_defaut = pipeline('noizetier_blocs_defaut', $blocs_defaut);
	}

	return $blocs_defaut;
}


/**
 * Retourne la liste des descriptions des blocs par defaut du squelette.
 *
 *
 *
 * @return array
 */
function noizetier_bloc_repertorier() {
	static $blocs = null;

	if (is_null($blocs)) {
		$options['blocs_defaut'] = noizetier_bloc_defaut();
		foreach ($options['blocs_defaut'] as $_bloc) {
			if ($configuration = noizetier_bloc_informer($_bloc, '', $options)) {
				$blocs[$_bloc] = $configuration;
			}
		}
	}

	return $blocs;
}


/**
 * Retourne la description complète d'un bloc.
 * La description est disponible dans un fichier YAML.
 *
 *
 * @return array|string
 */
function noizetier_bloc_informer($bloc, $information = '', $options = array()) {
	static $description_bloc = array();

	if (!isset($description_bloc[$bloc])) {
		if ($fichier = find_in_path("${bloc}/bloc.yaml")) {
			// Il y a un fichier YAML de configuration dans le répertoire du bloc
			include_spip('inc/yaml');
			if ($description = yaml_charger_inclusions(yaml_decode_file($fichier))) {
				$description['nom'] = isset($description['nom']) ? _T_ou_typo($description['nom']) : ucfirst($bloc);
				if (isset($description['description'])) {
					$description['description'] = _T_ou_typo($description['description']);
				}
				if (!isset($description['icon'])) {
					$description['icon'] = 'bloc-24.png';
				}
			}
		} elseif (!defined('_DIR_PLUGIN_ZCORE') and in_array($bloc, array('contenu', 'navigation', 'extra'))) {
			// Avec Zpip v1, les blocs sont toujours les mêmes : on en donne une description standard.
			$description = array(
				'nom' => _T("noizetier:nom_bloc_${bloc}"),
				'description' => _T("noizetier:description_bloc_${bloc}"),
				'icon' => "bloc-${bloc}-24.png",
			);
		} else {
			// Aucune description, on renvoie juste le nom qui coincide avec l'identifiant du bloc
			$description = array('nom' => ucfirst($bloc));
		}
		// Sauvegarde de la description du bloc pour une consultation ultérieure dans le même hit.
		$description_bloc[$bloc] = $description;
	}

	if (!$information) {
		return $description_bloc[$bloc];
	} elseif (isset($description_bloc[$bloc][$information])) {
		return $description_bloc[$bloc][$information];
	} else {
		return '';
	}
}


/**
 * Renvoie le nombre de noisettes de chaque bloc configurables d'une page, d'une composition
 * ou d'un objet.
 *
 * @param string $identifiant
 * 		L'identifiant de la page, de la composition ou de l'objet au format:
 * 		- pour une page : type
 * 		- pour une composition : type-composition
 * 		- pour un objet : type_objet-id
 *
 * @return array
 */
function noizetier_bloc_compter_noisettes($identifiant) {
	static $blocs_compteur = array();

	if (!isset($blocs_compteur[$identifiant])) {
		// Initialisation des compteurs par bloc
		$nb_noisettes = array();

		// Le nombre de noisettes par bloc doit être calculé par une lecture de la table spip_noisettes.
		$from = array('spip_noisettes');
		$select = array('bloc', "count(noisette) as 'noisettes'");
		// -- Contruction du where identifiant préciément la page ou l'objet concerné
		$identifiants = explode('-', $identifiant);
		if (!isset($identifiants[1])) {
			// L'identifiant est celui d'une page
			$where = array('type=' . sql_quote($identifiant));
		} elseif (intval($identifiants[1])) {
			// L'identifiant est celui d'un objet
			$where = array('objet=' . sql_quote($identifiants[0]), 'id_objet=' . intval($identifiants[1]));
		} else {
			$where = array('type=' . sql_quote($identifiant[0]), 'composition=' . sql_quote($identifiant[1]));
		}
		$group = array('bloc');
		$compteurs = sql_allfetsel($select, $from, $where, $group);
		if ($compteurs) {
			// On formate le tableau [bloc] = nb noisettes
			foreach ($compteurs as $_compteur) {
				$nb_noisettes[$_compteur['bloc']] = $_compteur['noisettes'];
			}
		}
		$blocs_compteur[$identifiant] = $nb_noisettes;
	}

	return (isset($blocs_compteur[$identifiant]) ? $blocs_compteur[$identifiant] : array());
}


// -------------------------------------------------------------------
// ---------------------------- API PAGES ----------------------------
// -------------------------------------------------------------------

/**
 * Retourne la liste des pages, des compositions explicites et des compositions virtuelles.
 * Chaque page est fournie avec l'ensemble de sa configuration.
 *
 * @uses noizetier_informer_page()
 * @api
 * @filtre
 *
 * @return array|null
 * 		Tableau des pages, l'index est l'identifiant de la page.
 */
function noizetier_page_repertorier() {
	static $pages = null;

	if (is_null($pages)) {
		// Choisir le bon répertoire des pages
		$options['repertoire_pages'] = noizetier_obtenir_dossier_pages();

		if ($options['repertoire_pages']) {
			// Initialiser les blocs par défaut
			$options['blocs_defaut'] = noizetier_bloc_defaut();

			// On recherche en premier lieu les pages et les compositions explicites
			if ($fichiers = find_all_in_path($options['repertoire_pages'], '.+[.]html$')) {
				foreach ($fichiers as $squelette => $chemin) {
					$page = basename($squelette, '.html');
					$dossier = dirname($chemin);
					// Exclure certaines pages :
					// -- celles du privé situes dans prive/contenu
					// -- page liée au plugin Zpip en v1
					// -- z_apl liée aux plugins Zpip v1 et Zcore
					// -- TODO : les compositions explicites si le plugin Compositions n'est pas activé
					if ((substr($dossier, -13) != 'prive/contenu')
					and (($page != 'page') or !defined('_DIR_PLUGIN_Z'))
					and (($page != 'z_apl') or (!defined('_DIR_PLUGIN_Z') and !defined('_DIR_PLUGIN_ZCORE')))) {
						if ($configuration = noizetier_page_informer($page, '', $options)) {
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

			// On ajoute les compositions virtuelles qui ne sont définies que dans une meta propre au noiZetier.
			if (defined('_DIR_PLUGIN_COMPOSITIONS')) {
				include_spip('inc/config');
				$options['compositions'] = lire_config('noizetier_compositions', array());
				if ($options['compositions']) {
					foreach ($options['compositions'] as $_composition => $_configuration) {
						if ($configuration = noizetier_page_informer($_composition, '', $options)) {
							$pages[$_composition] = $configuration;
						}
					}
				}
			}

			// Appel du pipeline noizetier_lister_pages pour éventuellement compléter ou modifier la liste
			$pages = pipeline('noizetier_lister_pages', $pages);
		}
	}

	return $pages;
}


/**
 * Retourne la configuration de la page, de la composition explicite ou de la composition virtuelle demandée.
 *
 * @uses noizetier_obtenir_dossier_pages()
 * @uses noizetier_bloc_defaut()
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
function noizetier_page_informer($page, $information = '', $options =array()) {

	static $description_page = array();

	if (!isset($description_page[$page])) {
		// Initialisation de la description
		$description = array();

		// Choisir le bon répertoire des pages
		if (empty($options['repertoire_pages'])) {
			$options['repertoire_pages'] = noizetier_obtenir_dossier_pages();
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
		$est_virtuelle = false;

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
				$description['blocs'] = $description['blocs'] ? array_intersect($options['blocs_defaut'], $blocs) : $options['blocs_defaut'];

				// Liste des plugins nécessaires pour utiliser la page
				if (spip_xml_match_nodes(',^necessite,', $xml, $necessites)) {
					$description['necessite'] = array();
					foreach (array_keys($necessites) as $_necessite) {
						list(, $attributs) = spip_xml_decompose_tag($_necessite);
						$description['necessite'][] = $attributs['id'];
					}
				}
			}
		} elseif (defined('_NOIZETIER_LISTER_PAGES_SANS_XML') ? _NOIZETIER_LISTER_PAGES_SANS_XML : false) {
			// 1c- il est autorisé de ne pas avoir de fichier XML de configuration
			$description['nom'] = $page;
			$description['icon'] = 'img/ic_page.png';
			$description['blocs'] = $options['blocs_defaut'];
		} elseif (defined('_DIR_PLUGIN_COMPOSITIONS')) {
			// 2- la page est une composition du noizetier
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
				$est_virtuelle = true;
			}
		}

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Renuméroter les blocs à cause des exclusions
			sort($description['blocs']);
			// Stockage des identifiants séparément
			$description['type'] = $identifiants[0];
			$description['composition'] = $identifiants[1];
			// Identifie si la page est celle d'un objet SPIP (toujours vrai pour une composition)
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


/**
 * Renvoie le type d'une page à partir de son identifiant.
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

// --------------------------------------------------------------------
// ---------------------------- API OBJETS ----------------------------
// --------------------------------------------------------------------

/**
 * Lister les objets ayant des noisettes spéciquement configurées pour leur page.
 *
 * @param string $objet
 * 		Type d'objet ou chaine vide.
 * @param string $id_objet
 * 		Id de l'objet ou 0.
 *
 * @return array|string
 * 		Si le type et l'id de l'objet sont fournis, on renvoie la description de la page de cet objet.
 * 		Sinon, on renvoie le tableau de toutes les objets sous la forme [type_objet][id_objet].
 */
function noizetier_objet_informer($objet = '', $id_objet = 0, $information = '') {
	static $objets = null;
	static $description_objet = array();

	if ((!$objet and !$id_objet and is_null($objets))
	or ($objet and $id_objet and !isset($description_objet[$objet][$id_objet]))) {
		// On récupère le ou les objets ayant des noisettes dans la table spip_noisettes.
		$from = array('spip_noisettes');
		$select = array('objet', 'id_objet', "count(noisette) as 'noisettes'");
		$where = array('id_objet>0');
		if ($objet and $id_objet) {
			$where = array('objet=' . sql_quote($objet), 'id_objet=' . intval($id_objet));
		}
		$group = array('objet', 'id_objet');
		$objets_configures = sql_allfetsel($select, $from, $where, $group);
		if ($objets_configures) {
			include_spip('inc/quete');
			include_spip('base/objets');
			foreach ($objets_configures as $_objet) {
				$description = array();
				// On calcule le titre de l'objet à partir de la fonction idoine
				$description['titre'] = generer_info_entite($_objet['id_objet'], $_objet['objet'], 'titre');
				// On recherche le logo de l'objet si il existe sinon on stocke le logo du type d'objet
				// (le chemin complet)
				$description['logo'] = '';
				if ($_objet['objet'] != 'document') {
					$logo_infos = quete_logo(id_table_objet($_objet['objet']), 'on', $_objet['id_objet'], 0, false);
					$description['logo'] = isset($logo_infos['src']) ? $logo_infos['src'] : '';
				}
				if (!$description['logo']) {
					$description['logo'] = noizetier_chemin_icone("{$_objet['objet']}.png");
				}
				$description['noisettes'] = $_objet['noisettes'];

				// On rajoute les blocs du type de page dont l'objet est une instance et on sauvegarde
				// la description complète.
				if ($objet and $id_objet) {
					$description['blocs'] = noizetier_page_informer($objet, 'blocs');
					$description_objet[$objet][$id_objet] = $description;
				} else {
					$description['blocs'] = noizetier_page_informer($_objet['objet'], 'blocs');
					$objets[$_objet['objet']][$_objet['id_objet']] = $description;
				}
			}
		}
	}

	if ($objet and $id_objet) {
		if (!$information) {
			return isset($description_objet[$objet][$id_objet])
				? $description_objet[$objet][$id_objet]
				: array();
		} else {
			return isset($description_objet[$objet][$id_objet][$information])
				? $description_objet[$objet][$id_objet][$information]
				: '';
		}
	} else {
		return $objets;
	}
}

include_spip('public/noizetier_balises');
