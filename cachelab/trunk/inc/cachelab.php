<?php
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

	$matche_chemin = $matche_objet = array();
	$nb_valides=0;
	$nb_echecaccesdata=0;
	$nb_absentducontexte=0;
	$nb_accesdata=0;
	$nb_datanotarray=0;
	
	$chemins = explode('|', $chemin);
	$cache = apcu_cache_info();
	foreach($cache['cache_list'] as $i => $d) {
		$cle = $d['info'];
		if ($d and strpos ($cle, ':cache:') and  apcu_exists($cle)
			//and ($meta_derniere_modif <= $d['creation_time'])
			)
		{
			$nb_valides++;
			$danslechemin = false;
			switch ($methode_chemin) {
			case 'strpos' :
				foreach ($chemins as $unchemin) {
					if ($unchemin and (strpos ($cle, $unchemin) !== false)) {
						if ($avec_listes)
							$matche_chemin[]=$d;
						$danslechemin = true;
						cachelab_applique ($action, $cle, null, $options);
						break;
					};
				}
				break;
			case 'regexp' :
				if ($chemin and ($danslechemin = preg_match(",$chemin,i", $cle))) {
					if ($avec_listes)
						$matche_chemin[] = $d;
					cachelab_applique ($action, $cle, null, $options);
				}
				break;
			default :
				die("Pas prévu (todo)");
			};

			if (!$danslechemin and $cle_objet and $id_objet) {
				global $Memoization;
				if ($data = $Memoization->get(substr($cle, $len_prefix))) {
					$nb_accesdata++;

					if (is_array($data)) {
						if (isset($data['contexte'])
							and isset ($data['contexte'][$cle_objet])
							and ($data['contexte'][$cle_objet]==$id_objet)) {
								if ($avec_listes)
									$matche_objet[] = $d;
								cachelab_applique ($action, $cle, $data, $options);
							}
						else
							$nb_absentducontexte++;
					}
					else {
						if (CACHELAB_LOG_ECHECS)
							spip_log ("clé=$cle : data n'est pas un tableau : ".print_r($data,1), 'cachelab');
						$nb_datanotarray++;
					};
				}
				else 
					$nb_echecaccesdata++;
			}
		}
	}

	$stats = array(
		'ok_parsed'=>$nb_valides, 
		'fail_data_access' => $nb_echecaccesdata,
		'ok_data_access' => $nb_accesdata,
		'fail_data_not_array' => $nb_datanotarray,
		'ok_data_dont_match' => $nb_absentducontexte
	);

	if ($avec_listes) {
		$stats['squelette'] = $matche_chemin;
		$stats['contexte'] = $matche_objet;
	}

	if ($avec_chrono) {
		$stats['chrono'] = microtime_do ('end', 'ms');
		spip_log ("cachelab_filtre ($action, $cle_objet, $id_objet, $chemin, $options) : {$stats['chrono']} ms", 'cachelab');
	}

	return $stats;
}

function cachelab_controle_invalideur($action, $objets_invalidants=array()) {
static $prev_derniere_modif_invalide;
	switch($action) {
	case 'stop' :
		$prev_derniere_modif_invalide = $GLOBALS['derniere_modif_invalide'];
		if (is_array($objets_invalidants))
			$GLOBALS['derniere_modif_invalide'] = $objets_invalidants;
		break;
	case 'go' :
		$GLOBALS['derniere_modif_invalide'] = $prev_derniere_modif_invalide;
		break;
	}
}
