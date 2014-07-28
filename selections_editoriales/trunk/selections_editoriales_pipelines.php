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

if (!defined('_ECRIRE_INC_VERSION')) return;

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
		AND $flux['args']['args'][0] == 'selections_contenu'
	) {
		$flux['data']['logo_survol'] = false;
		$flux['data']['logo_off'] = false;
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
		$id_selection = intval(sql_getfetsel('id_selection', 'spip_selections_contenus', 'id_selections_contenu = '.$id_selections_contenu));
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
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les selections
	if (!$e['edition'] AND in_array($e['type'], array('selection'))) {
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
		$flux['data'] .= '<span class="icone horizontale s24">'.bouton_action(
			filtrer('balise_img', chemin_image('selection-del-24'))._T('lien_supprimer'),
			generer_action_auteur('supprimer_selection', $id_selection, generer_url_ecrire('selections')),
			'link'
		).'</span>';
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
function selections_editoriales_afficher_complement_objet($flux){
	$exec = trouver_objet_exec($flux['args']['type']);
	$id = intval($flux['args']['id']);
	
	if (
		$exec !== false // page d'un objet éditorial
		and $exec['edition'] === false // pas en mode édition
		and $type = $exec['type']
		and autoriser('associerselections', $type, $id)
		and autoriser('creer', 'selection')
	 ) {
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/selections_objet', array(
			'objet' => $type,
			'id_objet' => $id,
			),
			array('ajax'=>'selections')
		);
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
function selections_editoriales_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('selection'=>'*'),'*');
	return $flux;
}

?>
