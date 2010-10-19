<?php

//email envoye lors de l'inscription
function inc_envoyer_inscription2_dist($id_auteur,$mode) {
    include_spip('inc/mail');
	
	if(!$mode){
		$mode = 'inscription';
	}
	// On récupère les données nécessaires à envoyer le mail de validation
	// La fonction envoyer_mail se chargera de nettoyer cela plus tard
	$nom_site_spip = $GLOBALS['meta']["nom_site"];
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	
	$prenom = (lire_config('inscription2/prenom')) ? "b.prenom," : "" ;
	$nom = (lire_config('inscription2/nom_famille')) ? "b.nom_famille," : "" ;

    $var_user = sql_fetsel(
        "a.nom,$prenom $nom a.id_auteur, a.alea_actuel, a.login, a.email",
        "spip_auteurs AS a LEFT JOIN spip_auteurs_elargis AS b USING(id_auteur)",
        "a.id_auteur =$id_auteur"
    );
	
	// On défini le nom qui sera utilisé dans le mail
	
	if($var_user['nom']){
		$nom_final = $var_user['nom'];
	}
	else{
		$nom_final = $var_user['email'];
	}
	
    spip_log("envoie mail id: $id_auteur","inscription2");
    spip_log($var_user,'inscription2');

	// Dans le cas ou on ne demande pas de mot de passe dans le formulaire de création de compte
	if($var_user['alea_actuel']==''){ 
 		include_spip('inc/acces'); # pour creer_uniqid
		$cookie = creer_uniqid();
		sql_updateq("spip_auteurs", array("cookie_oubli" => $cookie), "id_auteur=" . $id_auteur);
	
	}
 	if($mode=="inscription"){
 		// nettoyer le mode sup
		$message = _T('inscription2:message_auto')."\n\n"
				. _T('inscription2:email_bonjour', array('nom'=> $nom_final))."\n\n"
				. _T('inscription2:texte_email_inscription', array(
				'nom_site' => $nom_site_spip, 'url_site' => $adresse_site,
				'link_activation' => generer_url_public('spip_pass','p='.$cookie, true), 
				'link_suppresion' => generer_url_public('spip_pass','s='.$cookie, true),
				)
				);			
	$sujet = "[$nom_site_spip] "._T('inscription2:activation_compte');
	}else if($mode=="inscription_pass"){
		// a nettoyer
		$message = _T('inscription2:message_auto')."\n\n"
				. _T('inscription2:email_bonjour', array('nom'=> $nom_final))."\n\n"
				. _T('inscription2:texte_email_confirmation', array('login' => $var_user['login'], 'nom_site' => $nom_site_spip, 'url_site' => $adresse_site));
		$sujet = "[$nom_site_spip] "._T('inscription2:compte_active',array('nom_site'=>$nom_site_spip));
	}

    spip_log($message,'inscription2');

	if (envoyer_mail($var_user['email'],
			$sujet,
			$message))
		return;
	else
		return _T('inscription2:probleme_email');
}
?>