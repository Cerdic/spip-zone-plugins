<?php

/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2009
 *
 * Formulaire d'édition de ticketskiss
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_editer_ticket_charger($id_ticket='new', $retour='', $config_fonc='ticketskiss_edit_config', $row=array(), $hidden=''){
	// mettre une valeur new pour formulaires_editer_objet_charger()
	if (!$id_ticket) $id_ticket='new';

	if (!autoriser('ecrire', 'ticket', $id_ticket)) {
		$editable = false;
	}else{
		$valeurs = formulaires_editer_objet_charger('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
		$editable = true;
	}
	// si nouveau ticket et qu'une url d'exemple est donnee dans l'environnement, on la colle
	if ((!$id_ticket or $id_ticket=='new') and ($exemple = _request('exemple'))) {
		$valeurs['exemple'] = $exemple;
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
function formulaires_editer_ticket_verifier($id_ticket='new', $retour='', $config_fonc='ticketskiss_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('ticket',$id_ticket,array('titre','texte'));

	return $erreurs;
}

function ticketskiss_edit_config(){
	return array();
}

/**
 *
 * Fonction de traitement du formulaire
 *
 * @return
 * @param int $id_ticket[optional]
 * @param string $retour[optional]
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_editer_ticket_traiter($id_ticket='new',$retour='', $config_fonc='ticketskiss_edit_config', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
	$action_editer = charger_fonction("editer_ticket",'action');
	list($id,$err) = $action_editer();

	$message['message_ok'] = _T('ticketskiss:ticket_enregistre');
	if ($retour) {
		include_spip('inc/headers');
		$retour = parametre_url($retour,'id_ticket',$id);
		$message['redirect'] = redirige_formulaire($retour);
	}else{
		$message['redirect'] = parametre_url(self(),'id_ticket',$id);
	}
	return $message;
}
?>
