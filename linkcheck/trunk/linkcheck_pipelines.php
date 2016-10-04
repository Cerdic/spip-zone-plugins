<?php
/**
 * Plugin LinkCheck
 * (c) 2013-2015 Benjamin Grapeloux, Guillaume Wauquier
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

	include_spip('inc/linkcheck_fcts');
	include_spip('inc/linkcheck_vars');
	include_spip('inc/queue');

	//on verifie que l'on est bien dans un contexte de verification d'objet
	if ($flux['args']['id_objet'] && $flux['args']['type']) {
		$champs_a_traiter = false;
		$type_objet = $flux['args']['type'];
		$id_objet = intval($flux['args']['id_objet']);
		$table_sql = table_objet_sql($type_objet);

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
		if (is_array($champs_a_traiter)) {
			$tab_value=array();
			foreach (array_keys($champs_a_traiter) as $ct) {
				if (isset($flux['data'][$ct])) {
					$tab_value[$ct]=$flux['data'][$ct];
				}
			}

			//on parcours les liens
			$tab_liens = linkcheck_lister_liens($tab_value);

			//on les insère en base si besoin
			linkcheck_ajouter_liens($tab_liens, $type_objet, $id_objet);

			// maintenant on vérifie que tous les liens de la base correspondant à cet objet
			// soient encore présent dans l'objet
			// on recup tout les liens de l'article presents en base
			$sel = sql_allfetsel(
				'l.url, l.id_linkcheck',
				'spip_linkchecks_liens AS ll, spip_linkchecks AS l',
				'l.id_linkcheck=ll.id_linkcheck AND id_objet='.$id_objet.' AND ll.objet='.sql_quote($type_objet)
			);

			//pour chaque liens
			foreach ($sel as $lks) {
				//si il n'est plus ds l'article
				if (!in_array($lks['url'], $tab_liens)) {
					//on supprime son entrée ds la table de liaison
					sql_delete(
						'spip_linkchecks_liens',
						'id_linkcheck='.intval($lks['id_linkcheck']).' AND id_objet='.intval($id_objet).'
							AND objet='.sql_quote($type_objet)
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

			queue_add_job(
				'genie_linkcheck_test_postedition',
				'Tests post_edition des liens d\'un objet',
				array($id_objet, $type_objet),
				'genie/linkcheck_test_postedition'
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
 * Pipeline qui des alertes au webmestre du site, pour l'informer et
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
		if (autoriser('webmestre', $flux['args']['id_auteur'])) {
			include_spip('inc/linkcheck_fcts');
			$res = sql_getfetsel('id_linkcheck', 'spip_linkchecks', sql_in('etat', array('mort', 'malade')));
			if ($res>0) {
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
 * Pipeline qui des alertes au webmestre du site, pour l'informer et
 * l'insiter à corriger les liens défectueux du site
 *
 * @param array $flux
 * @return array
 */
function linkcheck_affiche_milieu($flux) {
	include_spip('inc/linkcheck_vars');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	$tab_type_objets=linkcheck_tables_a_traiter();
	if (is_array($e) && !$e['edition']) {
		foreach ($tab_type_objets as $objet) {
			$_objet = array_shift($objet);
			if ((isset($_objet['page']) && $e['type'] == $_objet['page']) || $e['type'] == $_objet['type']) {
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
