<?php
// balise/formulaire_modif_abonnement.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_MODIF_ABONNEMENT ($p) {

	return calculer_balise_dynamique($p, 'FORMULAIRE_MODIF_ABONNEMENT', array());
}

function balise_FORMULAIRE_MODIF_ABONNEMENT_stat ($args, $filtres) {

spiplistes_log("balise_FORMULAIRE_MODIF_ABONNEMENT_stat () <<");

	if(!$args[0]) {
		$args[0]='formulaire_modif_abonnement';
	}
	return array($args[0]);
}

function balise_FORMULAIRE_MODIF_ABONNEMENT_dyn ($formulaire) {

spiplistes_log("balise_FORMULAIRE_MODIF_ABONNEMENT_dyn () <<");

	include_spip ("inc/meta");
	include_spip ("inc/session");
	include_spip ("inc/filtres");
	include_spip ("inc/texte");
	include_spip ("inc/meta");
	include_spip ("inc/mail");
	include_spip ("inc/acces");
	include_spip ("inc/spiplistes_api");


	global $confirm, $d, $list, $email_desabo;

	$formulaire_cookie_affiche = $formulaire_affiche = $message_formulaire = $modif_affiche = $erreur = '';
	
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
	
	
	if(!empty($d)) {
		// cookie reçu
		
		// cherche l'abonné
		$sql_select = "id_auteur,statut,nom,email";
		$sql_result = spip_query("SELECT $sql_select FROM spip_auteurs WHERE cookie_oubli="._q($d)." AND statut<>'5poubelle' AND pass<>'' LIMIT 1");
		$row = spip_fetch_array($sql_result);
		
		if($row) {
			// abonné trouvé
			foreach(explode(",",$sql_select) as $key) {
				$$key = $row[$key];
			}
			$id_auteur = intval($id_auteur);
			$format = spiplistes_format_abo_demande($id_auteur);
	
			// confirme les modifications ?
			if($confirm == 'oui') {
				// désabonne l'auteur
				spiplistes_desabonner_listes_statut($id_auteur, array(_SPIPLISTES_PUBLIC_LIST, _SPIPLISTES_PRIVATE_LIST,_SPIPLISTES_MONTHLY_LIST));
	
				if(is_array($list) && count($list)) {	
					// on abonne l'auteur aux listes choisies
					$sql_values = "";
					while( list(,$val) = each($list) ) {
						$sql_values .= " ($id_auteur,"._q($val)."),";
					}
					$sql_values = rtrim($sql_values, ",");
					$sql_query = "INSERT INTO spip_auteurs_listes (id_auteur,id_liste) VALUES $sql_values";
					if(spip_query($sql_query)) {
						$message_formulaire = _T('spiplistes:abonnement_modifie');
					}
				} 
				
				// maj du format de reception
				$type_abo = _request('suppl_abo'); 
				if($format != $type_abo) {
					$format = $type_abo;
					spip_query("UPDATE spip_auteurs_elargis SET `spip_listes_format`="._q($format)." WHERE id_auteur=$id_auteur");	
					// affichage des modifs
					if($format == 'non') {
						$message_formulaire = _T('spiplistes:desabonnement_valid').":&nbsp;".$email;  
					}
					else {
						$message_formulaire = _T('spiplistes:abonnement_modifie');
						$confirm_formulaire = "<p>"._T('spiplistes:abonnement_nouveau_format').$format."<br />";
					}
				}
				
				// detruire le cookie perso
				spip_query("UPDATE spip_auteurs SET cookie_oubli='' WHERE cookie_oubli ="._q($d));
				$d = '';
				$modif_affiche = '1';
			} // end if($confirm == 'oui')
			
			// premier passage sur le formulaire...
			// recuperer le cookie de relance désabonnement, et afficher le formulaire de modif
			else {
				$formulaire_affiche = '1';
			}
		}
	} // end if($d)
	
	else if ($email_desabo) {
		// adresse email seule reçue
		// envoyer le cookie de relance modif abonnement par email
		if (email_valide($email_desabo)) {
			$res = spip_query("SELECT * FROM spip_auteurs WHERE email ="._q($email_desabo)." LIMIT 1");
			if ($row = spip_fetch_array($res)) {
				if ($row['statut'] == '5poubelle') {
					$erreur = _T('pass_erreur_acces_refuse');
				}
				else {
					$cookie = creer_uniqid();
					spip_query("UPDATE spip_auteurs SET cookie_oubli = "._q($cookie)." WHERE email ="._q($email_desabo)." LIMIT 1");
					
					$message = _T('spiplistes:abonnement_mail_passcookie', array('nom_site_spip' => $nomsite, 'adresse_site' => $urlsite, 'cookie' => $cookie));
					
					if (envoyer_mail($email_desabo, "[$nomsite] "._T('spiplistes:abonnement_titre_mail'), $message)) {
						$erreur = _T('spiplistes:pass_recevoir_mail');
					}
					else {
						$erreur = _T('pass_erreur_probleme_technique');
					}
				}
			}
			else {
				$erreur = _T('pass_erreur_non_enregistre', array('email_oubli' => htmlspecialchars($email_desabo)));
			}
		}
		else {
			$erreur = _T('spiplistes:erreur_adresse');
		}
		$formulaire_cookie_affiche = '1';
		$id_auteur = false;
	} // end else if ($email_desabo)
	else {
		$message_formulaire = _T('pass_erreur_code_inconnu');
	}
	
	if(!empty($message_formulaire)) $message_formulaire = "<span class='msg_formulaire'>$message_formulaire</span>";
	
	return array($formulaire, $GLOBALS['delais'],
			array(
				'message_formulaire' => $message_formulaire
				, 'id_auteur' => $id_auteur
				, 'confirm_formulaire' => $confirm_formulaire
				, 'formulaire_affiche' => $formulaire_affiche
				, 'formulaire_cookie_affiche' => $formulaire_cookie_affiche
				, 'd' => $d
				, 'format' => $format
				, 'modif_affiche' => $modif_affiche
				, 'erreur' => $erreur
					)
			);
}

?>