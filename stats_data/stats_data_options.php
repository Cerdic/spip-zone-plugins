<?php

function genie_popularites($t) {
	
	include_spip('genie/popularites'); // ?
	// Si c'est le premier appel, ne pas calculer
	$t = $GLOBALS['meta']['date_popularites'];
	ecrire_meta('date_popularites', time());

	if (!$t) {
		return 1;
	}

	$duree = time() - $t;
	list($a, $b) = genie_popularite_constantes($duree);

	// du passe, faisons table (SQL) rase
	sql_update('spip_articles', array('maj' => 'maj', 'popularite' => "popularite * $a"), 'popularite>1');

	// enregistrer les metas...
	$row = sql_fetsel('MAX(popularite) AS max, SUM(popularite) AS tot', "spip_articles");
	ecrire_meta("popularite_max", $row['max']);
	ecrire_meta("popularite_total", $row['tot']);

	// Une fois par jour purger les referers du jour ; qui deviennent
	// donc ceux de la veille ; au passage on stocke une date_statistiques
	// dans spip_meta - cela permet au code d'etre "reentrant", ie ce cron
	// peut etre appele par deux bases SPIP ne partageant pas le meme
	// _DIR_TMP, sans tout casser...

	$aujourdhui = date("Y-m-d");
	
	spip_log("cron pop de " . date("Y-m-d H:i:s", $t) , "debug_stats.4");
	
	if (($d = $GLOBALS['meta']['date_statistiques']) != $aujourdhui) {
		
		spip_log("Popularite: purger referer depuis $d", "debug_stats.4");
		ecrire_meta('date_statistiques', $aujourdhui);
		if (strncmp($GLOBALS['connexions'][0]['type'], 'sqlite', 6) == 0) {
			spip_query("UPDATE spip_referers SET visites_veille=visites_jour, visites_jour=0");
		} else
			// version 3 fois plus rapide, mais en 2 requetes
			#spip_query("ALTER TABLE spip_referers CHANGE visites_jour visites_veille INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',CHANGE visites_veille visites_jour INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
			#spip_query("UPDATE spip_referers SET visites_jour=0");
			// version 4 fois plus rapide que la premiere, en une seule requete
			// ATTENTION : peut poser probleme cf https://core.spip.net/issues/2505
		{
			sql_alter("TABLE spip_referers DROP visites_veille,
			CHANGE visites_jour visites_veille INT(10) UNSIGNED NOT NULL DEFAULT '0',
			ADD visites_jour INT(10) UNSIGNED NOT NULL DEFAULT '0'");
		}
		
		spip_log("Popularite: purger referers_articles depuis $d", "debug_stats.4");
		if (strncmp($GLOBALS['connexions'][0]['type'], 'sqlite', 6) == 0) {
			spip_query("UPDATE spip_referers_articles SET visites_veille=visites_jour, visites_jour=0");
		} else
			// version 3 fois plus rapide, mais en 2 requetes
			#spip_query("ALTER TABLE spip_referers CHANGE visites_jour visites_veille INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',CHANGE visites_veille visites_jour INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
			#spip_query("UPDATE spip_referers SET visites_jour=0");
			// version 4 fois plus rapide que la premiere, en une seule requete
			// ATTENTION : peut poser probleme cf https://core.spip.net/issues/2505
		{
			sql_alter("TABLE spip_referers_articles DROP visites_veille,
			CHANGE visites_jour visites_veille INT(10) UNSIGNED NOT NULL DEFAULT '0',
			ADD visites_jour INT(10) UNSIGNED NOT NULL DEFAULT '0'");
		}
	}

	// et c'est fini pour cette fois-ci
	return 1;

}

// surcharge du cron de calcul des visites pour ajoute rles visites_jour et visites_veilles sur les spip_referers_articles

function genie_visites($t) {
	
	include_spip('genie/visites'); // ?
	//var_dump("hop");
	//die();
	
	$encore = calculer_visites2($t);
	
	// Si ce n'est pas fini on redonne la meme date au fichier .lock
	// pour etre prioritaire lors du cron suivant
	if ($encore) {
		return (0 - $t);
	}
	
	// SPIP 3
	// nettoyer les IP des floodeurs quand on a fini de compter les stats
	if(function_exists('visites_nettoyer_flood'))
		visites_nettoyer_flood();
	
	return 1;
}

/**
 * Calcule les statistiques de visites sur le site et les articles et les liens entrants, en plusieurs étapes
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
function calculer_visites2($t) {
	include_spip('base/abstract_sql');
	
	// spip_log("calculer visites 2 OK " . date("Y-m-d H:i:s", $t) , "debug_stats.4");
	
	// Initialisations
	$visites = array(); # visites du site
	$visites_a = array(); # tableau des visites des articles
	$referers = array(); # referers du site
	$referers_a = array(); # tableau des referers des articles

	// charger un certain nombre de fichiers de visites,
	// et faire les calculs correspondants

	// Traiter jusqu'a 100 sessions datant d'au moins 30 minutes
	$sessions = preg_files(sous_repertoire(_DIR_TMP, 'visites'));

	$compteur = _CRON_LOT_FICHIERS_VISITE;
	$date_init = time() - 30 * 60;
	
	foreach ($sessions as $item) {
		if (($d = @filemtime($item)) < $date_init) {
			if (!$d) {
				$d = $date_init;
			} // si le fs ne donne pas de date, on prend celle du traitement, mais tout cela risque d'etre bien douteux
			$d = date("Y-m-d", $d);
			spip_log("traite la session $item");
			compte_fichier_visite($item,
				$visites[$d], $visites_a[$d], $referers[$d], $referers_a[$d]);
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
	// les tables spip_visites, spip_visites_articles, spip_referers
	// et spip_referers_articles ; attention a affecter tout ca a la bonne
	// date (celle de la visite, pas celle du traitement)
	foreach (array_keys($visites) as $date) {
		if ($visites[$date]) {
			
			// 1. les visites du site (facile)
			if (!sql_countsel('spip_visites', "date='$date'")) {
				sql_insertq('spip_visites',
					array('date' => $date, 'visites' => $visites[$date]));
			} else {
				sql_update('spip_visites', array('visites' => "visites+" . intval($visites[$date])), "date='$date'");
			}

			// 2. les visites des articles 
			if ($visites_a[$date]) {
				$ar = array();  # tableau num -> liste des articles ayant num visites
				foreach ($visites_a[$date] as $id_article => $n) {
					if (!sql_countsel('spip_visites_articles',
						"id_article=$id_article AND date='$date'")
					) {
						sql_insertq('spip_visites_articles',
							array(
								'id_article' => $id_article,
								'visites' => 0,
								'date' => $date
							));
					}
					$ar[$n][] = $id_article;
				}
				foreach ($ar as $n => $liste) {
					$tous = sql_in('id_article', $liste);
					sql_update('spip_visites_articles',
						array('visites' => "visites+$n"),
						"date='$date' AND $tous");

					$ref = $noref = array();
					foreach ($liste as $id) {
						if (isset($referers_a[$id])) {
							$ref[] = $id;
						} else {
							$noref[] = $id;
						}
					}
					// il faudrait ponderer la popularite ajoutee ($n) par son anciennete eventuelle
					// sur le modele de ce que fait genie/popularites
					if (count($noref)) {
						sql_update('spip_articles',
							array(
								'visites' => "visites+$n",
								'popularite' => "popularite+" . number_format(round($n * $b, 2), 2, '.', ''),
								'maj' => 'maj'
							),
							sql_in('id_article', $noref));
					}

					if (count($ref)) {
						sql_update('spip_articles',
							array(
								'visites' => "visites+" . ($n + 1),
								'popularite' => "popularite+" . number_format(round($n * $b, 2), 2, '.', ''),
								'maj' => 'maj'
							),
							sql_in('id_article', $ref));
					}

					## Ajouter un JOIN sur le statut de l'article ?
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
									'visites_veille' => 0, // $num ??
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

				// 4. Les referers d'articles
				if ($referers_a[$date]) {
					$ar = array();
					$insert = array();
					
					// s'assurer d'un slot pour chacun
					foreach ($referers_a[$date] as $id_article => $referers) {
						foreach ($referers as $referer => $num) {
							$referer_md5 = sql_hex(substr(md5($referer), 0, 15));
							$prim = "(id_article=$id_article AND referer_md5=$referer_md5)";
							if (!sql_countsel('spip_referers_articles', $prim)) {
								sql_insertq('spip_referers_articles',
									array(
										'visites' => $num,
										'id_article' => $id_article,
										'referer' => $referer,
										'referer_md5' => $referer_md5,
										'visites_jour' => $num,
										'visites_veille' => 0 // $num ?
									));
							} else {
								$ar[$num][] = $prim;
							}
						}
					}
					// ajouter les visites
					foreach ($ar as $num => $liste) {
						sql_update('spip_referers_articles', array('visites' => "visites+$num", 'visites_jour' => "visites_jour+$num"), join(" OR ", $liste));
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
