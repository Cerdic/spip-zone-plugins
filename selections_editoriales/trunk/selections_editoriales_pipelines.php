<?php
/**
 * Utilisations de pipelines par Sélections éditoriales
 *
 * @plugin     Sélections éditoriales
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Selections_editoriales\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Pas de logo de survol pour les contenus sélectionés
 *
 * @pipeline formulaire_charger
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_formulaire_charger($flux) {
	if (
		$flux['args']['form'] == 'editer_logo'
		and $flux['args']['args'][0] == 'selections_contenu'
	) {
		$flux['data']['logo_survol'] = false;
		$flux['data']['logo_off'] = false;
	}

	return $flux;
}


/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 *
 * Dans le formulaire editer_liens, recharger le bloc selections au complet
 * à l'ajout et suppression de sélections éditoriales
 *
 * On est obligé de désactiver le filtre js temporairement pour afficher le message js de
 * rechargement
 *
 * @pipeline formulaire_charger
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_formulaire_traiter($flux) {
	if (
		$flux['args']['form'] == 'editer_liens'
		and $flux['args']['args'][0] == 'selections'
		and (_request('ajouter_lien') || _request('supprimer_lien'))
	) {
		$GLOBALS['filtrer_javascript'] = 1;
		$flux['message_ok'] .= '<script type="text/javascript">if (window.jQuery) ajaxReload("selections");</script>';
	}

	return $flux;
}
/**
 * Insérer du JS à la fin du traiter
 *
 * @pipeline formulaire_fond
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_formulaire_fond($flux) {
	if (
		$flux['args']['form'] == 'editer_logo'
		and $flux['args']['args'][0] == 'selections_contenu'
		and $id_selections_contenu = intval($flux['args']['args'][1])
		and $flux['args']['je_suis_poste']
	) {
		// On cherche la sélection parente
		$id_selection = intval(sql_getfetsel(
			'id_selection',
			'spip_selections_contenus',
			'id_selections_contenu = '.$id_selections_contenu
		));
		// Animation de ce qu'on vient de modifier
		$callback = "jQuery('#selection$id_selection-contenu$id_selections_contenu').animateAppend();";
		// Rechargement du conteneur de la sélection
		$js = "if (window.jQuery) jQuery(function(){ajaxReload('selection$id_selection', {args:{editer_contenu_logo:'non', time:'".time()."'}, callback:function(){ $callback }});});";
		$js = "<script type='text/javascript'>$js</script>";
		$flux['data'] .= $js;
	}

	return $flux;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les selections
	if (!$e['edition'] and in_array($e['type'], array('selection'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

/**
 * Ajout d'un bouton de suppression si vide
 *
 * @pipeline boite_infos
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_boite_infos($flux) {
	if (
		$flux['args']['type'] == 'selection'
		and $id_selection = intval($flux['args']['id'])
		and include_spip('inc/autoriser')
		and autoriser('supprimer', 'selection', $id_selection)
		and include_spip('inc/filtres')
		and include_spip('inc/actions')
	) {
		$flux['data'] .= filtrer(
			'bouton_action_horizontal',
			generer_action_auteur('supprimer_selection', $id_selection, generer_url_ecrire('selections')),
			_T('lien_supprimer'),
			'selection-24.png',
			'del',
			'link'
		);
	}

	return $flux;
}

/**
 * Ajoute des sélections sous les objets configurés pour ça
 *
 *
 * @pipeline afficher_complement_objet
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_afficher_complement_objet($flux) {
	if (version_compare($GLOBALS['spip_version_branche'], '3.2.1', '<')) {
		$exec = trouver_objet_exec($flux['args']['type']);
		$id = intval($flux['args']['id']);

		if (
			$exec !== false // page d'un objet éditorial
			and $exec['edition'] === false // pas en mode édition
			and $type = $exec['type']
			and (
				autoriser('associerselections', $type, $id)
				and (
					autoriser('creer', 'selection')
					or autoriser('modifier', 'selection')
				)
			)
		) {
			$selections = recuperer_fond(
				'prive/squelettes/inclure/selections_objet',
				array(
					'objet' => $type,
					'id_objet' => $id,
					'editer_contenu' => _request('editer_contenu'),
					'editer_contenu_logo' => _request('editer_contenu_logo'),
					'ajouter' => _request('ajouter'),
					'id_selection_ajoutee' => _request('id_selection_ajoutee'),
				),
				array('ajax'=>'selections')
			);
			
			$flux['data'] .= $selections;
		}
	}
	
	return $flux;
}

/**
 * Ajoute des sélections sous les objets configurés pour ça, dans les enfants >= 3.2.1
 *
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_affiche_enfants($flux) {
	if (version_compare($GLOBALS['spip_version_branche'], '3.2.1', '>=')) {
		if (isset($flux['args']['objet'])) {
			$exec = trouver_objet_exec($flux['args']['objet']);
			$id = intval($flux['args']['id_objet']);

			if (
				$exec !== false // page d'un objet éditorial
				and $exec['edition'] === false // pas en mode édition
				and $type = $exec['type']
				and (
					autoriser('associerselections', $type, $id)
					and (
						autoriser('creer', 'selection')
						or autoriser('modifier', 'selection')
					)
				)
			) {
				$selections = recuperer_fond(
					'prive/squelettes/inclure/selections_objet',
					array(
						'objet' => $type,
						'id_objet' => $id,
						'editer_contenu' => _request('editer_contenu'),
						'editer_contenu_logo' => _request('editer_contenu_logo'),
						'ajouter' => _request('ajouter'),
						'id_selection_ajoutee' => _request('id_selection_ajoutee'),
					),
					array('ajax'=>'selections')
				);

				$flux['data'] .= $selections;
			}
		}
	}
	
	return $flux;
}

/**
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function selections_editoriales_optimiser_base_disparus($flux) {
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('selection' => '*'), '*');
	return $flux;
}

/**
 * Pipeline jqueryui_plugins pour demander au plugin l'insertion des scripts pour .sortable()
 *
 * @param array $plugins
 * @return array
 */
