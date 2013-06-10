<?php
/**
 * Utilisations de pipelines par Auteurs Événement
 *
 * @plugin     Auteurs Événement
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Auteur_evenement\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function auteur_evenement_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les evenements
	if (!$e['edition'] AND in_array($e['type'], array('evenement'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}



	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
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
function auteur_evenement_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
        $flux['data'] .='<br class="nettoyeur"';
		$flux['data'] .= recuperer_fond('prive/objets/liste/evenements', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('auteur_evenement:info_evenements_auteur')
		), array('ajax' => true));

	}
	return $flux;
}


?>