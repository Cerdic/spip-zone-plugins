<?php
/**
 * Utilisations de pipelines par Bouquinerie
 *
 * @plugin     Bouquinerie
 * @copyright  2017
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Bouquinerie\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajouter les objets sur les vues des parents directs
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function bouq_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec']) and $e['edition'] == false) {
		$id_objet = $flux['args']['id_objet'];

		if ($e['type'] == 'rubrique') {

			$flux['data'] .= recuperer_fond(
				'prive/objets/liste/livres',
				array(
					'titre' => _T('livre:titre_livres_rubrique'),
					'id_rubrique' => $id_objet
				)
			);

			if (autoriser('creerlivredans', 'rubrique', $id_objet)) {
				include_spip('inc/presentation');
				$flux['data'] .= icone_verticale(
					_T('livre:icone_creer_livre'),
					generer_url_ecrire('livre_edit', "id_rubrique=$id_objet"),
					'livre-24.png',
					'new',
					'right'
				) . "<br class='nettoyeur' />";
			}

		}
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
function bouq_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);



	// livres sur les livres
	// if (!$e['edition'] and in_array($e['type'], array('livre'))) {
	// 	$texte .= recuperer_fond('prive/objets/editer/liens', array(
	// 		'table_source' => 'livres',
	// 		'objet' => $e['type'],
	// 		'id_objet' => $flux['args'][$e['id_table_objet']]
	// 	));
	// }
	
	// livres_auteurs sur les livres
	if (!$e['edition'] and in_array($e['type'], array('livre'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'livres_auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function bouq_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/livres', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('livre:info_livres_auteur')
		), array('ajax' => true));
	}
	return $flux;
}

/**
 * Afficher le nombre d'éléments dans les parents
 *
 * @pipeline boite_infos
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function bouq_boite_infos($flux) {
	if (isset($flux['args']['type']) and isset($flux['args']['id']) and $id = intval($flux['args']['id'])) {
		$texte = '';
		if ($flux['args']['type'] == 'rubrique' and $nb = sql_countsel('spip_livres', array("statut='publie'", 'id_rubrique=' . $id))) {
			$texte .= '<div>' . singulier_ou_pluriel($nb, 'livre:info_1_livre', 'livre:info_nb_livres') . "</div>\n";
		}
		if ($texte and $p = strpos($flux['data'], '<!--nb_elements-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		}
	}
	return $flux;
}


/**
 * Compter les enfants d'un objet
 *
 * @pipeline objets_compte_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function bouq_objet_compte_enfants($flux) {
	if ($flux['args']['objet'] == 'rubrique' and $id_rubrique = intval($flux['args']['id_objet'])) {
		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['livres'] = sql_countsel('spip_livres', 'id_rubrique= ' . intval($id_rubrique) . " AND (statut = 'publie')");
		} else {
			$flux['data']['livres'] = sql_countsel('spip_livres', 'id_rubrique= ' . intval($id_rubrique) . " AND (statut <> 'poubelle')");
		}
	}

	return $flux;
}


/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les objets à la poubelle.
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function bouq_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('livre'=>'*', 'livres_auteur'=>'*'), '*');

	sql_delete('spip_livres', "statut='poubelle' AND maj < " . $flux['args']['date']);

	sql_delete('spip_livres_auteurs', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * Synchroniser la valeur de id secteur
 *
 * @pipeline trig_propager_les_secteurs
 * @param  string $flux Données du pipeline
 * @return string       Données du pipeline
**/
function bouq_trig_propager_les_secteurs($flux) {

	// synchroniser spip_livres
	$r = sql_select(
		'A.id_livre AS id, R.id_secteur AS secteur',
		'spip_livres AS A, spip_rubriques AS R',
		'A.id_rubrique = R.id_rubrique AND A.id_secteur <> R.id_secteur'
	);
	while ($row = sql_fetch($r)) {
		sql_update('spip_livres', array('id_secteur' => $row['secteur']), 'id_livre=' . $row['id']);
	}

	return $flux;
}

function bouq_accueil_informations($texte){
	
	$nbr_livre = sql_countsel('spip_livres', "statut='publie'");
	$texte = "<div class='accueil_informations livre liste'>";
	$texte .= "<h4><a href='".generer_url_ecrire('livres')."'>"._T("livre:titre_livres")."</a></h4>";
	$texte .= "<ul class='liste-items'>";
	$texte .= "<li class='item'>" . _T("texte_statut_publies") . ": " . $nbr_livre . '</li>';
	$texte .= "</ul>";
	$texte .= "</div>";
	return $texte;
}


/**
 * Pour les fiches Auteurs de livre, le statut après création est automatiquement 'Publié'
 *
 * @pipeline pre_insertion
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function bouq_pre_insertion($flux) {
	if ($flux['args']['table'] == 'spip_livres_auteurs') {
		$flux['data']['statut'] = 'publie';
	}
	return $flux;
}

/**
 * Ne pas jamais changer la date de parution du livre
 *
 * @pipeline pre_edition
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function bouq_pre_edition($flux) {
	if ($flux['args']['table'] == 'spip_livres' 
		and $flux['args']['action'] == 'instituer'
		and isset($flux['data']['statut'])) {
			$new_date = $flux['args']['date_ancienne'];
			$flux['data']['date_parution'] = $new_date;
	}
	return $flux;
}