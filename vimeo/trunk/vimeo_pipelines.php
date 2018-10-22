<?php
/**
 * Utilisations de pipelines par Vimeos
 *
 * @plugin     Vimeos
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Vimeo\Pipelines
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
function vimeo_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les vimeos
	if (!$e['edition'] and in_array($e['type'], array('vimeo'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));

	}

	if ($flux['args']['exec']==="vimeos") {

		$url = generer_action_auteur("vimeo",true,self());
		$label = _T('vimeo:recuperer_videos');
		$texte .= filtre_bouton_action_horizontal_dist($url, $label, "vimeo-24","", "ajax", "", "coucou");
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
function vimeo_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/vimeos', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('vimeo:info_vimeos_auteur')
		), array('ajax' => true));
	}
	return $flux;
}




/**
 * Optimiser la base de données
 *
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function vimeo_optimiser_base_disparus($flux) {

	sql_delete('spip_vimeos', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

function vimeo_taches_generales_cron($taches){
	$taches['compte_vimeo'] = 3600*24*5;
	return $taches;
}
