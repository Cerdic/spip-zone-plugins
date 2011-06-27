<?php

/**
 * Plugin abonnement pour Spip 2.0
 * Licence GPL (c) 2011
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_abonnement_charger_dist($id_abonnement='new',$retour='', $config_fonc='abonnements_edit_config', $row=array(), $hidden=''){
	if (!autoriser('modifier','abonnement', $id_abonnement)) {
		return false;
	}
	$valeurs = formulaires_editer_objet_charger('abonnement',$id_abonnement,0,'',$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

// Choix par defaut des options de presentation
// http://doc.spip.org/@articles_edit_config
function abonnements_edit_config($row)
{
	$config = $GLOBALS['meta'];
	return $config;
}

function formulaires_editer_abonnement_verifier_dist($id_abonnement='new',$retour='', $config_fonc='abonnements_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('abonnement',0,array('titre','duree','prix','periode'));
	if (count($erreurs) and !isset($erreurs['message_erreur'])) {
		$erreurs['message_erreur'] = _T('abo:erreurs_formulaire');
	}
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_groupe_mot_dist
function formulaires_editer_abonnement_traiter_dist($id_abonnement='new',$retour='', $config_fonc='abonnements_edit_config', $row=array(), $hidden=''){
	$res = array();
	$res['editable'] = true;
	$res['vide'] = ""; // ne pas avoir uniquement 2 arguments dans $res, sinon spip prend ca comme un appel deprecie (editable, message_ok)

	set_request('redirect','');
	$action_editer = charger_fonction("editer_abonnement",'action');
	list($id_abonnement, $err) = $action_editer();
	if ($err){
		$res['message_erreur'] = $err;
	}
	else {
		$res['message_ok'] = _T("abo:enregistrement_effectue");
		if ($retour) {
			$res['editable'] = false;
			$res['redirect'] = parametre_url($retour,'id_abonnement',$id_abonnement);
		}
	}
	return $res;
}

?>
