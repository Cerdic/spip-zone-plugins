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
	$valeurs['id_'.$nom_objet]=$id_objet;
	$valeurs['redirect']=$retour;
	
	// si on est dans le cas ou id_objet est new on récupére un tableau vide, c'est directement géré dans la fonction
	$valeurs['parents']=objets_get_parents($id_objet,$objet);
	
	//on récupére id_secteur de la rubrique dans laquelle on est
	if($id_objet=="new") {
		if($id_article=_request('id_article')){
			$valeurs['id_secteur']=sql_getfetsel("id_secteur","spip_articles","id_article=".(int)$id_article);
		}elseif($id_rubrique=_request('id_rubrique')){
			$valeurs['id_secteur']=sql_getfetsel("id_secteur","spip_rubriques","id_rubrique=".(int)$id_rubrique);
		}
	}else{//ce n'est pas un objet en cours de création
			$valeurs['statut']=sql_getfetsel("statut","spip_".$objet,'id_'.$nom_objet."=".(int)$id_objet);
	}
	
	
	return $valeurs;
}

function formulaires_editer_objets_verifier_dist($objet,$id_objet='new', $retour=''){
	$nom_objet=objets_nom_objet($objet);
	$erreurs = formulaires_editer_objet_verifier($objet, $id_objet, array('titre'));
	return $erreurs;
}

function formulaires_editer_objets_traiter_dist($objet,$id_objet='new', $retour=''){
	$nom_objet=objets_nom_objet($objet);
	
	if($id_objet!="new"){
		//on peut modifier le statut d'un objet existant
		sql_update('spip_'.$objet,array('statut'=>_request('statut')),'id_'.$nom_objet.'='.$id_objet);
	}
		
	$retour=formulaires_editer_objet_traiter($objet, $id_objet, '', '', $retour, '');
	
	return $retour;
}

?>