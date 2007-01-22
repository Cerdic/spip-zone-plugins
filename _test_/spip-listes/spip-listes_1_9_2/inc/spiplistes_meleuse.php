<?php
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/


include_spip('inc/meta');
include_spip('inc/texte');
include_spip('inc/filtres');

include_spip('spiplistes_boutons');
include_once(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');

$charset = $GLOBALS['meta']['charset'];

// Trouver un message a envoyer 
$result_pile = spip_query("SELECT * FROM spip_courriers AS messages WHERE statut='encour' ORDER BY date ASC LIMIT 0,1");
$message_pile = spip_num_rows($result_pile);

if ($message_pile > 0){

	// Message
	$row = spip_fetch_array($result_pile);
	$titre = typo($row["titre"]);
	$texte = $row["texte"];
	$texte = stripslashes($texte);
	$message_texte = $row["message_texte"];
	
	
	$type = $row["type"];
	$id_message = $row["id_courrier"];
	$id_liste = $row["id_liste"];
	$email_test = $row["email_test"];
	
	$nb_emails_envoyes = $row["nb_emails_envoyes"];
	$total_abonnes = $row["total_abonnes"];
	
	$nb_emails_echec = $row["nb_emails_echec"];
	$nb_emails_non_envoyes = $row["nb_emails_non_envoyes"];
	$nb_emails_texte = $row["nb_emails_texte"];
	$nb_emails_html = $row["nb_emails_html"];

	$debut_envoi = $row["date_debut_envoi"];

	$pied_page = "" ;
	$pied_page = pied_de_page_liste($id_liste) ;
	$lang = spiplistes_langue_liste($id_liste);
	
	if($lang != '') $GLOBALS['spip_lang'] = $lang ;
	
	// Determiner le destinataire ou la liste destinataire
	
	//est-ce un mail de test ?
	if( email_valide($email_test) ){
		spip_log( _T('spiplistes:email_test')." : ".$destinataires);
		$test = 'oui';
		$mail_collectif = 'non' ;
	} 
	else {
		//est-ce un mail collectif ?
		if($id_liste == 0){
			$mail_collectif = 'oui' ;
			spip_log(_T('spiplistes:envoi_tous')) ;
		} else {
			//c'est un mail pour une liste alors ?
			$mail_collectif = 'non' ;
			$result_d = spip_query("SELECT * FROM spip_listes WHERE id_liste="._q($id_liste));
	
			if(spip_num_rows($result_d)>0){
				while($ro = spip_fetch_array($result_d)) {
				$titre_liste = $ro["titre"];
				$id = $ro["id_liste"];
				$email_liste = $ro["email_envoi"];
				spip_log(_T('spiplistes:envoi_listes').$titre_liste);
				}
			}else{			//erreur
			   spip_log(_T('spiplistes:envoi_erreur'));
			}
		}
	}
	
	// on prepare l'email
	$nomsite = $GLOBALS['meta']['nom_site'];
	$urlsite = $GLOBALS['meta']['adresse_site'];
	srand((double)microtime()*1000000);
	$boundary = md5(uniqid(rand()));
	
	// email emmeteur
	$email_webmaster = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
	$from = email_valide($email_liste) ? $email_liste : $email_webmaster;
            
	$objet= filtrer_entites($titre);
	if ($charset <> 'utf-8') {
		$objet = str_replace("&#8217;", "'", $objet);
		$objet = str_replace("&#8220;", "\"", $objet);
		$objet = str_replace("&#8221;", "\"", $objet);
 	}
	
	if ($charset <> 'utf-8') {
		$texte = str_replace("&#8217;", "'", $texte);
		$texte = str_replace("&#8220;", "\"", $texte);
		$texte = str_replace("&#8221;", "\"", $texte);
	}
	  
	
	// on prepare le debut de la version html
	$pageh = "<html>\n\n<body>\n\n".$texte."\n\n";
	// la fin de la version html sera generee pour chaque destinataire
  
	// on prepare la version texte
	if($message_texte !=''){
		$page_ = $message_texte ;  
	}
	else{
		$page_ = version_texte($texte);
	}
    
	$page_.="\n\n________________________________________________________________________"  ;
	$page_.="\n\n"._T('spiplistes:editeur').$nomsite."\n"  ;
	$page_.=$urlsite."\n";
	$page_.="________________________________________________________________________"  ;
	

	$nb_inscrits=$total_abonnes;
	
	
	spip_log(_T('spiplistes:email_reponse').$from."\n"._T('spiplistes:contacts')." : ".$nb_inscrits) ;

	
	if($nb_inscrits > 0){

		spip_log(_T('spiplistes:message'). $titre);
	
		//Envoi par lots
		$debut = $nb_emails_envoyes + $nb_emails_non_envoyes ; // ??
		
		//initialiser la taille des lots
		$lot=$GLOBALS['meta']['spiplistes_lots'];
		if (!isset($lot)) {
			ecrire_meta('spiplistes_lots' , 30) ;
			ecrire_metas();
		}
		$limit=$GLOBALS['meta']['spiplistes_lots']; // nombre de messages envoyes par boucles.	
		
		//chopper un lot 
		
		if($test == 'oui')
			$result_inscrits = spip_query("SELECT id_auteur, nom, email, extra FROM spip_auteurs WHERE email ="._q($email_test)." ORDER BY id_auteur ASC");
		else
			$result_inscrits = spip_query("SELECT a.nom, a.id_auteur, a.email, a.extra FROM spip_auteurs AS a, spip_auteurs_courriers AS b WHERE a.id_auteur=b.id_auteur AND b.id_courrier = "._q($id_message)." ORDER BY a.id_auteur ASC  LIMIT 0,".intval($limit));
			
		$liste_abonnes = spip_num_rows($result_inscrits);
		if($liste_abonnes > 0){

			spip_log("envois effectues : ".$debut.", pas : ".$limit.", nb:".$nb_inscrits) ;	
	
			//envoyer le lot d'email selectionne
			while ($row2 = spip_fetch_array($result_inscrits)) {
				$str_temp = " ";
				$id_auteur = $row2['id_auteur'] ;
				
				//indiquer eventuellement le debut de l'envoi
				if($debut_envoi=="0000-00-00 00:00:00" AND $test !='oui')
					spip_query("UPDATE spip_courriers SET date_debut_envoi=NOW() WHERE id_courrier="._q($id_message)); 
		
				$extra = unserialize ($row2["extra"]);

				$nom_auteur = $row2["nom"];
				$str_temp .= $nom_auteur." format : ".$extra['abo'];

				$email = $row2["email"];
				$total=$total+1;
				unset ($cookie);
				

				$abo = false;				
				if (($extra["abo"] == 'texte') OR ($extra["abo"] == 'html')) 
					$abo = true;

				if ($abo) {
					$cookie = creer_uniqid();
					spip_query("UPDATE spip_auteurs SET cookie_oubli ="._q($cookie)." WHERE email ="._q($email));				
				
					//version texte utilisee en format texte et HTML multipart
					$pagem = $page_."\n\n"  ;

					if ($extra["abo"] == 'texte'){    // email TXT -----------------------
						// desabo pied de page texte			
						$pagem.= filtrer_entites(_T('spiplistes:abonnement_mail'))."\n" ;
						$pagem.= filtrer_entites(generer_url_public('abonnement','d='.$cookie))."\n\n"  ;
						
						$email_a_envoyer = new phpMail($email, $objet, '',$pagem);
						if (email_valide($from)){
							$email_a_envoyer->From = $from ;
							$email_a_envoyer->AddCustomHeader("Errors-To: ".$from);
							$email_a_envoyer->AddCustomHeader("Reply-To: ".$from);
							$email_a_envoyer->AddCustomHeader("Return-Path: ".$from);
		
							if ($email_a_envoyer->send()) {
								$str_temp .= "->ok";
								$nb_emails_envoyes++;
								$nb_emails_texte++;
								spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur="._q($id_auteur)." AND id_courrier="._q($id_message));				
							}
							else {
								$str_temp .= _T('spiplistes:erreur_mail');
								$nb_emails_echec++;
         			}
						}
						else {
							$str_temp .= _T('spiplistes:sans_adresse');
							$nb_emails_echec++;
						}
					}
					else if ($extra["abo"] == 'html') {  // email HTML ------------------
						// desabo pied de page HTML
						//spip_log($pied_page);
						$pagehm = $pageh.$pied_page."<a href=\"".generer_url_public('abonnement','d='.$cookie)."\">"._T('spiplistes:abonnement_mail')."</a>\n\n</body></html>";
							
						$email_a_envoyer = new phpMail($email, $objet, $pagehm, $pagem);
						if (email_valide($from)){
							$email_a_envoyer->From = $from ;
							$email_a_envoyer->AddCustomHeader("Errors-To: ".$from);
							$email_a_envoyer->AddCustomHeader("Reply-To: ".$from);
							$email_a_envoyer->AddCustomHeader("Return-Path: ".$from);
	
							if ($email_a_envoyer->send()){
								$str_temp .= "->ok";
								$nb_emails_envoyes++;
								$nb_emails_html++;
								spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur="._q($id_auteur)." AND id_courrier="._q($id_message));				
							}
							else {
								$str_temp .= _T('spiplistes:erreur_mail');
								$nb_emails_echec++;
							}
						} else {
							$str_temp .= _T('spiplistes:sans_adresse') ;
							$nb_emails_echec++;
	    			}
					} 
					else {  // email fin TXT /HTML  -----------------------------------------
						$nb_emails_non_envoyes++; //desabonnes 
						spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur="._q($id_auteur)." AND id_courrier="._q($id_message));	
						spip_log('pas abonne en ce moment');
					}  				
						
					spip_log($str_temp);								
				}
				else {
					$nb_emails_non_envoyes++; // pas de extra abo ou exeption
					spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur="._q($id_auteur)." AND id_courrier="._q($id_message));	
					spip_log('pas de extra abo');
				} /* fin abo*/
			}/* fin while */
			
			// si c'est un test on repasse en redac
			if ($test== 'oui') {
				spip_query("UPDATE spip_courriers SET statut='redac', email_test='', total_abonnes=0 WHERE id_courrier="._q($id_message));
				spip_log('repasse en redac');
			}
		} 
		else {   /* fin liste abonnes */	
			// archiver
			spip_log("UPDATE spip_courriers SET statut='publie' WHERE id_courrier="._q($id_message));
			spip_query("UPDATE spip_courriers SET statut='publie' WHERE id_courrier="._q($id_message));
			$fin_envoi="oui";
		}
	}
	else {
		//aucun destinataire connu pour ce message
		spip_log(_T('spiplistes:erreur_sans_destinataire')."---"._T('spiplistes:envoi_annule'));
		spip_query("UPDATE spip_courriers SET titre="._q(_T('spiplistes:erreur_destinataire')).", statut='publie' WHERE id_courrier="._q($id_message)); 
	}
		
	// faire le bilan apres l'envoi d'un lot	
	if($test != 'oui'){
		spip_query("UPDATE spip_courriers SET nb_emails_envoyes="._q($nb_emails_envoyes)." WHERE id_courrier="._q($id_message)); 
	   if($nb_emails_non_envoyes > 0)
	    spip_query("UPDATE spip_courriers SET nb_emails_non_envoyes="._q($nb_emails_non_envoyes)." WHERE id_courrier="._q($id_message));
	    if($nb_emails_echec > 0)
	    spip_query("UPDATE spip_courriers SET nb_emails_echec="._q($nb_emails_echec)." WHERE id_courrier="._q($id_message)); 
	    spip_query("UPDATE spip_courriers SET nb_emails_texte="._q($nb_emails_texte)." WHERE id_courrier="._q($id_message)); 
	    spip_query("UPDATE spip_courriers SET nb_emails_html="._q($nb_emails_html)." WHERE id_courrier="._q($id_message)); 
		if($fin_envoi=="oui"){
		spip_query("UPDATE spip_courriers SET date_fin_envoi=NOW() WHERE id_courrier="._q($id_message)); 
		}
	}
} 
else {
	spip_log(_T('spiplistes:envoi_fini'))   ;
}	/* flag pile*/



/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>