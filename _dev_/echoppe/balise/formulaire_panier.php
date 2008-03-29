<?php
function balise_FORMULAIRE_PANIER($p){
	return calculer_balise_dynamique($p, 'FORMULAIRE_PANIER', array('id_panier'));
}

function balise_FORMULAIRE_PANIER_stat($args, $filtres) {
	if(!$args[1]) {
		$args[1]='formulaire_panier';
	}
	return (array($args[0],$args[1]));
}

function balise_FORMULAIRE_PANIER_dyn($id_panier, $formulaire) {
	
	$contexte = array();
	
	$contexte['id_panier'] = _request('id_panier');
	$contexte['editer_produit'] = _request('editer_produit');
	
	$contexte['formulaire'] = "formulaires/panier"; 
	return array($contexte['formulaire'],0,$contexte);
	
}

?>
