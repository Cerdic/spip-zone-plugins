<?php
/**
 * Utilisations de pipelines par Optionsproduits
 *
 * @plugin     Optionsproduits
 * @copyright  2017
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Optionsproduits\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 *
 * @param string $flux
 * @return string
 */
function optionsproduits_insert_head($flux) {
	$flux .= "<script type='text/javascript' src='".timestamp(find_in_path('javascript/optionsproduits.js'))."'></script>";
	return $flux;
}

/**
 * Ajouter les objets sur les vues des parents directs
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function optionsproduits_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec']) and $e['edition'] == false) {
		$id_objet = $flux['args']['id_objet'];

		if ($e['type'] == 'optionsgroupe') {
			$flux['data'] .= recuperer_fond(
				'prive/objets/liste/options',
				array(
					'titre' => _T('option:titre_options'),
					'id_optionsgroupe' => $id_objet
				)
			);

			if (autoriser('creeroptiondans', 'optionsgroupes', $id_objet)) {
				include_spip('inc/presentation');
				$flux['data'] .= icone_verticale(
					_T('option:icone_creer_option'),
					generer_url_ecrire('option_edit', "id_optionsgroupe=$id_objet"),
					'option-24.png',
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
 *
 * @param  array $flux Données du pipeline
 *
 * @return array       Données du pipeline
 */
function optionsproduits_affiche_milieu($flux) {
	$texte = '';
	$e     = trouver_objet_exec($flux['args']['exec']);

	// options produits
	$objets = array_filter(explode(',',trim(lire_config('optionsproduits/objets'),',')));
	if (count($objets) && !$e['edition'] && in_array(table_objet_sql($e['type']), $objets)) {
		$texte .= recuperer_fond('prive/objets/liste/options_liees_objet', array(
			'objet'    => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
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

function optionsproduits_afficher_complement_objet($flux) {

	// objets liés à l'option
	if ($flux['args']['type'] == 'option') {
		$flux['data'] .= recuperer_fond('prive/objets/liste/objets_lies_option', array(
			'id_option' => $flux['args']['id'],
		));
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
function optionsproduits_objet_compte_enfants($flux) {
	if ($flux['args']['objet'] == 'optionsgroupe' and $id_optionsgroupe = intval($flux['args']['id_objet'])) {
		$flux['data']['options'] = sql_countsel('spip_options', 'id_optionsgroupe= ' . intval($id_optionsgroupe));
	}

	return $flux;
}


/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 *
 * @param  array $flux Données du pipeline
 *
 * @return array       Données du pipeline
 */
function optionsproduits_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('option' => '*'), '*');

	return $flux;
}

