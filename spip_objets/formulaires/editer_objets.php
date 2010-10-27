<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_objets_charger_dist($objet,$id_objet='new', $retour=''){
	
	$nom_objet=objets_nom_objet($objet);
	$valeurs = formulaires_editer_objet_charger($objet, $id_objet, '', '', $retour, '');
	
	$valeurs['objet']=$objet;
	$valeurs['id_objet']=$id_objet;
	$valeurs['nom_objet']=$nom_objet;
	$valeurs['redirect']=$retour;
	// si on est dans le cas ou id_objet est new on récupére un tableau vide, c'est directement géré dans la fonction
	$valeurs['parents']=objets_get_parents($id_objet,$objet);
	
	return $valeurs;
}

function formulaires_editer_objets_verifier_dist($objet,$id_objet='new', $retour=''){
	$nom_objet=objets_nom_objet($objet);
	$erreurs = formulaires_editer_objet_verifier($objet, $id_objet, array('titre'));
	return $erreurs;
}

function formulaires_editer_objets_traiter_dist($objet,$id_objet='new', $retour=''){
	$nom_objet=objets_nom_objet($objet);
	return formulaires_editer_objet_traiter($objet, $id_objet, '', '', $retour, '');
}

?>