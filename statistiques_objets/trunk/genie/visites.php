<?php
/**
 * Enregistrer les visites en base d'après les fichiers dans tmp/visites.
 *
 * Surcharge du plugin Statistiques de la dist pour prendre en compte tous les objets éditoriaux.
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 * @package   SPIP\Statistiques_objets\Administrations
 */

/**
 * Gestion du compage des statistiques de visites (cron)
 *
 * @plugin Statistiques pour SPIP
 * @license GNU/GPL
 * @package SPIP\Statistiques\Genie
 **/

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}
if (!defined('_CRON_LOT_FICHIERS_VISITE')) {
	define('_CRON_LOT_FICHIERS_VISITE', 100);
}

### Pour se debarrasser du md5, comment faire ? Un index sur 'referer' ?
### ou alors la meme notion, mais sans passer par des fonctions HEX ?


/**
 * Prendre en compte un fichier de visite
 *
 * [MODIF] : prendre en compte n'importe quel objet au lieu des seuls articles
 *
 * @param string $fichier
 *     Nom du fichier de visite
 * @param int $visites
 *     Nombre de visites
 * @param string $visites_objets
 *     [ type d'objet => [ couples id_objet => nombre : comptage par identifiant d'objet ] ]
 * @param array $referers
 *     Couples url_referer => nombre : comptage par url de referer
 * @param string $referers_objets
 *     [ type d'objet => [ couples id_objet => nombre : comptage par identifiant d'objet puis url de referer ] ]
 * @return void
 **/
function compte_fichier_visite($fichier, &$visites, &$visites_objets, &$referers, &$referers_objets) {

	include_spip('base/objets'); // on ne sait jamais
	include_spip('inc/config');

	// Noter la visite du site (article 0)
	$visites++;

	$content = array();
	if (lire_fichier($fichier, $content)) {
		$content = @unserialize($content);
	}
	if (!is_array($content)) {
		return;
	}

	foreach ($content as $source => $num) {
		list($log_type, $log_id_num, $log_referer)
			= preg_split(",\t,", $source, 3);

		// Noter le referer
		if ($log_referer) {
			$log_referer = nettoyer_referer($log_referer);
			if (!isset($referers[$log_referer])) {
				$referers[$log_referer] = 0;
			}
			$referers[$log_referer]++;
		}

		// [MODIF] S'il s'agit d'un objet éditorial configuré, noter ses visites
		if (
			$objet = objet_type($log_type)
			and $table_objet_sql = table_objet_sql($objet)
			and $objets_configures = lire_config('activer_statistiques_objets')
			and in_array($table_objet_sql, $objets_configures)
			and $id_objet = intval($log_id_num)
		) {

			if (!isset($visites_objets[$objet][$id_objet])) {
				$visites_objets[$objet][$id_objet] = 0;
			}
			$visites_objets[$objet][$id_objet]++;
			if ($log_referer) {
				if (!isset($referers_objets[$objet][$id_objet][$log_referer])) {
					$referers_objets[$objet][$id_objet][$log_referer] = 0;
				}
				$referers_objets[$objet][$id_objet][$log_referer]++;
			}
		}

	}
}


/**
 * Calcule les statistiques de visites, en plusieurs étapes
 *
 * @uses compte_fichier_visite()
 * @uses genie_popularite_constantes()
 *
 * @param int $t
 *     Timestamp de la dernière exécution de cette tâche
 * @return null|int
 *     - null si aucune visite à prendre en compte ou si tous les fichiers de visite sont traités,
 *     - entier négatif s'il reste encore des fichiers à traiter
 **/
