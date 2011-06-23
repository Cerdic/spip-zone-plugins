<?php

/**
 * Plugin Montant pour Spip 2.0
 * Licence GPL (c) 2009
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_montant_charger_dist($id_montant='new',$retour='', $config_fonc='montants_edit_config', $row=array(), $hidden=''){
	if (!autoriser('modifier','montant', $id_montant)) {
		return false;
	}
	$valeurs = formulaires_editer_objet_charger('montant',$id_montant,0,'',$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

// Choix par defaut des options de presentation
// http://doc.spip.org/@articles_edit_config
function montants_edit_config($row)
{
	$config = $GLOBALS['meta'];
	return $config;
}

function formulaires_editer_montant_verifier_dist($id_montant='new',$retour='', $config_fonc='montants_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('montant',0,array('objet','prix_ht'));
	if (count($erreurs) and !isset($erreurs['message_erreur'])) {
		$erreurs['message_erreur'] = _T('montants:erreurs_formulaire');
	}
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_groupe_mot_dist
function formulaires_editer_montant_traiter_dist($id_montant='new',$retour='', $config_fonc='montants_edit_config', $row=array(), $hidden=''){
	$res = array();
	$res['editable'] = true;
	$res['vide'] = ""; // ne pas avoir uniquement 2 arguments dans $res, sinon spip prend ca comme un appel deprecie (editable, message_ok)

	set_request('redirect','');
	$action_editer = charger_fonction("editer_montant",'action');
	list($id_grappe, $err) = $action_editer();
	if ($err){
		$res['message_erreur'] = $err;
	}
	else {
		$res['message_ok'] = _T("montants:enregistrement_effectue");
		if ($retour) {
			$res['editable'] = false;
			$res['redirect'] = parametre_url($retour,'id_montant',$id_montant);
		}
	}
	return $res;
}

?>
