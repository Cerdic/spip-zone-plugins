<?php
/**
 * Utilisations de pipelines par Création de cartes
 *
 * @plugin     Création de cartes
 * @copyright  2016
 * @author     kent1
 * @licence    GNU/GPL
 * @package    SPIP\Cartes\Pipelines
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
function cartes_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les cartes
	if (!$e['edition'] and in_array($e['type'], array('carte'))) {
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
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function cartes_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/cartes', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('carte:info_cartes_auteur')
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
function cartes_optimiser_base_disparus($flux) {
	sql_delete('spip_cartes', "statut='poubelle' AND maj < " . $flux['args']['date']);
	return $flux;
}

/**
 * Utilisation du pipeline pre_boucle (SPIP)
 * Sur les boucles cartes, bounds doit être de type textuel
 *
 * @pipeline pre_boucle
 * @param object $boucle
 * @return object
 */
function cartes_pre_boucle($boucle) {
	if ($boucle->type_requete == 'cartes') {
		$boucle->select[]= 'AsText(cartes.bounds) AS geometry_map';
	}
	return $boucle;
}

/**
 * Utilisation du pipeline recuperer_fond (SPIP)
 * Ajouter Leaflet.label dans le script js de GIS
 *
 * @pipeline recuperer_fond
 * @param array $flux 	Données du pipeline
 * @return array 		Données du pipeline modifiées
 */
function cartes_recuperer_fond($flux) {
	if ($flux['args']['fond'] == 'javascript/gis.js') {
		$ajouts = "\n". spip_file_get_contents(find_in_path('javascript/leaflet.label-src.js'));
		$flux['data']['texte'] .= $ajouts;
	}
	return $flux;
}

/**
 * Utilisation du pipeline insert_head_css (SPIP)
 * Ajout de la css de Leaflet.label dans le head de chaque page
 *
 * @pipeline insert_head_css
 * @param string $flux 	Contenu textuel de la balise #INSERT_HEAD_CSS
 * @return string 		Contenu de la balise modifié
 */
function cartes_insert_head_css($flux) {
	$flux .= "\n".'<link rel="stylesheet" href="'. direction_css(find_in_path('css/leaflet.label.css')) .'" />';
	return $flux;
}
