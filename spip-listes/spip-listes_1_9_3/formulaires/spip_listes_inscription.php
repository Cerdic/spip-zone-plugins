<?php

	// formulaires/spip_listes_inscriptions.php
	
	// $LastChangedRevision: 26478 $
	// $LastChangedBy: paladin@quesaco.org $
	// $LastChangedDate: 2009-02-09 11:25:06 +0100 (Lun 09 fév 2009) $
	
	// formulaire d'inscription
	// necessite SPIP >= 2
	
function formulaires_spip_listes_inscription_charger_dist ($id_liste='')
{
	$valeurs = array(
		'email' => '',
		'id_liste' => $id_liste
	);
	
	return $valeurs;
}

function formulaires_spip_listes_inscription_verifier_dist ($id_liste='')
{
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	foreach(array('email') as $obligatoire) {
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	}
	
	if(!in_array(_request('format_abo'),array('html','texte')))
		$erreurs['format'] = "format inconnu";
	
	$listes = _request('listes') ;
	if(is_array($listes))
	 foreach($listes as $liste)
		if(!intval($liste)) 
		{
			$erreurs['liste'] = _T('spiplistes:liste_inconnue');
		}


	// verifier que si un email a ete saisi, il est bien valide :
	include_spip('inc/filtres');
	if (_request('email') AND !email_valide(_request('email'))) 
	{
		$erreurs['email'] = _T('spiplistes:cet_email_pas_valide');
	}
	
	// Verifier si le mail est deja connu
	$email = _request('email') ;
	if(email_valide(_request('email')))
		if (sql_getfetsel("id_auteur","spip_auteurs","id_auteur !='".intval($id_auteur)."' AND email = '$email'")) {
			$erreurs['email'] = _T('spiplistes:cet_email_deja_enregistre');
		}
	

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera re-soumis
}


function formulaires_spip_listes_inscription_traiter_dist($id_liste=''){
	
	// enregistre dans spip_auteurs, spip_auteurs_elargis, spip_auteurs_listes			
			
	$val['email'] = _request('email');
	$val['nom'] = _request('email');
	include_spip('inc/acces');
	$alea_actuel = creer_uniqid();
	$alea_futur = creer_uniqid();
	$val['alea_actuel'] = $alea_actuel;
	$val['alea_futur'] = $alea_futur;
	$val['low_sec'] = '';
	$val['statut'] = 'aconfirmer';
	
	$format = _request('format_abo') ;
	$listes = _request('listes');
	
	$id_auteur = sql_insertq("spip_auteurs",$val);			
	$ok = sql_insertq("spip_auteurs_elargis",array('id_auteur'=>$id_auteur,'spip_listes_format'=>$format));
	
	if($listes) {
		foreach($listes as $liste) {
			$ok = sql_insertq("spip_auteurs_listes",array('id_auteur'=>$id_auteur,'id_liste'=>$liste));
		}
	}
			
	// envoyer mail de confirmation
	
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$email_to = _request('email');
	$email_from = _request('email');
	$sujet = _T('spiplistes:confirmation_inscription');
	$message = _T('spiplistes:inscription_reponses_s', array('s' => $GLOBALS['meta']["nom_site"])) ;

	$envoyer_mail($email_to,$sujet,$message,$email_from);
	
	return array('message_ok'=>_T('spiplistes:demande_ok'),'editable' => false,);
}

