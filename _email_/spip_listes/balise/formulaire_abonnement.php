<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

// Balise independante du contexte


function balise_FORMULAIRE_ABONNEMENT ($p) {

	return calculer_balise_dynamique($p, 'FORMULAIRE_ABONNEMENT', array());
}

// args[0] indique le focus eventuel
// args[1] indique la rubrique eventuelle de proposition
// [(#FORMULAIRE_INSCRIPTION{nom_inscription, #ID_RUBRIQUE})]
function balise_FORMULAIRE_ABONNEMENT_stat($args, $filtres) {
	if ( ($GLOBALS['meta']['accepter_inscriptions'] != 'oui') AND ($GLOBALS['meta']['accepter_visiteurs'] != 'oui' ) )
		return '';
	else 
		return array('redac', 
			(isset($args[0]) ? $args[0] : ''),
			(isset($args[1]) ? $args[1] : ''));
}

// Si inscriptions pas autorisees, retourner une chaine d'avertissement
// Sinon inclusion du squelette
// Si pas de mon ou pas de mail valide, premier appel rien d'autre a faire
// Autrement 2e appel, envoyer un mail et le squelette ne produira pas de
// formulaire.


function balise_FORMULAIRE_ABONNEMENT_dyn($mode, $focus, $id_rubrique=0) {

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
	
	// recuperation de la config
	
	$acces_abonne = get_extra(1,"auteur");
	($acces_abonne['config'] == 'membre') ? $acces_membres = 'oui' : $acces_membres = 'non';
	
	// aller chercher le formulaire html qui va bien				
	$formulaire = "formulaires/formulaire_abonnement";		

	
	
	// permet eventuellement d'afficher les liens vers les options
	
	// echo "<a href='".$PHP_SELF."'>"._T('spiplistes:abonner')."</a> | ";
	// if($inscriptions_ecrire) echo"<a href='".$PHP_SELF."?type=redac'>"._T('spiplistes:devenir_redac')."</a> | ";
	// echo "<a href='".$PHP_SELF."?oubli_pass=oui'>"._T('login_motpasseoublie')."</a>";
	// echo "<a href='abonnement.php3'>"._T('spiplistes:desabo')."</a>";

	
	
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
	
	// afficher le formulaire d'oubli du pass
	if($oubli_pass=="oui") {
		return array($formulaire, 'formulaire_abonnement', $GLOBALS['delais'],
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
	//code pour s inscrire
	else if ($inscriptions_ecrire OR $inscriptions_publiques OR (lire_meta('forums_publics') == 'abo') ) {
		// debut presentation
	
		($type=="redac" AND $inscriptions_ecrire) ? $inscription_redac = "oui" : $inscription_redac = "non" ;
		($type!="redac" AND $inscriptions_publiques) ? $inscription_visiteur = "oui" : $inscription_visiteur = "non" ;
		
				
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
					'acces_membres' => $acces_membres,
					'inscription_visiteur' => $inscription_visiteur,
					'login' => $affiche_formulaire,
					'reponse_formulaire' => $reponse_formulaire,
					'liste' => $liste
						)
				);
				
				
}


// inscrire les visiteurs dans l'espace public (statut 6forum) ou prive (statut nouveau->1comite)
function formulaire_inscription($type,$acces_membres,$formulaire) {
	
	$request_uri = $GLOBALS["REQUEST_URI"];
	global $mail_inscription;
	global $nom_inscription;
	global $list;
	global $liste;
	global $id_fond; //fond name of the form posting values
	

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

        	//SELECT des listes de l'abonne		
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
      $message .= _T('spiplistes:abonnement_mail')." ".$adresse_site."/spip.php?page=abonnement&d=".$cookie;
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
		$query = "SELECT id_auteur FROM spip_auteurs WHERE login='$login'";
		$result = spip_query($query);
		if (!spip_num_rows($result)) break;
	}

	return $login;
}





	//code de spip pour info

/*	if (!(($mode == 'redac' AND $GLOBALS['meta']['accepter_inscriptions'] == 'oui')
	OR ($mode == 'forum' AND (
		$GLOBALS['meta']['accepter_visiteurs'] == 'oui'
		OR $GLOBALS['meta']['forums_publics'] == 'abo'
		)
	    )))
		return _T('pass_rien_a_faire_ici');

	$nom = _request('nom_inscription');
	$mail = _request('mail_inscription');
	if (!$mail)
		$message = '';
	else {
		include_spip('inc/filtres'); // pour email_valide
		$message = message_inscription($mail, $nom, $mode, $id_rubrique);
		if (is_array($message)) {
			if (function_exists('envoyer_inscription'))
				$f = 'envoyer_inscription';
			else 
				$f = 'envoyer_inscription_dist';
			$message = $f($message, $nom, $mode, $id_rubrique);
		}
	}

	// #ENV*{message} est le message d'erreur
	// #ENV*{commentaire} explique si on s'inscrit a l'espce public ou prive
	// il disparait s'il y a un message d'erreur (pour faire moins verbeux)
	$commentaire = '';
	if (!$message) {
		if ($mode=='redac') $commentaire = _T('pass_espace_prive_bla');
		if ($mode=='forum') $commentaire = _T('pass_forum_bla');
	} */

