<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');

function formulaires_propaganda_charger_dist($id_article='',$retour=''){
	$valeurs = array();
	
	$valeurs['editable'] = true;
	
	$config = unserialize($GLOBALS['meta']['propaganda']);
	if($config['droit_envoi'] !== 'oui'){
		if(!$GLOBALS['visiteur_session']['id_auteur']){
			$valeurs['editable'] = false;
			$valeurs['message_erreur'] = _T('propaganda:connexion_obligatoire');		
		}
	}
	if($id_article){
		$valeurs['articles'] = $id_article;
		/**
		 * Utiliser également les documents des traductions de cet article
		 */
		if($config['documents_traduction'] == 'oui'){
				
		}
	}
	
	if($GLOBALS['visiteur_session']['id_auteur']>0){
		$valeurs['nom_expediteur'] = $GLOBALS['visiteur_session']['nom'];
		$valeurs['email_expediteur'] = $GLOBALS['visiteur_session']['email'];
	}
	
	$fields = array('document_carte','nom_expediteur','email_expediteur','nom_destinataire','email_destinataire','sujet','texte_message_auteur','document_carte');
	foreach($fields as $champ){
		if(_request($champ)){
			$valeurs[$champ] = _request($champ);
		}
	}
	 
	return $valeurs;
}

function formulaires_propaganda_verifier_dist($id_article='',$retour=''){
	$erreurs = array();
	
	$obligatoire = array('document_carte','nom_expediteur','email_expediteur','nom_destinataire','email_destinataire','sujet','texte_message_auteur','document_carte');
	foreach($obligatoire as $champ){
		if(!_request($champ)){
			$erreurs[$champ] = _T('propaganda:champ_obligatoire');
		}else if((strlen(_request($champ))<3)  && ($champ != 'document_carte')){
			$erreurs[$champ] = _T('propaganda:champ_trop_court',array('taille'=>3));
		}
	}
	
	return $erreurs;
}

function formulaires_propaganda_traiter_dist($id_article='',$retour=''){
	$action_envoyer = charger_fonction("envoyer_propaganda",'action');
	list($id,$err) = $action_envoyer();

	if($err){
		$message['message_errer'] = $err;
	}
	else{
		$message['message_ok'] = _T('propaganda:carte_envoyee');
		if ($retour) {
			include_spip('inc/headers');
			$retour = parametre_url($retour,'id_ticket',$id);
			$message['redirect'] = redirige_formulaire($retour);
		}
	}
	return $message;
}
?>