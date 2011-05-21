<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abomailman_creation_liste_charger_dist($id_abomailman = ""){
	//initialise les variables d'environnement pas défaut
	$valeurs = array();
	$valeurs['editable'] = true; 

	// On verifie que la liste existe
	if ($id_abomailman){
		$valeurs = sql_fetsel('*','spip_abomailmans',"id_abomailman=$id_abomailman");
		$valeurs['langue'] = $valeurs['lang'];
		if(!$valeurs['id_abomailman']){
			$valeurs['editable'] = false;
			$valeurs['message_erreur'] = _T('abomailmans:liste_non_existante');
		}
	}	 
	
	if(!$valeurs['langue']){
		$valeurs['langue'] = lang_select();
	}
	unset($valeurs['lang']);

	$recuptemplate = explode('&',$valeurs['modele_defaut']);
	$valeurs['template'] = $recuptemplate[0];
	$valeurs['envoi_liste_parametres']=recup_param($valeurs['modele_defaut']);
	 
	return $valeurs;
}

function formulaires_abomailman_creation_liste_verifier_dist($id_abomailman = ""){

	//initialise le tableau des erreurs
	$erreurs = array();

	// Faire une fonction de verif sur le mail et le titre pour validite
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$email_sympa = _request('email_subscribe');
	$email_sympa = _request('email_unsubscribe');
	$email_sympa = _request('email_sympa');
	$valeurs['template'] = str_replace('\'','',_request('template'));
	$valeurs['envoi_liste_parametres'] = _request('envoi_liste_parametres');
	//if($valeurs['template']) $valeurs['template'];
	$valeurs['periodicite'] = _request('periodicite');
	$desactive = _request('desactive');
	$lang = _request('lang');

	// Si on fait une suppression, on ne vérifie pas le reste
	if($desactive != '2'){
		if(!$email){
			$erreurs['email'] = _T("abomailmans:erreur_email_liste_oublie");
		}

		if(!$titre){
			$erreurs['titre'] = _T("abomailmans:titre_liste_obligatoire");
		}

		if (!count($erreurs)){
			include_spip('inc/filtres'); # pour email_valide()
			if (!email_valide($email)){
				$erreurs['email'] = _T("abomailmans:email_valide");
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
   $message = array();
	$message['editable'] = true;
	$valeurs['envoi_liste_parametres'] = _request('envoi_liste_parametres');

	$datas = array();

    // Récupération des données
	$datas['titre'] = _request('titre');
	$datas['descriptif'] = _request('descriptif');
	$datas['email'] = _request('email');
	$datas['email_subscribe'] = _request('email_subscribe');
	$datas['email_unsubscribe'] = _request('email_unsubscribe');
	$datas['email_sympa'] = _request('email_sympa');
	$datas['desactive'] = _request('desactive');
	$datas['modele_defaut'] = str_replace('\'','',_request('template'))."".$valeurs['envoi_liste_parametres'];
	$datas['periodicite'] = _request('periodicite');
	$datas['lang'] = _request('langue');

	// on récupère les données de la liste
	if(intval($id_abomailman)){
		if($datas['desactive'] == '2'){
			sql_delete("spip_abomailmans","id_abomailman = $id_abomailman");
			$message['message_ok'] = _T('abomailmans:liste_supprimee',array("id"=>$id_abomailman,"titre"=> $datas['titre']));
			$message['editable'] = false;
		}else{
			sql_updateq("spip_abomailmans",$datas,"id_abomailman = $id_abomailman");
			$message['message_ok'] = _T('abomailmans:liste_updatee',array("id"=>$id_abomailman,"titre"=> $datas['titre']));
		}
	}else{
		$id_abomailman = sql_insertq("spip_abomailmans",$datas);
		$message['message_ok'] = _T('abomailmans:liste_creee',array("id"=>$id_abomailman,"titre"=> $datas['titre']));
		$message['editable'] = false;
	}

	$message['redirect'] = parametre_url(self(),'id_abo',$id_abomailman);

   return $message;
}

?>