function calculer_visites($t) {
	include_spip('base/abstract_sql');
	include_spip('base/objets'); // on ne sait jamais

	// Initialisations
	$visites         = array(); # visites du site
	$visites_objets  = array(); # tableau des visites des objets
	$referers        = array(); # referers du site
	$referers_objets = array(); # tableau des referers des objets

	// charger un certain nombre de fichiers de visites,
	// et faire les calculs correspondants

	// Traiter jusqu'a 100 sessions datant d'au moins 30 minutes
	$sessions = preg_files(sous_repertoire(_DIR_TMP, 'visites'));

	$compteur = _CRON_LOT_FICHIERS_VISITE;
	$date_init = time() - 30 * 60;
	foreach ($sessions as $item) {
		if (($d = @filemtime($item)) < $date_init){
			if (!$d) {
				$d = $date_init;
			} // si le fs ne donne pas de date, on prend celle du traitement, mais tout cela risque d'etre bien douteux
			$d = date("Y-m-d", $d);
			spip_log("traite la session $item");
			// [MODIF]
			compte_fichier_visite(
				$item,
				$visites[$d],
				$visites_objets[$d],
				$referers[$d],
				$referers_objets[$d]
			);
			spip_unlink($item);
			if (--$compteur <= 0) {
				break;
			}
		}
		#else spip_log("$item pas vieux");
	}
	if (!count($visites)) {
		return;
	}

	include_spip('genie/popularites');
	list($a, $b) = genie_popularite_constantes(24 * 3600);

	// Maintenant on dispose de plusieurs tableaux qu'il faut ventiler dans
	// les tables spip_visites, spip_visites_articles, spip_referers, spip_referers_articles,
	// spip_visites_objets et spip_referers_objets.
	// Attention a affecter tout ca a la bonne date (celle de la visite, pas celle du traitement)
	foreach (array_keys($visites) as $date) {
		if ($visites[$date]) {

			// 1. les visites du site (facile)
			if (!sql_countsel('spip_visites', "date='$date'")) {
				sql_insertq('spip_visites',
					array('date' => $date, 'visites' => $visites[$date]));
			} else {
				sql_update('spip_visites', array('visites' => "visites+" . intval($visites[$date])), "date='$date'");
			}

			// 2. les visites des objets
			if ($visites_objets[$date]) {
				// insérer la référence dans spip_visites_objets si elle n'existe pas
				// à ce stade le nombre de visites est de 0
				$ar = array();  // tableau nb de visites => listes des objets ayant ce nb de visites
				foreach ($visites_objets[$date] as $objet => $visites_objet ) {
					foreach ($visites_objet as $id_objet => $n) {
						$exp_visite = array('visites' => 0, 'date' => $date);
						// cas des articles
						if ($objet == 'article') {
							$table_visites            = 'spip_visites_articles';
							$where_visite             = 'id_article='.intval($id_objet).' AND date='.sql_quote($date);
							$exp_visite['id_article'] = $id_objet;
						}
						// autres types d'objets
						else {
							$table_visites          = 'spip_visites_objets';
							$where_visite           = 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND date='.sql_quote($date);
							$exp_visite['objet']    = $objet;
							$exp_visite['id_objet'] = $id_objet;
						}
						if (!sql_countsel($table_visites, $where_visite)) {
							sql_insertq($table_visites, $exp_visite);
						}
						$ar[$n][$objet][] = $id_objet;
					}
				}
				// on met à jour le nombre de visites
				foreach ($ar as $n => $liste_objets) {
					foreach ($liste_objets as $objet => $liste) {

						$where = 'date='.sql_quote($date);
						$exp = array('visites' => "visites+$n");
						// cas des articles
						if ($objet == 'article') {
							$table_visites = 'spip_visites_articles';
							$where .= ' AND '.sql_in('id_article', $liste);
						}
						// les autres objets
						else {
							$table_visites = 'spip_visites_objets';
							$where .= ' AND objet='.sql_quote($objet).' AND '.sql_in('id_objet', $liste);
						}
						sql_update($table_visites, $exp, $where);
						$log_update = sql_update($table_visites, $exp, $where, '', '', false);

						$ref = $noref = array();
						foreach ($liste as $id) {
							if (isset($referers_objets[$objet][$id])) {
								$ref[$objet][] = $id;
							} else {
								$noref[$objet][] = $id;
							}
						}

						// il faudrait ponderer la popularite ajoutee ($n) par son anciennete eventuelle
						// sur le modele de ce que fait genie/popularites
						if (count($noref)) {
							foreach ($noref as $objet => $ids){
								$table_objet_sql = table_objet_sql($objet);
								$id_table_objet = id_table_objet($objet);
								$trouver_table = charger_fonction('trouver_table','base');
								$desc = $trouver_table($table_objet_sql);
								$exp = array(
									'visites' => "visites+$n",
									'popularite' => "popularite+" . number_format(round($n * $b, 2), 2, '.', ''),
								);
								if (isset($desc['field']['maj'])) {
									$exp['maj'] = 'maj';
								}
								sql_update(
									$table_objet_sql,
									$exp,
									sql_in($id_table_objet, $ids)
								);
							}
						}

						if (count($ref)) {
							foreach ($noref as $objet => $ids){
								$table_objet_sql = table_objet_sql($objet);
								$id_table_objet  = id_table_objet($objet);
								$trouver_table = charger_fonction('trouver_table','base');
								$desc = $trouver_table($table_objet_sql);
								$exp = array(
									'visites' => "visites+" . ($n + 1),
									'popularite' => "popularite+" . number_format(round($n * $b, 2), 2, '.', ''),
								);
								if (isset($desc['field']['maj'])) {
									$exp['maj'] = 'maj';
								}
								sql_update(
									$table_objet_sql,
									$exp,
									sql_in($id_table_objet, $ids)
								);
							}

						}
						## Ajouter un JOIN sur le statut de l'article ?
					}

				}
			}

			if (!isset($GLOBALS['meta']['activer_referers']) or $GLOBALS['meta']['activer_referers'] == "oui") {
				// 3. Les referers du site
				// insertion pour les nouveaux, au tableau des increments sinon
				if ($referers[$date]) {
					$ar = array();
					$trouver_table = charger_fonction('trouver_table', 'base');
					$desc = $trouver_table('referers');
					$n = preg_match('/(\d+)/', $desc['field']['referer'], $r);
					$n = $n ? $r[1] : 255;
					foreach ($referers[$date] as $referer => $num) {
						$referer_md5 = sql_hex(substr(md5($referer), 0, 15));
						$referer = substr($referer, 0, $n);
						if (!sql_countsel('spip_referers', "referer_md5=$referer_md5")) {
							sql_insertq('spip_referers',
								array(
									'visites' => $num,
									'visites_jour' => $num,
									'visites_veille' => $num,
									'date' => $date,
									'referer' => $referer,
									'referer_md5' => $referer_md5
								));
						} else {
							$ar[$num][] = $referer_md5;
						}
					}

					// appliquer les increments sur les anciens
					// attention on appelle sql_in en mode texte et pas array
					// pour ne pas passer sql_quote() sur les '0x1234' de referer_md5, cf #849
					foreach ($ar as $num => $liste) {
						sql_update('spip_referers', array('visites' => "visites+$num", 'visites_jour' => "visites_jour+$num"),
							sql_in('referer_md5', join(', ', $liste)));
					}
				}

				// 4. Les referers des objets
				if ($referers_objets[$date]) {
					$ar = array();
					$insert = array();
					foreach($referers_objets[$date] as $objet => $referers_objet){
						// s'assurer d'un slot pour chacun
						foreach ($referers_objet as $id_objet => $referers) {
							foreach ($referers as $referer => $num) {
								$referer_md5 = sql_hex(substr(md5($referer), 0, 15));
								$exp_referer   = array(
									'visites'       => $num,
									'referer'       => $referer,
									'referer_md5'   => $referer_md5
								);
								// cas des articles
								if ($objet == 'article'){
									$prim                      = '(id_article='.intval($id_objet).' AND referer_md5='.sql_quote($referer_md5).')';
									$table_referer             = 'spip_referers_articles';
									$exp_referer['id_article'] = $id_objet;
								}
								// autres types d'objets
								else {
									$prim                    = '(objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND referer_md5='.sql_quote($referer_md5).')';
									$table_referer           = 'spip_referers_objets';
									$exp_referer['objet']    = $objet;
									$exp_referer['id_objet'] = $id_objet;
								}
								if (!sql_countsel($table_referer, $prim)) {
									sql_insertq($table_referer, $exp_referer);
								} else {
									$ar[$num][$objet][] = $prim;
								}
							}
						}
						// ajouter les visites
						foreach ($ar as $num => $objets) {
							foreach($objets as $objet => $liste) {
								$exp_referers = array('visites' => "visites+$num");
								if ($objet == 'article') {
									$table_referers = 'spip_referers_articles';
								} else {
									$table_referers = 'spip_referers_objets';
									$exp_referers['objet'] = $objet;
								}
								sql_updateq($table_referers, $exp_referers, join(" OR ", $liste));
								## Ajouter un JOIN sur le statut de l'article ?
							}
						}
					}
				}
			}
		}
	}

	// S'il reste des fichiers a manger, le signaler pour reexecution rapide
	if ($compteur == 0) {
		spip_log("il reste des visites a traiter...");

		return -$t;
	}
}

