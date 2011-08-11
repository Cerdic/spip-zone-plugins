<?php

/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2009
 *
 * Formulaire d'édition de tickets
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_editer_ticket_charger($id_ticket='new', $retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	// mettre une valeur new pour formulaires_editer_objet_charger()
	
	if (!intval($id_ticket)) $id_ticket='oui'; // oui pour le traitement de l'action (new, c'est pas suffisant)

	if (!autoriser('ecrire', 'ticket', $id_ticket)) {
		$editable = false;
	}else{
		$valeurs = formulaires_editer_objet_charger('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
		$editable = true;
	}
	// si nouveau ticket et qu'une url d'exemple est donnee dans l'environnement, on la colle
	if ((!$id_ticket or $id_ticket=='oui') and ($exemple = _request('exemple'))) {
		$valeurs['exemple'] = $exemple;
	}
	
	if ((!$id_ticket or $id_ticket=='oui')){
		$valeurs['id_assigne'] = $GLOBALS['visiteur_session']['id_auteur'];
	}
	$valeurs['editable'] = $editable;
	return $valeurs;
}

/**
 *
 * Fonction de vérification des valeurs
 *
 * @return
 * @param int $id_ticket[optional]
 * @param string $retour[optional] URL de retour
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_editer_ticket_verifier($id_ticket='new', $retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('ticket',$id_ticket,array('titre','texte'));
	return $erreurs;
}

function tickets_edit_config(){
	return array();
}

/**
 *
 * Fonction de traitement du formulaire
 *
 * @return
 * @param int $id_ticket[optional]
 * @param string $retour[optional] Une url de retour (on lui passera id_ticket=XX en paramètre)
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_editer_ticket_traiter($id_ticket='new',$retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);

	$message['message_ok'] = _T('tickets:ticket_enregistre');
	/**
	 * Si pas d'adresse de retour on revient sur la page en cours avec l'id_ticket en paramètre
	 * Utile pour l'utilisation dans le public
	 */
	if (!$retour) {
		$message['redirect'] = parametre_url(parametre_url(self(),'id_ticket', $res['id_ticket']),'ticket','');
	} else {
		// sinon on utilise la redirection donnee.
		$message['redirect'] = parametre_url($retour, 'id_ticket', $res['id_ticket']);
	}
	
	return $message;
}
?>
