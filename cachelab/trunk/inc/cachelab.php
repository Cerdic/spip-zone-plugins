<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('public/cachelab_utils');

/**
 *
 * Applique une action sur un cache donné et renvoie éventuellement une donnée
 * Nécessite Mémoization (toutes méthodes OK).
 *
 * @param $action : del, pass, list, clean, list_html, get, get_html ou user defined
 * @param $cle : clé du cache ciblé
 * @param null $data : valeur du cache pour cette clé (pas forcément fourni)
 * @param string $options
 * @param null $return : résultat éventuellement fourni, pour les actions list et get
 * @return bool : indique si l'action a pu être appliquée ou non
 */
function cachelab_appliquer($action, $cle, $data = null, $options = '', &$return = null) {
global $Memoization;
	if (!isset($Memoization) or !$Memoization) {
		spip_log("cachelab_appliquer ($action, $cle...) : Memoization n'est pas activé", 'cachelab_erreur');
		return false;
	}

static $len_prefix;
	if (!$len_prefix) {
		$len_prefix = strlen(_CACHE_NAMESPACE);
	}
	$joliecle = substr($cle, $len_prefix);

	switch ($action) {
	case 'del':
		$del = $Memoization->del($joliecle);
		if (!$del) {
			spip_log("Échec 'del' $joliecle", 'cachelab_erreur');
			return false;
		};
			break;

	// gérés par cachelab_cibler
	case 'pass':	// passe
	case 'list':	// renvoie les clés
	case 'clean':	// nettoie
			break;
		
	case 'list_html':	// renvoie les contenus indexés par les clés sans préfixes
						// attention ça peut grossir !
		if (!is_array($return)) {
			$return = array();
		}
		$return[$joliecle] = $data['texte'];
			break;

	case 'get':	// renvoie le 1er cache ciblé
		if (!$data) {
			$data = $Memoization->get($joliecle);
		}
		$return = $data;
			break;

	case 'get_html':	// renvoie le contenu du 1er cache
		if (!$data) {
			$data = $Memoization->get($joliecle);
		}
		$return = $data['texte'];
			break;

	default:
		$f = 'cachelab_appliquer_'.$action;
		if (function_exists($f)) {
			return $f($action, $cle, $data, $options, $return);
		} else {
			spip_log("L'action $action n'est pas définie pour cachelab_applique", 'cachelab_erreur');
			return false;
		}
	}
	return true;
}

/**
 *
 * Applique une action donnée à tous les caches vérifiant certaines conditions
 *
 * @uses apcu_cache_info() et donc nécessite que Memoization soit activé avec APC ou APCu
 *
 * @param string $action   : l'action à appliquer
 * @param array $conditions : les conditions définissant la cible
 * @param array $options    : options de l'action et/ou des conditions
 * @return array|null
 *      le résultat si c'est une action 'get' ou 'get_...'
 *      la liste des stats sinon, avec éventuellement la liste des résultats s'ils sont demandés (pour 'list_html'...)
 *
 */
