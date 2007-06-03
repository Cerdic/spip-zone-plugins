<?php


if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_MODIF_ABONNEMENT ($p) {

	return calculer_balise_dynamique($p, 'FORMULAIRE_MODIF_ABONNEMENT', array());
}

function balise_FORMULAIRE_MODIF_ABONNEMENT_stat ($args, $filtres) {
	
	if(!$args[0]) $args[0]='formulaire_modif_abonnement';
	return array($args[0]);
}



function balise_FORMULAIRE_MODIF_ABONNEMENT_dyn($formulaire) {

include_spip ("inc/meta");
include_spip ("inc/session");
include_spip ("inc/filtres");
include_spip ("inc/texte");
include_spip ("inc/meta");
include_spip ("inc/mail");
include_spip ("inc/acces");

	global $confirm,$d,$list,$champs_extra,$email_desabo;

	
	//utiliser_langue_site();
	$nomsite=lire_meta("nom_site");
	$urlsite=lire_meta("adresse_site");
	
	// aller chercher le formulaire html qui va bien				
$formulaire = "formulaires/formulaire_modif_abonnement";	
	
	// 3 Cas :
	// 1) La personne valide le formulaire de modif, traitement des données
	// 2) Recuperer le cookie de relance désabonnement / afficher le forumlaire de modif
	// 3) Envoyer par mail le cookie de relance modif abonnement
	//presentation
	
	
	 // La personne valide le formulaire
	
	 // revoir le test ?
	if($champs_extra AND ($confirm == 'oui') ){
		$res = spip_query("SELECT * FROM spip_auteurs WHERE cookie_oubli="._q($d)." AND statut<>'5poubelle' AND pass<>''");
	  if ($row = spip_fetch_array($res)) {
	  	$id_auteur = $row['id_auteur'];
			$statut = $row['statut'];
			$nom = $row['nom'];
			$mail_abo = $row['email'];
	
	    // abonnement aux listes
	    //(http://www.phpfrance.com/tutorials/index.php?page=2&id=13)
	
			//selectionne les listes et desabonne l'auteur
			$listes = spip_query ("SELECT * FROM spip_listes WHERE statut = 'liste'");
			while($row = spip_fetch_array($listes)) {
				$id_liste = $row['id_liste'] ;
				$result=spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($id_liste));
			}
	
			if(is_array($list)){
	
		 		// on abonne l'auteur aux listes choisies
		 		while( list(,$val) = each($list) ){
				
		      $result=spip_query("INSERT INTO spip_auteurs_listes (id_auteur,id_liste) VALUES ("._q($id_auteur).","._q($val).")");
				
		 		}
			} else { $desabo="oui"; }
	
			// fin de l'abo  aux listes
		
		 	// prendre en compte les extras
		
		  $extras = bloog_extra_recup_saisie('auteurs');
		
		  spip_query("UPDATE spip_auteurs SET extra = "._q($extras)." WHERE cookie_oubli ="._q($d));
		  spip_query("UPDATE spip_auteurs SET cookie_oubli = '0' WHERE cookie_oubli ="._q($d));
		
		  // affichage des modifs
		
		  $extra = get_extra($id_auteur,'auteur');
	
	   	If ($extra['abo'] == 'non')  {
				$msg_formulaire = "<h4>"._T('spiplistes:desabonnement_valid')."</h4>".$mail_abo;
			  
		  }
	   	else {
	   		$msg_formulaire = "<h4>"._T('spiplistes:abonnement_modifie')."</h4>" ;
	   		$confirm_formulaire = "<p>"._T('spiplistes:abonnement_nouveau_format').$extra['abo']."<br />";
	   	}
	
	
	  } else  {
	           	$msg_formulaire = _T('pass_erreur_code_inconnu');
	  }
	  
	  return array($formulaire, $GLOBALS['delais'],
			array(
				'message_formulaire' => $msg_formulaire,
				'id_auteur' => $id_auteur,
				'confirm_formulaire' => $confirm_formulaire,
				'formulaire_affiche' => '',
				'formulaire_cookie_affiche' => '',
				'modif_affiche' => '1'
					)
			);
	}
	
	// recuperer le cookie de relance désabonnement, et afficher le formulaire de modif
	if ($d  AND ($confirm != 'oui')) {
	
		$res = spip_query ("SELECT * FROM spip_auteurs WHERE cookie_oubli="._q($d)." AND statut<>'5poubelle' AND pass<>''");
		if ($row = spip_fetch_array($res)) {
			$formulaire_affiche = '1';
	  	$id_auteur = $row['id_auteur'];
			$extra_aut = $row['extra'];
		}
		else
		{
	     	$formulaire_affiche = '';
				$msg_formulaire = _T('pass_erreur_code_inconnu');
	  }
	  
	  return array($formulaire, $GLOBALS['delais'],
			array(
				'message_formulaire' => $msg_formulaire,
				'id_auteur' => $id_auteur,
				'confirm_formulaire' => $confirm_formulaire,
				'formulaire_affiche' => $formulaire_affiche,
				'formulaire_cookie_affiche' => '',
				'd' => $d,
				'extra_aut' => $extra_aut
					)
			);
			
	}  else {
	
	// envoyer le cookie de relance modif abonnement
	if ($email_desabo) {
		if (email_valide($email_desabo)) {
			$res = spip_query("SELECT * FROM spip_auteurs WHERE email ="._q($email_desabo));
			if ($row = spip_fetch_array($res)) {
				if ($row['statut'] == '5poubelle')
					$erreur = _T('pass_erreur_acces_refuse');
				else {
					$cookie = creer_uniqid();
					spip_query("UPDATE spip_auteurs SET cookie_oubli = "._q($cookie)." WHERE email ="._q($email_desabo));
					$message = _T('spiplistes:abonnement_mail_passcookie', array('nom_site_spip' => $nomsite, 'adresse_site' => $urlsite, 'cookie' => $cookie));
					
					if (envoyer_mail($email_desabo, "[$nomsite] "._T('spiplistes:abonnement_titre_mail'), $message))
						$erreur = _T('spiplistes:pass_recevoir_mail');
					else
						$erreur = _T('pass_erreur_probleme_technique');
				}
			}
			else
				$erreur = _T('pass_erreur_non_enregistre', array('email_oubli' => htmlspecialchars($email_desabo)));
		}
		else
			$erreur = _T('spiplistes:erreur_adresse');

	}
	
	if($confirm != 'oui'){
	  $formulaire_cookie = '1';      
	}
	}
	
	return array($formulaire, $GLOBALS['delais'],
			array(
				'message_formulaire' => $msg_formulaire,
				'id_auteur' => '',
				'confirm_formulaire' => $confirm_formulaire,
				'formulaire_affiche' => '',
				'formulaire_cookie_affiche' => $formulaire_cookie,
				'erreur' => $erreur
					)
			);

}

?>
