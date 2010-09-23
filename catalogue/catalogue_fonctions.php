<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 Ateliers CYM
 */

function catalogue_insert_head($flux){
	$flux .= '<!-- insertion css catalogue --><link rel="stylesheet" type="text/css" href="'.find_in_path('catalogue.css').'" media="all" />';
	return $flux;
}


if(!function_exists(monetaire)){

    function monetaire($montant) {
        // affiche un montant en euro correctement formatt�
        setlocale(LC_MONETARY, 'fr_FR');
		
		// Si l'on vient du formulaire, le type des variables est soit en double, soit en string!
		// Si l'ont vient de null part, le type des variables est un string!
		// La méthode utilisé on passe quoiqu'il arrive tout en double, si c'est un string la valeur obtenue après la function doubleval() sera 0
		// C'est pourquoi il suffit de vérifier si la valeur du $montant en double est égal à 0 pour initialiser $montant à 0 si jamais on vient de nulle part!
		if( (doubleval($montant)==0) || empty($montant)){
			$montant = 0;
		}
		
        $montant = money_format('%i', $montant);
        $montant = ereg_replace("EUR", "&euro;", $montant);
        $montant = ereg_replace(" ", "&nbsp;", $montant);
        return $montant;
	}
}

?>