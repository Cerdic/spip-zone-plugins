<?php

// balise/formulaire_abonnement.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if(!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');


// Balise independante du contexte


function balise_FORMULAIRE_ABONNEMENT ($p) {

spiplistes_log("balise_FORMULAIRE_ABONNEMENT()", _SPIPLISTES_LOG_DEBUG);

	return(calculer_balise_dynamique($p, 'FORMULAIRE_ABONNEMENT', array('id_liste')));
}

// args[0] indique une liste, mais ne sert pas encore
// args[1] indique un eventuel squelette alternatif
// [(#FORMULAIRE_ABONNEMENT{mon_squelette})]
// un cas particulier est :
// [(#FORMULAIRE_ABONNEMENT{listeX})]
// qui permet d'afficher le formulaire d'abonnement a la liste numero X

function balise_FORMULAIRE_ABONNEMENT_stat($args, $filtres) {

spiplistes_log("balise_FORMULAIRE_ABONNEMENT_stat()", _SPIPLISTES_LOG_DEBUG);

	if(!$args[1]) {
		$args[1]='formulaire_abonnement';
	}
	// cherche appel de liste en arguments
	preg_match_all("/liste([0-9]+)/x", $args[1], $matches);
	if($id_liste = intval($matches[1][0])) {
spiplistes_log("balise_FORMULAIRE_ABONNEMENT_stat() UNE SEULE LISTE DEMANDEE ", _SPIPLISTES_LOG_DEBUG);
		$args[1]='formulaire_abonnement_une_liste';
		$args[0]=$id_liste;
	}
	
	return(array($args[0],$args[1]));
}

// Si inscriptions pas autorisees, retourner une chaine d'avertissement
// Sinon inclusion du squelette
// Si pas de nom ou pas de mail valide, premier appel rien d'autre a faire
// Autrement 2e appel, envoyer un mail et le squelette ne produira pas de
// formulaire.


