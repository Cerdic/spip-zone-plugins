<?php
/**
* Plugin Notation 
* par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
**/
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_NOTATION ($p) {
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
    $_id_objet = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_NOTATION',
		array(
			'NOTATION_TYPE_BOUCLE', // demande du type d'objet
			$_id_objet
		)
	);

}


function balise_FORMULAIRE_NOTATION_stat($args, $filtres) {
	// si on force les parametres par #FORMULAIRE_NOTATION{article,12}
	// on enleve les parametres calcules
	if (isset($args[3])) {
		array_shift($args);
		array_shift($args);
	}
	$objet = $args[0];
	$id_objet = $args[1];
	// pas dans une boucle ? on generera une erreur ?
	if ($objet == 'balise_hors_boucle') {
		$objet = '';
		$id_objet = '';
	} else {		
		$objet = table_objet($objet);
	}
	// on envoie les arguments � la fonction charger 
	// du formulaire CVT fomulaires/notation.php
	return array($objet, $id_objet);
	
}

// balise type_boucle de Rastapopoulos dans le plugin etiquettes
// present aussi dans plugin ajaxforms...
// bref, a integrer dans le core ? :p
function balise_NOTATION_TYPE_BOUCLE($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? $type : "balise_hors_boucle";
	return $p;  
}
?>
