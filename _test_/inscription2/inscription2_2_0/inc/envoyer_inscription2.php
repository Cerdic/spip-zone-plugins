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
	// 1- Prénom Nom_de_famille si dispo
	// 2- Nom si dispo
	// 3- Vraiment en dernier recours l'email
	
	if($var_user['nom_famille']){
		$nom_final = $var_user['prenom']." ".$var_user['nom_famille'];
	}
	else if($var_user['nom']){
		$nom_final = $var_user['nom'];
	}
	else{
		$nom_final = $var_user['email'];
	}
	
    spip_log("envoie mail id: $id_auteur","inscription2");
    spip_log($var_user,'inscription2');

	// Dans le cas ou on ne demande pas de mot de passe dans le formulaire de création de compte
	if($var_user['alea_actuel']==''){ 
 		$var_user['alea_actuel'] = rand(1,99999); 
 		sql_updateq(
			"spip_auteurs",
			array(
				"alea_actuel" => $var_user['alea_actuel']
			),
			"id_auteur = $id_auteur"
 		);
	}
 	if($mode=="inscription"){
		$message = _T('inscription2:message_auto')."\n\n"
				. _T('inscription2:email_bonjour', array('nom'=> $var_user['prenom']." ".$var_user['nom']))."\n\n"
				. _T('inscription2:texte_email_inscription', array(
				'link_activation' => $adresse_site.'/spip.php?page=inscription2_confirmation&id='
				   .$var_user['id_auteur'].'&cle='.$var_user['alea_actuel'].'&mode=conf', 
				'link_suppresion' => $adresse_site.'/spip.php?page=inscription2_confirmation&id='
				   .$var_user['id_auteur'].'&cle='.$var_user['alea_actuel'].'&mode=sup',
				'login' => $var_user['login'], 'nom_site' => $nom_site_spip)
				);			
		$sujet = "[$nom_site_spip] "._T('inscription2:activation_compte');
	}else if($mode=="inscription_pass"){
		$message = _T('inscription2:message_auto')."\n\n"
				. _T('inscription2:email_bonjour', array('nom'=> $nom_final))."\n\n"
				. _T('inscription2:texte_email_confirmation', array('login' => $var_user['login'], 'nom_site' => $nom_site_spip, 'url_site' => $adresse_site));
		$sujet = "[$nom_site_spip] "._T('inscription2:compte_active',array('nom_site'=>$nom_site_spip));
	}
	else if($mode=="rappel_mdp"){
		$args = "id=". $var_user['id_auteur']."&cle=".$var_user['alea_actuel']."&mode=conf";
		$page_confirmation = generer_url_public('inscription2_confirmation',$args,'false','false');
		
	 	$message = _T('inscription2:message_auto')."\n\n" 
	 	. _T('inscription2:email_bonjour', array('nom'=>sinon($var_user['prenom'],$var_user['nom'])))."\n\n" 
	 	. _T('inscription2:rappel_password')."\n\n"
	 	. _T('inscription2:choisir_nouveau_password')."\n\n"
	 	. $page_confirmation."\n\n"
	 	. _T('inscription2:rappel_login') . $var_user['login'] ;
	 	$sujet = "[$nom_site_spip] "._T('inscription2:rappel_password');
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