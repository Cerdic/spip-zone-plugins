<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip ('lib/microtime.inc');

if (!function_exists('plugin_est_actif')) {
	function plugin_est_actif($prefixe) {
		$f = chercher_filtre('info_plugin');
		return $f($prefixe, 'est_actif');
	}
}

function cachelab_applique ($action, $cle, $arg=null, $options='') {
global $Memoization;
static $len_prefix;
	if (!$len_prefix)
		$len_prefix = strlen(_CACHE_NAMESPACE);
	$joliecle = substr($cle, $len_prefix);

	switch ($action) {
	case 'del' :
		$del = $Memoization->del($joliecle);
		if (!$del)
			spip_log ("Échec 'del' $joliecle", 'cachelab');
		break;

	case 'mark' :
		if ($arg === null)
			$data = $Memoization->get($joliecle);
		else
			$data = $arg;
		if (is_array($data)) {
			$data['cachelab_mark'] = (isset($options['mark']) ? $options['mark'] : 1);
			$data = $Memoization->set($joliecle, $data);
		}
		else
			spip_log("clé=$joliecle : pour $action avec arg=".print_r($arg,1)." et opt=".print_r($options,1).", data n'est pas un tableau : ".print_r($data, 1), 'cachelab');
		break;

	case 'echo_cache' :
		$data = $Memoization->get($joliecle);
		echo "«<xmp>".substr(print_r($data,1), 0,2000)."</xmp>»";
		break;

	case 'echo_html' :
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
}

// $chemin : liste de chaines à tester dans le chemin du squelette, séparées par |
// 	OU une regexp (hors délimiteurs et modificateurs) si la méthode est 'regexp'
function cachelab_filtre ($action, $conditions, $options=array()) {
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
	if ($cle_objet and !$id_objet)
		die ("$cle_objet est inconnu : passez le en argument d'url ou définissez XRAY_ID_OBJET_SPECIAL en php");
	// pour 'contexte' on simule un 'more' pour donner un exemple d'extension
	if (isset($conditions['contexte']) and $conditions['contexte'] and !isset($conditions['more']))
		$conditions['more'] = 'contexte';
	if ($more = (isset($conditions['more']) ? (string)$conditions['more'] : '')) {
		$morefunc='cachelab_filtrecache_'.$more;
		// Signature nécessaire : $morefunc ($action, $conditions, $options, &$stats)
		if (!function_exists($morefunc))
			die ("La fonction '$morefunc' n'est pas définie");
	}

	// options
	$methode_chemin = (isset ($options['methode_chemin']) ? $options['methode_chemin'] : 'strpos');
	$do_clean = ($action != 'pass') and (!isset ($options['clean']) or $options['clean']);	// clean par défaut
	$do_lists = ($action == 'list') or (isset ($options['list']) and $options['list']);		// pas de listes par défaut
	$do_chrono = (isset ($options['chrono']) and $options['chrono']);						// pas de chrono par défaut
	if ($do_chrono) {
		include_spip ('lib/microtime.inc');
		microtime_do ('begin');
	}
	
	// retours
	$stats=array();
	$stats['nb_alien']=$stats['nb_site']=$stats['nb_clean']=$stats['nb_no_data']=$stats['nb_not_array']=$stats['nb_cible']=0;
	$stats['l_no_data'] = $stats['l_not_array'] = $stats['l_cible'] = array();

	// On y va
	$cache = apcu_cache_info();
	$meta_derniere_modif = lire_meta('derniere_modif');
	$len_prefix = strlen(_CACHE_NAMESPACE);

	foreach($cache['cache_list'] as $i => $d) {
		// on "continue=passe au suivant" dés qu'on sait que le cache n'est pas cible

		$cle = $d['info'];

		// on saute les caches d'autres origines
		// (et les caches d'un précédent _CACHE_NAMESPACE pour ce même site)
		if (strpos ($cle, _CACHE_NAMESPACE) !== 0) {
			$stats['nb_alien']++;
			continue;
		}

		// on ne veut examiner que les caches de squelettes SPIP
		if ((substr($cle, $len_prefix-1, 7) != ':cache:')
			or !apcu_exists($cle))
			continue;

		// effacer ou au moins sauter les caches périmés
		if ($meta_derniere_modif > $d['creation_time']) {
			if ($do_clean) {
				$Memoization->del(substr($cle,$len_prefix));
				$stats['nb_clean']++;
			};
			continue;
		}

		// caches SPIP véritablement candidats
		$stats['nb_candidats']++;

		if ($session) {
			if (substr ($cle, -9) != "_$session")
				continue;
		}

		if ($chemin) {
			switch ($methode_chemin) {
			case 'strpos' :
				foreach ($chemins as $unchemin)
					if ($unchemin and (strpos ($cle, $unchemin) !== false))
						break;
				continue 2;
			case 'regexp' :
				if ($chemin and ($danslechemin = preg_match(",$chemin,i", $cle)))
					break;
				continue 2;
			default :
				die("Méthode pas prévue pour le filtrage par le chemin");
			};
		}

		// récupérer le contenu du cache
		if (($cle_objet and $id_objet) or $morefunc) {
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

		if ($cle_objet
			and (!isset ($data['contexte'][$cle_objet])
				or ($data['contexte'][$cle_objet]!=$id_objet)))
			continue;

		if ($morefunc
			and !$morefunc ($action, $conditions, $options, $cle, $data, $stats))
			continue;

		// restent les cibles
		$stats['nb_cible']++;
		if ($do_lists) 
			$stats['l_cible'][] = $cle;
		cachelab_applique ($action, $cle, null, $options);
	}

	if ($do_chrono) {
		$stats['chrono'] = microtime_do ('end', 'ms');
		spip_log ("cachelab_filtre ($action, $cle_objet, $id_objet, $chemin, $options) : {$stats['chrono']} ms", 'cachelab');
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

function cachelab_filtrecache_contexte($action, $conditions, $options, $cle, &$data, &$stats) {
	if (!isset ($data['contexte'])
		or !isset($conditions['contexte'])
		or !is_array($conditions['contexte']))
		return false;
	$diff = array_diff_assoc($conditions['contexte'], $data['contexte']);
	return empty($diff);
}