/**
 * Nettoyer les IPs des flooders 24H apres leur dernier passage
 */
function visites_nettoyer_flood() {
	if (is_dir($dir = _DIR_TMP . 'flood/')) {
		include_spip('inc/invalideur');
		if (!defined('_IP_FLOOD_TTL')) {
			define('_IP_FLOOD_TTL', 24 * 3600);
		} // 24H par defaut
		$options = array(
			'mtime' => $_SERVER['REQUEST_TIME'] - _IP_FLOOD_TTL,
		);
		purger_repertoire($dir, $options);
	}
}

/**
 * Nettoyer les urls en enlevant les variables de personnalisation marketing, ou variantes Amp
 */

function nettoyer_referer($url){

	// &utm_xxx=
	$url = preg_replace("`[?&]utm_.*$`","",$url);

	// &fbclid=
	$url = preg_replace("`[?&]fbclid.*$`","",$url);

	// &amp=1
	$url = preg_replace("`[?&]amp=1$`","",$url);

	return $url ;
}

/**
 * Cron de calcul de statistiques des visites
 *
 * Calcule les stats en plusieurs étapes
 *
 * @uses calculer_visites()
 *
 * @param int $t
 *     Timestamp de la dernière exécution de cette tâche
 * @return int
 *     Positif si la tâche a été terminée, négatif pour réexécuter cette tâche
 **/
function genie_visites_dist($t) {
	$encore = calculer_visites($t);

	// Si ce n'est pas fini on redonne la meme date au fichier .lock
	// pour etre prioritaire lors du cron suivant
	if ($encore) {
		return (0 - $t);
	}

	// nettoyer les IP des floodeurs quand on a fini de compter les stats
	visites_nettoyer_flood();

	return 1;
}
