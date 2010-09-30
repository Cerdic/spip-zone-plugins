<?php
function dot2_migrer_utilisateur($blog_id,$user_id){
	$user = sql_fetsel('user_super,user_url,user_name,user_firstname,user_displayname,user_email','dc_user',"`user_id`=".sql_quote($user_id));
	

	// ! \\ SPIP ne fonctionne pas comme Doctclear au niveau des noms donc on va essayer de convertir
	if ($user['user_displayname']){
		$nom	= $user['user_displayname'];
	}
	else if ($user['user_firstname'] or $user['user_name']){
		$nom	= $user['user_firstname'].' '.$user['user_name'];
	}
	else{
		$nom	= $user_id;
	}
	
	// Détermination du statut
	$permissions = sql_fetsel('permissions','dc_permissions',"`user_id`=".sql_quote($user_id)."and `blog_id`=".sql_quote($blog_id));
		if($user['user_super']==1){
			$statut = '0minirezo';
			$webmestre='oui';
		}
		else if (match($permissions['permissions'],'|admin')){
			$statut = '0minirezo';
			$webmestre='non';
		}
		else{
			$statut	= '1comite';
			$webmestre='non';
	
		}

	//Création d'un mot de passe
	include_spip('inc/acces');
	$pass = creer_pass_aleatoire(8, $nom);
	
	//On insère en BDD
	$crud = charger_fonction('crud','action');
	$resultat = $crud('create','auteurs',null,array('login'=>$user_id,'nom'=>$nom,'statut'=>$statut,'url_site'=>$user['user_url'],'email'=>$user['user_email'],'pass'=>$pass,'webmestre'=>$webmestre));
	$id_auteur = $resultat['result']['id'];
	spip_log("Création de l'auteur $id_auteur ($user_id)",'dot2');
	
	// On envoie le mail
	include_spip('formulaires/inscription');
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	if (function_exists('envoyer_inscription'))
		$f = 'envoyer_inscription';
	else 	$f = 'envoyer_inscription_dist';
	list($sujet,$msg,$from,$head) = $f(array('login'=>$user_id,'pass'=>$pass),$nom,'',$id_auteur);
	if (!$envoyer_mail ($user['user_email'], $sujet, $msg, $from, $head)){
		spip_log("Impossible d'envoyer le mail pour l'auteur $id_auteur","dot2");	
	}
	return $id_auteur;
}


?>