function cachelab_cibler($action, $conditions = array(), $options = array()) {
global $Memoization;
	if (!isset($Memoization) or !$Memoization or !in_array($Memoization->methode(), array('apc', 'apcu'))) {
		spip_log("cachelab_cibler($action...) : Mémoization n'est pas activé avec APC ou APCu", 'cachelab_erreur');
		die("cachelab_cibler($action...) : le plugin Mémoization doit être activé avec APC ou APCu");
	}
	$return = null;

	// filtrage
	$session = (isset($conditions['session']) ? $conditions['session'] : null);
	if ($session=='courante') {
		$session = spip_session();
	}

	$chemin = (isset($conditions['chemin']) ? $conditions['chemin'] : null);
	$chemins = explode('|', $chemin); // sert seulement pour methode_chemin == strpos

	$cle_objet = (isset($conditions['cle_objet']) ? $conditions['cle_objet'] : null);
	$id_objet = (isset($conditions['id_objet']) ? $conditions['id_objet'] : null);
	if ($cle_objet and !$id_objet) {
		spip_log("cachelab_cibler : $cle_objet inconnu\n".print_r(debug_backtrace(), 1), 'cachelab_erreur');
		$cle_objet=null;
	}

	// pour 'contexte' on simule un 'plus' pour donner un exemple d'extension
	if (isset($conditions['contexte']) and $conditions['contexte'] and !isset($conditions['plus'])) {
		$conditions['plus'] = 'contexte';
	}
	if ($plus = (isset($conditions['plus']) ? (string)$conditions['plus'] : '')) {
		$plusfunc='cachelab_ciblercache_'.$plus;
		// Signature nécessaire : $plusfunc ($action, $conditions, $options, &$stats)
		if (!function_exists($plusfunc)) {
			spip_log("La fonction '$plusfunc' n'est pas définie", 'cachelab_erreur');
			return null;
		}
	} else {
		$plusfunc = '';
	}

	// options
	// explode+strpos par défaut pour les chemins
	$methode_chemin = (isset($options['methode_chemin']) ? $options['methode_chemin'] : 'strpos');
	$partie_chemin = (isset($options['partie_chemin']) ? $options['partie_chemin'] : 'tout');
	// clean par défaut
	$do_clean = (isset($options['clean']) ? $options['clean'] : (!defined('CACHELAB_CLEAN') or CACHELAB_CLEAN));
	// pas de listes par défaut
	$do_lists = ($action == 'list') or (isset($options['list']) and $options['list']);
	include_spip('lib/microtime.inc');
	microtime_do('begin');

	// retours
	$stats=array();
	$stats['nb_alien']=$stats['nb_candidats']=$stats['nb_clean']=$stats['nb_cible']=0;
	$stats['l_cible'] = array();

	// On y va
	$cache = apcu_cache_info();
	$meta_derniere_modif = $GLOBALS['meta']['derniere_modif'];
	$len_prefix = strlen(_CACHE_NAMESPACE);

	foreach ($cache['cache_list'] as $i => $d) {
		// on "continue=passe au suivant" dés qu'on sait que le cache n'est pas cible

		$cle = $d['info'];
		$data=null;

		// on saute les caches d'autres origines
		// (et les caches d'un autre _CACHE_NAMESPACE pour ce même site)
		if (strpos($cle, _CACHE_NAMESPACE) !== 0) {
			$stats['nb_alien']++;
			continue;
		}

		// on ne veut examiner que les caches de squelettes SPIP
		if (substr($cle, $len_prefix-1, 7) != ':cache:') {
			continue;
		}

		// effacer ou sauter les caches invalidés par une invalidation totale
		// ou que apcu ne suit plus
		if ($meta_derniere_modif > $d['creation_time']
			or !apcu_exists($cle)) {
			if ($do_clean) {
				$del=$Memoization->del(substr($cle, $len_prefix));
				if (!$del) {
					// Se produit parfois en salve de 10 à 50 logs simultanés (mm t, mm pid)
					spip_log("Echec du clean du cache $cle par Memoization (création : {$d['creation_time']}, invalidation : $meta_derniere_modif)", 'cachelab_erreur');
				}
				$stats['nb_clean']++;
			};
			continue;
		}

		// caches SPIP véritablement candidats
		$stats['nb_candidats']++;

		// 1er filtrage : par la session
		if ($session) {
			if (substr($cle, -9) != "_$session") {
			continue;
			}
		}

		// 2eme filtrage : par le chemin
		if ($chemin) {
			switch ($partie_chemin) {
			case 'tout':
			case 'chemin':
				$partie_cle = $cle;
				break;
			case 'fichier':
				$parties = explode('/', $cle);
				$partie_cle = array_pop($parties);
				break;
			case 'dossier':
				$parties = explode('/', $cle);
				$parties = array_pop($parties);
				$partie_cle = array_pop($parties);
				break;
			default:
				spip_log("Option partie_chemin incorrecte : '$partie_chemin'", 'cachelab_erreur');
				return null;
			}
			// mémo php : « continue resumes execution just before the closing curly bracket ( } ),
			// and break resumes execution just after the closing curly bracket. »
			switch ($methode_chemin) {
			case 'strpos':
				foreach ($chemins as $unchemin) {
					if ($unchemin and (strpos($partie_cle, $unchemin) !== false)) {
						break 2;	// trouvé : sort du foreach et du switch et poursuit le test des autres conditions
					}
				}
				continue 2;	 // échec : passe à la $cle suivante
			case '==' :
			case 'egal' :
			case 'equal':
				foreach ($chemins as $unchemin) {
					if ($unchemin==$partie_cle) {
						break 2;	// trouvé : sort du foreach et du switch et poursuit le test des autres conditions
					}
				}
				continue 2;	 // échec : passe à la $cle suivante
			case 'regexp':
				if ($chemin and ($danslechemin = preg_match(",$chemin,i", $partie_cle))) {
					break;	// trouvé : poursuit le test des autres conditions
				}
				continue 2;	// échec : passe à la clé suivante
			default:
				spip_log("Méthode '$methode_chemin' pas prévue pour le filtrage par le chemin", 'cachelab_erreur');
				return null;
			};
		}

		// pour les filtres suivants on a besoin du contenu du cache
		if ($cle_objet or $plusfunc) {
			global $Memoization;
			$data = $Memoization->get(substr ($cle, $len_prefix));
			if (!$data or !is_array ($data)) {
				spip_log ("clé=$cle : data est vide ou n'est pas un tableau : " . print_r ($data, 1), 'cachelab_erreur');
				continue;
			};

			// 3eme filtre : par une valeur dans l'environnement
			if ($cle_objet
				and (!isset($data['contexte'][$cle_objet])
					or ($data['contexte'][$cle_objet] != $id_objet))) {
				continue;
			}

			// 4eme filtre : par une extension
			if ($plusfunc
				and !$plusfunc($action, $conditions, $options, $cle, $data, $stats)) {
				continue;
			}
		}

		// restent les cibles atteintes
		$stats['nb_cible']++;
		if ($do_lists) {
			$stats['l_cible'][] = $cle;
		}

		cachelab_appliquer($action, $cle, $data, $options, $return);

		if ($return
			and (($action=='get')
				or (substr($action, 0, 4)=='get_'))) {
			return $return; // TODO chrono aussi dans ce cas
		}
	}

	$stats['chrono'] = microtime_do('end', 'ms');
	$msg = "cachelab_cibler($action) en {$stats['chrono']} ({$stats['nb_cible']} caches sur {$stats['nb_candidats']})"
		."\n".print_r($conditions, 1);
	if (count($options)) {
		$msg .= "\n".print_r($options, 1);
	}
	if (defined('LOG_CACHELAB_CHRONO') and LOG_CACHELAB_CHRONO) {
		spip_log($msg, 'cachelab_chrono.'._LOG_INFO);
	}
	if (defined('LOG_CACHELAB_SLOW') and ($stats['chrono']  > LOG_CACHELAB_SLOW)) {
		spip_log($msg, 'cachelab_slow.'._LOG_INFO_IMPORTANTE);
	}
	if (($action=='del') and defined('LOG_CACHELAB_TOOMANY_DEL') and ($stats['nb_cible']  > LOG_CACHELAB_TOOMANY_DEL)) {
		if (function_exists ('debug_log')) {
			debug_log ($msg, 'cachelab_toomany_del', true);
		}
		else {
			spip_log($msg, 'cachelab_toomany_del.'._LOG_INFO_IMPORTANTE);
		}
	}

	if ($return) {
		$stats['val'] = $return;
	}
	return $stats;
}

