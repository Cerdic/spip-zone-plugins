<?php

// balise/formulaire_abonnement.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');


// Balise independante du contexte


function balise_FORMULAIRE_ABONNEMENT ($p) {

spiplistes_log("balise_FORMULAIRE_ABONNEMENT() << ", _SPIPLISTES_LOG_DEBUG);

	return calculer_balise_dynamique($p, 'FORMULAIRE_ABONNEMENT', array('id_liste'));
}

// args[0] indique une liste, mais ne sert pas encore
// args[1] indique un eventuel squelette alternatif
// [(#FORMULAIRE_ABONNEMENT{mon_squelette})]
// un cas particulier est :
// [(#FORMULAIRE_ABONNEMENT{listeX})]
// qui permet d'afficher le formulaire d'abonnement a la liste numero X

function balise_FORMULAIRE_ABONNEMENT_stat($args, $filtres) {

spiplistes_log("balise_FORMULAIRE_ABONNEMENT_stat() << ", _SPIPLISTES_LOG_DEBUG);

	if(!$args[1]) {
		$args[1]='formulaire_abonnement';
	}
	// cherche appel de liste en arguments
	preg_match_all("/liste([0-9]+)/x", $args[1], $matches);
	if($id_liste=intval($matches[1][0])) {
spiplistes_log("balise_FORMULAIRE_ABONNEMENT_stat() UNE SEULE LISTE DEMANDEE ", _SPIPLISTES_LOG_DEBUG);
		$args[1]='formulaire_abonnement_une_liste';
		$args[0]=$id_liste;
	}
	
	return (array($args[0],$args[1]));
}

// Si inscriptions pas autorisees, retourner une chaine d'avertissement
// Sinon inclusion du squelette
// Si pas de nom ou pas de mail valide, premier appel rien d'autre a faire
// Autrement 2e appel, envoyer un mail et le squelette ne produira pas de
// formulaire.


