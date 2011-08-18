<?php

/*
 * Plugin Composants
 * Licence GPL (c) 2011 Cyril Marion
 *
 */
 
/**
 * Prendre en compte les tables dans la recherche d'éléments. 
 *
 * @param 
 * @return 
**/
function composants_rechercher_liste_des_champs($tables){
	
	// ajouter la recherche sur un composant
	$tables['composant']['titre'] = 12;
	$tables['composant']['descriptif'] = 4;
	
	return $tables;
}

/**
 * Gerer l'url d'un composant
 *
**/
function composants_declarer_url_objets($array){ 
	$array[] = 'composant'; 
	return $array; 
} 

/**
 * Pouvoir mettre des mots-cle sur les composants
 *
**/
function composants_declarer_liaison_mots($liaisons){
	$liaisons['composants'] = new declaration_liaison_mots('composants', array(
		'exec_formulaire_liaison' => "composant",
		'singulier' => "composants:composant", //"mediatheque:un_document",
		'pluriel'   => "composants:composants", //"mediatheque:des_documents",
		'libelle_objet' => "composants:objet_composant",
		'libelle_liaisons_objets' => "composants:item_mots_cles_association_composant",
	));

	return $liaisons;
}



?>