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

	//////////////////////////
	// Trouver un courrier a envoyer 

	$sql_select = "titre,texte,message_texte,type,id_courrier,id_liste,email_test,total_abonnes,date_debut_envoi";
	
	$sql_query = "SELECT $sql_select FROM spip_courriers WHERE statut='"._SPIPLISTES_STATUT_ENCOURS."' LIMIT 1";

	$sql_result = spip_query($sql_query);

	$meleuse_statut = "1";
	
	if(spip_num_rows($sql_result)) {

		// boucle (sur LIMIT 1) pour pouvoir sortir par break si erreur
		while($row = spip_fetch_array($sql_result)) {
		
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			foreach(array('id_courrier','id_liste','total_abonnes') as $key) {
				$$key = intval($$key);
			}
			$objet_html = filtrer_entites(typo($titre));
			$page_html = stripslashes($texte);
			$message_texte = stripslashes($message_texte);
			
			$nb_emails = array();
			
			// compteur pourla session uniquement
			// le total de chaque sera ajouté en fin de session
			$nb_emails_envoyes =
				$nb_emails_echec = 
				$nb_emails_non_envoyes = 
				$nb_emails['texte'] = 
				$nb_emails['html'] = 0
				;
		
			$pied_page_html = "" ;
			
			$str_log = "MEL: ID_COURRIER: #$id_courrier"; 
			
			//////////////////////////
			// Determiner le destinataire ou la liste destinataire
			if($is_a_test = email_valide($email_test)) {
			// courrier à destination adresse email de test
				spiplistes_log("MEL: "._T('spiplistes:email_test')." : ".$destinataires, LOG_DEBUG);
				$str_log .= " to: $email_test (TEST)"; 
			} 
			else if($id_liste > 0) {
			// courrier à destination des abonnés d'une liste
				spiplistes_log("MEL: Envoi sur liste abos #$id_liste", LOG_DEBUG);
				$str_log .= " to: ID_LISTE #$id_liste ($total_abonnes users)"; 
	
				$pied_page_html = pied_de_page_liste($id_liste);
				$lang = spiplistes_langue_liste($id_liste);
				if($lang != '') $GLOBALS['spip_lang'] = $lang ;
	
				$result_d = spip_query("SELECT * FROM spip_listes WHERE id_liste=$id_liste LIMIT 1");
		
				if($ro = spip_fetch_array($result_d)){
					$titre_liste = $ro["titre"];
					$id = $ro["id_liste"];
					$email_envoi = $ro["email_envoi"];
					spiplistes_log("MEL: "._T('spiplistes:envoi_listes').$titre_liste, LOG_DEBUG);
				}
				else {
					$str_log .= " [ERROR] ID_LISTE #id_liste MISSING"; 
					spiplistes_log("MEL: "._T('spiplistes:envoi_erreur'), LOG_DEBUG);
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
			
			//////////////////////////////
			// on prepare l'email
			$nomsite = $GLOBALS['meta']['nom_site'];
			$urlsite = $GLOBALS['meta']['adresse_site'];
				
			// email emetteur
			$email_webmaster = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
			$from = email_valide($email_envoi) ? $email_envoi : $email_webmaster;
		
			$is_from_valide = email_valide($from);         
					 
			if ($GLOBALS['meta']['spiplistes_charset_envoi'] <> 'utf-8') {
				$remplacements = array("&#8217;"=>"'","&#8220;"=>'"',"&#8221;"=>'"');
				$objet_html = strtr($objet_html, $remplacements);
				$page_html = strtr($page_html, $remplacements);
			}
			
			if($opt_lien_en_tete_courrier) {
				$page_html = ""
					. _T('spiplistes:Complement_lien_de_tete'
						, array('liencourrier'=>generer_url_public('courrier', "id_courrier=$id_courrier"), 'nomsite'=>$nomsite)
						)
					. $page_html
					;
			}
			
			////////////////////////////////////		  
			// Prepare la version texte
			$objet_texte = version_texte($objet_html);
			$page_texte = ($message_texte !='') ? $message_texte : version_texte($page_html);
			$pied_page_texte = version_texte($pied_page_html);
			
			$ii = str_repeat("-", 40);
			$page_texte .= ""
				. "\n\n$ii"
				. "\n\n"._T('spiplistes:editeur').$nomsite."\n"
				. $urlsite."\n"
				. "$ii"
				;
		
			if($GLOBALS['meta']['spiplistes_charset_envoi'] != $GLOBALS['meta']['charset']){
				include_spip('inc/charsets');
				foreach(array('objet_html', 'objet_texte', 'page_html', 'page_texte', 'pied_page_html', 'pied_page_texte') as $key) {
					$$key = unicode2charset(charset2unicode($$key),$GLOBALS['meta']['spiplistes_charset_envoi']);
				}
			}
			
			$email_a_envoyer['texte'] = new phpMail('', $objet_texte, '', $page_texte, $GLOBALS['meta']['spiplistes_charset_envoi']);
			$email_a_envoyer['texte']->From = $from ; 
			$email_a_envoyer['texte']->AddCustomHeader("Errors-To: ".$from); 
			$email_a_envoyer['texte']->AddCustomHeader("Reply-To: ".$from); 
			$email_a_envoyer['texte']->AddCustomHeader("Return-Path: ".$from); 
			$email_a_envoyer['texte']->SMTPKeepAlive = true;
			
			$email_a_envoyer['html'] = new phpMail('', $objet_html, $page_html, $page_texte, $GLOBALS['meta']['spiplistes_charset_envoi']);
			$email_a_envoyer['html']->From = $from ; 
			$email_a_envoyer['html']->AddCustomHeader("Errors-To: ".$from); 
			$email_a_envoyer['html']->AddCustomHeader("Reply-To: ".$from); 
			$email_a_envoyer['html']->AddCustomHeader("Return-Path: ".$from); 	
			$email_a_envoyer['html']->SMTPKeepAlive = true;
		
			spiplistes_log("MEL: "._T('spiplistes:email_reponse').$from."\n"._T('spiplistes:contacts')." : ".$total_abonnes, LOG_DEBUG);
			
			if($total_abonnes) {
		
				$limit = intval($GLOBALS['meta']['spiplistes_lots']); // nombre de messages envoyes par boucles.	
				
				spiplistes_log("MEL: titre: $titre, total_abos: $total_abonnes, limit: $limit", LOG_DEBUG);

				//chopper un lot 
				
				if($is_a_test) {
					$result_inscrits = spip_query("SELECT id_auteur,nom,email FROM spip_auteurs WHERE email="._q($email_test)." LIMIT 1");
				}
				else {
					// un id pour ce processus
					$id_process = intval(substr(creer_uniqid(),0,5));
					spip_query("UPDATE spip_auteurs_courriers SET etat=$id_process WHERE etat='' AND id_courrier=$id_courrier LIMIT $limit");
					$result_inscrits = spip_query(
						"SELECT a.nom, a.id_auteur, a.email 
						FROM spip_auteurs AS a, spip_auteurs_courriers AS b 
						WHERE a.id_auteur=b.id_auteur AND b.id_courrier=$id_courrier AND etat=$id_process"
						);
					spiplistes_log("MEL: marque le lot: $id_process", LOG_DEBUG);
				}
					
				$liste_abonnes = spip_num_rows($result_inscrits);
				spiplistes_log("MEL: nb destinataires: $liste_abonnes", LOG_DEBUG);
				if($liste_abonnes > 0) {
		
					// ne sert qu'a l affichage
					$debut = $nb_emails_envoyes + $nb_emails_non_envoyes; 
					spiplistes_log("MEL: envois effectues : ".$debut.", pas : ".$limit.", nb:".$liste_abonnes, LOG_DEBUG);	

					//envoyer le lot d'email selectionne
					while ($row2 = spip_fetch_array($result_inscrits)) {
						$str_temp = " ";
						$id_auteur = intval($row2['id_auteur']);
						$nom_auteur = $row2['nom'];
						$email = $row2['email'];

						// Marquer le debut de l'envoi
						if(!intval($date_debut_envoi)) {
							spip_query("UPDATE spip_courriers SET date_debut_envoi=NOW() WHERE id_courrier=$id_courrier LIMIT 1"); 
						}
				
						$format_abo = spiplistes_demande_format_abo($id_auteur);
							
						$str_temp .= $nom_auteur."(".$format_abo.") - $email";
						$total++;
						unset ($cookie);
		
						if($format_abo) {
							$cookie = creer_uniqid();
							spip_query("UPDATE spip_auteurs SET cookie_oubli ="._q($cookie)." WHERE email ="._q($email)." LIMIT 1");				
		
							if ($is_from_valide) {
								switch($format_abo) {
									case 'html':
										$body =
											"<html>\n\n<body>\n\n"
											. $page_html
											. $pied_page_html
											. "<a href=\"".generer_url_public('abonnement','d='.$cookie)."\">"._T('spiplistes:abonnement_mail')
											. "</a>\n\n</body></html>"
											;
										break;
									case 'texte':
										$body =
											$page_texte ."\n\n"
											. $pied_page_texte
										  . filtrer_entites(_T('spiplistes:abonnement_mail'))."\n"
										  . filtrer_entites(generer_url_public('abonnement','d='.$cookie))."\n\n"
										  ;
										break;
								}

								$email_a_envoyer[$format_abo]->Body = $body;
								$email_a_envoyer[$format_abo]->SetAddress($email, $nom_auteur);
								
								// envoie le mail
								if($opt_simuler_envoi ? true : $email_a_envoyer[$format_abo]->send()) {
									spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur=$id_auteur AND id_courrier=$id_courrier");				
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
							spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur=$id_auteur AND id_courrier="._q($id_courrier));	
							$str_temp .= _L('pas abonne en ce moment');
						} /* fin abo*/
						spiplistes_log("MEL: ".$str_temp, LOG_DEBUG);
					}/* fin while */
					
					// si c'est un test on repasse en redac
					if($is_a_test) {
						spip_query("UPDATE spip_courriers SET statut='"._SPIPLISTES_STATUT_REDAC."',email_test='',total_abonnes=0 WHERE id_courrier=$id_courrier");
						spiplistes_log('MEL: repasse en redac', LOG_DEBUG);
					}
					$email_a_envoyer['texte']->SmtpClose();
					$email_a_envoyer['html']->SmtpClose();
				} 
			}
			else {
				//aucun destinataire connu pour ce message
				spiplistes_log("MEL: "._T('spiplistes:erreur_sans_destinataire')."---"._T('spiplistes:envoi_annule'), LOG_DEBUG);
				spip_query("UPDATE spip_courriers SET statut='"._SPIPLISTES_STATUT_IGNORE."' WHERE id_courrier=$id_courrier LIMIT 1");
				spiplistes_supprime_liste_envois($id_courrier);
				$str_log .= " END #$id_courrier";
				// 
				break;
			}

			if(!$is_a_test) {
				// faire le bilan apres l'envoi d'un lot
				$sql_update = " nb_emails_envoyes=(nb_emails_envoyes + $nb_emails_envoyes),
								nb_emails_texte=(nb_emails_texte + ".$nb_emails['texte']."),
								nb_emails_html=(nb_emails_html + ".$nb_emails['html']."),"
								;
				if($nb_emails_echec) $sql_update .= "nb_emails_echec=(nb_emails_echec + $nb_emails_echec),";
				if($nb_emails_non_envoyes) $sql_update .= "nb_emails_non_envoyes=(nb_emails_non_envoyes + $nb_emails_non_envoyes),";

				$str_log .= " (HTML: ".$nb_emails['html'].") (TEXT: ".$nb_emails['texte'].") (NONE: $nb_emails_non_envoyes)";

				///////////////////////
				// si courrier pas terminé, redemande la main au CRON, sinon nettoyage.
				if($t = spiplistes_nb_courriers_en_cours($id_courrier)) {
					$str_log .= " LEFT $t"; 
					$meleuse_statut = "-1";
				}
				else {
					$statut = ($type=_SPIPLISTES_TYPE_NEWSLETTER) ? _SPIPLISTES_STATUT_PUBLIE : _SPIPLISTES_STATUT_AUTO;
					$sql_update .= "statut='$statut',date_fin_envoi=NOW(),";
					spiplistes_supprime_liste_envois($id_courrier);
					$str_log .= " END #$id_courrier";
					$meleuse_statut = "1";
				}
				$sql_update = rtrim($sql_update, ",");
				spip_query("UPDATE spip_courriers SET $sql_update WHERE id_courrier=$id_courrier LIMIT 1"); 
			}
		} // end while()
	} // end if()
	else {
		$str_log .= " NO JOBS"; 
		spiplistes_log("MEL: "._T('spiplistes:envoi_fini'), LOG_DEBUG);
	}

	spiplistes_log($str_log);

	if(($ii = spiplistes_nb_courriers_en_cours()) > 0) {
	// il en reste après la meleuse ? Signale au CRON tache non terminée
		spiplistes_log("MEL: il reste des courriers a envoyer ($ii) !", LOG_DEBUG);
		$meleuse_statut = "-1";
	}
	
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