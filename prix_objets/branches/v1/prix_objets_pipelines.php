<?php
/**
 * Utilisations de pipelines par Prix Objets
 *
 * @plugin     Prix Objets
 * @copyright  2012 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Prix_objets\Pipelines
 */
if (!defined("_ECRIRE_INC_VERSION"))
	return;

	/**
	 * Ajouter du contenu sur les pages exec/ de SPIP, après le contenu prévu au centre de la page.
	 *
	 * @pipeline affiche_milieu
	 *
	 * @param array $flux
	 * @return array
	 */
function prix_objets_affiche_milieu($flux) {
	// affichage du formulaire d'activation désactivation projets
	include_spip('inc/config');
	$objets = lire_config('prix_objets/objets_prix', array());
	$e = trouver_objet_exec($flux['args']['exec']);
	$type = $e['type'];
	$id_table_objet = $e['id_table_objet'];
	$id = intval($flux['args'][$id_table_objet]);
	if (in_array($type, $objets)) {
		if ($type == 'article') {
			$id_article = $flux['args']['id_article'];
			$rubriques_produits = rubrique_prix($id_article);
			if (($rubriques_produits and $id_article) or (!$rubriques_produits)) {
				$contexte = array(
					'id_objet' => $id_article,
					'objet' => 'article'
				);
				$contenu = recuperer_fond('prive/objets/editer/prix', $contexte, array(
					'ajax' => true
				));
				if ($p = strpos($flux['data'], "<!--affiche_milieu-->"))
					$flux['data'] = substr_replace($flux['data'], $contenu, $p, 0);
				else
					$flux['data'] .= $contenu;
			}
		}
		elseif ($id) {
			$contexte = array(
				'id_objet' => $id,
				'objet' => $type
			);
			$contenu = recuperer_fond('prive/objets/editer/prix', $contexte, array(
				'ajax' => true
			));
			if ($p = strpos($flux['data'], "<!--affiche_milieu-->"))
				$flux['data'] = substr_replace($flux['data'], $contenu, $p, 0);
			else
				$flux['data'] .= $contenu;
		}
	}
	return $flux;
}

/**
 * Declare l'object pour le Plugin shop https://github.com/abelass/shop.
 *
 * @pipeline shop_objets
 *
 * @param array $flux
 * @return array
 */
 function prix_objets_shop_objets($flux) {
	$flux['data']['prix_objets'] = array(
		'action' => 'prix_objets',
		'nom_action' => _T('prix_objets:prix_objets_titre'),
		'icone' => 'prix_objets-16.png',
		'configurer' => array(
			'titre' => _T('prix_objets:titre_prix_objets'),
			'chemin' => 'prive/squelettes/contenu/configurer_prix_objets'
		)
	);

	return $flux;
}

/**
 * Ajouter les configurations dans celle de réservation événements.
 *
 * @pipeline reservation_evenement_objets_configuration
 *
 * @param array $flux
 *        	Données du pipeline
 * @return array Données du pipeline
 */
function prix_objets_reservation_evenement_objets_configuration($flux) {

	$flux['data']['prix_objets'] = array(
		'label' => _T('paquet-prix_objets:prix_objets_nom'),
	);

	return $flux;
}

/**
 * Ajouter des contenus dans la partie <head> des pages de l’espace privé.
 *
 * @pipeline header_prive
 *
 * @param array $flux
 * @return array
 */
function prix_objets_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_PRIX_OBJETS .'css/prix_objets_admin.css" type="text/css" media="all" />';
	return $flux;
}

/**
 * Active des modules de jquery ui
 *
 * @pipeline jqueryui_plugins
 *
 * @param array $scripts
 *        	Données du pipeline
 * @return array
 */
function prix_objets_jqueryui_plugins($scripts) {
	$scripts[] = "jquery.ui.sortable";
	return $scripts;
}
