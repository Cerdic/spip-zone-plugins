<?php
/**
 * Plugin kaye
 * (c) 2012 Cédric Couvrat
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */


function kaye_jqueryui_plugins($scripts){
   $scripts[] = "jquery.ui.tabs";
   return $scripts;
}

function kaye_insert_head($flux){
    $flux .= "<script>
			$(document).ready(function() {
    		$(\"#tabs\").tabs();
  			});
  			</script> \n";
    return $flux;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 */
function kaye_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les classes
	if (!$e['edition'] AND in_array($e['type'], array('classe'))) {
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
 */
function kaye_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/classes', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('classe:info_classes_auteur')
		), array('ajax' => true));

	}
	return $flux;
}


?>