<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Contexte du formulaire
function balise_FORMULAIRE_UPLOAD ($p) {
	// on prend nom de la cle primaire de l'objet pour calculer sa valeur
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
	return calculer_balise_dynamique(
		$p,
		'FORMULAIRE_UPLOAD',
		array(
			'FORMULAIRE_UPLOAD_TYPE_BOUCLE', // demande du type d'objet
			$_id_objet
		)
	);
}

function balise_FORMULAIRE_UPLOAD_stat($args, $filtres) {
	// si on force les parametres par #FORMULAIRE_UPLOAD{article,12,inc-upload_truc}
	// on enleve les parametres calcules
	if (isset($args[3])) {
		array_shift($args);
		array_shift($args);
	}
	$objet = $args[0];
	$id_objet = $args[1];
	if(!$fond_documents = $args[2])
		$fond_documents = 'inc-upload_documents';
	// pas dans une boucle ? on attache a l'auteur connecté
	if ($objet == 'balise_hors_boucle') {
		$objet = 'auteur';
		$id_objet = $GLOBALS['auteur_session']['id_auteur'];
	} else {
		$objet = table_objet($objet);
	}
	// on envoie les arguments a la fonction charger 
	// du formulaire CVT fomulaires/upload.php
	return array($objet, $id_objet, $fond_documents);

}

// balise type_boucle de Rastapopoulos dans le plugin etiquettes
// present aussi dans plugin ajaxforms, notation...
// bref, a integrer dans le core ? :p
function balise_FORMULAIRE_UPLOAD_TYPE_BOUCLE($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? $type : "balise_hors_boucle";
	return $p;  
}

?>