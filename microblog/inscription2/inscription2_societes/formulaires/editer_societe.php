<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_societe_charger_dist($id_societe='new', $retour='', $config_fonc='societe_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('societe',$id_societe,0,0,$retour,$config_fonc,$row,$hidden);
	
	return $valeurs;
}

function societe_edit_config(){
	return array();
}

function formulaires_editer_societe_verifier_dist($id_societe='new', $retour='', $config_fonc='societe_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('societe',$id_societe,array('nom'));
	return $erreurs;
}
	
function formulaires_editer_societe_traiter_dist($id_societe='new', $retour='', $config_fonc='societe_edit_config', $row=array(), $hidden=''){
	
	$message = "";
	$action_editer = charger_fonction("editer_societe",'action');
	list($id,$err) = $action_editer();
	if ($err){
		$message .= $err;
	}
	elseif ($retour) {
		include_spip('inc/headers');
		$retour = parametre_url($retour,'id_societe',$id);
		$message .= redirige_formulaire($retour);
	}
	return $message;
}
?>
