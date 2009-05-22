<?php
/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2009
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_ticket_charger($id_ticket='new', $retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	
	if (!autoriser('ecrire', 'ticket', $id_ticket)) {
		$editable = false;
	}else{
		$valeurs = formulaires_editer_objet_charger('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
		$editable = true;
	}
	
	$valeurs['editable'] = $editable;
	return $valeurs;
}

function formulaires_editer_ticket_verifier($id_ticket='new', $retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('ticket',$id_ticket,array('titre','texte'));

	return $erreurs;
}

function tickets_edit_config(){
	return array();
}

function formulaires_editer_ticket_traiter($id_ticket='new',$retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	$message = "";
	$action_editer = charger_fonction("editer_ticket",'action');
	list($id,$err) = $action_editer();
	if ($err){
		$message .= $err;
	}
	elseif ($retour) {
		include_spip('inc/headers');
		$retour = parametre_url($retour,'id_ticket',$id);
		$message .= redirige_formulaire($retour);
	}
	return $message;
}
?>