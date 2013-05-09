<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


/**
 * Ce formulaire permet de noter des objets de SPIP.
 * Par defaut, l'objet et son identifiant sont pris dans la boucle
 * <BOUCLE_(ARTICLES){id_article}> #FORMULAIRE_NOTATION ...
 *
 * Mais il est possible de forcer un objet particulier :
 * #FORMULAIRE_NOTATION{article,8}
 *
 */
function balise_FORMULAIRE_NOTATION ($p) {
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
    $i_boucle = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
    $_id_objet = $p->boucles[$i_boucle]->primary;
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
		$args[0] = '';
		$args[1] = '';
	} else {
		$args[0] = table_objet($objet);
	}
	
	// ca s'apparenterait presque a une autorisation...
	// si on n'avait pas a envoyer la valeur $accepter_forum au formulaire
	$accepter_note = substr($GLOBALS['meta']["notations_publics"], 0, 3);
	// il y a un cas particulier pour l'acceptation de forum d'article...
	if ($f = charger_fonction($objet . '_accepter_notes', 'inc', true)){
		$accepter_note = $f($id_objet);
	}
	
	if ($accepter_note == 'non') {
		return false;
	}
	
	// on envoie les arguments a la fonction charger
	// du formulaire CVT fomulaires/notation.php
	return $args;

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
