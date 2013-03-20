<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/statistiques');
// moyenne glissante sur 30 jours
define('MOYENNE_GLISSANTE_JOUR', 30);
// moyenne glissante sur 12 mois
define('MOYENNE_GLISSANTE_MOIS', 12);

function inc_stats_trads_to_array_dist($unite, $duree, $id_tradlang_module, $options = array()) {
	$now = time();

	if (!in_array($unite,array('jour','mois')))
		$unite = 'jour';
	$serveur = '';

	$table = "spip_versions";
	$order = "date";
	$where = array(
		'objet = "tradlang"',
		'id_version > 0',
		'id_auteur > 0'
	);
	
	if ($duree)
		$where[] = sql_date_proche($order,-$duree,'day',$serveur);

	$where = implode(" AND ",$where);
	$format = ($unite=='jour'?'%Y-%m-%d':'%Y-%m-01');
	$res = sql_select("COUNT(*) AS v, DATE_FORMAT($order,'$format') AS d", $table, $where, "d", "d", "",'',$serveur);
	
	$format = str_replace('%','',$format);
	$periode = ($unite=='jour'?24*3600:365*24*3600/12);
	$step = intval(round($periode*1.1,0));
	$glisse = constant('MOYENNE_GLISSANTE_'.strtoupper($unite));
	moyenne_glissante();
	$data = array();
	$r = sql_fetch($res,$serveur);
	if (!$r){
		$r = array('d'=>date($format,$now),'v'=>0);
	}
	do {
		$data[$r['d']] = array('versions'=>$r['v'],'moyenne'=>moyenne_glissante($r['v'], $glisse));
		$last = $r['d'];

		// donnee suivante
		$r = sql_fetch($res,$serveur);
		// si la derniere n'est pas la date courante, l'ajouter
		if (!$r AND $last!=date($format,$now))
			$r = array('d'=>date($format,$now),'v'=>0);

		// completer les trous manquants si besoin
		if ($r){
			$next = strtotime($last);
			$current = strtotime($r['d']);
			while (($next+=$step)<$current AND $d=date($format,$next)){
				if (!isset($data[$d]))
					$data[$d] = array('versions'=>0,'moyenne'=>moyenne_glissante(0, $glisse));
				$last = $d;
				$next = strtotime($last);
			}
		}
	}
	while ($r);

	// projection pour la derniere barre :
	// mesure courante
	// + moyenne au pro rata du temps qui reste
	$moyenne = end($data);
	$moyenne = prev($data);
	$moyenne = ($moyenne AND isset($moyenne['moyenne']))?$moyenne['moyenne']:0;
	$data[$last]['moyenne'] = $moyenne;

	// temps restant
	$remaining = strtotime(date($format,strtotime(date($format,$now))+$step))-$now;

	$prorata = $remaining/$periode;

	// projection
	$data[$last]['prevision'] = $data[$last]['versions'] + intval(round($moyenne*$prorata));

  return $data;
}


?>