<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_ANNEE ($p) {
	
	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;	
	$type = $p->boucles[$b]->type_requete;
	$id_objet = $p->boucles[$b]->primary;

	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_ANNEE',
		array(
			'type_boucle',
			$id_objet
		)
	);
	
}


function balise_FORMULAIRE_ANNEE_stat($args, $filtres) {

	$type = $args[0];
	$id_objet = $args[1];
	
	return array($type, $id_objet);
	
}
	
?>