/*
// fonction qu'on peut redefinir pour filtrer les adresses mail et les noms,
// et donner des infos supplémentaires
// Std: controler que le nom (qui sert a calculer le login) est assez long
// et que l'adresse est valide (et on la normalise)
// Retour: une chaine message d'erreur 
// ou un tableau avec au minimum email, nom, mode (redac / forum)

function test_inscription_dist($mode, $mail, $nom, $id_rubrique=0) {

	include_spip('inc/filtres');
	$nom = trim(corriger_caracteres($nom));
	if (!$nom || strlen($nom) > 64)
	    return _T('ecrire:info_login_trop_court');
	if (!$r = email_valide($mail)) return _T('info_email_invalide');
	return array('email' => $r, 'nom' => $nom, 'bio' => $mode);
}

// cree un nouvel utilisateur et renvoie un message d'impossibilite ou la
// ligne SQL le decrivant.

function message_inscription($mail, $nom, $mode, $id_rubrique=0) {

	if (function_exists('test_inscription'))
		$f = 'test_inscription';
	else 
		$f = 'test_inscription_dist';
	$declaration = $f($mode, $mail, $nom, $id_rubrique);

	if (is_string($declaration))
		return  $declaration;

	$row = spip_query("SELECT statut, id_auteur, login, email FROM spip_auteurs WHERE email=" . spip_abstract_quote($declaration['email']));
	$row = spip_fetch_array($row);

	if (!$row) 
		// il n'existe pas, creer les identifiants  
		return inscription_nouveau($declaration);
	if (($row['statut'] == '5poubelle') AND !$declaration['pass'])
		// irrecuperable
		return _T('form_forum_access_refuse');

	if (($row['statut'] != 'nouveau') AND !$declaration['pass'])
		// deja inscrit
		return _T('form_forum_email_deja_enregistre');

	// existant mais encore muet, ou ressucite: renvoyer les infos
	$row['pass'] = creer_pass_pour_auteur($row['id_auteur']);
	return $row;
}

// On enregistre le demandeur comme 'nouveau', en memorisant le statut final
// provisoirement dans le champ Bio, afin de ne pas visualiser les inactifs
// A sa premiere connexion il obtiendra son statut final (auth->activer())

function inscription_nouveau($declaration)
{
	if (!isset($declaration['login']))
		$declaration['login'] = test_login($declaration['nom'], $declaration['email']);

	$declaration['statut'] = 'nouveau';

	$n = spip_abstract_insert('spip_auteurs', ('(' .join(',',array_keys($declaration)).')'), ("(" .join(", ",array_map('spip_abstract_quote', $declaration)) .")"));

	$declaration['id_auteur'] = $n;

	$declaration['pass'] = creer_pass_pour_auteur($declaration['id_auteur']);
	return $declaration;
}

// envoyer identifiants par mail
// fonction redefinissable

function envoyer_inscription_dist($ids, $nom, $mode, $id_rubrique) {
	include_spip('inc/mail');
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$adresse_site = $GLOBALS['meta']["adresse_site"];
	
	$message = _T('form_forum_message_auto')."\n\n"
	  . _T('form_forum_bonjour', array('nom'=>$nom))."\n\n"
	  . _T((($mode == 'forum')  ?
		'form_forum_voici1' :
		'form_forum_voici2'),
	       array('nom_site_spip' => $nom_site_spip,
		     'adresse_site' => $adresse_site . '/',
		     'adresse_login' => $adresse_site .'/'. _DIR_RESTREINT_ABS))
	  . "\n\n- "._T('form_forum_login')." " . $ids['login']
	  . "\n- ".  _T('form_forum_pass'). " " . $ids['pass'] . "\n\n";

	if (envoyer_mail($ids['email'],
			 "[$nom_site_spip] "._T('form_forum_identifiants'),
			 $message))
		return _T('form_forum_identifiant_mail');
	else
		return _T('form_forum_probleme_mail');
}




function creer_pass_pour_auteur($id_auteur) {
	include_spip('inc/acces');
	$pass = creer_pass_aleatoire(8, $id_auteur);
	$mdpass = md5($pass);
	$htpass = generer_htpass($pass);
	spip_query("UPDATE spip_auteurs	SET pass='$mdpass', htpass='$htpass' WHERE id_auteur = ".intval($id_auteur));
	ecrire_acces();
	
	return $pass;
}

*/


?>