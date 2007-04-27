<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

// Balise independante du contexte

function balise_FORMULAIRE_ABONNEMENT_AUTEURS_COMPLETS ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_ABONNEMENT_AUTEURS_COMPLETS', array('id_liste'));
}

// args[0] indique une liste, mais ne sert pas encore
// args[1] indique un eventuel squelette alternatif
// [(#FORMULAIRE_ABONNEMENT{mon_squelette})]
// un cas particulier est :
// [(#FORMULAIRE_ABONNEMENT{listeX})]
// qui permet d'afficher le formulaire d'abonnement a la liste numero X

function balise_FORMULAIRE_ABONNEMENT_AUTEURS_COMPLETS_stat($args, $filtres) {

	if(!$args[1]) $args[1]='formulaire_abonnement_auteurs_complets';
	preg_match_all("/liste([0-9]+)/x",
               $args[1], $matches);

	if($id_liste=intval($matches[1][0])) {
	$args[1]='formulaire_abonnement_auteurs_complets';
	$args[0]=$id_liste;
	}

	return array($args[0],$args[1]);

}

// Si inscriptions pas autorisees, retourner une chaine d'avertissement
// Sinon inclusion du squelette
// Si pas de nom ou pas de mail valide, premier appel rien d'autre a faire
// Autrement 2e appel, envoyer un mail et le squelette ne produira pas de
// formulaire.


