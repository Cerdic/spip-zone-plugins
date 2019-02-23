<?php
/**
 * Utilisations de pipelines par Location d&#039;immeubles
 *
 * @plugin     Location d&#039;immeubles
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_immeubles\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function location_immeubles_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	$objet = $e['type'];


	// objets_locations sur les immeubles
	if (!$e['edition'] and in_array($objet, array('immeuble','espaces'))) {

		$texte .= recuperer_fond('prive/objets/liste/objets_locations_details', array(
			'objet' => $objet,
			'id_objet' => $flux['args'][$e['id_table_objet']],
			'titre' => _T('objets_locations_detail:info_objets_locations_details_' . $objet)
		), array('ajax' => true));
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
