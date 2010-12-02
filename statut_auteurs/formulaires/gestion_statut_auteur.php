<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/meta');


function formulaires_gestion_statut_auteur_charger_dist(){
	
	$valeurs = array('nouveau_statut_code'=>'');
	
	return $valeurs;
}

function formulaires_gestion_statut_auteur_verifier_dist(){
	
	if(strlen($statut_code=_request('nouveau_statut_code'))<3){
		//les nouveaux statuts doivent avoir plus de 3 caracteres, pas trop limitant non plus  
		$erreurs['message_erreur']="Vous devez gérer un statut supérieur à 3 caractères";
	}
	$statuts=array_keys(statut_auteurs_get_statuts());
	$statuts=array_merge($statuts,array_values($GLOBALS['liste_des_statuts'])); //on considére aussi les statuts de SPIP 
	if(in_array($statut_code,$statuts)){
		$erreurs['message_erreur']="Le statut existe d&eacute;j&agrave;";
	}
	
	if(strlen(_request('nouveau_statut_libelle'))<3){
		//les nouveaux objets doivent avoir plus de 3 caracteres, pas trop limitant non plus  
		$erreurs['message_erreur']="Vous devez gérer un statut supérieur à 3 caractères";
	}
	
	return $erreurs;
}

function formulaires_gestion_statut_auteur_traiter_dist(){
			
		//on va enregistrer le nouveau statut dans les metas 
		$statuts=statut_auteurs_get_statuts();
		$statut_code=_request('nouveau_statut_code');
		$statut_libelle=_request('nouveau_statut_libelle');
		
		$statuts[$statut_code]=$statut_libelle;
		
		ecrire_meta("statut_auteurs:autre_statut_auteur",serialize($statuts));
			
		$message['message_ok']="Le nouveau statut &agrave; bien &eacute;t&eacute; cr&eacute;e";
	
		return $message;
	
}

?>