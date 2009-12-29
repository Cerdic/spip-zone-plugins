<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */
function catalogue_insert_head($flux){
$flux .= '<!-- insertion css catalogue --><link rel="stylesheet" type="text/css" href="'.find_in_path('cat.css').'" media="all" />
';
return $flux;
}

function monetaire($montant) {
	// affiche un montant en euro correctement formatté
	setlocale(LC_MONETARY, 'fr_FR');
	$montant = money_format('%i', $montant);
	$montant = ereg_replace("EUR", "&euro;", $montant);
	$montant = ereg_replace(" ", "&nbsp;", $montant);
	return $montant;
}


?>