<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('plugin_est_actif')) {
	function plugin_est_actif($prefixe) {
		$f = chercher_filtre('info_plugin');
		return $f($prefixe, 'est_actif');
	}
}

function cachelab_applique ($action, $cle, $data=null, $options='') {
global $Memoization;
static $len_prefix;
	if (!$len_prefix)
		$len_prefix = strlen(_CACHE_NAMESPACE);
	$joliecle = substr($cle, $len_prefix);

	switch ($action) {
	case 'del' :
		$del = $Memoization->del($joliecle);
		if (!$del) {
			spip_log ("Échec 'del' $joliecle", 'cachelab');
			return false;
		};
		break;

	case 'echo_cache' :
		if (!$data)
			$data = $Memoization->get($joliecle);
		echo "«<xmp>".substr(print_r($data,1), 0,2000)."</xmp>»";
		break;

	case 'echo_html' :
		if (!$data)
			$data = $Memoization->get($joliecle);
		echo "<p>«<xmp>".print_r($data,1)."</xmp>»</p>";
		break;

	case 'pass' :
	case 'list' :
		break;

	default :
		// on pourrait appeler cachelab_applique_$action(...)
		break;
	}
	return true;
}

// $chemin : liste de chaines à tester dans le chemin du squelette, séparées par |
// 	OU une regexp (hors délimiteurs et modificateurs) si la méthode est 'regexp'
function cachelab_filtre ($action, $conditions=array(), $options=array()) {
global $Memoization;
	if (!$Memoization or !in_array($Memoization->methode(), array('apc', 'apcu')))
		die ("Il faut mémoization avec APC ou APCu");

	// filtrage
	$session = (isset($conditions['session']) ? $conditions['session'] : null);
	if ($session=='courante')
		$session = spip_session();

	$chemin = (isset($conditions['chemin']) ? $conditions['chemin'] : null);
	$chemins = explode('|', $chemin); // sert seulement pour methode_chemin == strpos

	$cle_objet = (isset($conditions['cle_objet']) ? $conditions['cle_objet'] : null);
	$id_objet = (isset($conditions['id_objet']) ? $conditions['id_objet'] : null);
	if ($cle_objet and !$id_objet) {
		spip_log("cachelab_filtre : $cle_objet inconnu\n".print_r(debug_backtrace(),1), "cachelab_erreur");
		$cle_objet=null;
	}

	if (isset($conditions['more']))			// obsolète (todo : fix vieux codes appelant)
		$conditions['plus'] = $conditions['more'];
	// pour 'contexte' on simule un 'plus' pour donner un exemple d'extension
	if (isset($conditions['contexte']) and $conditions['contexte'] and !isset($conditions['plus']))
		$conditions['plus'] = 'contexte';
	if ($plus = (isset($conditions['plus']) ? (string)$conditions['plus'] : '')) {
		$plusfunc='cachelab_filtrecache_'.$plus;
		// Signature nécessaire : $plusfunc ($action, $conditions, $options, &$stats)
		if (!function_exists($plusfunc)) {
			spip_log ("La fonction '$plusfunc' n'est pas définie", 'cachelab_erreur');
			return;
		}
	}

	// options
	// explode+strpos par défaut pour les chemins
	$methode_chemin = (isset ($options['methode_chemin']) ? $options['methode_chemin'] : 'strpos');
	$partie_chemin = (isset ($options['partie_chemin']) ? $options['partie_chemin'] : 'tout');
	// clean par défaut
	$do_clean = (isset ($options['clean']) ? $options['clean'] : (!defined('CACHELAB_CLEAN') or CACHELAB_CLEAN)); 
	// pas de listes par défaut
	$do_lists = ($action == 'list') or (isset ($options['list']) and $options['list']);
	// pas de chrono par défaut sauf si CACHELAB_CHRONO
	$do_chrono = (isset ($options['chrono']) ? $options['chrono'] : (defined('CACHELAB_CHRONO') and CACHELAB_CHRONO)); 
	if ($do_chrono) {
		include_spip ('lib/microtime.inc');
		microtime_do ('begin');
	}

	// retours
	$stats=array();
	$stats['nb_alien']=$stats['nb_candidats']=$stats['nb_clean']=$stats['nb_no_data']=$stats['nb_not_array']=$stats['nb_cible']=0;
	$stats['l_no_data'] = $stats['l_not_array'] = $stats['l_cible'] = array();

	// On y va
	$cache = apcu_cache_info();
	$meta_derniere_modif = lire_meta('derniere_modif');
	$len_prefix = strlen(_CACHE_NAMESPACE);

	foreach($cache['cache_list'] as $i => $d) {
		// on "continue=passe au suivant" dés qu'on sait que le cache n'est pas cible

		$cle = $d['info'];
		$data=null;

		// on saute les caches d'autres origines
		// (et les caches d'un autre _CACHE_NAMESPACE pour ce même site)
		if (strpos ($cle, _CACHE_NAMESPACE) !== 0) {
			$stats['nb_alien']++;
			continue;
		}

		// on ne veut examiner que les caches de squelettes SPIP
		if (substr($cle, $len_prefix-1, 7) != ':cache:')
			continue;

		// effacer ou sauter les caches invalidés par une invalidation totale
		// ou que apcu ne suit plus
		if ($meta_derniere_modif > $d['creation_time']
			or !apcu_exists($cle)) {
			if ($do_clean) {
				$del=$Memoization->del(substr($cle,$len_prefix));
				if (!$del)
					spip_log ("Echec du clean du cache $cle (création : {$d['creation_time']}, invalidation : $meta_derniere_modif)", "cachelab_erreur");
				$stats['nb_clean']++;
			};
			continue;
		}

		// caches SPIP véritablement candidats
		$stats['nb_candidats']++;

		// 1er filtrage : par la session
		if ($session) {
			if (substr ($cle, -9) != "_$session")
				continue;
		}

		// 2eme filtrage : par le chemin
		if ($chemin) {
			switch ($partie_chemin) {
			case 'tout' :
			case 'chemin' :
				$partie_cle = $cle;
				break;
			case 'fichier' :
				$parties = explode('/', $cle);
				$partie_cle = array_pop($parties);
				break;
			case 'dossier' :
				$parties = explode('/', $cle);
				$parties = array_pop($parties);
				$partie_cle = array_pop($parties);
				break;
			default :
				spip_log ("Option partie_chemin incorrecte : '$partie_chemin'", 'cachelab_erreur');
				return;
			}
			// mémo php : « continue resumes execution just before the closing curly bracket ( } ), and break resumes execution just after the closing curly bracket. »
			switch ($methode_chemin) {
			case 'strpos' :
				foreach ($chemins as $unchemin)
					if ($unchemin and (strpos ($partie_cle, $unchemin) !== false))
						break 2;	// trouvé : sort du foreach et du switch et poursuit le test des autres conditions
				continue 2;	 // échec : passe à la $cle suivante
			case 'regexp' :
				if ($chemin and ($danslechemin = preg_match(",$chemin,i", $partie_cle)))
					break;	// trouvé : poursuit le test des autres conditions
				continue 2;	// échec : passe à la clé suivante
			default :
				spip_log ("Méthode '$methode_chemin' pas prévue pour le filtrage par le chemin", 'cachelab_erreur');
				return;
			};
		}

		// pour les filtres suivants on a besoin du contenu du cache
		if ($cle_objet or $plusfunc) {
			global $Memoization;
			$data = $Memoization->get(substr($cle, $len_prefix));
			if (!$data) {
				$stats['nb_no_data']++;
				continue;
			}
			if (!is_array($data)) {
				spip_log ("clé=$cle : data n'est pas un tableau : ".print_r($data,1), 'cachelab');
				$stats['nb_not_array']++;
				if ($do_lists)
					$stats['l_not_array'][] = $cle;
				continue;
			};
		};

		// 3eme filtre : par une valeur dans l'environnement
		if ($cle_objet
			and (!isset ($data['contexte'][$cle_objet])
				or ($data['contexte'][$cle_objet]!=$id_objet)))
			continue;

		// 4eme filtre : par une extension
		if ($plusfunc
			and !$plusfunc ($action, $conditions, $options, $cle, $data, $stats))
			continue;

		// restent les cibles
		$stats['nb_cible']++;
		if ($do_lists) 
			$stats['l_cible'][] = $cle;

		cachelab_applique ($action, $cle, $data, $options);
	}


	if ($do_chrono) {
		$stats['chrono'] = microtime_do ('end', 'ms');
		spip_log ("cachelab_filtre ($action, session=$session, objet $cle_objet=$id_objet, chemin=$chemin) : {$stats['nb_cible']} caches ciblés (sur {$stats['nb_candidats']}) en {$stats['chrono']} ms", 'cachelab');
	}

	return $stats;
}

function controler_invalideur($action, $objets_invalidants=array()) {
static $prev_derniere_modif_invalide;
	switch($action) {
	case 'stop' :
		$objets_invalidants = array();
		// nobreak;
	case 'select' :
		$prev_derniere_modif_invalide = $GLOBALS['derniere_modif_invalide'];
		if (is_array($objets_invalidants))
			$GLOBALS['derniere_modif_invalide'] = $objets_invalidants;
		break;
	case 'go' :
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
function cachelab_filtrecache_contexte($action, $conditions, $options, $cle, &$data, &$stats) {
	if (!isset ($data['contexte'])
		or !isset($conditions['contexte'])
		or !is_array($conditions['contexte']))
		return false;
	$diff = array_diff_assoc($conditions['contexte'], $data['contexte']);
	return empty($diff);
}
