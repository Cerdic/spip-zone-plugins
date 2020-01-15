<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_lots_askwiki_charger($retour = ''){
	
	//url WP
	$url_wikipedia = which_wikipedia('wiki');
	
	//nawak pas utile amha ?
	$contexte['id_contact'] = _request('id_contact');
	$contexte['contact'] = _request('contact');
    $contexte['nblimite'] = _request('nblimite');
    
    $contexte['recherche'] = _request('recherche');
    $contexte['lettre'] = _request('lettre');
    $contexte['debut_liste_contacts'] = _request('debut_liste_contacts');
   // $contexte['redirect'] = $retour;
    
    //previsu
    //todo ne pas refaire le test, et reprendre ce tableau pour enregistrer
    if ($id_contact = intval(_request('askwiki_contact'))){

    	$contexte['id_contact'] = $id_contact;
    	
    	$page_wikipedia = titre_page_wiki($id_contact,'contact');
		$phrase_wikipedia = askwiki_first_line($page_wikipedia);
		$naissance = askwiki_datelife($page_wikipedia,0);
		$deces = askwiki_datelife($page_wikipedia,1);
		
		if(!$phrase_wikipedia){ 
			$contexte['page_wikipedia'.$id_contact] = "La page $url_wikipedia".''."$page_wikipedia n'existe pas."; 
		}
		else{
			$page_wiki = "$url_wikipedia".''."$page_wikipedia"; 
			$contexte['page_wikipedia'.$id_contact] = supprimer_tags($page_wiki);
			$contexte['phrase_wikipedia'.$id_contact] = supprimer_tags($phrase_wikipedia);
		}
		$contexte['naissance'.$id_contact] = $naissance;
		if($deces){
			$contexte['deces'.$id_contact] = $deces;
		}
		$contexte['td_plus'.$id_contact] = true;
		
		//if($retour){
			//$contexte['redirect'] = $retour.$id_contact;
		//}
    }
	//spip_log("retour charger".$contexte['redirect'],'test_retour');
	return $contexte;
}

function formulaires_lots_askwiki_verifier($retour = ''){
	$erreurs = array();
	
	// Si c'est un test sur un seul contact
	if ($id_contact = intval(_request('enregistrer_askwiki'))){
		//$erreurs['contacts'] = $retour;
	}
	
	if (!_request('askwiki_contact') and !_request('contacts')){
		#$erreurs['contacts'] = _T('lots_askwiki:erreur_aucun_contact');
	}
	
	return $erreurs;
}

function formulaires_lots_askwiki_traiter($retour = ''){
	// On revient toujours en éditable
	$retours = array('editable' => true);
	/*
	if($retour){
		$retours['redirect'] = $retour;
		spip_log("retour traiter $retour",'test_retour');
	}
	*/
	
	
	// enregistrer sur un seul contact
	$objet = 'contact';
	if ($id_objet = intval(_request('enregistrer_askwiki'))){
		
		include_spip('action/editer_objet');
		
		$url_wikipedia = which_wikipedia('wiki');
    	$page_wikipedia = titre_page_wiki($id_objet,$objet);
		$phrase_wikipedia = askwiki_first_line($page_wikipedia);
		$naissance = askwiki_datelife($page_wikipedia,0);
		$deces = askwiki_datelife($page_wikipedia,1);
		
		if($phrase_wikipedia){
			
				$set['page_wikipedia'] = $url_wikipedia.''.$page_wikipedia;
				$set['phrase_wikipedia'] = supprimer_tags($phrase_wikipedia);
				
				if($naissance){
					$set['date_naissance'] = $naissance;
				}
				if($deces){
					$set['date_deces'] = $deces;
				}
				objet_modifier($objet, $id_objet, $set);
		} else {
			$set['page_wikipedia'] = "-";
			objet_modifier($objet, $id_objet, $set);
		}
		$retours['message_ok'] = _T('lots_askwiki:objet_modifie');
		$retours['redirect'] = $retour;
	}
		
	//si c'est sur l'ensemble coché
	//todo
	
	return $retours;
}


//non utilise ici
function traiter_inviter_contacts($contacts){
	$contacts = array_map('intval',$contacts);
	// On envoie pas à n'importe qui, on filtre ceux qui peuvent être invités
	$contacts = sql_allfetsel(
		'id_contact',
		'spip_contacts',
		array(
			sql_in('id_contact', $contacts),
			sql_in('statut', array('contact', 'sans_nouvelles'))
		)
	);
	$contacts = array_map('reset', $contacts);

	// S'il en reste dans la liste
	if ($contacts){
		$nombre = 0;
		$ok = true;
			
		// L'éventuel message perso
		$message = _request('message');
		foreach ($contacts as $id_contact){
			// On programme l'invitation au plus tôt
			$id_job = job_queue_add('inviter_contact', "Inviter le contact $id_contact", array($id_contact, $message), 'action/', true);
			// Si c'est bon
			if ($id_job){
				// On lie l'invitation au contact
				job_queue_link($id_job, array('objet'=>'contact', 'id_objet'=>$id_contact));
				// On change le statut
				sql_updateq(
					'spip_contacts',
					array(
						'statut' => 'en_cours'
					),
					'id_contact = '.$id_contact
				);
				// On incrémente le nombre d'invitation envoyée
				$nombre+=1;
			}
			// Si c'est pas bon
			else{
				$ok = false;
			}
		}

		// Si au moins un message a bien été envoyé on le dit
		if ($nombre)
			$retours['message_ok'] = $nombre > 1 ? _T('lots_askwiki:lots_askwiki_message_ok_pluriel', array('nombre'=>$nombre)) : _T('lots_askwiki:lots_askwiki_message_ok_singulier');
		// Si au moins un message a merdé on le dit
		if (!$ok)
			$retours['message_erreur'] = _T('lots_askwiki:lots_askwiki_message_erreur');
	}
	// Si on invite personne dans la sélection
	else{
		if (count($contacts)>1)
			$retours['message_ok'] = _T('lots_askwiki:lots_askwiki_message_aucun');
		else
			$retours['message_ok'] = _T('lots_askwiki:lots_askwiki_message_aucun_1');
	}
	return $retours;
}
?>
