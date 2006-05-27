<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

global $balise_FORMULAIRE_ABONNEMENT_collecte;
$balise_FORMULAIRE_ABONNEMENT_collecte = array('liste');


function balise_FORMULAIRE_ABONNEMENT_stat ($args, $filtres) {
	
	if(!$args[1]) $args[1]='formulaire_abonnement';
	return array($args[0],$args[1]);
}

function balise_FORMULAIRE_ABONNEMENT_dyn($liste,$formulaire) {
	global $oubli_pass,$email_oubli,$type;
	// recuperation de la config
	
	$acces_abonne = get_extra(1,"auteur");
	($acces_abonne['config'] == 'membre') ? $acces_membres = 'oui' : $acces_membres = 'non';
	
	
	
	/* permet eventuellement d'afficher les liens vers les options
	
	echo "<a href='".$PHP_SELF."'>"._T('spiplistes:abonner')."</a> | ";
	if($inscriptions_ecrire) echo"<a href='".$PHP_SELF."?type=redac'>"._T('spiplistes:devenir_redac')."</a> | ";
	echo "<a href='".$PHP_SELF."?oubli_pass=oui'>"._T('login_motpasseoublie')."</a>";
	echo "<a href='abonnement.php3'>"._T('spiplistes:desabo')."</a>";
	*/
	
	
	// code inscription au site ou/et  a la lettre d'info
	
	
	/******************************************************************/
	if (!defined("_ECRIRE_INC_VERSION"))
	include ("ecrire/inc_version.php3");
	include_ecrire ("inc_meta.php3");
	include_ecrire ("inc_session.php3");
	include_ecrire ("inc_filtres.php3");
	include_ecrire ("inc_texte.php3");
	include_ecrire ("inc_meta.php3");
	include_ecrire ("inc_mail.php3");
	include_ecrire ("inc_acces.php3");
	
	//utiliser_langue_site();
	
	
	
	$inscriptions_ecrire = (lire_meta("accepter_inscriptions") == "oui") ;
	unset($erreur);
	$affiche_formulaire="";
	$inscription_redac ="";
	$inscription_visiteur ="";
	
	// envoyer le cookie de relance mot de passe
	if ($email_oubli) {
		if (email_valide($email_oubli)) {
			$email = addslashes($email_oubli);
			$res = spip_query("SELECT * FROM spip_auteurs WHERE email ='$email'");
			if ($row = spip_fetch_array($res)) {
				if ($row['statut'] == '5poubelle')
					$erreur = _T('pass_erreur_acces_refuse');
				else {
					$cookie = creer_uniqid();
					spip_query("UPDATE spip_auteurs SET cookie_oubli = '$cookie' WHERE email ='$email'");
	
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
	
	if($oubli_pass=="oui") {
		return array($formulaire, $GLOBALS['delais'],
			array(
				'oubli_pass' => $oubli_pass,
				'erreur' => $erreur,
				'inscription_redac' => '',
				'inscription_visiteur' => '',
				'login' => false,
				'reponse_formulaire' => '',
				'liste' => ''
					)
			);
	}
	else if ($inscriptions_ecrire || (lire_meta('accepter_visiteurs') == 'oui') OR (lire_meta('forums_publics') == 'abo')) {
		// debut presentation
	
		if ($type=="redac" AND $inscriptions_ecrire){
			$inscription_redac = "oui";
		}else{
			if($acces_membres == 'oui') $inscription_visiteur = "oui";
		}
			
		list($affiche_formulaire,$reponse_formulaire)=formulaire_inscription(($type=="redac")? 'redac' : 'forum',$acces_membres,$formulaire);
	}
	else {
		return "<br />\n"._T('pass_erreur')."<br />\n<p>"._T('pass_rien_a_faire_ici')."</p>";
	}
	
	
		
	return array($formulaire, $GLOBALS['delais'],
			array(
				'oubli_pass' => $oubli_pass,
				'erreur' => $erreur,
				'inscription_redac' => $inscription_redac,
				'inscription_visiteur' => $inscription_visiteur,
				'login' => $affiche_formulaire,
				'reponse_formulaire' => $reponse_formulaire,
				'liste' => $liste
					)
			);
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
		$query = "SELECT id_auteur FROM spip_auteurs WHERE login='$login'";
		$result = spip_query($query);
		if (!spip_num_rows($result)) break;
	}

	return $login;
}



// inscrire les visiteurs dans l'espace public (statut 6forum) ou prive (statut nouveau->1comite)
function formulaire_inscription($type,$acces_membres,$formulaire) {
	$request_uri = $GLOBALS["REQUEST_URI"];
	global $mail_inscription;
	global $nom_inscription;
	global $list;
	global $liste;
	global $id_fond;//fond name of the form posting values
	

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


	
	if($acces_membres == 'non') $nom_inscription = test_login2($mail_inscription) ;

      //utiliser_langue_site();
	$nomsite=lire_meta("nom_site");
	$urlsite=lire_meta("adresse_site");
	
	//Verify the form source. This way it is possible to create many newsletter forms
	//in the same page (but with different fond) to separate subscription and deletion as an example
	$verify_source_fond = false;
	if(!$id_fond) {$verify_source_fond = true;}
	elseif($id_fond==$formulaire) $verify_source_fond = true;
	
	if($mail_inscription && $verify_source_fond){
		$mail_valide = email_valide_bloog($mail_inscription);	
	}
		
  if ($mail_valide && $nom_inscription) {
		$query = "SELECT * FROM spip_auteurs WHERE email='".addslashes($mail_inscription)."'";
		$result = spip_query($query);

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
				spip_query ("DELETE FROM spip_auteurs WHERE id_auteur=$id_auteur");
				$continue = true;
			}else{
        	// envoyer le cookie de relance modif abonnement

        	$cookie = creer_uniqid();
        	spip_query("UPDATE spip_auteurs SET cookie_oubli = '$cookie' WHERE email ='$mail_inscription'");

        	$message = _T('spiplistes:abonnement_mail_passcookie', array('nom_site_spip' => $nomsite, 'adresse_site' => $urlsite, 'cookie' => $cookie));
				if (envoyer_mail($mail_inscription, "[$nomsite] "._T('spiplistes:abonnement_titre_mail'), $message)){
					$reponse_formulaire =_T('spiplistes:pass_recevoir_mail');
					//echo _T('spiplistes:pass_recevoir_mail');
				}else{
					$reponse_formulaire =_T('pass_erreur_probleme_technique');
					//echo _T('pass_erreur_probleme_technique');
				}

    	}
		} else {
			$continue = true;
		}
		
		// envoyer identifiants par mail
		if ($continue) {
		
         //ajouter un code pour retrouver l'abonne

			$pass = creer_pass_aleatoire(8, $mail_inscription);
			$login = test_login2($mail_inscription);
			$mdpass = md5($pass);
			$htpass = generer_htpass($pass);

			$cookie = creer_uniqid();
			
			$type_abo = $GLOBALS['suppl_abo'] ;
			//verify suppl_abo is correct
			if($type_abo!="non" && $type_abo!="texte" && $type_abo!="html") return;
			
			$extras = bloog_extra_recup_saisie('auteurs');

			$query = "INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, extra, cookie_oubli) ".
				"VALUES ('".addslashes($nom_inscription)."', '".addslashes($mail_inscription)."', '$login', '$mdpass', '$statut', '$htpass', '$extras', '$cookie')";
			$result = spip_query($query);
			
			// abonnement aux listes http://www.phpfrance.com/tutorials/index.php?page=2&id=13

      $query = "SELECT * FROM spip_auteurs WHERE email='".addslashes($mail_inscription)."'";
			$result = spip_query($query);

			// l'abonne existe deja.
	 		if ($row = spip_fetch_array($result)) {
				$id_auteur = $row['id_auteur'];
				$statut = $row['statut'];


        // on abonne l'auteur aux listes
        if(is_array($list)){

          while( list(,$val) = each($list) ){

            $query="DELETE FROM spip_auteurs_articles WHERE id_auteur='$id_auteur' AND id_article='$val'";
        		$result=spip_query($query);
        		$query="INSERT INTO spip_auteurs_articles (id_auteur,id_article) VALUES ('$id_auteur','$val')";
        		$result=spip_query($query);


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
      }else if($type_abo=="texte" || $type_abo=="html")  {

        //SELECT des listes de l'abonnï¿½
            
						
				$query = "SELECT * FROM spip_auteurs_articles AS abonnements, spip_articles AS listes WHERE abonnements.id_auteur='$id_auteur' AND abonnements.id_article=listes.id_article AND listes.statut='liste'";
				$result_list = spip_query($query);

				//lister les listes
        $message_list = '' ;
        $i = 0 ;

        while($row = spip_fetch_array($result_list)) {
					
					$id_article = $row['id_article'] ;
			
					$query = "SELECT * FROM spip_articles WHERE id_article=$id_article";
		      $result = spip_query($query);
          $row = spip_fetch_array($result);
          $titre = $row['titre'] ;
          $message_list .= "\n- ".$titre ;
          $i++ ;
        }


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
          $message .= "- "._T('form_forum_login')." $login\n";
					$message .= "- "._T('form_forum_pass')." $pass\n\n";
        }

        if($type == 'redac') {
			  	$message .="\n\n"._T('spiplistes:inscription_mail_redac', array('nom_site_spip' => $nom_site_spip, 'adresse_site' => $adresse_site))."\n\n";
		      $message .= "- "._T('form_forum_login')." $login\n";
					$message .= "- "._T('form_forum_pass')." $pass\n\n";
        }


      }

      $message .= "\n\n-----------------------------------------\n\n" ;
      $message .= _T('spiplistes:abonnement_mail')." ".$adresse_site."/abonnement.php3?d=".$cookie;
      $message .= "\n\n-----------------------------------------\n\n" ;
		
			if($abonne_existant != 'oui'){

			if (envoyer_mail($mail_inscription, "[$nom_site_spip] "._T('spiplistes:form_forum_identifiants'), $message)) {
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
	else {
		//Non c'è email o non è valida
		if($mail_inscription AND !$mail_valide && $verify_source_fond){
        $reponse_formulaire ="<h2>"._T('spiplistes:erreur_adresse')."</h2>";
    }
		
		if($acces_membres == 'oui'){
        $reponse_formulaire =_T('form_forum_indiquer_nom_email');
    }

		/***********/

		//Infos sur la liste
		if(!$liste) $liste='';

    /**************/
		return array(true,$reponse_formulaire);
	}
	return array(false,$reponse_formulaire);
}

?>
