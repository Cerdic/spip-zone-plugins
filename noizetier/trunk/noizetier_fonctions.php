<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_CACHE_AJAX_NOISETTES', _DIR_CACHE . 'noisettes_ajax.php');
define('_CACHE_CONTEXTE_NOISETTES', _DIR_CACHE . 'noisettes_contextes.php');
define('_CACHE_INCLUSIONS_NOISETTES', _DIR_CACHE . 'noisettes_inclusions.php');


// -------------------------------------------------------------------
// ------------------------- API NOISETTES ---------------------------
// -------------------------------------------------------------------

/**
 * Retourne les elements du contexte uniquement
 * utiles a la noisette demande.
 *
 * @param
 *
 * @return
 **/
function noizetier_choisir_contexte($noisette, $contexte_entrant, $id_noisette) {
	$contexte_noisette = array_flip(noizetier_noisette_contexte($noisette));

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


function noizetier_type_noisette_compter($page) {

	// Initialisation des compteurs par bloc
	$nb_types = array(
		'composition' => 0,
		'type'        => 0,
		'commun'      => 0
	);

	// Acquisition du type et de la composition éventuelle.
	$type = noizetier_page_type($page);
	$composition = noizetier_page_composition($page);

	// Les compteurs de types de noisette d'une page sont calculés par une lecture de la table spip_noizetier_noisettes.
	$from = array('spip_noizetier_noisettes');
	$where = array(
		'plugin=' . sql_quote('noizetier'),
		'type=' . sql_quote($type),
		'composition=' . sql_quote($composition));
	$compteur = sql_countsel($from, $where);

	// On cherche maintenant les 3 compteurs possibles :
	if ($composition) {
		// - les types de noisette spécifiques de la composition si la page en est une.
		if ($compteur) {
			$nb_types['composition'] = $compteur;
		}
		$where[2] = 'composition=' . sql_quote('');
		$compteur = sql_countsel($from, $where);
		if ($compteur) {
			$nb_types['type'] = $compteur;
		}
	} else {
		// - les types de noisette spécifiques de la page ou du type de la composition
		if ($compteur) {
			$nb_types['type'] = $compteur;
		}
	}
	// - les types de noisette communs à toutes les pages.
	$where[1] = 'type=' . sql_quote('');
	$compteur = sql_countsel($from, $where);
	if ($compteur) {
		$nb_types['commun'] = $compteur;
	}

	$nb_types['total'] = array_sum($nb_types);

	return $nb_types;
}


/**
 * Ajoute, à un rang donné ou en dernier rang, une noisette à un bloc d'une page ou d'un contenu.
 *
 * @param string       $noisette
 * 		Nom de la noisette à ajouter.
 * @param string|array $page
 *      Identifiant de la page ou de la composition (chaine) ou tableau associatif contenant
 *      le type d'objet (index `objet`) et l'id de l'objet (index `id_objet`).
 * @param string       $bloc
 * 		Nom du bloc où ajouter la noisette.
 * @param int          $rang
 * 		Rang où insérer la noisette. Si l'argument n'est pas fourni ou est égal à 0 on insère la
 *      noisette en fin de bloc.
 *
 * @return int
 * 		Retourne l'identifiant de la nouvelle instance de noisette créée ou 0 en cas d'erreur.
 **/
function noizetier_noisette_ajouter($noisette, $page, $bloc, $rang = 0) {

	// Initialisation de la valeur de sortie.
	$id_noisette = 0;

	if ($noisette) {
		include_spip('inc/ncore_type_noisette');
		$champs = type_noisette_lire(
			'noizetier',
			$noisette,
			'parametres',
			false);

		include_spip('inc/saisies');
		$parametres = saisies_lister_valeurs_defaut($champs);

		// On initialise la description de la noisette à ajouter
		$description = array(
			'plugin'      => 'noizetier',
			'type'        => '',
			'composition' => '',
			'objet'       => '',
			'id_objet'    => 0,
			'bloc'        => $bloc,
			'rang'        => $rang,
			'noisette'    => $noisette,
			'parametres'  => serialize($parametres)
		);

		// On construit le where pour savoir quelles noisettes chercher et on complète
		// la description avec l'identifiant de la page ou de l'objet.
		$where = array('plugin=' . sql_quote('noizetier'), 'bloc=' . sql_quote($bloc));
		if (is_array($page)) {
			$description['objet'] = $page['objet'];
			$description['id_objet'] = $page['id_objet'];
			$where[] = 'objet=' . sql_quote($description['objet']);
			$where[] = 'id_objet=' . intval($description['id_objet']);
		}
		else {
			$description['type'] = noizetier_page_type($page);
			$description['composition'] = noizetier_page_composition($page);
			$where[] = 'type=' . sql_quote($description['type']);
			$where[] = 'composition=' . sql_quote($description['composition']);
		}

		// La noisette est ajoutée soit à un rang donné par l'argument fourni si celui-ci est > 0,
		// soit en fin de liste : dans ce cas, on cherche donc le dernier rang utilisé et on se
		// positionne au rang suivant et on finalise la description de la noisette
		// Le rang d'une noisette commence à 1.
		if (!$description['rang']) {
			$description['rang'] = intval(sql_getfetsel('max(rang)', 'spip_noizetier', $where)) + 1;
		}

		if ($id_noisette = sql_insertq('spip_noizetier', $description)) {
			// On invalide le cache
			include_spip('inc/invalideur');
			suivre_invalideur("id='noisette/$id_noisette'");
		}
	}

	return $id_noisette;
}



/**
 * Réordonne les noisettes d'un bloc d'une page ou d'un objet à partir d'un index donné du tableau.
 * L'ordre est renvoyé pour l'ensemble des noisettes du bloc.
 * Si l'index à partir duquel les noisettes sont réordonnées n'est pas fourni ou est égal à 0
 * la fonction réordonne toutes les noisettes.
 *
 * @param array	$ordre
 * @param int	$index_initial
 *
 * @return bool
 */
function noizetier_noisette_ranger($ordre, $index_initial = 0) {

	if ($index_initial < count($ordre)) {
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}

		// On modifie le rang de chaque noisette en suivant l'ordre du tableau à partir de l'index
		// initial.
		foreach ($ordre as $_cle => $_id_noisette) {
			if ($_cle >= $index_initial) {
				$modification = array('rang' => $_cle + 1);
				$where = array('id_noisette=' . intval($_id_noisette));
				sql_updateq('spip_noizetier', $modification, $where);
			}
		}

		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}
	}

	return true;
}