function balise_FORMULAIRE_ABONNEMENT_dyn($id_liste, $formulaire) {

spiplistes_log("balise_FORMULAIRE_ABONNEMENT_dyn() << ", _SPIPLISTES_LOG_DEBUG);

	include_spip ("inc/meta");
	include_spip ("inc/session");
	include_spip ("inc/filtres");
	include_spip ("inc/texte");
	include_spip ("inc/meta");
	include_spip ("inc/mail");
	include_spip ("inc/acces");
		
		
	//recuperation des variables utiles
	$oubli_pass = _request('oubli_pass');
	$email_oubli = _request('email_oubli');
	$type = _request('type');
	$mail_inscription_ = _request('mail_inscription_');
	$desabo = _request('desabo');
	
	
	// recuperation de la config
	$acces_abonne = lire_meta('abonnement_config');
	$acces_membres = ($acces_abonne == 'membre') ? 'oui' : 'non';
		
	// aller chercher le formulaire html qui va bien				
	$formulaire = "formulaires/".$formulaire ;		
			
	// code inscription au site ou/et  a la lettre d'info	
		
	$inscriptions_ecrire = (lire_meta("accepter_inscriptions") == "oui") ;
	$inscriptions_publiques = (lire_meta('accepter_visiteurs') == "oui");
	
	$affiche_formulaire = $inscription_redac = $inscription_visiteur = "";
	
	// envoyer le cookie de relance mot de passe si pass oublie
	if ($email_oubli) {
		if (email_valide($email_oubli)) {
			$res = spip_query("SELECT * FROM spip_auteurs WHERE email ="._q($email_oubli)." LIMIT 1");
			if ($row = spip_fetch_array($res)) {
				if ($row['statut'] == '5poubelle')
					$erreur = _T('pass_erreur_acces_refuse');
				else {
					$cookie = creer_uniqid();
					spip_query("UPDATE spip_auteurs SET cookie_oubli = "._q($cookie)." WHERE email ="._q($email_oubli)." LIMIT 1");
	
					$nom_site_spip = lire_meta("nom_site");
					$adresse_site = lire_meta("adresse_site");
	
				
					$message = _T('spiplistes:abonnement_mail_passcookie'
						, array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site, 'cookie' => $cookie));
				
					if (envoyer_mail($email_oubli, "[$nom_site_spip] "._T('pass_oubli_mot'), $message)) {
						$erreur = _T('pass_recevoir_mail');
					}
					else {
						$erreur = _T('pass_erreur_probleme_technique');
					}
				}
			}
			else
				$erreur = _T('pass_erreur_non_enregistre', array('email_oubli' => htmlspecialchars($email_oubli)));
		}
		else
			$erreur = _T('pass_erreur_non_valide', array('email_oubli' => htmlspecialchars($email_oubli)));
	} // end if $email_oubli
	
	// afficher le formulaire d'oubli du pass
	if($oubli_pass == "oui") {
		return array($formulaire, $GLOBALS['delais'],
			array(
				'oubli_pass' => $oubli_pass,
				'erreur' => $erreur,
				'inscription_redac' => '',
				'inscription_visiteur' => '',
				'mode_login' => false,
				'reponse_formulaire' => '',
				'liste' => ''
					)
			);
	}
	//code pour s inscrire
	else if($inscriptions_ecrire OR $inscriptions_publiques OR (lire_meta('forums_publics') == 'abo') ) {
		// debut presentation
	
		$inscription_redac = ($inscriptions_ecrire && ($type=="redac")) ? "oui" : "non" ;
		
		$inscription_visiteur = (($type!="redac") && $inscriptions_publiques && ($acces_membres=='oui')) ? "oui" : "non" ;
				
		list($affiche_formulaire, $reponse_formulaire) = 
			formulaire_inscription($mail_inscription_, (($type=="redac") ? 'redac' : 'forum'), $acces_membres, $formulaire);
	}
	else {
		spiplistes_log(_T('pass_erreur')." acces visiteurs non autorises", _SPIPLISTES_LOG_DEBUG);
	}
	

	return array($formulaire, $GLOBALS['delais'],
				array(
					'oubli_pass' => $oubli_pass,
					'erreur' => $erreur,
					'inscription_redacteur' => $inscription_redac,
					'acces_membres' => $acces_membres,
					'inscription_visiteur' => $inscription_visiteur,
					'mode_login' => $affiche_formulaire,
					'reponse_formulaire' => $reponse_formulaire,
					'accepter_auteur' => lire_meta("accepter_inscriptions") ,
					'id_liste' => $id_liste
					)
				);
				
				
} // end balise_FORMULAIRE_ABONNEMENT_dyn()