/**
 * @param $action
 * @param array $objets_invalidants
 */
function controler_invalideur($action, $objets_invalidants = array()) {
static $prev_derniere_modif_invalide;
	switch ($action) {
	case 'stop':
		$objets_invalidants = array();
		// nobreak;
	case 'select':
		$prev_derniere_modif_invalide = $GLOBALS['derniere_modif_invalide'];
		if (is_array($objets_invalidants)) {
			$GLOBALS['derniere_modif_invalide'] = $objets_invalidants;
		}
		break;
	case 'go':
		$GLOBALS['derniere_modif_invalide'] = $prev_derniere_modif_invalide;
		break;
	}
}

//
// Exemple d'extension utilisable avec 'plus'=>'contexte'
// Filtrer non sur une seule valeur de l'environnement comme avec 'cle_objet'
// mais sur un ensemble de valeurs spécifié par $conditions['contexte']
// qui est un tableau de (clé, valeur)
// Toutes les valeurs doivent être vérifiées dans l'environnement.
//
/**
 * @param $action
 * @param $conditions
 * @param $options
 * @param $cle
 * @param $data
 * @param $stats
 * @return bool
 */
function cachelab_ciblercache_contexte($action, $conditions, $options, $cle, &$data, &$stats) {
	if (!isset($data['contexte'])
		or !isset($conditions['contexte'])
		or !is_array($conditions['contexte'])) {
		return false;
	}
	$diff = array_diff_assoc($conditions['contexte'], $data['contexte']);
	return empty($diff);
}