/**
 * Déplace d'un rang, vers le haut ou vers le bas, une noisette au sein d'un bloc.
 * Le déplacement se fait en mode rouleau.
 *
 * @param int    $id_noisette
 * @param string $sens
 * @param array  $noisette
 *
 * @return boolean
 */
function noizetier_noisette_deplacer($id_noisette, $sens, $noisette) {

	$retour = false;

	if (in_array($sens, array('bas', 'haut')) and intval($id_noisette)) {
		// On récupère l'ordre actuel des noisettes du bloc
		$where = array(
			'plugin=' . sql_quote('noizetier'),
			'type=' . sql_quote($noisette['type']),
			'composition=' . sql_quote($noisette['composition']),
			'objet=' . sql_quote($noisette['objet']),
			'id_objet=' . intval($noisette['id_objet']),
			'bloc=' . sql_quote($noisette['bloc']),
		);
		$ordre = sql_allfetsel('id_noisette', 'spip_noizetier', $where, '', 'rang');
		$ordre = array_map('intval', array_column($ordre, 'id_noisette'));

		// Si il y a plus d'une noisette dans le bloc et que la noisette appartient bien au bloc.
		if (count($ordre) > 1) {
			// Mise à jour de l'ordre en fonction de la demande.
			$index_noisette = array_search($id_noisette, $ordre);
			$index_max = count($ordre) - 1;
			if ($sens == 'bas') {
				if ($index_noisette < $index_max) {
					// La noisette peut être échangée avec la suivante
					$id_destination = $ordre[$index_noisette + 1];
					$ordre[$index_noisette + 1] = $id_noisette;
					$ordre[$index_noisette] = $id_destination;
				} else {
					// La noisette passe en début de liste
					unset($ordre[$index_noisette]);
					array_unshift($ordre, $id_noisette);
				}
			} elseif ($sens == 'haut') {
				if ($index_noisette > 0) {
					// La noisette peut être échangée avec la précédente
					$id_destination = $ordre[$index_noisette - 1];
					$ordre[$index_noisette - 1] = $id_noisette;
					$ordre[$index_noisette] = $id_destination;
				} else {
					// La noisette passe en fin de liste
					array_shift($ordre);
					$ordre[] = $id_noisette;
				}
			}

			// On appelle la fonction de mise à jour du nouvel ordre
			noizetier_noisette_ranger($ordre);

			// On invalide le cache
			include_spip('inc/invalideur');
			suivre_invalideur("id='noisette/$id_noisette'");
		}
	}

	return $retour;
}


