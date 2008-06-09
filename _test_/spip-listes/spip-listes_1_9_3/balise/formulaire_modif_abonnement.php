<?php

// balise/formulaire_modif_abonnement.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/spiplistes_api_globales');

function balise_FORMULAIRE_MODIF_ABONNEMENT ($p) {

	return calculer_balise_dynamique($p, 'FORMULAIRE_MODIF_ABONNEMENT', array());
}

function balise_FORMULAIRE_MODIF_ABONNEMENT_stat ($args, $filtres) {

spiplistes_log("balise_FORMULAIRE_MODIF_ABONNEMENT_stat () <<", _SPIPLISTES_LOG_DEBUG);

	if(!$args[0]) {
		$args[0]='formulaire_modif_abonnement';
	}
	return array($args[0]);
}

function balise_FORMULAIRE_MODIF_ABONNEMENT_dyn ($formulaire) {

spiplistes_log("balise_FORMULAIRE_MODIF_ABONNEMENT_dyn () <<", _SPIPLISTES_LOG_DEBUG);

	include_spip ("inc/meta");
	include_spip ("inc/session");
	include_spip ("inc/filtres");
	include_spip ("inc/texte");
	include_spip ("inc/meta");
	include_spip ("inc/mail");
	include_spip ("inc/acces");
	include_spip ("inc/spiplistes_api");

	$confirm = _request('confirm');
	$d = _request('d');
	$list = _request('list');
	$email_desabo = _request('email_desabo');

	$formulaire_cookie_affiche = $formulaire_affiche = $message_formulaire = $modif_affiche = $erreur = '';
	
	//utiliser_langue_site();
	$nomsite = $GLOBALS['meta']['nom_site'];
	$urlsite = $GLOBALS['meta']['adresse_site'];
	
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
		$sql_result = sql_select(
			$sql_select
			, 'spip_auteurs'
			, array(
				"cookie_oubli=".sql_quote($d)
				, "statut<>".sql_quote('5poubelle')
				, "pass<>".sql_quote('')
			)
			, '', '', 1
		);
		$row = sql_fetch($sql_result);
		
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
				spiplistes_abonnements_desabonner_statut($id_auteur, explode(";", _SPIPLISTES_LISTES_STATUTS_TOUS));
	
				if(is_array($list) && count($list)) {	
					// on abonne l'auteur aux listes choisies
					if(spiplistes_abonnements_ajouter($id_auteur, $list) !== false) {
						$message_formulaire = _T('spiplistes:abonnement_modifie');
					}
				} 
				
				// maj du format de reception
				$type_abo = _request('suppl_abo'); 
				if($format != $type_abo) {
					$format = $type_abo;
					spiplistes_format_abo_modifier($id_auteur, $format);
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
				//spip_query("UPDATE spip_auteurs SET cookie_oubli='' WHERE cookie_oubli =".sql_quote($d));
				spiplistes_auteurs_cookie_oubli_updateq('', $d, $true);
				
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
			$res = sql_select(
				"*"
				, "spip_auteurs"
				, "email =".sql_quote($email_desabo)
				, '', '', 1
			);
			if ($row = sql_fetch($res)) {
				if ($row['statut'] == '5poubelle') {
					$erreur = _T('pass_erreur_acces_refuse');
				}
				else {
					$cookie = creer_uniqid();
					spiplistes_auteurs_cookie_oubli_updateq($cookie, $email_desabo);
					
					$message = _T('spiplistes:abonnement_mail_passcookie', array('nom_site_spip' => $nomsite, 'adresse_site' => $urlsite, 'cookie' => $cookie));
					
					if(
						spiplistes_envoyer_mail(
							$email_desabo
							, "[$nomsite] "._T('spiplistes:abonnement_titre_mail')
							, $message
							)
					) {
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