<?php
/**
 * Source pour les itérateurs : referers d'un type objet éditorial (optionnel).
 *
 * Adaptation de stats_referers_to_array.php pour prendre en compte tous les objets éditoriaux
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
include_spip('inc/referenceurs');

function inc_stats_referers_objet_to_array_dist($limit, $jour, $objet, $id_objet, $options = array()) {

	$visites = 'visites';
	$table = "spip_referers";
	$where = array();
	$serveur = '';

	if (in_array($jour, array('jour', 'veille'))) {
		$visites .= "_$jour";
		$where[] = "$visites>0";
	}
	//$res = $referenceurs (0, "SUM(visites_$jour)", 'spip_referers', "visites_$jour>0", "referer", $limit);

	// Par défaut on cherche dans spip_referers.
	// Pour un type d'objet ou un objet en particulier,
	// on regarde dans spip_referers_objets ou spip_referers_articles.
	if ($objet) {
		switch ($objet) {

			// articles
			case 'article':
				$table = "spip_referers_articles";
				if (intval($id_objet)) {
					$where[] = "id_article = " . intval($id_objet);
				}
				break;

			// tous les autres objets
			default:
				$table = "spip_referers_objets";
				$where[] = 'objet='.sql_quote($objet);
				if (intval($id_objet)) {
					$where[] = "id_objet = " . intval($id_objet);
				}

		}
	}

	$where = implode(" AND ", $where);
	$limit = $limit ? "0," . intval($limit) : '';

	$result = sql_select("referer_md5, referer, $visites AS vis", $table, $where, '', "maj DESC", $limit, '', $serveur);
	//var_dump(sql_select("referer_md5, referer, $visites AS vis", $table, $where, '', "maj DESC", $limit, '', $serveur, false));

	$referers = array();
	$trivisites = array(); // pour le tri
	while ($row = sql_fetch($result, $serveur)) {
		$referer = interdire_scripts($row['referer']);
		$buff = stats_show_keywords($referer, $referer);

		if ($buff["host"]) {
			$refhost = $buff["hostname"];
			$visites = $row['vis'];
			$host = $buff["scheme"] . "://" . $buff["host"];

			$referers[$refhost]['referer_md5'] = $row['referer_md5'];

			if (!isset($referers[$refhost]['liens'][$referer])) {
				$referers[$refhost]['liens'][$referer] = 0;
			}
			if (!isset($referers[$refhost]['hosts'][$host])) {
				$referers[$refhost]['hosts'][$host] = 0;
			}

			if (!isset($referers[$refhost]['visites'])) {
				$referers[$refhost]['visites'] = 0;
			}
			if (!isset($referers[$refhost]['visites_racine'])) {
				$referers[$refhost]['visites_racine'] = 0;
			}
			if (!isset($referers[$refhost]['referers'])) {
				$referers[$refhost]['referers'] = array();
			}

			$referers[$refhost]['hosts'][$host]++;
			$referers[$refhost]['liens'][$referer]++;
			$referers[$refhost]['visites'] += $visites;
			$trivisites[$refhost] = $referers[$refhost]['visites'];

			$tmp = "";
			$set = array(
				'referer' => $referer,
				'visites' => $visites,
				'referes' => $id_objet ? '' : referes_objets($row['referer_md5'], '', $objet)
			);
			if (isset($buff["keywords"])
				and $c = $buff["keywords"]
			) {
				if (!isset($referers[$refhost]['keywords'][$c])) {
					$referers[$refhost]['keywords'][$c] = true;
					$set['keywords'] = $c;
				}
			} else {
				$tmp = $buff["path"];
				if ($buff["query"]) {
					$tmp .= "?" . $buff['query'];
				}
				if (strlen($tmp)) {
					$set['path'] = "/$tmp";
				}
			}
			if (isset($set['path']) or isset($set['keywords'])) {
				$referers[$refhost]['referers'][] = $set;
			} else {
				$referers[$refhost]['visites_racine'] += $visites;
			}
		}
	}

	// trier les liens pour trouver le principal
	foreach ($referers as $k => $r) {
		arsort($referers[$k]['liens']);
		$referers[$k]['liens'] = array_keys($referers[$k]['liens']);
		arsort($referers[$k]['hosts']);
		$referers[$k]['hosts'] = array_keys($referers[$k]['hosts']);
		$referers[$k]['url'] = reset($referers[$k]['hosts']);
	}

	if (count($trivisites)) {
		array_multisort($trivisites, SORT_DESC, $referers);
	}

	return $referers;
}