// -------------------------------------------------------------------
// --------------------------- API ICONES ----------------------------
// -------------------------------------------------------------------

/**
 * Retourne le chemin complet d'une icone.
 * La fonction vérifie d'abord que l'icone est dans le thème du privé (chemin_image),
 * sinon cherche dans le path SPIP (find_in_path).
 *
 * @package SPIP\NOIZETIER\API\ICONE
 * @api
 * @filtre
 *
 * @param string $icone
 *
 * @return string
 */
 function noizetier_icone_chemin($icone){
	// TODO : faut-il garder cette fonction ou simplifier en utilisant uniquement chemin_image() ?
	if (!$chemin = chemin_image($icone)) {
		$chemin = find_in_path($icone);
	}

	return $chemin;
}


/**
 * Liste d'icones d'une taille donnée en pixels obtenues en fouillant dans les thème
 * spip du privé.
 *
 * @package SPIP\NOIZETIER\API\ICONE
 * @api
 * @filtre
 *
 * @param $taille	int
 * 		Taille en pixels des icones à répertorier.
 *
 * @return array
 * 		Tableau des chemins complets des icones trouvés dans le path SPIP.
 */
function noizetier_icone_repertorier($taille = 24) {
	static $icones = null;

	if (is_null($icones)) {
		$pattern = ".+-${taille}[.](jpg|jpeg|png|gif)$";
		$icones = find_all_in_path('prive/themes/spip/images/', $pattern);
	}

	return $icones;
}


// -------------------------------------------------------------------
// ---------------------------- API BLOCS ----------------------------
// -------------------------------------------------------------------

/**
 * La liste des blocs par defaut d'une page peut etre modifiee via le pipeline noizetier_blocs_defaut.
 *
 * @package SPIP\NOIZETIER\API\BLOC
 * @api
 * @filtre
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
 * Retourne la description complète d'un bloc.
 * La description est disponible dans un fichier YAML.
 *
 * @package SPIP\NOIZETIER\API\BLOC
 * @api
 * @filtre
 *
 * @return array|string
 */
function noizetier_bloc_informer($bloc = '', $information = '') {

	static $blocs = null;
	static $description_bloc = array();

	if ((!$bloc and is_null($blocs))
	or ($bloc and !isset($description_bloc[$bloc]))) {
		// On détermine les blocs pour lesquels on retourne la description
		$identifiants_blocs = $bloc ? array($bloc) : noizetier_bloc_defaut();

		foreach ($identifiants_blocs as $_bloc) {
			$description = array();
			if ($fichier = find_in_path("${_bloc}/bloc.yaml")) {
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
			} elseif (!defined('_DIR_PLUGIN_ZCORE') and in_array($_bloc, array('contenu', 'navigation', 'extra'))) {
				// Avec Zpip v1, les blocs sont toujours les mêmes : on en donne une description standard.
				$description = array(
					'nom' => _T("noizetier:nom_bloc_${_bloc}"),
					'description' => _T("noizetier:description_bloc_${_bloc}"),
					'icon' => "bloc-${_bloc}-24.png",
				);
			} else {
				// Aucune description, on renvoie juste le nom qui coincide avec l'identifiant du bloc
				$description = array('nom' => ucfirst($_bloc));
			}
			// Sauvegarde de la description du bloc pour une consultation ultérieure dans le même hit.
			if ($bloc) {
				$description_bloc[$_bloc] = $description;
			} else {
				$blocs[$_bloc] = $description;
			}
		}
	}

	if ($bloc) {
		if (!$information) {
			return $description_bloc[$bloc];
		} else {
			return isset($description_bloc[$bloc][$information])
				? $description_bloc[$bloc][$information]
				: '';
		}
	} else {
		return $blocs;
	}
}


/**
 * Renvoie le nombre de noisettes de chaque bloc configurables d'une page, d'une composition
 * ou d'un objet.
 *
 * @package SPIP\NOIZETIER\API\BLOC
 * @api
 * @filtre
 *
 * @param string $identifiant
 * 		L'identifiant de la page, de la composition ou de l'objet au format:
 * 		- pour une page : type
 * 		- pour une composition : type-composition
 * 		- pour un objet : type_objet-id_objet
 *
 * @return array
 */
