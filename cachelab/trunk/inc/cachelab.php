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

if (!defined ('CACHELAB_LOG_ECHECS'))
	define ('CACHELAB_LOG_ECHECS', true);

if ($cle_objet and !$id_objet)
	die ("$cle_objet est inconnu : passez le en argument d'url ou définissez XRAY_ID_OBJET_SPECIAL en php");

function cachelab_applique ($action, $cle, $arg=null, $options='') {
global $Memoization;
static $len_prefix;
	if (!$len_prefix)
		$len_prefix = strlen(_CACHE_NAMESPACE);
	$joliecle = substr($cle, $len_prefix);

	switch ($action) {
	case 'del' :
		$del = $Memoization->del($joliecle);
		if (!$del and CACHELAB_CACHE_ECHECS)
			spip_log ("Échec del $joliecle", 'cachelab');
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
		elseif (CACHELAB_LOG_ECHECS)
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

	$chemin = (isset($conditions['chemin']) ? $conditions['chemin'] : null);
	$cle_objet = (isset($conditions['cle_objet']) ? $conditions['cle_objet'] : null);
	$id_objet = (isset($conditions['id_objet']) ? $conditions['id_objet'] : null);

	$methode_chemin = (isset ($options['methode_chemin']) ? $options['methode_chemin'] : 'strpos');
	$avec_listes = (isset ($options['listes']) and $options['listes']);
	$avec_chrono = (isset ($options['chrono']) and $options['chrono']);
	if ($avec_chrono) {
		include_spip ('lib/microtime.inc');
		microtime_do ('begin');
	}

	$len_prefix = strlen(_CACHE_NAMESPACE);

	$matche_chemin = $no_data = $data_not_array = $cible = array();
	$nb_caches=$nb_matche_chemin=$nb_no_data=$nb_data_not_array=$nb_cible=0;

	$chemins = explode('|', $chemin);
	$cache = apcu_cache_info();
	foreach($cache['cache_list'] as $i => $d) {
		$cle = $d['info'];
		if ($d and strpos ($cle, ':cache:') and  apcu_exists($cle)
			//and ($meta_derniere_modif <= $d['creation_time'])
			)
		{
			$nb_caches++;
			$danslechemin = !$chemin;
			if ($chemin) {
				switch ($methode_chemin) {
				case 'strpos' :
					foreach ($chemins as $unchemin) {
						if ($unchemin and (strpos ($cle, $unchemin) !== false)) {
							$danslechemin = true;
							cachelab_applique ($action, $cle, null, $options);
							break;
						};
					}
					break;
				case 'regexp' :
					if ($chemin and ($danslechemin = preg_match(",$chemin,i", $cle))) {
						if ($avec_listes)
							$matche_chemin[] = $cle;
						cachelab_applique ($action, $cle, null, $options);
					}
					break;
				default :
					die("Pas prévu (TODO)");
				};
				if ($danslechemin) {
					$nb_matche_chemin++;
					if ($avec_listes)
						$matche_chemin[]=$cle;
				};
			}
			else
				$nb_matche_chemin = '_';	// pas de filtrage par le chemin

			if ($danslechemin and $cle_objet and $id_objet) {
				global $Memoization;
				if ($data = $Memoization->get(substr($cle, $len_prefix))) {
					if (is_array($data)) {
						if (isset($data['contexte'])
							and isset ($data['contexte'][$cle_objet])
							and ($data['contexte'][$cle_objet]==$id_objet)
							) {
							$nb_cible++;
							if ($avec_listes)
								$cible[] = $cle;
							cachelab_applique ($action, $cle, $data, $options);
						}
					}
					else {
						if (CACHELAB_LOG_ECHECS)
							spip_log ("clé=$cle : data n'est pas un tableau : ".print_r($data,1), 'cachelab');
						if ($avec_listes)
							$data_not_array[]=$cle.($data ? ' not array' : ' empty');
						$nb_data_not_array++;
					};
				}
				else
					$nb_no_data++;
			}
			elseif($danslechemin) {// condition sur chemin sans condition sur contexte
				$nb_cible++;
				cachelab_applique ($action, $cle, $data, $options);
			}
		}
	}

	$stats = array(
		'caches'=>$nb_caches, 
		'matche_chemin'=>$nb_matche_chemin,
		'no_data' => $nb_no_data,	// yen a plein (ça correspond à quoi ?)
		'data_not_array' => $nb_data_not_array, // normalement yen a pas
		'cible'=>$nb_cible
	);

	if ($avec_listes) {
		$stats['liste_matche_chemin'] = $matche_chemin;
		$stats['liste_data_not_array'] = $data_not_array;
		$stats['liste_cible'] = $cible;
	}

	if ($avec_chrono) {
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
