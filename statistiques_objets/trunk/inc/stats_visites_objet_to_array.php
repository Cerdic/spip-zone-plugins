<?php
/**
 * Source pour les itérateurs : visites d'un type objet éditorial (optionnel)
 *
 * Adaptation de stats_visites_to_array.php pour prendre en compte tous les objets éditoriaux
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/statistiques');
// moyenne glissante sur 30 jours
define('MOYENNE_GLISSANTE_JOUR', 30);
// moyenne glissante sur 12 mois
define('MOYENNE_GLISSANTE_MOIS', 12);

function inc_stats_visites_objet_to_array_dist($unite, $duree, $objet='', $id_objet='', $options = array()) {
	$now = time();

	if (!in_array($unite, array('jour', 'mois'))) {
		$unite = 'jour';
	}
	$serveur = '';

	// par défaut, on compte toutes les visites dans spip_visites
	$table = "spip_visites";
	$where = array();
	$order = "date";
	if ($duree) {
		$where[] = sql_date_proche($order, -$duree, 'day', $serveur);
	}
	// pour un type d'objet ou un objet en particulier,
	// on regarde dans spip_visites_objets ou spip_visites_articles
	if ($objet) {
		if ($objet == 'article'){
			$table = "spip_visites_articles";
		} else {
			$table = "spip_visites_objets";
			$where[] = 'objet='.sql_quote($objet);
		}
		if (intval($id_objet)) {
			if ($objet == 'article'){
				$where[] = "id_article=" . intval($id_objet);
			} else {
				$where[] = 'id_objet='. intval($id_objet);
			}
		}
	}

	$where = implode(" AND ", $where);
	$format = ($unite == 'jour' ? '%Y-%m-%d' : '%Y-%m-01');

	$res = sql_select("SUM(visites) AS v, DATE_FORMAT($order,'$format') AS d", $table, $where, "d", "d", "", '', $serveur);
	//var_dump(sql_select("SUM(visites) AS v, DATE_FORMAT($order,'$format') AS d", $table, $where, "d", "d", "", '', $serveur,false));

	$format = str_replace('%', '', $format);
	$periode = ($unite == 'jour' ? 24 * 3600 : 365 * 24 * 3600 / 12);
	$step = intval(round($periode * 1.1, 0));
	$glisse = constant('MOYENNE_GLISSANTE_' . strtoupper($unite));
	moyenne_glissante();
	$data = array();
	$r = sql_fetch($res, $serveur);
	if (!$r) {
		$r = array('d' => date($format, $now), 'v' => 0);
	}
	do {
		$data[$r['d']] = array('visites' => $r['v'], 'moyenne' => moyenne_glissante($r['v'], $glisse));
		$last = $r['d'];

		// donnee suivante
		$r = sql_fetch($res, $serveur);
		// si la derniere n'est pas la date courante, l'ajouter
		if (!$r and $last != date($format, $now)) {
			$r = array('d' => date($format, $now), 'v' => 0);
		}

		// completer les trous manquants si besoin
		if ($r) {
			$next = strtotime($last);
			$current = strtotime($r['d']);
			while (($next += $step) < $current and $d = date($format, $next)) {
				if (!isset($data[$d])) {
					$data[$d] = array('visites' => 0, 'moyenne' => moyenne_glissante(0, $glisse));
				}
				$last = $d;
				$next = strtotime($last);
			}
		}
	} while ($r);

	// projection pour la derniere barre :
	// mesure courante
	// + moyenne au pro rata du temps qui reste
	$moyenne = end($data);
	$moyenne = prev($data);
	$moyenne = ($moyenne and isset($moyenne['moyenne'])) ? $moyenne['moyenne'] : 0;
	$data[$last]['moyenne'] = $moyenne;

	// temps restant
	$remaining = strtotime(date($format, strtotime(date($format, $now)) + $step)) - $now;

	$prorata = $remaining / $periode;

	// projection
	$data[$last]['prevision'] = $data[$last]['visites'] + intval(round($moyenne * $prorata));

	/**
	 * Compter les fichiers en attente de depouillement dans tmp/visites/
	 * pour affiner la prediction.
	 * A activer dans le mes_options si l'hebergement tient le coup en cas de gros pics de traffic
	 */
	if (!$id_objet and defined('_STATS_COMPTE_EN_ATTENTE') AND _STATS_COMPTE_EN_ATTENTE) {
		// eviter un depassement memoire en mesurant un echantillon pour commencer
		$n = count(glob(_DIR_RACINE . "tmp/visites/0*"));
		if ($n < 10000) {
			$n = count(glob(_DIR_RACINE . "tmp/visites/*"));
		} else {
			$n += count(glob(_DIR_RACINE . "tmp/visites/4*"));
			$n += count(glob(_DIR_RACINE . "tmp/visites/8*"));
			$n += count(glob(_DIR_RACINE . "tmp/visites/c*"));
			$n = 4 * $n;
		}
		$data[$last]['prevision'] += $n;
	}

	return $data;
}
