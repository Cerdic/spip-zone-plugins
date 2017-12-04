<?php
/**
 * Plugin LinkCheck
 * (c) 2013-2017 Benjamin Grapeloux, Guillaume Wauquier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter aprés l'ajout ou la modification d'un objet, enregistre les
 * nouveaux liens, efface les anciens et programme une vérification de
 * ces liens
 *
 * @param array $flux
 * @return array
 */
function linkcheck_post_edition($flux) {
	$objet = false;
	/**
	 * Le pipeline d'institution ne passe pas type
	 */
	if (isset($flux['args']['type'])) {
		$objet = $flux['args']['type'];
	} elseif ($flux['args']['action'] == 'instituer'
		and isset($flux['data']['statut'])
		and isset($flux['args']['table'])) {
		$objet = objet_type($flux['args']['table']);
	}

	//on verifie que l'on est bien dans un contexte de verification d'objet
	if ($flux['args']['id_objet'] && $objet) {
		include_spip('inc/linkcheck_fcts');
		include_spip('inc/linkcheck_vars');
		$champs_a_traiter = false;
		$id_objet = intval($flux['args']['id_objet']);
		$table_sql = table_objet_sql($objet);
		$id_table_objet = id_table_objet($objet);
		$tables_a_traiter = linkcheck_tables_a_traiter();
		foreach ($tables_a_traiter as $table) {
			foreach ($table as $table_sql_def => $info_table) {
				if ($table_sql_def == $table_sql) {
					$champs_a_traiter = linkcheck_champs_a_traiter($info_table);
					break;
				}
			}

			if (is_array($champs_a_traiter)) {
				break;
			}
		}
		/**
		 * On ne comptabilise les liens que sur les objets prévisualisables
		 *
		 * Ici on vérifie si le statut de l'objet est prévisualisable
		 */
		$fields = objet_info($objet, 'field');
		$not_statuts = array();
		if (isset($fields['statut'])) {
			$info_statuts = objet_info($objet, 'statut');
			$statuts = array();
			if (isset($info_statuts[0]['previsu'])) {
				$statuts = explode(',', str_replace('/auteur', '', $info_statuts[0]['previsu']));
				foreach ($statuts as $key => $val) {
					if ($val == '!') {
						unset($statuts[$key]);
					}
				}
			} else {
				$not_statuts = array('refuse', 'poubelle');
			}
			$statut = sql_getfetsel('statut', $table_sql, $id_table_objet.'='.intval($id_objet));
			if (count($statuts) > 0 && in_array($statut, $statuts)) {
				$objet_ok = true;
			} elseif (count($statuts) == 0 && !in_array($statut, $not_statuts)) {
				$objet_ok = true;
			} else {
				$objet_ok = false;
			}
		} else {
			$objet_ok = true;
		}
		/**
		 * L'objet n'a plus de statut prévisualisable donc ses liens ne sont plus pris en compte
		 * On les supprime et si le lien n'est plus utilisé sur d'autres objets, il est également supprimé
		 */
		if (!$objet_ok) {
			$liens_lies = sql_allfetsel(
				'id_linkcheck',
				'spip_linkchecks_liens',
				'id_objet='.intval($id_objet).' AND objet='.sql_quote($objet)
			);
			foreach ($liens_lies as $lien) {
				sql_delete(
					'spip_linkchecks_liens',
					'id_linkcheck='.intval($lien['id_linkcheck']).'
						AND id_objet='.intval($id_objet).'
						AND objet='.sql_quote($objet)
				);
				if (!sql_countsel('spip_linkchecks_liens', 'id_linkcheck='.intval($lien['id_linkcheck']))) {
					sql_delete('spip_linkchecks', 'id_linkcheck='.intval($lien['id_linkcheck']));
				}
			}
			return $flux;
		}
		if (is_array($champs_a_traiter) && count($champs_a_traiter)) {
			$tab_value = sql_fetsel(array_keys($champs_a_traiter), $table_sql, $id_table_objet.'='.intval($id_objet));
			foreach (array_keys($champs_a_traiter) as $ct) {
				if (isset($flux['data'][$ct])) {
					$tab_value[$ct]=$flux['data'][$ct];
				}
			}
			//on parcours les liens
			$tab_liens = linkcheck_lister_liens($tab_value);

			// On regarde si l'objet parent est publie
			$objet_publie = objet_test_si_publie($objet, $id_objet);

			if ($objet_publie) {
				$publie = 'oui';
			} else {
				$publie = 'non';
			}
			//on les insère en base si besoin
			linkcheck_ajouter_liens($tab_liens, $objet, $id_objet, $publie);

			// maintenant on vérifie que tous les liens de la base correspondant à cet objet
			// soient encore présent dans l'objet
			// on recup tout les liens de l'article presents en base
			$sel = sql_allfetsel(
				'l.url, l.id_linkcheck',
				'spip_linkchecks_liens AS ll, spip_linkchecks AS l',
				'l.id_linkcheck=ll.id_linkcheck AND id_objet='.intval($id_objet).' AND ll.objet='.sql_quote($objet)
			);

			//pour chaque liens
			foreach ($sel as $lks) {
				//si il n'est plus ds l'article
				if (!in_array($lks['url'], $tab_liens)) {
					//on supprime son entrée ds la table de liaison
					sql_delete(
						'spip_linkchecks_liens',
						'id_linkcheck='.intval($lks['id_linkcheck']).' AND id_objet='.intval($id_objet).'
							AND objet='.sql_quote($objet)
					);

					//on regarde s'il est utilisé ailleurs ds le site
					$tpl = sql_getfetsel(
						'count(*)',
						'spip_linkchecks_liens',
						'id_linkcheck='.intval($lks['id_linkcheck'])
					);
					//s'il ne l'est pas
					if ($tpl == 0) {
						//on le supprime de la table liens
						sql_delete('spip_linkchecks', 'id_linkcheck='.intval($lks['id_linkcheck']));
					}
				}
			}
			include_spip('inc/queue');
			queue_add_job(
				'genie_linkcheck_test_postedition',
				'Tests post_edition des liens d\'un objet',
				array($id_objet, $objet),
				'genie/linkcheck_test_postedition',
				true,
				0,
				-5
			);
		}
	}

	return $flux;
}