function balise_FORMULAIRE_ABONNEMENT_AUTEURS_COMPLETS_dyn($id_liste, $formulaire) {

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

//les variables du plugin auteurs complets
$nom_famille = _request('nom_famille');
$prenom = _request('prenom');
$telephone = _request('telephone');
$fax = _request('fax');
$skype = _request('skype');
$organisation = _request('organisation');
$url_organisation = _request('url_organisation');
$bio = _request('bio');
$nom_site = _request('nom_site');
$url_site = _request('url_site');
$adresse = _request('adresse');
$codepostal = _request('codepostal');
$ville = _request('ville');
$pays = _request('pays');
$latitude = _request('latitude');
$longitude = _request('longitude');

// recuperation de la config
$acces_abonne = lire_meta('abonnement_config');
($acces_abonne == 'membre') ? $acces_membres = 'oui' : $acces_membres = 'non';

// aller chercher le formulaire html qui va bien
$formulaire = "formulaires/".$formulaire ;

// code inscription au site ou/et  a la lettre d'info

$inscriptions_ecrire = (lire_meta("accepter_inscriptions") == "oui") ;
$inscriptions_publiques = (lire_meta('accepter_visiteurs') == "oui");
unset($erreur);
$affiche_formulaire="";
$inscription_redac ="";
$inscription_visiteur ="";
	
	// envoyer le cookie de relance mot de passe si pass oublie
	if ($email_oubli) {
		if (email_valide($email_oubli)) {
			$res = spip_query("SELECT * FROM spip_auteurs WHERE email ="._q($email_oubli));
			if ($row = spip_fetch_array($res)) {
				if ($row['statut'] == '5poubelle')
					$erreur = _T('pass_erreur_acces_refuse');
				else {
					$cookie = creer_uniqid();
					spip_query("UPDATE spip_auteurs SET cookie_oubli = "._q($cookie)." WHERE email ="._q($email));
	
					$nom_site_spip = lire_meta("nom_site");
					$adresse_site = lire_meta("adresse_site");
	
					$message = _T('pass_mail_passcookie', array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site, 'cookie' => $cookie));
					if (envoyer_mail($email, "[$nom_site_spip] "._T('pass_oubli_mot'), $message))
						$erreur = _T('pass_recevoir_mail');
					else
						$erreur = _T('pass_erreur_probleme_technique');
				}
			}
			else
				$erreur = _T('pass_erreur_non_enregistre', array('email_oubli' => htmlspecialchars($email_oubli)));
		}
		else
			$erreur = _T('pass_erreur_non_valide', array('email_oubli' => htmlspecialchars($email_oubli)));
	}
	
	// afficher le formulaire d'oubli du pass
	if($oubli_pass=="oui") {
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
	else if ($inscriptions_ecrire OR $inscriptions_publiques OR (lire_meta('forums_publics') == 'abo') ) {
		// debut presentation
	
		($inscriptions_ecrire AND $type=="redac") ? $inscription_redac = "oui" : $inscription_redac = "non" ;
		($type!="redac" AND $inscriptions_publiques AND $acces_membres=='oui') ? $inscription_visiteur = "oui" : $inscription_visiteur = "non" ;
		
				
			list($affiche_formulaire,$reponse_formulaire)=formulaire_inscription_auteurs_complets(($type=="redac")? 'redac' : 'forum',$acces_membres,$formulaire);
	}
	else {
		return "<br />\n"._T('pass_erreur')."<br />\n<p>"._T('pass_rien_a_faire_ici')."</p>";
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
}


// inscrire les visiteurs dans l'espace public (statut 6forum) ou prive (statut nouveau->1comite)
function formulaire_inscription_auteurs_complets($type,$acces_membres,$formulaire) {

	$request_uri = $GLOBALS["REQUEST_URI"];
	global $mail_inscription_;
	global $nom_inscription_;
	global $list;
	global $liste;
	global $id_fond; //fond name of the form posting values
	global $nom_famille,$prenom,$telephone,$fax,$skype,$organisation,$url_organisation,$bio,$nom_site,$url_site,$adresse,$codepostal,$ville,$pays,$latitude,$longitude;

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

	if($acces_membres == 'non') $nom_inscription_ = test_login2($mail_inscription_) ;

	//utiliser_langue_site();
	$nomsite=lire_meta("nom_site");
	$urlsite=lire_meta("adresse_site");
	
	//Verify the form source. This way it is possible to create many newsletter forms
	//in the same page (but with different fond) to separate subscription and deletion as an example
	$verify_source_fond = false;
	if(!$id_fond) {$verify_source_fond = true;}
	elseif($id_fond==$formulaire) $verify_source_fond = true;
	
	if($mail_inscription_ && $verify_source_fond){
		$mail_valide = email_valide($mail_inscription_);
	}

	if ($mail_valide && $nom_inscription_) {

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
			}elseif ($statut == 'nouveau') {
				spip_query ("DELETE FROM spip_auteurs WHERE id_auteur="._q($id_auteur));
				$continue = true;
			}else{

			// envoyer le cookie de relance modif abonnement
			$cookie = creer_uniqid();
			spip_query("UPDATE spip_auteurs SET cookie_oubli = "._q($cookie)." WHERE email ="._q($mail_inscription_));

			$message = _T('auteurscomplets:abonnement_mail_passcookie', array('nom_site_spip' => $nomsite, 'adresse_site' => $urlsite, 'cookie' => $cookie));
				if (envoyer_mail($mail_inscription_, "[$nomsite] "._T('spiplistes:abonnement_titre_mail'), $message)){
					$reponse_formulaire =_T('auteurscomplets:pass_recevoir_mail');
				}else{
					$reponse_formulaire =_T('pass_erreur_probleme_technique');
				}

			}
			} else {
				$continue = true;
			}

			// envoyer identifiants par mail
			if ($continue) {
				//ajouter un code pour retrouver l'abonne
				$pass = creer_pass_aleatoire(8, $mail_inscription_);
				$login_ = test_login2($mail_inscription_);
				$mdpass = md5($pass);
				$htpass = generer_htpass($pass);

				$cookie = creer_uniqid();

				$type_abo = 'html' ;

				//verify suppl_abo is correct
				if($type_abo!="non" && $type_abo!="texte" && $type_abo!="html") return;

				$extras = bloog_extra_recup_saisie('auteurs');

				// Modifier l'auteur dans la base
				$result = spip_query("INSERT INTO spip_auteurs (nom, bio, email, nom_famille, prenom, organisation, url_organisation, telephone, fax, skype, adresse, codepostal, ville, pays, latitude, longitude, nom_site, url_site, login, pass, statut, htpass, extra, cookie_oubli) "."VALUES ("._q($nom_inscription_).", "._q($bio).", "._q($mail_inscription_).","._q($nom_famille).","._q($prenom).","._q($organisation).","._q($url_organisation).","._q($telephone).","._q($fax).","._q($skype).","._q($adresse).","._q($codepostal).","._q($ville).","._q($pays).","._q($latitude).","._q($longitude).","._q($nom_site).","._q($url_site).","._q($login_).","._q($mdpass).","._q($statut).","._q($htpass).","._q($extras).","._q($cookie).")");

				// abonnement aux listes http://www.phpfrance.com/tutorials/index.php?page=2&id=13
				$result = spip_query("SELECT * FROM spip_auteurs WHERE email="._q($mail_inscription_));

				// l'abonne existe deja
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

				if ($type_abo=="non"){
					$message .= _T('spiplistes:mail_non', array('nom_site_spip' => $nom_site_spip))."\n";
				}else if($type_abo=="html")  {
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

						if($i>1){
							$message .= "\n"._T('spiplistes:inscription_responses').$nom_site_spip._T('spiplistes:inscription_format').$type_abo."." ;
		        				$message .= "\n"._T('spiplistes:inscription_listes').$message_list ;
						}
	        				if($i==1){
							$message .= "\n"._T('spiplistes:inscription_response').$nom_site_spip._T('spiplistes:inscription_format').$type_abo."." ;
							$message .= "\n"._T('spiplistes:inscription_liste').$message_list ;
						}
						if($i==0){
							$message .= "\n"._T('spiplistes:inscription_response').$nom_site_spip._T('spiplistes:inscription_format').$type_abo."." ;
						}
					}

        				if(($acces_membres == 'oui') && ($type == 'forum') ){
						$message .="\n\n"._T('spiplistes:inscription_mail_forum', array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site))."\n\n";
						$message .= "- "._T('form_forum_login')." $login_\n";
						$message .= "- "._T('form_forum_pass')." $pass\n\n";
					}

					if(($type == 'redac') OR ($inscriptions_ecrire AND $acces_membres == 'non')) {
						$message .="\n\n"._T('spiplistes:inscription_mail_redac', array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site))."\n\n";
						$message .= "- "._T('form_forum_login')." $login_\n";
						$message .= "- "._T('form_forum_pass')." $pass\n\n";
					}
				}

				$message .= "\n\n-----------------------------------------\n\n" ;
				$message .= _T('spiplistes:abonnement_mail').' '.generer_url_public("abonnement_ac","d=$cookie") ;
				$message .= "\n\n-----------------------------------------\n\n" ;

				if($abonne_existant != 'oui'){

					if (envoyer_mail($mail_inscription_, "[$nom_site_spip] "._T('spiplistes:form_forum_identifiants'), $message)) {

						if($acces_membres == 'oui'){
							$reponse_formulaire =_T('form_forum_identifiant_mail');
						}else{
							$reponse_formulaire =_T('spiplistes:form_forum_identifiant_confirm');
						}
					}
					else {
						$reponse_formulaire =_T('form_forum_probleme_mail');
					}
				}
			}
		}
			else {
			//Non c'�email o non �valida
				if($mail_inscription_ AND !$mail_valide && $verify_source_fond){
					$reponse_formulaire =_T('spiplistes:erreur_adresse');
				}

			//Infos sur la liste
				if(!$liste) $liste='';
				return array(true,$reponse_formulaire);
			}
		return array(false,$reponse_formulaire);
}
function test_login2($mail) {
	if (strpos($mail, "@") > 0) $login_base = substr($mail, 0, strpos($mail, "@"));
	else $login_base = $mail;

	$login_base = strtolower($login_base);
	$login_base = ereg_replace("[^a-zA-Z0-9]", "", $login_base);
	if (!$login_base) $login_base = "user";

	for ($i = 0; ; $i++) {
		if ($i) $login = $login_base.$i;
		else $login = $login_base;
		$result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login="._q($login));
		if (!spip_num_rows($result)) break;
	}
	return $login;
}

?>
