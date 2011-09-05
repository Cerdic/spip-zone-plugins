<?php

/**
 * Formulaire pour filtrer la pages d'affichage des livrables
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/autoriser');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_projet_livrable_charger($id_projet=''){
	
	if(is_numeric($id_projet)){
		if (!autoriser('ecrire', 'projet', $id_projet)) {
			$editable = false;
		}else{
			$id_projet = _request('id_projet');
			if (!_request('id_livrable'))
				$id_livrable = sql_getfetsel('id_livrable', 'spip_tickets', 'id_ticket=' . intval($id_ticket));
			$valeurs['id_ticket'] = $id_ticket;
			$valeurs['id_livrable'] = $id_livrable;
			$editable = true;
		}
	}else{
		$editable = false;
	}
	
	$valeurs['editable'] = $editable;
	return $valeurs;
}

/**
 * 
 * Fonction de vérification des valeurs
 * 
 */
function formulaires_ticket_livrable_verifier($id_ticket=''){
	$erreurs = array();

	$id_livrable_ancien = sql_getfetsel('id_livrable','spip_tickets','id_ticket='.intval($id_ticket));
	$id_livrable = _request('id_livrable');
	if($id_livrable == $id_livrable_ancien){
		$erreurs['message_erreur'] = _T('livrables:erreur_meme_livrable');	
	}
	return $erreurs;
}


/**
 * 
 * Fonction de traitement du formulaire
 * 
 */
function formulaires_ticket_livrable_traiter($id_ticket=''){
	$res = array();
	$id_livrable = _request('id_livrable');
	sql_updateq('spip_tickets',array('id_livrable' => $id_livrable),'id_ticket='.intval($id_ticket));
	$res['message_ok'] = _T('preprod:succes_ticket_modifie', array('id' => $id_ticket));

	return $res;
}
?>