/**
 * Pipeline qui ajoute des taches automatiques
 *
 * @param array $taches
 * @return $taches
 */
function linkcheck_taches_generales_cron($taches) {
	$taches['linkcheck_tests_ok'] = 2*24*3600; // tous les 2 jours
	$taches['linkcheck_tests_vide'] = 12*3600; // toutes les 12 heures //on test ceux qui n'ont pas d'état
	$taches['linkcheck_tests_mort'] = 7*24*3600; // toutes les semaines
	$taches['linkcheck_tests_malade'] = 24*3600; // tous les jours
	$taches['linkcheck_tests_deplace'] = 3.5*24*3600; // 2 fois par semaine
	$taches['linkcheck_mail'] = 24*3600; // tous les jours
	return $taches;
}

/**
 * Pipeline qui affiche des alertes au webmestre du site, pour l'informer et
 * l'inciter à corriger les liens défectueux du site
 *
 * On n'affiche le message que lorsqu'il y a au moins un lien mort ou malade dans le site
 *
 * @param array $flux
 * @return array
 */
function linkcheck_alertes_auteur($flux) {
	include_spip('inc/config');
	if (lire_config('linkcheck/afficher_alerte')) {
		include_spip('inc/autoriser');
		if (autoriser('voir', 'linkchecks')) {
			include_spip('inc/linkcheck_fcts');
			$res = sql_getfetsel('id_linkcheck', 'spip_linkchecks', sql_in('etat', array('mort', 'malade')));
			if ($res > 0) {
				$comptes = linkcheck_chiffre();
				$texte = _T(
					'linkcheck:liens_invalides',
					array(
						'mort' => (isset($comptes['nb_lien_mort']) ? $comptes['nb_lien_mort'] : '0'),
						'malade' => (isset($comptes['nb_lien_malade']) ? $comptes['nb_lien_malade'] : '0'),
						'deplace' => (isset($comptes['nb_lien_deplace']) ? $comptes['nb_lien_deplace'] : '0')
					)
				);
				$flux['data'][] = $texte." <a href='" . generer_url_ecrire('linkchecks') . "'>"._T('linkcheck:linkcheck').'</a>';
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline affiche_gauche (SPIP)
 * Affiche la liste des liens contenus dans l'objet
 *
 * @pipeline affiche_gauche
 * @param array $flux
 * @return array
 */
function linkcheck_affiche_gauche($flux) {
	include_spip('inc/autoriser');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	if (is_array($e) && !$e['edition']) {
		include_spip('inc/linkcheck_vars');
		$tab_type_objets = linkcheck_tables_a_traiter();
		foreach ($tab_type_objets as $objet) {
			$_objet = array_shift($objet);
			if (autoriser('modifier', $e['type'], $flux['args'][$e['id_table_objet']]) && ((isset($_objet['page']) && $e['type'] == $_objet['page']) || $e['type'] == $_objet['type'])) {
				$texte .= recuperer_fond('prive/objets/liste/linkchecks_lies', array(
					'objet_source' => 'linkcheck',
					'objet' => $e['type'],
					'id_objet' => $flux['args'][$e['id_table_objet']]
				));
				if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
					$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
				} else {
					$flux['data'] .= $texte;
				}
				break;
			}
		}
	}

	return $flux;
}

/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param array $flux
 * @return array
 */
function linkcheck_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('linkcheck'=>'*'), '*');
	return $flux;
}