function balise_FORMULAIRE_ABONNEMENT_dyn($id_liste, $formulaire) {

spiplistes_log("balise_FORMULAIRE_ABONNEMENT_dyn()", _SPIPLISTES_LOG_DEBUG);

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
	$mail_inscription_ = trim(strtolower(_request('mail_inscription_')));
	$desabo = _request('desabo');
	
	// recuperation de la config SPIP-Listes
	// 'simple' = ne renvoie que la confirmation d'abonnement
	// 'membre' = complete le mail par un mot de passe pour se loger
	$acces_membres = ($GLOBALS['meta']['abonnement_config'] == 'membre') ? 'oui' : 'non';
		
	// aller chercher le formulaire html qui va bien				
	$formulaire = "formulaires/".$formulaire ;		
			
	// code inscription au site ou/et a la lettre d'info	
	$inscriptions_ecrire = ($GLOBALS['meta']['accepter_inscriptions'] == "oui");
	$inscriptions_publiques = ($GLOBALS['meta']['accepter_visiteurs'] == "oui");
	
	$affiche_formulaire = $inscription_redac = $inscription_visiteur = "";
	
	$nom_site_spip = $GLOBALS['meta']['nom_site'];
	$adresse_site = $GLOBALS['meta']['adresse_site'];
	if ($GLOBALS['meta']['spiplistes_charset_envoi']!=$GLOBALS['meta']['charset']){
		include_spip('inc/charsets');
		$nom_site_spip = unicode2charset(charset2unicode($nom_site_spip),$GLOBALS['meta']['spiplistes_charset_envoi']);
	}

	// envoyer le cookie de relance mot de passe si pass oublie
	if($email_oubli)
	{
		if(email_valide($email_oubli)) {

			$sql_result = spiplistes_auteurs_auteur_select('statut', "email=".sql_quote($email_oubli));
			
			if($row = sql_fetch($sql_result)) {
				if($row['statut'] == '5poubelle')
					$erreur = _T('pass_erreur_acces_refuse');
				else {
					$cookie = creer_uniqid();
					spiplistes_auteurs_cookie_oubli_updateq($cookie, $email_oubli);
					
					$message = _T('spiplistes:abonnement_mail_passcookie'
						, array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site, 'cookie' => $cookie));
				
					
					if(spiplistes_envoyer_mail($email_oubli, "[$nom_site_spip] "._T('pass_oubli_mot'), $message)) {
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
		{
			$erreur = _T('pass_erreur_non_valide', array('email_oubli' => htmlspecialchars($email_oubli)));
		}
	} // end if $email_oubli
	
	// afficher le formulaire d'oubli du pass
	if($oubli_pass == "oui") {
		return array($formulaire, $GLOBALS['delais'],
			array(
				'oubli_pass' => $oubli_pass
				, 'erreur' => $erreur
				, 'inscription_redac' => ''
				, 'inscription_visiteur' => ''
				, 'mode_login' => false
				, 'reponse_formulaire' => false
				, 'liste' => ''
			)
		);
	}
	
	//code pour s inscrire
	
	else if(
		$inscriptions_ecrire 
		|| $inscriptions_publiques 
		|| ($GLOBALS['meta']['forums_publics'] == 'abo') 
	) {
		// debut presentation
	
		$inscription_redac = 
			($inscriptions_ecrire && ($type=="redac")) 
			? "oui" 
			: "non"
			;
		
		$inscription_visiteur = 
			(($type!="redac") && $inscriptions_publiques && ($acces_membres=='oui')) 
			? "oui" 
			: "non"
			;
				
		list($affiche_formulaire, $reponse_formulaire) = 
			spiplistes_formulaire_inscription(
				$mail_inscription_
				, (($type=="redac") ? 'redac' : 'forum')
				, $acces_membres
				, $formulaire
				, $nom_site_spip
			);
	}
	else {
		spiplistes_log(_T('pass_erreur')." acces visiteurs non autorises", _SPIPLISTES_LOG_DEBUG);
	}
	
	return array($formulaire, $GLOBALS['delais'],
				array(
					'oubli_pass' => $oubli_pass
					, 'erreur' => $erreur
					, 'inscription_redacteur' => $inscription_redac
					, 'acces_membres' => $acces_membres
					, 'inscription_visiteur' => $inscription_visiteur
					, 'mode_login' => $affiche_formulaire
					, 'reponse_formulaire' => $reponse_formulaire
					, 'accepter_auteur' => $GLOBALS['meta']['accepter_inscriptions']
					, 'id_liste' => $id_liste
					)
				);
				
				
} // end balise_FORMULAIRE_ABONNEMENT_dyn()


// Abonnement d'un visiteur.
// Si adresse_mail deja dans la base, rajoute l'abonnement
// sinon, cree un login a partir de l'email et l'abonne
// Dans ces deux cas, renvoie un mail de confirmation.
function spiplistes_formulaire_inscription ($mail_inscription_, $type, $acces_membres, $formulaire, $nom_site_spip) {
	
	$nom_inscription_ = _request('nom_inscription_');
	$listes_demande = _request('list');
	$liste = _request('liste');
	$id_fond = _request('id_fond'); //fond name of the form posting values
	
	if(is_array($listes_demande)) {
		$listes_demande = array_map('intval', $listes_demande);
	}

	$adresse_site = $GLOBALS['meta']['adresse_site'];

	if($type == 'redac') {
		if($GLOBALS['meta']['accepter_inscriptions'] != "oui") return;
		$statut = "nouveau";
	}
	else if($type == 'forum') {
		$statut = "6forum";
	}
	else {
		return; // tentative de hack...?
	}
	
	if($acces_membres == 'non') {
		$nom_inscription_ = spiplistes_login_from_email($mail_inscription_);
	}

	//Verify the form source. This way it is possible to create many newsletter forms
	//in the same page (but with different fond) to separate subscription and deletion as an example
	$verify_source_fond = false;
	if(!$id_fond) {
		$verify_source_fond = true;
	}
	elseif($id_fond==$formulaire) $verify_source_fond = true;

	
	if($mail_inscription_ && $verify_source_fond) {
		$mail_valide = email_valide($mail_inscription_);	
	}
		
	if($mail_valide && $nom_inscription_) {
	
//spiplistes_log("mail inscription : ->".$mail_inscription_, _SPIPLISTES_LOG_DEBUG);

		// si l'abonne existe deja.
		if($row = sql_fetch(
			spiplistes_auteurs_auteur_select('id_auteur,statut', "email=".sql_quote($mail_inscription_))
			)
		) {
			$id_auteur = intval($row['id_auteur']);
			$statut = $row['statut'];
			$abonne_existant = "oui" ;

			$continue = false;
			
			if($statut == '5poubelle') {
				$reponse_formulaire = _T('form_forum_access_refuse');
			}
			else if($statut == 'nouveau') {
				// nouveau. N'a pas confirme (pas recu mail de confirmation ?)
				if($id_auteur > 1) {
					spiplistes_auteurs_auteur_delete("id_auteur=".sql_quote($id_auteur));
				}
				$continue = true;
			}
			else {
				// envoyer le cookie de relance modif abonnement
	
				$cookie = creer_uniqid();
				spiplistes_auteurs_cookie_oubli_updateq($cookie, $mail_inscription_);
				
				$message = 
					_T('spiplistes:abonnement_mail_passcookie'
						, array('nom_site_spip' => $nom_site_spip
						, 'adresse_site' => $adresse_site
						, 'cookie' => $cookie)
					);
//spiplistes_log("message:".$message, _SPIPLISTES_LOG_DEBUG);
				
				$reponse_formulaire =
					(
						spiplistes_envoyer_mail(
							$mail_inscription_
							, "[$nom_site_spip] "._T('spiplistes:abonnement_titre_mail')
							, $message
							)
					)
					? _T('spiplistes:pass_recevoir_mail')
					: $reponse_formulaire =_T('pass_erreur_probleme_technique')
					;
			}
		}
		else {
			$continue = true;
		}
			
		// envoyer identifiants par mail
		if($continue) {
	
			//ajouter un code pour retrouver l'abonne
			
			$pass = creer_pass_aleatoire(8, $mail_inscription_);
			$login_ = trim(spiplistes_login_from_email($mail_inscription_));
			$mdpass = md5($pass);
			$htpass = generer_htpass($pass);
			
			$cookie = creer_uniqid();
						
			$type_abo = _request('suppl_abo') ;
			//verify suppl_abo is correct
			if($desabo!="oui" && $type_abo!="texte" && $type_abo!="html") return;
			
			// inscription d'un abonne
			if($login_) {
				if(sql_count(
						spiplistes_auteurs_auteur_select('id_auteur', "email=".sql_quote($mail_inscription_))
					)
				) {
					while($row = sql_fetch($sql_result)) {
					}
				}
				else {
					// n'existe pas, creation du compte ...
					$id_auteur = $id_abo = 
						// en SPIP 192 & 193, renvoie insert_id
						spiplistes_auteurs_auteur_insertq(
							array(
								'nom' => $nom_inscription_
								, 'email' => $mail_inscription_
								, 'login' => $login_
								, 'pass' => $mdpass
								, 'statut' => $statut
								, 'htpass' => $htpass
								, 'cookie_oubli' => $cookie
								)
						);
					$ecrire_acces = true;
				}
				spiplistes_format_abo_modifier($id_abo, $type_abo);
			}
	
			// abonnement aux listes demandees
			if(
				is_array($listes_demande)
				&& count($listes_demande)
				&& (
					$row = sql_fetch(
						spiplistes_auteurs_auteur_select("id_auteur", "email=".sql_quote($mail_inscription_))
			 			)
					)
			) {
				$id_auteur = $row['id_auteur'];
//spiplistes_log("inscription id : ->".$row['id_auteur'], _SPIPLISTES_LOG_DEBUG);
				spiplistes_abonnements_ajouter(intval($row['id_auteur']), $listes_demande);
			}
		
			// creation .htpasswd & LDAP si besoin systeme
			if($ecrire_acces) {
				ecrire_acces();
			}
					
			$message = _T('form_forum_message_auto')."\n\n"._T('spiplistes:bonjour')."\n";
					
			if($desabo=="oui"){
				$message .= _T('spiplistes:mail_non', array('nom_site_spip' => $nom_site_spip))."\n";
			}
			else if($type_abo=="texte" || $type_abo=="html")  {
				// prepare le message a envoyer par mail
				$listes_abonnements = spiplistes_abonnements_listes_auteur($id_auteur, true);
				$nb = count($listes_abonnements);
				$message_list = 
					($nb)
					? "\n- " . implode("\n- ", $listes_abonnements) . ".\n"
					: ""
					;

				$m1 = ($nb > 1) ? 'inscription_reponses_s' : 'inscription_reponse_s';
				if($nb > 1) {
					$m2 = _T('spiplistes:inscription_listes_f', array('f' => $type_abo));
				} else if($nb == 1) {
					$m2 = _T('spiplistes:inscription_liste_f', array('f' => $type_abo));
				} else {
					$m2 = _T('spiplistes:vous_abonne_aucune_liste');
				}
				$message .= ""
					. "\n"._T('spiplistes:'.$m1, array('s' => htmlentities($nom_site_spip)))
					. ".\n"
					. $m2.$message_list
					;
			} // end else if
		
			if(($acces_membres == 'oui') && ($type == 'forum')) {
				$message .= ""
					. "\n\n"
					. _T('spiplistes:inscription_mail_forum'
						, array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site))
					. "\n\n"
					. "- "._T('form_forum_login')." $login_\n"
					. "- "._T('form_forum_pass')." $pass\n\n"
					;
			}
			
			if(($type == 'redac') || ($inscriptions_ecrire && ($acces_membres == 'non'))) {
				$message .= ""
					. "\n\n"
						. _T('spiplistes:inscription_mail_redac'
						, array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site))
					. "\n\n"
					. "- "._T('form_forum_login')." $login_\n"
					. "- "._T('form_forum_pass')." $pass\n\n"
					;
			}
		} // fin continue
	
		$tt = "\n\n".str_pad('-', 40, '-')."\n\n";
		$message .= ""
			. $tt
			. _T('spiplistes:abonnement_mail_text')."\n".generer_url_public("abonnement","d=$cookie")
			. $tt
			;
			
		if ($GLOBALS['meta']['spiplistes_charset_envoi'] != $GLOBALS['meta']['charset']){
			include_spip('inc/charsets');
			$message = unicode2charset(charset2unicode($message), $GLOBALS['meta']['spiplistes_charset_envoi']);
		}

		if($abonne_existant != 'oui') {
		
			if(
				spiplistes_envoyer_mail(
					$mail_inscription_
					, "[$nom_site_spip] "._T('spiplistes:form_forum_identifiants')
					, $message
				)
			) {
//spiplistes_log("inscription : ->".$mail_inscription_, _SPIPLISTES_LOG_DEBUG);
				$reponse_formulaire =
					($acces_membres == 'oui')
					? _T('form_forum_identifiant_mail')
					: $reponse_formulaire =_T('spiplistes:form_forum_identifiant_confirm')
					;
			}
			else {
				$reponse_formulaire =_T('form_forum_probleme_mail');
			}
		} // end if abonne_existant
	} // end if($mail_valide && $nom_inscription_)
	else {
		//Non c'� email o non � valida
		if($mail_inscription_ && !$mail_valide && $verify_source_fond) {
			$reponse_formulaire =_T('spiplistes:erreur_adresse');
		}
		return(array(true, $reponse_formulaire));
	}
	return(array(false, $reponse_formulaire));

} // end spiplistes_formulaire_inscription()


//CP-20080511: creation du login a partir de email
function spiplistes_login_from_email ($mail) {
	
	if(!email_valide($mail)) {
		return(false);
	}

	// recupere la partie gauche de @
	$login_tmp = ereg_replace(
		"[^a-z0-9]"
		, ""
		, substr($mail, 0, strpos($mail, "@"))
		);
	
	if(!$login_tmp) {
		$login_tmp = "user";
	}

	// demande la liste des logins pour assurer unicite
	$sql_result = sql_select('login', 'spip_auteurs');
	$logins_base = array();
	while($row = sql_fetch($sql_result)) {
		$logins_base[] = $row['login'];
	}
	
	// creation du login
	for ($ii = 0; ; $ii++) {
		$login =
			($ii > 0)
		 	? $login_tmp.$ii
			: $login_tmp
			;
		if(
			!in_array($login, $logins_base)
			||
			!sql_count(
				spiplistes_auteurs_auteur_select('id_auteur', "login=".sql_quote($login))
			)
		) break;
		if($ii > 32766) {
			return(false);
		}
	}

	return($login);
}


?>