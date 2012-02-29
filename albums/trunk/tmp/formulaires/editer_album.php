<?php
/**
 * Formulaire de creation et d'edition d'une album
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

// CHARGER
function formulaires_editer_album_charger_dist($id_album='new',$id_rubrique=0,$retour='',$lier_trad=0,$config_fonc='',$row=array(),$hidden=''){
	$valeurs = formulaires_editer_objet_charger('album',$id_album,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

// IDENTIFIER
/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_album_identifier_dist($id_album='new',$id_rubrique=0,$retour='',$lier_trad=0,$config_fonc='',$row=array(),$hidden=''){
	return serialize(array(intval($id_album)));
}

// VERIFIER
function formulaires_editer_album_verifier_dist($id_album='new',$id_rubrique=0,$retour='',$lier_trad=0,$config_fonc='',$row=array(),$hidden=''){
	return formulaires_editer_objet_verifier('album',$id_album);
}

// TRAITER
function formulaires_editer_album_traiter_dist($id_album='new',$id_rubrique=0,$retour='',$lier_trad=0,$config_fonc='',$row=array(),$hidden=''){
	return formulaires_editer_objet_traiter('album',$id_album,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
