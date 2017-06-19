<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_CACHE_AJAX_NOISETTES', _DIR_CACHE . 'noisettes_ajax.php');
define('_CACHE_CONTEXTE_NOISETTES', _DIR_CACHE . 'noisettes_contextes.php');
define('_CACHE_DESCRIPTIONS_NOISETTES', _DIR_CACHE . 'noisettes_descriptions.php');
define('_CACHE_INCLUSIONS_NOISETTES', _DIR_CACHE . 'noisettes_inclusions.php');


// -------------------------------------------------------------------
// ------------------------- API NOISETTES ---------------------------
// -------------------------------------------------------------------

function noizetier_noisette_charger($recharger = false) {

	// Retour de la fonction
	$retour = false;

	// Initialiser le contexte de rechargement
	// TODO : en attente de voir si on rajoute un var_mode ou autre
	$forcer_chargement = $recharger;

	// Initaliser la table et le where des noisettes.
	$from ='spip_noizetier_noisettes';

	// On recherche les noisettes directement par leur fichier YAML de configuration car il est
	// obligatoire contrairement à une page.
	if ($fichiers = find_all_in_path('noisettes/', '.+[.]yaml$')) {
		$noisettes_nouvelles = $noisettes_modifiees = $noisettes_obsoletes = array();
		// Récupération des signatures md5 des noisettes déjà enregistrées.
		// Si on force le rechargement il est inutile de gérer les signatures et les noisettes modifiées ou obsolètes.
		$signatures = array();
		if (!$forcer_chargement) {
			$select = array('noisette', 'signature');
			if ($signatures = sql_allfetsel($select, $from)) {
				$signatures = array_column($signatures, 'signature', 'page');
			}
			// On initialise la liste des noisettes à supprimer avec l'ensemble des noisettes en base de données.
			$noisettes_obsoletes = $signatures ? array_keys($signatures) : array();
		}

		foreach ($fichiers as $_squelette => $_chemin) {
			$noisette = basename($_squelette, '.yaml');
			// On passe le md5 de la page si il existe sinon la chaine vide. Cela permet de déterminer
			// si on doit ajouter la page ou la mettre à jour.
			// Si le md5 est le même et qu'il n'est donc pas utile de recharger la page, la configuration
			// retournée est vide.
			$options['md5'] = isset($signatures[$noisette]) ? $signatures[$noisette] : '';
			$options['recharger'] = $forcer_chargement;
			$options['yaml'] = $_chemin;
			if ($configuration = noizetier_noisette_phraser($noisette, $options)) {
				if (empty($configuration['identique'])) {
					// La noisette a été chargée (nouvelle) ou rechargée (modifiée).
					// Néanmoins, on n'inclue cette noisette que si les plugins qu'elle nécessite explicitement dans son
					// fichier de configuration sont bien tous activés.
					// Rappel: si une noisette est incluse dans un plugin non actif elle ne sera pas détectée
					//         lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
					//         Ce n'est pas ce cas qui est traité ici.
					$noisette_a_garder = true;
					$necessite = unserialize($configuration['necessite']);
					if (!empty($necessite)) {
						foreach ($necessite as $plugin) {
							if (!defined('_DIR_PLUGIN_'.strtoupper($plugin))) {
								$noisette_a_garder = false;
								break;
							}
						}
					}

					// Si la noisette est à garder on détermine si elle est nouvelle ou modifiée.
					// En mode rechargement forcé toute noisette est considérée comme nouvelle.
					// Sinon, la noisette doit être retirée de la base car un plugin qu'elle nécessite a été désactivée:
					// => il suffit pour cela de la laisser dans la liste des noisettes obsolètes.
					if ($noisette_a_garder) {
						if (!$options['md5'] or $forcer_chargement) {
							// La noisette est soit nouvelle soit on est en mode rechargement forcé:
							// => il faut la rajouter dans la table.
							$noisettes_nouvelles[] = $configuration;
						} else {
							// La configuration stockée dans la table a été modifiée et le mode ne force pas le rechargement:
							// => il faut mettre à jour la noisette dans la table.
							$noisettes_modifiees[] = $configuration;
							// => il faut donc la supprimer de la liste des noisettes obsolètes
							$noisettes_obsoletes = array_diff($noisettes_obsoletes, array($noisette));
						}
					}
				} else {
					// La noisette n'a pas changée et n'a donc pas été réchargée:
					// => Il faut donc juste indiquer qu'elle n'est pas obsolète.
					$noisettes_obsoletes = array_diff($noisettes_obsoletes, array($noisette));
				}
			} else {
				// Il y a eu une erreur sur lors du rechargement de la noisette.
				// => il faut donc ne rien faire pour laisser la noisette dans les obsolètes
			}
		}

		// Mise à jour de la table des pages
		// -- Suppression des pages obsolètes ou de toute les pages non virtuelles si on est en mode
		//    rechargement forcé.
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}
		if ($noisettes_obsoletes) {
			sql_delete($from, sql_in('noisette', $noisettes_obsoletes));
		} elseif ($forcer_chargement) {
			sql_delete($from);
		}
		// -- Update des pages modifiées
		if ($noisettes_modifiees) {
			sql_replace_multi($from, $noisettes_modifiees);
		}
		// -- Insertion des nouvelles pages
		if ($noisettes_nouvelles) {
			sql_insertq_multi($from, $noisettes_nouvelles);
		}
		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}

		$retour = true;
	}

	return $retour;
}


