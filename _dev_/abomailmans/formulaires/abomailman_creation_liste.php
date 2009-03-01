<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
   
include_spip('base/abstract_sql');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abomailman_creation_liste_charger_dist($id_abomailman = ""){
	global $visiteur_session;
	
	if($visiteur_session['statut'] == '0minirezo'){
		//initialise les variables d'environnement pas défaut
		$valeurs = array();
		$valeurs['editable'] = true;
		
		// On verifie que la liste existe
		if ($id_abomailman){
			$valeurs = sql_fetsel('*','spip_abomailmans',"id_abomailman =$id_abomailman");
			$valeurs['langue'] = $valeurs['lang'];
			spip_log("La liste existe, on peut charger le formulaire avec ses infos préremplies","abomailman");
			if(!$valeurs['id_abomailman']){
				$valeurs['editable'] = false;
				$valeurs['message_erreur'] = _T('abomailmans:liste_non_existante');
			}
		}
		if(!$valeurs['langue']){
			$valeurs['langue'] = lang_select();
		}
		unset($valeurs['lang']);
	}else{
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('abomailman:creation_droits_insuffisants');
	}
	return $valeurs;
}


function formulaires_abomailman_creation_liste_verifier_dist($id_abomailman = ""){

	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');

	//initialise le tableau des erreurs
	$erreurs = array();

	// Faire une fonction de verif sur le mail et le titre pour validite
	
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$email_sympa = _request('email_sympa');
	$desactive = _request('desactive');
	$lang = _request('lang');
	
	// Si on fait une suppression, on ne vérifie pas le reste
	if($desactive != '2'){
		if(!$email){
			$erreurs['email'] = _T("abomailmans:email_oublie");
			spip_log("Aucun email de liste n'est inséré","abomailman");
		}
		
		if(!$titre){
			$erreurs['titre'] = _T("abomailmans:titre_liste_obligatoire");
		}
		
		if (!count($erreurs)){
			include_spip('inc/filtres'); # pour email_valide()
			if (!email_valide($email)){
				$erreurs['email'] = _T("abomailmans:email_valide");
				spip_log("Email non valide $email pour la liste","abomailman");
			}
		}	
	}

    //message d'erreur genéralisé
    if (count($erreurs)) {
        $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
    }

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_abomailman_creation_liste_traiter_dist($id_abomailman = ""){
    $message = '';
	$message['editable'] = true;
    
	$datas = array();
    
    // Récupération des données
	$datas['titre'] = _request('titre');
	$datas['descriptif'] = _request('descriptif');
	$datas['email'] = _request('email');
	$datas['email_sympa'] = _request('email_sympa');
	$datas['desactive'] = _request('desactive');
	$datas['lang'] = _request('langue');
	
    // on récupère les données de la liste
    if($id_abomailman){
    	if($datas['desactive'] == '2'){
    		sql_delete("spip_abomailmans","id_abomailman = $id_abomailman");
    		$message['message_ok'] = _T('abomailmans:liste_supprimee',array("id"=>$id_abomailman,"titre"=> $datas['titre']));
    		$message['editable'] = false;
    	}else{
    		sql_updateq("spip_abomailmans",$datas,"id_abomailman = $id_abomailman");
    		$message['message_ok'] = _T('abomailmans:liste_updatee',array("id"=>$id_abomailman,"titre"=> $datas['titre']));
    	}
    	$message['redirect'] = parametre_url(self(),'id_abomailman',$id_abomailman);
    }
	else{
		$id_abomailman = sql_insertq("spip_abomailmans",$datas);
		$message['message_ok'] = _T('abomailmans:liste_creee',array("id"=>$id_abomailman,"titre"=> $datas['titre']));
		$message['editable'] = false;
		$message['redirect'] = parametre_url(self(),'id_abomailman',$id_abomailman);
	}
	
    return $message;
}

?>
