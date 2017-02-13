<?php
/**
 * Vérifier si des articles ont été mis à jour et les enregistrer en alertes
 *
 * @plugin     Alertes
 * @copyright  2016-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/Alertes/Genie
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_alertes_maj_art_dist($time) {
	$message = array();

	$message[] = "\n----------\nLancement du cron " . __FUNCTION__;
	include_spip('base/abstract_sql');
	include_spip('inc/config');
	$config = lire_config('config_alertes');
	$message[] = "activer_alertes : " . print_r($config['activer_alertes'], true);
	$message[] = "activer_alertes_articles : " . print_r($config['activer_alertes_articles'], true);
	$message[] = "rubriques : " . print_r($config['rubriques'], true);
	$message[] = "secteurs : " . print_r($config['secteurs'], true);
	if ($config['activer_alertes'] === 'oui' and $config['activer_alertes_articles'] === 'oui') {
		/**
		 * On s'occupe des rubriques abonnées
		 */
		if (isset($config['rubriques']) and !empty(trim($config['rubriques']))) {
			$articles = sql_allfetsel('id_article,date,date_modif,id_rubrique,id_secteur', 'spip_articles',
				'date < date_modif AND id_rubrique IN (' . $config['rubriques'] . ')');
			$alertes_rubriques = sql_allfetsel('id_auteur', 'spip_alertes',
				"objet='rubrique' AND id_objet IN (" . sql_quote($config['rubriques']) . ')');
			if ((is_array($articles) and count($articles)) and (is_array($alertes_rubriques) and count($alertes_rubriques))) {
				foreach ($articles as $article) {
					$art_modif = sql_fetsel('id_article,date', 'spip_alertes_articles',
						'id_article=' . $article['id_article']);
					// On a bien des articles, alors on analyse tout ça.
					if (is_array($art_modif) and count($art_modif)) {
						if (date_format(date_create($art_modif['date']), 'YmdHi') < date_format(date_create($article['date_modif']), 'YmdHi')) {
							// On met à jour les données dans spip_alertes_articles car l'article a été modifié
							sql_updateq('spip_alertes_articles',
								array('id_article' => $article['id_article'], 'date' => date_format(date_create($article['date_modif']), 'Y-m-d H:i:s')),
								"id_article=" . $article['id_article']);
							// On insert l'article dans les alertes des abonnés
							foreach ($alertes_rubriques as $auteur) {
								sql_insertq('spip_alertes_cron', array(
									'id_auteur' => $auteur['id_auteur'],
									'id_objet' => $article['id_article'],
									'objet' => 'article',
									'date_pour_envoi' => date_format(date_create(), 'Y-m-d H:i:s'),
								));
							}
							$message[] = "L'article #" . $article['id_article'] . " a été mis à jour.";
						} else {
							$message[] = "L'article #" . $article['id_article'] . " n'a pas été remis à jour";
						}
					} else {
						sql_insertq('spip_alertes_articles',
							array('id_article' => $article['id_article'], 'date' => $article['date_modif']));
						$message[] = "L'article " . $article['id_article'] . " a été ajouté à spip_alertes_articles et aux alertes des auteurs abonnés.";
						foreach ($alertes_rubriques as $auteur) {
							sql_insertq('spip_alertes_cron', array(
								'id_auteur' => $auteur['id_auteur'],
								'id_objet' => $article['id_article'],
								'objet' => 'article',
								'date_pour_envoi' => date_format(date_create(), 'Y-m-d H:i:s'),
							));
						}
					}
				}
			} else {
				$message[] = "Aucun article n'a été ajouté à spip_alertes_articles.";
				$message[] = count($articles) . " articles ont été mis à jour après leur publication.";
				if (!count($alertes_rubriques)) {
					$message[] = "Aucun abonné au(x) rubrique(s) #" . $config['rubriques'];
				} else {
					$message[] = "spip_alertes des rubriques " . print_r($config['rubriques'], true);
					$message[] = print_r($alertes_rubriques, true);
				}
			}
		}
		/**
		 * On s'occupe des secteurs abonnés
		 */
		if (isset($config['secteurs']) and !empty(trim($config['secteurs']))) {
			$articles = sql_allfetsel('id_article,date,date_modif,id_rubrique,id_secteur', 'spip_articles',
				'date < date_modif AND id_secteur IN (' . $config['secteurs'] . ')');
			$alertes_secteurs = sql_allfetsel('id_auteur', 'spip_alertes',
				"objet='secteur' AND id_objet IN (" . sql_quote($config['secteurs']) . ')');
			if ((is_array($articles) and count($articles)) and (is_array($alertes_secteurs) and count($alertes_secteurs))) {
				foreach ($articles as $article) {
					$art_modif = sql_fetsel('id_article,date', 'spip_alertes_articles',
						'id_article=' . $article['id_article']);
					// On a bien des articles, alors on analyse tout ça.
					if (is_array($art_modif) and count($art_modif)) {
						if (date_format(date_create($art_modif['date']), 'YmdHi') < date_format(date_create($article['date_modif']), 'YmdHi')) {
							// On met à jour les données dans spip_alertes_articles car l'article a été modifié
							sql_updateq('spip_alertes_articles',
								array('id_article' => $article['id_article'], 'date' => date_format(date_create($article['date_modif']), 'Y-m-d H:i:s')),
								"id_article=" . $article['id_article']);
							// On insert l'article dans les alertes des abonnés
							foreach ($alertes_secteurs as $auteur) {
								sql_insertq('spip_alertes_cron', array(
									'id_auteur' => $auteur['id_auteur'],
									'id_objet' => $article['id_article'],
									'objet' => 'article',
									'date_pour_envoi' => date_format(date_create(), 'Y-m-d H:i:s'),
								));
							}
							$message[] = "L'article #" . $article['id_article'] . " a été mis à jour.";
						} else {
							$message[] = "L'article #" . $article['id_article'] . " n'a pas été remis à jour";
						}
					} else {
						sql_insertq('spip_alertes_articles',
							array('id_article' => $article['id_article'], 'date' => $article['date_modif']));
						$message[] = "L'article " . $article['id_article'] . " a été ajouté à spip_alertes_articles et aux alertes des auteurs abonnés.";
						foreach ($alertes_secteurs as $auteur) {
							sql_insertq('spip_alertes_cron', array(
								'id_auteur' => $auteur['id_auteur'],
								'id_objet' => $article['id_article'],
								'objet' => 'article',
								'date_pour_envoi' => date_format(date_create(), 'Y-m-d H:i:s'),
							));
						}
					}
				}
			} else {
				$message[] = "Aucun article n'a été ajouté à spip_alertes_articles.";
				$message[] = count($articles) . " articles ont été mis à jour après leur publication.";
				if (!count($alertes_secteurs)) {
					$message[] = "Aucun abonné au(x) secteur(s) #" . $config['secteurs'];
				} else {
					$message[] = "spip_alertes des secteurs " . print_r($config['secteurs'], true);
					$message[] = print_r($alertes_secteurs, true);
				}
			}
		}
		$message[] = "----------\n";
		spip_log(implode("\n", $message), 'alertes');
	}

	return $time;
}