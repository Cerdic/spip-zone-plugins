<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_NOTATION ($p) {
	// on récupère le type d'objet de la boucle courante et son id
	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$type = $p->boucles[$b]->type_requete;
	$id_objet = $p->boucles[$b]->primary;
	// et on les pass à la fonction suivante ATTENTION = type_boucle est interprété avec la balise type_boucle de /notation.php
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_NOTATION',
		array(
			'type_boucle',
			$id_objet
		)
	);

}


function balise_FORMULAIRE_NOTATION_stat($args, $filtres) {
	// on récupère les arguments
	$type = $args[0];
	$id_objet = $args[1];
	// et on les passe à la fonciton charger du formulaire CVT fomulaires/notation.php
	return array($type, $id_objet);
	
}
	
?>