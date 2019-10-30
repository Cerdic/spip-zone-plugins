<?php
/**
 * Utilisations de pipelines par le plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2016-2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Effectuer des traitements juste avant l'envoi des pages publiques.
 *
 * => Ajout des métadonnéess Open Graph, Dublin Core et Twitter
 * dans le <head> public de certaines pages.
 *
 * @Note : on retrouve les informations du contexte de la page
 * au moyen d'un squelette pour bénéficier de la mise en cache
 * et éviter des requêtes SQL à chaque hit via decoder_url().
 *
 * @uses metasplus_identifier_contexte()
 * @uses metasplus_selectionner_fond()
 * 
 * @param $flux
 * @return mixed
 */
function metasplus_affichage_final($flux) {

	include_spip('inc/config');
	include_spip('inc/utils'); // pour self()

	// Tests préliminaires avant d'inclure éventuellement les métas
	if (
		// C'est du HTML et on est pas dans le privé
		$GLOBALS['html']
		and !test_espace_prive()
		// Il y a un <head>
		and $pos_head = strpos($flux, '</head>')
		// Au moins un protocole est activé
		and count(lire_config('metasplus'))
		// Le contexte est retrouvé
		and $url = self()
		and $contexte = recuperer_fond('metasplus_identifier_contexte', array('url' => $url))
		and is_array($contexte = unserialize($contexte))
		// La page n'est pas en erreur
		and empty($contexte['erreur'])
		// La page n'est pas exclue
		and is_array($pages_exclues = (
			(defined('_METASPLUS_PAGES_EXCLUES') and _METASPLUS_PAGES_EXCLUES) ?
				explode(',', _METASPLUS_PAGES_EXCLUES) :
				array()
		))
		and (!in_array($contexte['type-page'], $pages_exclues))
		// Ce n'est pas une page d'un pseudo fichier (ex. robots.txt.html)
		and !strpos($contexte['type-page'], '.')
	) {

		// Trouver le squelette à utiliser
		include_spip('metasplus_fonctions');
		$fond = metasplus_selectionner_fond($contexte);

		// Si le squelette n'est pas vide, on ajoute son contenu à la fin du head
		if (
			$fond
			and $metas = recuperer_fond($fond, $contexte)
		) {
			$metas = "<!-- Plugin Métas + -->\n$metas\n";
			$flux = substr_replace($flux, $metas, $pos_head, 0);
		}
	}

	return $flux;
}


/**
 * pipeline post_edition pour supprimer la meta metasplus/id_doc_logo
 * quand on supprime l'image dans le formulaire de configuration
 *
 * @param $flux
 * @return $flux
 * @author tofulm
 */
function metasplus_post_edition($flux) {
	if (
		isset($flux['args']['table'])
		and $flux['args']['table'] === 'spip_documents'
		and isset($flux['args']['operation'])
		and $flux['args']['operation'] === 'supprimer_document'
		and isset($flux['args']['action'])
		and $flux['args']['action'] === 'supprimer_document'
		and include_spip('inc/config')
		and $flux['args']['id_objet'] == lire_config('metasplus/id_doc_logo')
	) {
		effacer_config('metasplus/id_doc_logo');
	}
	return $flux;
}


/**
 * Gérer les informations affichées dans l’espace privé
 * dans le cadre d’information des objets SPIP
 *
 * => Ajout du bouton de prévisualisation des métas+
 *
 * @param $flux
 * @return $flux
 * @author tofulm
 */
function metasplus_boite_infos($flux) {

	if (
		$objet = $flux['args']['type']
		and $id_objet = $flux['args']['id']
		and autoriser('previsualiser_metasplus', $objet, $id_objet)
	) {
		include_spip('base/objets');
		include_spip('inc/filtres');
		$type_page = objet_info($objet, 'page');
		$id_table_objet = id_table_objet($objet);
		$contexte = array(
			'type-page'      => $type_page,
			'objet'          => $objet,
			'id_objet'       => $id_objet,
			$id_table_objet  => $id_objet,
		);
		$fond_previsu = recuperer_fond('prive/squelettes/inclure/metasplus_bouton_previsu', $contexte);
		$flux['data'] .= $fond_previsu;
	}

	return $flux;
}
