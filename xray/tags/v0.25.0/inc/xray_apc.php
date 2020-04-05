<?php

// il faut définir un inc/xray_options.php lorsqu'on include_spip ('inc/xray_apc') dans le code php d'un plugin
if (!defined('XRAY_PATTERN_STATS_SPECIALES'))
	include_spip('inc/xray_options');
if (!defined('XRAY_PATTERN_STATS_SPECIALES'))
	include_spip ('inc/xray_options_default');
if (!defined('XRAY_PATTERN_STATS_SPECIALES'))
	die ("erreur : XRAY_PATTERN_STATS_SPECIALES n'est pas défini");

function xray_stats($cache=null) {
	if (!$cache)
		$cache = apcu_cache_info();
	if (!$cache)
		return "Pas de cache";
	// on ordonne par date de création
	$list = array();
	foreach($cache['cache_list'] as $i => $entry) {
		$k = 'a_'.sprintf('%015d', $entry['creation_time']).$entry['info'];
		$entry ['date_crea'] = date(DATE_FORMAT, $entry['creation_time']);
		$entry ['info_exists'] = apcu_exists ($entry['info']);
		$list[$k] = $entry;
	}
	// tri à l'envers pour ne pas réindexer le tableaux numériquement avec array_shift
	krsort($list, SORT_STRING);
	
	$meta_derniere_modif = lire_meta('derniere_modif');

	$stats=array();
	$stats['existent']=$stats['invalides']=$stats['speciaux']=$stats['generaux']=$stats['fantomes']
		=array(
			'nb'=>0, 
			'taille'=>0, 
			'naissance'=>0, 
			'nb_hits'=>0, 
			'nb_requetes'=>0,
			'mem_hits'=>0,
			'mem_requetes'=>0
		);
	$existent = &$stats['existent'];
	$invalides = &$stats['invalides'];
	$speciaux = &$stats['speciaux'];
	$generaux = &$stats['generaux'];
	$fantomes = &$stats['fantomes'];
	while (count($list)) {
		$d = array_pop($list);
		if ($d and apcu_exists($d['info'])) {
			$existent['nb']++;
			$existent['taille'] += $d['mem_size'];
			if (!$existent['naissance'] or ($existent['naissance'] > $d['creation_time']))
				$existent['naissance'] = date(JOLI_DATE_FORMAT,$d['creation_time']);
			$existent['nb_hits'] += $d['num_hits'];
			$existent['nb_requetes'] += $d['num_hits'] + 1;
			$existent['mem_hits'] += $d['mem_size']*$d['num_hits'];
			$existent['mem_requetes'] += $d['mem_size']*($d['num_hits'] + 1);
			if ($meta_derniere_modif > $d['creation_time']) {
				$invalides['nb']++;
				$invalides['taille'] += $d['mem_size'];
				$invalides['nb_hits'] += $d['num_hits'];
				$invalides['nb_requetes'] += $d['num_hits'] + 1;
				$invalides['mem_hits'] += $d['mem_size']*$d['num_hits'];
				$invalides['mem_requetes'] += $d['mem_size']*($d['num_hits'] + 1);
				if (!$invalides['naissance'] or ($invalides['naissance'] > $d['creation_time']))
					$invalides['naissance'] = date(JOLI_DATE_FORMAT,$d['creation_time']);
			}
			elseif (preg_match(XRAY_PATTERN_STATS_SPECIALES, $d['info'])) {
				$speciaux['nb']++;
				$speciaux['taille'] += $d['mem_size'];
				$speciaux['nb_hits'] += $d['num_hits'];
				$speciaux['nb_requetes'] += $d['num_hits'] + 1;
				$speciaux['mem_hits'] += $d['mem_size']*$d['num_hits'];
				$speciaux['mem_requetes'] += $d['mem_size']*($d['num_hits'] + 1);
				if (!$speciaux['naissance'] or ($speciaux['naissance'] > $d['creation_time']))
					$speciaux['naissance'] = date(JOLI_DATE_FORMAT,$d['creation_time']);
			}
			else {
				$generaux['nb']++;
				$generaux['taille'] += $d['mem_size'];
				$generaux['nb_hits'] += $d['num_hits'];
				$generaux['nb_requetes'] += $d['num_hits'] + 1;
				$generaux['mem_hits'] += $d['mem_size']*$d['num_hits'];
				$generaux['mem_requetes'] += $d['mem_size']*($d['num_hits'] + 1);
				if (!$generaux['naissance'] or ($generaux['naissance'] > $d['creation_time']))
					$generaux['naissance'] = date(JOLI_DATE_FORMAT,$d['creation_time']);

			}
		}
		else {
			$fantomes['nb']++;
			$fantomes['taille'] += $d['mem_size'];
			$fantomes['nb_hits'] += $d['num_hits'];
			$fantomes['nb_requetes'] += $d['num_hits'] + 1;
			$fantomes['mem_hits'] += $d['mem_size']*$d['num_hits'];
			$fantomes['mem_requetes'] += $d['mem_size']*($d['num_hits'] + 1);
			if (!$fantomes['naissance'] or ($fantomes['naissance'] > $d['creation_time']))
					$fantomes['naissance'] = date(JOLI_DATE_FORMAT,$d['creation_time']);
		}
	};
	return $stats;
}

function xray_stats_print(&$stats, $what, $label) {
	if (!$stats[$what]['nb'])
		return "<tr><td colspan=2>$label : aucun cache</td></tr>";
	echo "
		<tr><td colspan=2><b>$label</b></td></tr>
		<tr class=tr-0><td class=td-0>Nb caches</td><td>{$stats[$what]['nb']}</td></tr>
		<tr class=tr-0><td class=td-0>Taille totale</td><td>".taille_en_octets($stats[$what]['taille'])."</td></tr>
		<tr class=tr-0><td class=td-0>Nb requetes</td><td>{$stats[$what]['nb_requetes']}</td></tr>
		<tr class=tr-0><td class=td-0>Nb hits</td><td>{$stats[$what]['nb_hits']} soit ".round(100*$stats[$what]['nb_hits']/$stats[$what]['nb_requetes'],1)."%</td></tr>
		<tr class=tr-0><td class=td-0 title='Service par le cache pondéré par la taille'>Rendement</td><td>".round(100*$stats[$what]['mem_hits']/$stats[$what]['mem_requetes'],1)."%</td></tr>
		<tr class=tr-0><td class=td-0>Plus vieux cache</td><td>{$stats[$what]['naissance']}</td></tr>";
}
