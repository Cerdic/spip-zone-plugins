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
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
	Prend dans le panier des courriers (spip_courriers) les encours
	- formate le titre, texte pour l'envoi
	la queue (spip_auteurs_courriers) a été remplie par cron_spiplistes_cron()
	se sert de la queue pour ventiler les envois par lots

	le courrier (spip_courriers) doit avoir date <= time() et statut 'encour'
	si email_test, la meleuse envoie le courrier à email_test, supprime email_test du courrier 
		et repositionne le statut du courrier en 'redac'
	si pas email_test mais id_liste, regarde la queue d'envois (spip_auteurs_courriers) 
		et passe le statut du courrier (spip_courriers) à :
			'publie' si type == 'nl' (newsletter)
			'auto' si type == 'auto' (liste programmée)
		et envoie les courriers précisés aux abonnés de cette liste
		et supprime l'identifiant du courrier dans la queue d'envois (spip_auteurs_courriers)

	renvoie:
	- nul, si la tache n'a pas a etre effectuee
	- positif, si la tache a ete effectuee
	- negatif, si la tache doit etre poursuivie ou recommencee

*/
	
function spiplistes_meleuse () {

	include_spip('inc/spiplistes_api');
	include_spip('inc/meta');
	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/acces');
	
	include_once(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');
	
	// initialise les options
	foreach(array('opt_simuler_envoi','opt_lien_en_tete_courrier') as $key) {
		$$key = __plugin_lire_s_meta($key, 'spiplistes_preferences');
	}

	$meleuse_statut = 1;
		
	//////////////////////////
	// Trouver un courrier a envoyer 

	$sql_select = "titre,texte,message_texte,type,id_courrier,id_liste,email_test,total_abonnes,date_debut_envoi";
	
	$sql_query = "SELECT $sql_select FROM spip_courriers WHERE statut='"._SPIPLISTES_STATUT_ENCOURS."' LIMIT 1";

	$sql_result = spip_query($sql_query);

	if(spip_num_rows($sql_result)) {

		// boucle (sur LIMIT 1) pour pouvoir sortir par break si erreur
		while($row = spip_fetch_array($sql_result)) {
		
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			foreach(array('id_courrier','id_liste','total_abonnes') as $key) {
				$$key = intval($$key);
			}
			$titre = typo($titre);
			$texte = stripslashes($texte);
			$message_texte = stripslashes($message_texte);
			
			$nb_emails = array();
			
			$nb_emails_envoyes =
				$nb_emails_echec = 
				$nb_emails_non_envoyes = 
				$nb_emails['texte'] = 
				$nb_emails['html'] = 0
				;
		
			$pied_page = "" ;
			
			$str_log = "CRON:"; 
			
			//////////////////////////
			// Determiner le destinataire ou la liste destinataire
			if(email_valide($email_test)) {
			// courrier à destination adresse email de test
				spiplistes_log( _T('spiplistes:email_test')." : ".$destinataires, LOG_DEBUG);
				$test = 'oui';
				$mail_collectif = 'non' ;
				$str_log .= " EMAIL_TEST to: $email_test"; 
			} 
			else if($id_liste > 0) {
			// courrier à destination des abonnés d'une liste
				spiplistes_log("Envoi sur liste abos #$id_liste", LOG_DEBUG);
				$str_log .= " ID_LISTE to: #$id_liste"; 
	
				$pied_page = pied_de_page_liste($id_liste);
				$lang = spiplistes_langue_liste($id_liste);
				if($lang != '') $GLOBALS['spip_lang'] = $lang ;
	
				$mail_collectif = 'non' ;
				$result_d = spip_query("SELECT * FROM spip_listes WHERE id_liste=$id_liste LIMIT 1");
		
				if($ro = spip_fetch_array($result_d)){
					$titre_liste = $ro["titre"];
					$id = $ro["id_liste"];
					$email_liste = $ro["email_envoi"];
					spiplistes_log(_T('spiplistes:envoi_listes').$titre_liste, LOG_DEBUG);
				}
				else {
					$str_log .= " [ERROR] ID_LISTE #id_liste MISSING"; 
					spiplistes_log(_T('spiplistes:envoi_erreur'), LOG_DEBUG);
					spip_query("UPDATE spip_courriers SET statut='"._SPIPLISTES_STATUT_ERREUR."' WHERE id_courrier=$id_courrier LIMIT 1"); 
					// quitte while() principal
					break;
				}
			}
			else {
				// erreur dans un script d'appel ? Ou url ? Ou base erreur ?
				$str_log .= " [ERROR] MISSING PARAMS (id_liste AND email_test)";
					spip_query("UPDATE spip_courriers SET statut='"._SPIPLISTES_STATUT_ERREUR."' WHERE id_courrier=$id_courrier LIMIT 1"); 
				// quitte while() principal
				break;
			}
			
			// on prepare l'email
			$nomsite = $GLOBALS['meta']['nom_site'];
			$urlsite = $GLOBALS['meta']['adresse_site'];
				
			// email emetteur
			$email_webmaster = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
			$from = email_valide($email_liste) ? $email_liste : $email_webmaster;
		
			$is_from_valide = email_valide($from);         
					 
			$objet= filtrer_entites($titre);
			$remplacements = array("&#8217;"=>"'","&#8220;"=>'"',"&#8221;"=>'"');
			if ($GLOBALS['meta']['spiplistes_charset_envoi'] <> 'utf-8') {
				$objet = strtr($objet, $remplacements);
				$texte = strtr($texte, $remplacements);
			}
			
			if($opt_lien_en_tete_courrier) {
				$texte = ""
					. _T('spiplistes:Complement_lien_de_tete'
						, array('liencourrier'=>generer_url_public('courrier', "id_courrier=$id_courrier"), 'nomsite'=>$nomsite)
						)
					. $texte
					;
			}
			
			// on prepare le debut de la version html
			$pageh = "<html>\n\n<body>\n\n".$texte."\n\n";
			// la fin de la version html sera generee pour chaque destinataire
		  
			// on prepare la version texte
			if($message_texte !='')
				$page_ = $message_texte ;  
			else
				$page_ = version_texte($texte);
			 
			$page_.="\n\n________________________________________________________________________"  ;
			$page_.="\n\n"._T('spiplistes:editeur').$nomsite."\n"  ;
			$page_.=$urlsite."\n";
			$page_.="________________________________________________________________________"  ;
		
			if ($GLOBALS['meta']['spiplistes_charset_envoi']!=$GLOBALS['meta']['charset']){
				include_spip('inc/charsets');
				$pageh = unicode2charset(charset2unicode($pageh),$GLOBALS['meta']['spiplistes_charset_envoi']);
				$page_ = unicode2charset(charset2unicode($page_),$GLOBALS['meta']['spiplistes_charset_envoi']);
				$pied_page = unicode2charset(charset2unicode($pied_page),$GLOBALS['meta']['spiplistes_charset_envoi']);
			}
			
			$email_a_envoyer['texte'] = new phpMail('', $objet, '',$page_, $GLOBALS['meta']['spiplistes_charset_envoi']);
			$email_a_envoyer['texte']->From = $from ; 
			$email_a_envoyer['texte']->AddCustomHeader("Errors-To: ".$from); 
			$email_a_envoyer['texte']->AddCustomHeader("Reply-To: ".$from); 
			$email_a_envoyer['texte']->AddCustomHeader("Return-Path: ".$from); 
			$email_a_envoyer['texte']->SMTPKeepAlive = true;
			
			$email_a_envoyer['html'] = new phpMail('', $objet, $pageh, $page_, $GLOBALS['meta']['spiplistes_charset_envoi']);
			$email_a_envoyer['html']->From = $from ; 
			$email_a_envoyer['html']->AddCustomHeader("Errors-To: ".$from); 
			$email_a_envoyer['html']->AddCustomHeader("Reply-To: ".$from); 
			$email_a_envoyer['html']->AddCustomHeader("Return-Path: ".$from); 	
			$email_a_envoyer['html']->SMTPKeepAlive = true;
		
			spiplistes_log(_T('spiplistes:email_reponse').$from."\n"._T('spiplistes:contacts')." : ".$total_abonnes) ;
			
			if($total_abonnes){
		
				spiplistes_log(_T('spiplistes:message'). $titre);
		
				$limit=$GLOBALS['meta']['spiplistes_lots']; // nombre de messages envoyes par boucles.	
				
				//chopper un lot 
				
				if($test == 'oui')
					$result_inscrits = spip_query("SELECT id_auteur, nom, email FROM spip_auteurs WHERE email ="._q($email_test)." ORDER BY id_auteur ASC");
				else{
					//$result_inscrits = spip_query("SELECT a.nom, a.id_auteur, a.email, a.extra FROM spip_auteurs AS a, spip_auteurs_courriers AS b WHERE a.id_auteur=b.id_auteur AND b.id_courrier = "._q($id_courrier)." ORDER BY a.id_auteur ASC  LIMIT 0,".intval($limit));
					// un id pour ce processus
					$id_process = substr(creer_uniqid(),0,5);
					spip_query("UPDATE spip_auteurs_courriers SET etat="._q($id_process)." WHERE etat='' AND id_courrier = "._q($id_courrier)." LIMIT ".intval($limit));
					$result_inscrits = spip_query(
						"SELECT a.nom, a.id_auteur, a.email 
						FROM spip_auteurs AS a, spip_auteurs_courriers AS b 
						WHERE a.id_auteur=b.id_auteur AND b.id_courrier = "._q($id_courrier)." AND etat="._q($id_process)."
						ORDER BY a.id_auteur ASC");
				}
					
				$liste_abonnes = spip_num_rows($result_inscrits);
				if($liste_abonnes > 0){
		
					// ne sert qu'a l affichage
					$debut = $nb_emails_envoyes + $nb_emails_non_envoyes ; // ??
					spiplistes_log("envois effectues : ".$debut.", pas : ".$limit.", nb:".$liste_abonnes) ;	
		#	spip_timer();
					//envoyer le lot d'email selectionne
					while ($row2 = spip_fetch_array($result_inscrits)) {
						$str_temp = " ";
						$id_auteur = $row2['id_auteur'] ;
		
						//indiquer eventuellement le debut de l'envoi
						if(!$date_debut_envoi) {
							spip_query("UPDATE spip_courriers SET date_debut_envoi=NOW() WHERE id_courrier="._q($id_courrier)." LIMIT 1"); 
							$date_debut_envoi = true; // ne pas faire 20 update au premier lot :)
						}
				
						$abo = spip_fetch_array(spip_query("SELECT `spip_listes_format` FROM `spip_auteurs_elargis` WHERE `id_auteur`=$id_auteur")) ;		
						
						$format_abo = $abo["spip_listes_format"];
		
						$nom_auteur = $row2["nom"];
						$email = $row2["email"];
						
						$str_temp .= $nom_auteur."(".$format_abo.") - $email";
						$total=$total+1;
						unset ($cookie);
		
						if ( ($format_abo == 'texte') 
						  OR ($format_abo == 'html') ) {
							$cookie = creer_uniqid();
							spip_query("UPDATE spip_auteurs SET cookie_oubli ="._q($cookie)." WHERE email ="._q($email));				
		
							if ($is_from_valide){
								if ($format_abo == 'html')  // email HTML ------------------
									// desabo pied de page HTML
									$body = $pageh.$pied_page."<a href=\"".generer_url_public('abonnement','d='.$cookie)."\">"._T('spiplistes:abonnement_mail')."</a>\n\n</body></html>";
								else						// email TXT -----------------------
									// desabo pied de page texte			
									$body = $page_ ."\n\n"
									  . filtrer_entites(_T('spiplistes:abonnement_mail'))."\n"
									  . filtrer_entites(generer_url_public('abonnement','d='.$cookie))."\n\n"  ;
		
								$email_a_envoyer[$format_abo]->Body = $body;
								$email_a_envoyer[$format_abo]->SetAddress($email,$nom_auteur);
		
								$envoi_ok =  $opt_simuler_envoi ? true : $email_a_envoyer[$format_abo]->send();
								
								if ($envoi_ok) {
									spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur="._q($id_auteur)." AND id_courrier="._q($id_courrier));				
									$str_temp .= "  [OK]";
									$nb_emails_envoyes++;
									$nb_emails[$format_abo]++;
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
						else {  // email fin TXT /HTML  -----------------------------------------
							$nb_emails_non_envoyes++; //desabonnes 
							spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur="._q($id_auteur)." AND id_courrier="._q($id_courrier));	
							$str_temp .= _L('pas abonne en ce moment');
						} /* fin abo*/
						spiplistes_log($str_temp);
					}/* fin while */
					
					// si c'est un test on repasse en redac
					if ($test== 'oui') {
						spip_query("UPDATE spip_courriers SET statut='redac', email_test='', total_abonnes=0 WHERE id_courrier="._q($id_courrier));
						spiplistes_log('repasse en redac');
					}
					$email_a_envoyer['texte']->SmtpClose();
					$email_a_envoyer['html']->SmtpClose();
				} 
				else {   /* fin liste abonnes */	
					// archiver
					spiplistes_log("UPDATE spip_courriers SET statut='publie' WHERE id_courrier="._q($id_courrier));
					spip_query("UPDATE spip_courriers SET statut='publie' WHERE id_courrier="._q($id_courrier));
					$fin_envoi="oui";
				}
			}
			else {
				//aucun destinataire connu pour ce message
				spiplistes_log(_T('spiplistes:erreur_sans_destinataire')."---"._T('spiplistes:envoi_annule'), LOG_DEBUG);
				spip_query("UPDATE spip_courriers SET titre="._q(_T('spiplistes:erreur_destinataire')).", statut='publie' WHERE id_courrier="._q($id_courrier)); 
			}
			// faire le bilan apres l'envoi d'un lot en esperant que les differents processus simultanes se telescopent pas trop
			if($test != 'oui'){
				$stats = spip_fetch_array(spip_query("SELECT nb_emails_envoyes,nb_emails_non_envoyes,nb_emails_echec,nb_emails_texte,nb_emails_html FROM spip_courriers AS messages WHERE id_courrier = $id_courrier"));
				$nb_emails_envoyes = $nb_emails_envoyes + $stats['nb_emails_envoyes'] ;
				spip_query("UPDATE spip_courriers SET nb_emails_envoyes="._q($nb_emails_envoyes)." WHERE id_courrier="._q($id_courrier)); 
				if($nb_emails_non_envoyes > 0){
					$nb_emails_non_envoyes = $nb_emails_non_envoyes + $stats['nb_emails_non_envoyes'] ;
					spip_query("UPDATE spip_courriers SET nb_emails_non_envoyes="._q($nb_emails_non_envoyes)." WHERE id_courrier="._q($id_courrier));
				 }
				if($nb_emails_echec > 0){
					$nb_emails_echec = $nb_emails_echec + $stats['nb_emails_echec'] ;
					spip_query("UPDATE spip_courriers SET nb_emails_echec="._q($nb_emails_echec)." WHERE id_courrier="._q($id_courrier)); 
				 }
				$nb_emails['texte'] = $nb_emails['texte'] + $stats['nb_emails_texte'] ;
				$nb_emails['html'] = $nb_emails['html'] + $stats['nb_emails_html'] ;
				spip_query("UPDATE spip_courriers SET nb_emails_texte="._q($nb_emails['texte'])." WHERE id_courrier="._q($id_courrier)); 
				 spip_query("UPDATE spip_courriers SET nb_emails_html="._q($nb_emails['html'])." WHERE id_courrier="._q($id_courrier)); 
				if($fin_envoi=="oui")
					spip_query("UPDATE spip_courriers SET date_fin_envoi=NOW() WHERE id_courrier="._q($id_courrier)); 
		}
		} // end while()
	} // end if()
	else {
		$str_log .= " NO JOBS"; 
		spiplistes_log(_T('spiplistes:envoi_fini'), LOG_DEBUG);
	}

	spiplistes_log($str_log);
	
	return($meleuse_statut);
}


/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes              					      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                     								      */
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