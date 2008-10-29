<?php


if (!defined("_ECRIRE_INC_VERSION")) return;


function balise_FORMULAIRE_AJOUTER_UN_DOCUMENT_dist ($p) {
	// on recupere le nom de la boucle
	// sauf qu'il faut passer par une balise renvoyant le nom 
	# $table = $p->boucles[$p->id_boucle]->id_table;
	
	// on recupere la valeur de la cle primaire de l'objet
	$pk = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique($p,'FORMULAIRE_AJOUTER_UN_DOCUMENT', array('AJAXFORM_TYPE_BOUCLE',$pk));
}

function balise_FORMULAIRE_AJOUTER_UN_DOCUMENT_stat($args,$filtres) {
	// si on force les parametres par #FORMULAIRE_AJOUTER_UN_DOCUMENT{article,12}
	// on enleve les parametres calcules
	if (isset($args[3])) {
		array_shift($args);
		array_shift($args);
	}
	$objet = $args[0];
	$id_objet = $args[1];
	// pas dans une boucle ? 
	// on ajoutera le document sans le lier a un objet particulier.
	if ($objet == 'balise_hors_boucle') {
		$objet = '';
		$id_objet = '';
	} else {		
		$objet = table_objet($objet);
	}
	return array($objet, $id_objet);
}

?>
