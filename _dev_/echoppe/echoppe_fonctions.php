<?php

include_spip('public/boucles');
include_spip('base/echoppe');



//global $tables_jointures;

//$tables_jointures['spip_echoppe_categories'][] = 'spip_echoppe_categories_descriptions';
//$tables_jointures['spip_echoppe_categories_produits'][] = 'spip_echoppe_produits';

function generer_logo($nom_fichier){
	
	$logo = '<img src="IMG/'.$nom_fichier.'" alt="'.textebrut($nom_fichier).'" />';
	if (strlen($nom_fichier) > 0) return $logo;
	
}

?>
