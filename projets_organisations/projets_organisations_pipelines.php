<?php

/**
 * Afficher l'organisation liée à un projet
 *
 * @param 
 * @return 
**/
function projets_organisations_affiche_milieu($flux) {

	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// organisations sur les projets
	if (!$e['edition'] AND $e['type'] == 'projet') {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projet',
			'objet' => $flux['args'][$e['id_table_objet']],
			'id_objet' => 'organisation'
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
 * Afficher les projets d'une organisation.
 *
 * Il peut y en avoir beaucoup, on le met après le contenu d'une organisation donc.
**/
function projets_organisations_afficher_complement_objet($flux) {

	$texte = "";
	$type = $flux['args']['type'];

	// projets sur les organisations
	if (!$e['edition'] AND $type == 'organisation') {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets',
			'objet' => $type,
			'id_objet' => $flux['args']['id']
		));
	}

	if ($texte) {
		$flux['data'] .= $texte;
	}

	return $flux;
}



?>