function noizetier_noisette_phraser($noisette, $options = array()) {

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
	// - type-noisette si la noisette est dédiée uniquement à une page ou une composition
	// - noisette sinon
	$identifiants = explode('-', $noisette);

	// Initialisation de la description par défaut de la page
	$description_defaut = array(
		'noisette'       => $noisette,
		'type'           => isset($identifiants[1]) ? $identifiants[0] : '',
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
			if ($noisettes = sql_allfetsel('noisette, ajax', 'spip_noizetier_noisettes')) {
				$noisettes = array_column($noisettes, 'ajax', 'noisette');
				foreach ($noisettes as $_noisette => $_configuration) {
					$est_ajax[$_noisette] = ($_configuration['ajax'] == 'defaut')
						? $defaut_ajax
						: ($_configuration['ajax'] == 'non' ? false : true);
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
 * Retourne la liste des descriptions des blocs par defaut du squelette.
 *
 * @package SPIP\NOIZETIER\API\BLOC
 * @api
 * @filtre
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
 * @package SPIP\NOIZETIER\API\BLOC
 * @api
 * @filtre
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
 * @package SPIP\NOIZETIER\API\BLOC
 * @api
 * @filtre
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
	$options['repertoire_pages'] = noizetier_page_obtenir_dossier();

	// Initialiser le contexte de rechargement
	// TODO : en attente de voir si on rajoute un var_mode ou autre
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
				if ($configuration = noizetier_page_phraser($page, $options)) {
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


function noizetier_page_phraser($page, $options = array()) {

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
 *
 * @return array
 */
function noizetier_page_informer($page) {

	static $description_page = array();

	if (!isset($description_page[$page])) {
		// Chargement de toute la configuration de la page en base de données.
		$description = sql_fetsel('*', 'spip_noizetier_pages', array('page=' . sql_quote($page)));

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Traitements des champs textuels
			// TODO : faut-il rajouter le _T_ou_typo sur les champs concernés ?
//			$description['nom'] = _T_ou_typo($description['nom']);
//			if (isset($description['description'])) {
//				$description['description'] = _T_ou_typo($description['description']);
//			}
			// Traitements des champs sérialisés
			$description['blocs_exclus'] = unserialize($description['blocs_exclus']);
			$description['necessite'] = unserialize($description['necessite']);
			$description['branche'] = unserialize($description['branche']);
			// Calcul des blocs
			$description['blocs'] = noizetier_page_lister_blocs($page, $description['blocs_exclus']);
			$description_page[$page] = $description;
		} else {
			$description_page[$page] = array();
		}
	}

	return $description_page[$page];
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
function noizetier_page_obtenir_dossier() {

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
 * Lister les objets ayant des noisettes spéciquement configurées pour leur page.
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
 * 		Si le type et l'id de l'objet sont fournis, on renvoie la description de la page de cet objet.
 * 		Sinon, on renvoie le tableau de toutes les objets sous la forme [type_objet][id_objet].
 */
function noizetier_objet_informer($type_objet = '', $id_objet = 0, $information = '') {
	static $objets = null;
	static $description_objet = array();

	if ((!$type_objet and !$id_objet and is_null($objets))
	or ($type_objet and $id_objet and !isset($description_objet[$type_objet][$id_objet]))) {
		// On récupère le ou les objets ayant des noisettes dans la table spip_noisettes.
		$from = array('spip_noisettes');
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

	$est_active = false;

	include_spip('inc/config');
	$tables_actives = lire_config('noizetier/objets_noisettes', array());
	if ($tables_actives and in_array($type_objet, array_map('objet_type', $tables_actives))) {
		$est_active = true;
	}

	return $est_active;
}


include_spip('public/noizetier_balises');
include_spip('noizetier_vieilles_fonctions');
