<?php

/*
 * Plugin Livrables
 * Licence GPL (c) 2011 Cyril Marion
 *
 */
 
/**
 * Prendre en compte les tables dans la recherche d'éléments. 
 *
 * @param 
 * @return 
**/
function livrables_rechercher_liste_des_champs($tables){
	
	// ajouter la recherche sur un composant
	$tables['livrables']['titre'] = 12;
	$tables['livrables']['descriptif'] = 4;
	$tables['livrables']['url'] =3;
	
	return $tables;
}

/**
 * Gerer l'url d'un livrable
 *
**/
function livrables_declarer_url_objets($array){ 
	$array[] = 'livrable'; 
	return $array; 
} 

/**
 * Pouvoir mettre des mots-cle sur les livrables
 *
**/
function livrables_declarer_liaison_mots($liaisons){
	$liaisons['livrables'] = new declaration_liaison_mots('livrables', array(
		'exec_formulaire_liaison' => "livrable",
		'singulier' => "livrables:livrable", //"mediatheque:un_document",
		'pluriel'   => "livrables:livrables", //"mediatheque:des_documents",
		'libelle_objet' => "livrables:objet_livrable",
		'libelle_liaisons_objets' => "livrables:item_mots_cles_association_livrables",
	));

	return $liaisons;
}

/**
 * Editer le livrable d'un ticket kiss
 *
**/
function livrables_affiche_milieu($flux){
	// sur la page exec=ticket_afficher
	if ($flux['args']['exec'] == 'ticket_afficher') {
		$flux['data'] .= recuperer_fond("prive/boite/formulaire-ticket-livrable", array('id_ticket' => $flux["args"]["id_ticket"]), array('ajax'=>true));
	}
	return $flux;
}


/**
 * Ajouter un peu de styles
 *
**/
function livrables_insert_head_css($flux)
{
    $css = find_in_path("livrables.css");
	if ($css) 
    	$flux .= '<!-- css plugin livrables --><link rel="stylesheet" type="text/css" media="all" href="'.$css.'" />';
    return $flux;	
}



?>