// Abonnement d'un visiteur.
// Si adresse_mail d�j� dans la base, rajoute l'abonnement
// sinon, cr�� un login � partir de l'email et l'abonne
// Dans ces deux cas, renvoie un mail de confirmation.
function formulaire_inscription($mail_inscription_, $type, $acces_membres, $formulaire) {
	
	
		 $nom_inscription_ = _request('nom_inscription_');
		 $list = _request('list');
		 $liste = _request('liste');
		 $id_fond = _request('id_fond'); //fond name of the form posting values
	

	if ($type == 'redac') {
		if (lire_meta("accepter_inscriptions") != "oui") return;
		$statut = "nouveau";
	}
	else if ($type == 'forum') {
		$statut = "6forum";
	}
	else {
		return; // tentative de hack...?
	}


	
	if($acces_membres == 'non') {
		$nom_inscription_ = test_login2($mail_inscription_);
	}

	//utiliser_langue_site();
	$nomsite = lire_meta("nom_site");
	$urlsite = lire_meta("adresse_site");
	
	//Verify the form source. This way it is possible to create many newsletter forms
	//in the same page (but with different fond) to separate subscription and deletion as an example
	$verify_source_fond = false;
	if(!$id_fond) {$verify_source_fond = true;}
	elseif($id_fond==$formulaire) $verify_source_fond = true;

	
	if($mail_inscription_ && $verify_source_fond) {
		$mail_valide = email_valide($mail_inscription_);	
	}
		
	if ($mail_valide && $nom_inscription_) {
	
spiplistes_log("### : ->".$mail_inscription_, _SPIPLISTES_LOG_DEBUG);
		$result = spip_query("SELECT * FROM spip_auteurs WHERE email="._q($mail_inscription_));
	
		//echo "<div class='reponse_formulaire'>";
	

	
		// l'abonne existe deja.
		if ($row = spip_fetch_array($result)) {
			$id_auteur = $row['id_auteur'];
			$statut = $row['statut'];
			$abonne_existant = "oui" ;

			unset ($continue);
			if ($statut == '5poubelle') {
				$reponse_formulaire = _T('form_forum_access_refuse');
			}
			elseif ($statut == 'nouveau') {
				spip_query ("DELETE FROM spip_auteurs WHERE id_auteur="._q($id_auteur));
				$continue = true;
			}
			else{
			// envoyer le cookie de relance modif abonnement

			$cookie = creer_uniqid();
			spip_query("UPDATE spip_auteurs SET cookie_oubli = "._q($cookie)." WHERE email ="._q($mail_inscription_));

			$message = _T('spiplistes:abonnement_mail_passcookie', array('nom_site_spip' => $nomsite, 'adresse_site' => $urlsite, 'cookie' => $cookie));
				if (envoyer_mail($mail_inscription_, "[$nomsite] "._T('spiplistes:abonnement_titre_mail'), $message)){
					$reponse_formulaire =_T('spiplistes:pass_recevoir_mail');
					//echo _T('spiplistes:pass_recevoir_mail');
				}else{
					$reponse_formulaire =_T('pass_erreur_probleme_technique');
					//echo _T('pass_erreur_probleme_technique');
				}
			}
		}
		else {
			$continue = true;
		}
			
		// envoyer identifiants par mail
		if ($continue) {

		//ajouter un code pour retrouver l'abonne
		
		$pass = creer_pass_aleatoire(8, $mail_inscription_);
		$login_ = trim(test_login2($mail_inscription_));
		$mdpass = md5($pass);
		$htpass = generer_htpass($pass);
		
		$cookie = creer_uniqid();
					
		$type_abo = _request('suppl_abo') ;
		//verify suppl_abo is correct
		if($desabo!="oui" && $type_abo!="texte" && $type_abo!="html") return;
		
		// inscription d'un abonn�
		if(!empty($login_)) {
			$sql_result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login=$login LIMIT 1");
			if(sql_count($sql_result)) {
				while($row = spip_fetch_array($sql_result)) {
				}
			}
			else {
			// n'existe pas, cr�ation du compte ...
				$sql_result = 
					spip_query("INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, cookie_oubli) "
					. "VALUES ("._q($nom_inscription_).","._q($mail_inscription_).","._q($login_).","._q($mdpass).","._q($statut).","._q($htpass).","._q($cookie).")"
					);
spiplistes_log("insert inscription : ->".$mail_inscription_, _SPIPLISTES_LOG_DEBUG);
				$id_abo = spip_insert_id();
			}
				spip_query("INSERT INTO spip_auteurs_elargis (id_auteur,`spip_listes_format`) VALUES ("._q($id_abo).","._q($type_abo).")");		
		}

		// abonnement aux listes http://www.phpfrance.com/tutorials/index.php?page=2&id=13
		
		$result = spip_query("SELECT * FROM spip_auteurs WHERE email="._q($mail_inscription_));
	
		// l'abonne existe deja.
		 if ($row = spip_fetch_array($result)) {
		 $id_auteur = $row['id_auteur'];
		 $statut = $row['statut'];
			
			// on abonne l'auteur aux listes
			if(is_array($list)){
				while( list(,$val) = each($list) ){
					$result=spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($val));
					$result=spip_query("INSERT INTO spip_auteurs_listes (id_auteur,id_liste) VALUES ("._q($id_auteur).","._q($val).")");
				}
			}
		 }
	
		// abo
		
		ecrire_acces();
		
		$nom_site_spip = lire_meta("nom_site");
		$adresse_site = lire_meta("adresse_site");
	
		$message = _T('form_forum_message_auto')."\n\n"._T('spiplistes:bonjour')."\n";
				
		if ($desabo=="oui"){
			$message .= _T('spiplistes:mail_non', array('nom_site_spip' => $nom_site_spip))."\n";
		}
		else if($type_abo=="texte" || $type_abo=="html")  {
			
			//SELECT des listes de l'abonne		
			$result_list = spip_query("SELECT * FROM spip_auteurs_listes AS abonnements, spip_listes AS listes WHERE abonnements.id_auteur="._q($id_auteur)." AND abonnements.id_liste=listes.id_liste AND listes.statut='liste'");
			
			//lister les listes
			$message_list = '' ;
			$i = 0 ;
			
			while($row = spip_fetch_array($result_list)) {			
				$id_liste = $row['id_liste'] ;	
				$result = spip_query("SELECT * FROM spip_listes WHERE id_liste="._q($id_liste));
				$row = spip_fetch_array($result);
				$titre = $row['titre'] ;
				$message_list .= "\n- ".$titre ;
				$i++ ;
			}
			

			if($i>1) {
				$message .= ""
					. "\n"._T('spiplistes:inscription_responses').$nom_site_spip."."
					. "\n"._T('spiplistes:inscription_listes').$message_list 
					;
			} 
			if($i==1) {
				$message .= ""
					. "\n"._T('spiplistes:inscription_response').$nom_site_spip."."
					. "\n"._T('spiplistes:inscription_liste').$message_list 
					;
			} 
			if($i==0) {
				$message .= "\n"._T('spiplistes:inscription_response').$nom_site_spip._T('spiplistes:inscription_format').$type_abo."." ;
			}
		} // end else
	
			if(($acces_membres == 'oui') && ($type == 'forum') ) {
				$message .= ""
					. "\n\n"._T('spiplistes:inscription_mail_forum', array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site))."\n\n"
					. "- "._T('form_forum_login')." $login_\n"
					. "- "._T('form_forum_pass')." $pass\n\n"
					;
			}
			
			if(($type == 'redac') || ($inscriptions_ecrire && $acces_membres == 'non')) {
				$message .= ""
					. "\n\n"._T('spiplistes:inscription_mail_redac', array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site))."\n\n"
					. "- "._T('form_forum_login')." $login_\n"
					. "- "._T('form_forum_pass')." $pass\n\n"
					;
			}
		}
	
		$message .= ""
			. "\n\n-----------------------------------------\n\n"
			. _T('spiplistes:abonnement_mail').' '.generer_url_public("abonnement","d=$cookie")
			. "\n\n-----------------------------------------\n\n"
			;
			
		if($abonne_existant != 'oui') {
		
			if (envoyer_mail($mail_inscription_, "[$nom_site_spip] "._T('spiplistes:form_forum_identifiants'), $message)) {
spiplistes_log("inscription : ->".$mail_inscription_, _SPIPLISTES_LOG_DEBUG);
				if($acces_membres == 'oui') {
					$reponse_formulaire =_T('form_forum_identifiant_mail');
				}
				else{
					$reponse_formulaire =_T('spiplistes:form_forum_identifiant_confirm');
				}
			}
			else {
				$reponse_formulaire =_T('form_forum_probleme_mail');
			}
		} // end if abonne_existant
	
	} // end if ($mail_valide && $nom_inscription_)
	else {
		//Non c'� email o non � valida
		// adresse email invalide ?
		if($mail_inscription_ && !$mail_valide && $verify_source_fond) {
			$reponse_formulaire =_T('spiplistes:erreur_adresse');
		}
		
		//Infos sur la liste
		if(!$liste) $liste='';
		return array(true, $reponse_formulaire);
	}
	return array(false, $reponse_formulaire);

} // end formulaire_inscription()


function test_login2($mail) {
	if (strpos($mail, "@") > 0) $login_base = substr($mail, 0, strpos($mail, "@"));
	else $login_base = $mail;

	$login_base = strtolower($login_base);
	$login_base = ereg_replace("[^a-zA-Z0-9]", "", $login_base);
	
	if (!$login_base) {
		$login_base = "user";
	}

// passage � revoir. trop de req pour une simple cr�ation (CP-20071016)
	for ($i = 0; ; $i++) {
		if ($i) $login = $login_base.$i;
		else $login = $login_base;
		$result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login="._q($login));
		if (!sql_count($result)) break;
	}

	return $login;
}


?>