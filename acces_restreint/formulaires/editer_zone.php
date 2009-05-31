<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_zone_charger_dist($id_zone='new', $retour='', $config_fonc='zones_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('zone',$id_zone,0,0,$retour,$config_fonc,$row,$hidden);

	include_spip('inc/acces_restreint');
	// charger les rubriques associees a la zone
	$valeurs['rubriques'] = accesrestreint_liste_contenu_zone_rub_direct($id_zone);
	
	return $valeurs;
}

function zones_edit_config(){
	return array();
}

function formulaires_editer_zone_verifier_dist($id_zone='new', $retour='', $config_fonc='zones_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('zone',$id_zone,array('titre'));

	return $erreurs;
}

function formulaires_editer_zone_traiter_dist($id_zone='new', $retour='', $config_fonc='zones_edit_config', $row=array(), $hidden=''){

	$message = "";
	$action_editer = charger_fonction("editer_zone",'action');
	list($id,$err) = $action_editer();
	if ($err){
		$message .= $err;
	}
	elseif ($retour) {
		include_spip('inc/headers');
		$retour = parametre_url($retour,'id_zone',$id);
		$message .= redirige_formulaire($retour);
	}
	return $message;
}

?>