function selections_editoriales_jqueryui_plugins($plugins) {
	// On envoie que si on est dans l'espace prive
	if (test_espace_prive()) {
		$plugins[] = 'jquery.ui.core';
		$plugins[] = 'jquery.ui.widget';
		$plugins[] = 'jquery.ui.mouse';
		$plugins[] = 'jquery.ui.sortable';
		$plugins[] = 'jquery.ui.droppable';
		$plugins[] = 'jquery.ui.draggable';
	}

	return $plugins;
}

/**
 * Pipeline chercher_logo pour trouver le logo du contenu choisi,
 * si jamais ya pas de logo pour l'objet selections_cotenu
 **/
function selections_editoriales_quete_logo_objet($flux) {
	// Si personne n'a trouvé de logo avant
	if (
		empty($flux['data'])
		and $flux['args']['objet'] == 'selections_contenu'
		and $selections_contenu = sql_fetsel(
			'objet, id_objet',
			'spip_selections_contenus',
			'id_selections_contenu = '.intval($flux['args']['id_objet'])
		) and $objet = $selections_contenu['objet']
		and ($id_objet = intval($selections_contenu['id_objet'])) > 0
	) {
		$flux['data'] = quete_logo_objet($id_objet, $objet, $flux['args']['mode']);
	}

	return $flux;
}

/**
 * Met à jour les liens de selections après l'édition d'un objet
 *
 * @pipeline post_edition
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 **/
function selections_editoriales_post_edition($flux) {
	if (isset($flux['args']['table']) and $flux['args']['table'] !== 'spip_selections') {
		// le serveur n'est pas toujours la
		$serveur = (isset($flux['args']['serveur']) ? $flux['args']['serveur'] : '');
		$type = objet_type($flux['args']['table']);
		$marquer_doublons_selection = charger_fonction('marquer_doublons_selection', 'inc');
		$marquer_doublons_selection(
			$flux['data'],
			$flux['args']['id_objet'],
			$type,
			id_table_objet($type, $serveur),
			table_objet($type, $serveur),
			$flux['args']['table'],
			'',
			$serveur
		);
	}

	return $flux;
}

/**
 * Affiche le nombre d'utilisation d'une sélection sur sa page
 *
 * @param array $flux
 *    Données du pipeline
 * @return array
 */
function selections_editoriales_afficher_config_objet($flux) {
	if ($flux['args']['type'] == 'selection') {
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/selection_infos', array('id_selection' => $flux['args']['id']));
	}
	return $flux;
}