function noizetier_bloc_compter_noisettes($identifiant) {
	static $blocs_compteur = array();

	if (!isset($blocs_compteur[$identifiant])) {
		// Initialisation des compteurs par bloc
		$nb_noisettes = array();

		// Le nombre de noisettes par bloc doit être calculé par une lecture de la table spip_noizetier.
		$from = array('spip_noizetier');
		$select = array('bloc', "count(noisette) as 'noisettes'");
		// -- Contruction du where identifiant précisément la page ou l'objet concerné
		$identifiants = explode('-', $identifiant);
		if (isset($identifiants[1]) and ($id = intval($identifiants[1]))) {
			// L'identifiant est celui d'un objet
			$where = array('objet=' . sql_quote($identifiants[0]), 'id_objet=' . $id);
		} else {
			if (!isset($identifiants[1])) {
				// L'identifiant est celui d'une page
				$identifiants[1] = '';
			}
			$where = array('type=' . sql_quote($identifiants[0]), 'composition=' . sql_quote($identifiants[1]));
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

function noizetier_page_charger($recharger = false) {

	// Retour de la fonction
	$retour = false;

	// Initialiser les blocs par défaut
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

		include_spip('inc/noizetier_phraser');
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
				if ($configuration = phraser_page($page, $options)) {
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

function noizetier_page_lister_blocs($page, $blocs_exclus = array()) {

	// Initialisation des blocs avec la liste des blocs par défaut
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
 * Retourne la configuration de la page, de la composition explicite ou de la composition virtuelle demandée.
 * La configuration est stockée en base de données, certains champs sont recalculés avant d'être fournis.
 *
 * @uses noizetier_bloc_defaut()

 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 * @filtre
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
function noizetier_page_informer($page, $traitement_typo = true) {

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
 * Renvoie le type d'une page à partir de son identifiant.
 *
 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 * @filtre
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
 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 * @filtre
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
 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 * @filtre
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
 * @package SPIP\NOIZETIER\API\OBJET
 * @api
 * @filtre
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
 * @filtre
 *
 * @return string
 * 		Le répertoire des pages sous la forme dossier/.
 */
function noizetier_page_repertoire() {

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


// --------------------------------------------------------------------
// ---------------------------- API OBJETS ----------------------------
// --------------------------------------------------------------------

/**
 * Lister les contenus ayant des noisettes spécifiquement configurées pour leur page.
 *
 * @package SPIP\NOIZETIER\API\OBJET
 * @api
 * @filtre
 *
 * @param string $objet
 * 		Type d'objet ou chaine vide.
 * @param string $id_objet
 * 		Id de l'objet ou 0.
 *
 * @return array|string
 * 		Si le type et l'id du contenu sont fournis, on renvoie la description de la page de ce contenu.
 * 		Sinon, on renvoie le tableau des descriptions des pages de tous les contenus indexés par [type_objet][id_objet].
 */
function noizetier_objet_informer($type_objet = '', $id_objet = 0, $information = '') {

	static $objets = null;
	static $description_objet = array();

	if ((!$type_objet and !$id_objet and is_null($objets))
	or ($type_objet and $id_objet and !isset($description_objet[$type_objet][$id_objet]))) {
		// On récupère le ou les objets ayant des noisettes dans la table spip_noizetier.
		$from = array('spip_noizetier');
		$select = array('objet', 'id_objet', "count(noisette) as 'noisettes'");
		$where = array('id_objet>0');
		if ($type_objet and $id_objet) {
			$where = array('objet=' . sql_quote($type_objet), 'id_objet=' . intval($id_objet));
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
					$description['logo'] = noizetier_icone_chemin("{$_objet['objet']}.png");
				}
				$description['noisettes'] = $_objet['noisettes'];

				// On rajoute les blocs du type de page dont l'objet est une instance et on sauvegarde
				// la description complète.
				if ($type_objet and $id_objet) {
					$description['blocs'] = noizetier_page_lister_blocs($type_objet);
					$description_objet[$type_objet][$id_objet] = $description;
				} else {
					$description['blocs'] = noizetier_page_lister_blocs($_objet['objet']);
					$objets[$_objet['objet']][$_objet['id_objet']] = $description;
				}
			}
		}
	}

	if ($type_objet and $id_objet) {
		if (!$information) {
			return isset($description_objet[$type_objet][$id_objet])
				? $description_objet[$type_objet][$id_objet]
				: array();
		} else {
			return isset($description_objet[$type_objet][$id_objet][$information])
				? $description_objet[$type_objet][$id_objet][$information]
				: '';
		}
	} else {
		// Filtrage des objets répertoriés:
		// - de façon systématique, on ne retient que les objets dont le type est activé dans la configuration du plugin.
		$objets_repertories = $objets;
		foreach ($objets_repertories as $_type_objet => $_objets) {
			if (!noizetier_objet_type_active($_type_objet)) {
				unset($objets_repertories[$_type_objet]);
			}
		}
		return $objets_repertories;
	}
}


/**
 * Renvoie la liste des types d'objet ne pouvant pas être personnaliser car ne possédant pas de page
 * détectable par le noiZetier.
 *
 * @package SPIP\NOIZETIER\API\OBJET
 * @api
 * @filtre
 *
 * @return array|null
 */
function noizetier_objet_lister_exclusions() {

	static $exclusions = null;

	if (is_null($exclusions)) {
		$exclusions = array();
		include_spip('base/objets');

		// On récupère les tables d'objets sous la forme spip_xxxx.
		$tables = lister_tables_objets_sql();
		$tables = array_keys($tables);

		// On récupère la liste des pages disponibles et on transforme le type d'objet en table SQL.
		$where = array('composition=' . sql_quote(''), 'est_page_objet=' . sql_quote('oui'));
		$pages = sql_allfetsel('type', 'spip_noizetier_pages', $where);
		$pages = array_map('reset', $pages);
		$pages = array_map('table_objet_sql', $pages);

		// On exclut donc les tables qui ne sont pas dans la liste issues des pages.
		$exclusions = array_diff($tables, $pages);
	}

	return $exclusions;
}


/**
 * Détermine si un type d'objet est activé par configuration du noiZetier.
 * Si oui, ses objets peuvent recevoir une configuration de noisettes.
 *
 * @package SPIP\NOIZETIER\API\OBJET
 * @api
 * @filtre
 *
 * @param string $type_objet
 * 		Type d'objet SPIP comme article, rubrique...
 *
 * @return boolean
 * 		True si le type d'objet est activé, false sinon.
 */
function noizetier_objet_type_active($type_objet) {

	static $tables_actives = null;
	$est_active = false;

	// Si la liste des tables d'objet actives est null on la calcule une seule fois
	if ($tables_actives === null) {
		include_spip('inc/config');
		$tables_actives = array_map('objet_type', lire_config('noizetier/objets_noisettes', array()));
	}

	// Si la liste est non vide, on détermine si le type d'objet est bien activé.
	if ($tables_actives and in_array($type_objet, $tables_actives)) {
		$est_active = true;
	}

	return $est_active;
}

// --------------------------------------------------------------------
// ------------------------- API CONFIGURATION ------------------------
// --------------------------------------------------------------------

/**
 * Détermine si la configuration d'une page ou d'une noisette contenue dans son
 * fichier XML ou YAML a été modifié ou pas.
 *
 * @package SPIP\NOIZETIER\API\PAGE
 * @api
 * @filtre
 *
 * @param string $entite
 * 		`page` pour désigner une page ou `noisette` pour une noisette.
 * @param string $identifiant
 * 		Identifiant de la page ou de la noisette.
 *
 * @return boolean
 * 		`true` si la configuration a été modifiée, `false` sinon.
 */
 // TODO : a voir si cette fonction n'est pas utilisée pour les noisettes on la renommera en noizetier_page_modifiee()
function noizetier_configuration_est_modifiee($entite, $identifiant) {

	$est_modifiee = true;

	// Détermination du répertoire par défaut
	$repertoire = ($entite == 'page') ? noizetier_page_repertoire() : 'noisettes/';

	// Récupération du md5 enregistré en base de données
	$from = "spip_noizetier_${entite}s";
	$where = array($entite . '=' . sql_quote($identifiant));
	$md5_enregistre = sql_getfetsel('signature', $from, $where);

	if ($md5_enregistre) {
		// On recherche d'abord le fichier YAML qui est commun aux 2 entités et sinon le fichier
		// XML si c'est une page.
		if (($fichier = find_in_path("${repertoire}${identifiant}.yaml"))
		or (($entite == 'page') and ($fichier = find_in_path("${repertoire}${identifiant}.xml")))) {
			$md5 = md5_file($fichier);
			if ($md5 == $md5_enregistre) {
				$est_modifiee = false;
			}
		}
	}

	return $est_modifiee;
}

include_spip('public/noizetier_balises');
include_spip('noizetier_vieilles_fonctions');
