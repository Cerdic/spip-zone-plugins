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

include_spip('inc/spiplistes_api_globales');

/*
	Prend dans le panier des courriers a envoyer (spip_courriers) les encours
	- formate le titre, texte pour l'envoi
	
	les etiquettes sont dans la queue d'envois (spip_auteurs_courriers)
	- id_auteur (pour reprendre l'adresse mail de id_auteur)
	- id_courrier (le courrier a dupliquer/envoyer)
	
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

	include_spip('inc/meta');
	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/acces');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_courrier');
	
	include_once(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');

	// initialise les options (préférences)
	foreach(array(
		'opt_simuler_envoi'
		, 'opt_suspendre_meleuse'
		, 'opt_lien_en_tete_courrier', 'lien_patron'
		, 'opt_ajout_pied_courrier', 'pied_patron'
		, 'opt_ajout_tampon_editeur', 'tampon_patron'
		) as $key) {
		$$key = __plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	}

	// Trouver un courrier a envoyer 
	$sql_courrier_select = array(
		'titre', 'texte', 'message_texte', 'type'
		, 'id_courrier', 'id_liste', 'email_test', 'total_abonnes', 'date_debut_envoi'
		);

	// prend le premier courrier en attente si present
	$sql_courrier_a_traiter = spiplistes_courriers_casier_premier(
		  $sql_courrier_select
		, "statut=".sql_quote(_SPIPLISTES_STATUT_DEPART)." OR statut=".sql_quote(_SPIPLISTES_STATUT_READY)
	);
	
	$nb_courriers = sql_count($sql_courrier_a_traiter);
	
	$nb_etiquettes = spiplistes_courriers_en_queue_compter("etat=".sql_quote(''));
	
	$str_log = "MEL:";
	
	// si meleuse suspendue, signale en log 
	if($opt_suspendre_meleuse == 'oui') {
		spiplistes_log("MEL: SUSPEND MODE !!!");
		return(0 - $nb_courriers);
	}

	$meleuse_statut = "1";
	
	if(
		$nb_courriers // courriers non auto au depart ?
		|| $nb_etiquettes
	) {

		spiplistes_log("MEL: ".($nb_courriers + $nb_etiquettes)." JOBS. Distribution... ($nb_courriers, $nb_etiquettes)");
		
		// signale en log si mode simulation
		if($opt_simuler_envoi == 'oui') {
			spiplistes_log("MEL: SIMULATION MODE !!!");
		}

		$nomsite = $GLOBALS['meta']['nom_site'];
		$urlsite = $GLOBALS['meta']['adresse_site'];

		// prépare le tampon editeur
		if($opt_ajout_tampon_editeur && !empty($tampon_patron)) {
			$tampon_html = spiplistes_tampon_html_get($tampon_patron);
			$tampon_texte = spiplistes_courrier_tampon_texte($tampon_patron, $tampon_html);
		}
		else {
			$tampon_html = $tampon_texte = "";
		}
		
		if($nb_courriers) {
			// courriers (probablement non auto) au départ ?
			// c'est le cas par exemple des tests
		} else if($nb_etiquettes) {
			// il reste des etiquettes ? envoi massif non terminé !
			// prendre la premiere etiquette sur le tas et traiter son courrier
			if($id_courrier = sql_getfetsel(
					  'id_courrier'
					, 'spip_auteurs_courriers'
					, "etat=".sql_quote('')." LIMIT 1"
				)
			) {
				$sql_courrier_a_traiter = spiplistes_courriers_casier_premier(
					  $sql_courrier_select
					, "id_courrier=".sql_quote($id_courrier)
				);
				spiplistes_log("MEL: etiquette en cours pour ID_COURRIER #$id_courrier", _SPIPLISTES_LOG_DEBUG);
			}
		}
		
		// boucle (sur LIMIT 1) pour pouvoir sortir par break si erreur
		while($row = sql_fetch($sql_courrier_a_traiter)) {
		
			foreach($sql_courrier_select as $key) {
				$$key = $row[$key];
			}
			foreach(array('id_courrier','id_liste','total_abonnes') as $key) {
				$$key = intval($$key);
			}
			$objet_html = filtrer_entites(typo($titre));
			$page_html = stripslashes($texte);
			$message_texte = stripslashes($message_texte);
			
			$nb_emails = array();
			
			// compteur pour la session uniquement
			// le total de chaque sera ajouté en fin de session
			$nb_emails_envoyes =
				$nb_emails_echec = 
				$nb_emails_non_envoyes = 
				$nb_emails['texte'] = 
				$nb_emails['html'] = 0
				;
			
			$pied_page_html = "" ;
			
			$str_log .= " ID_COURRIER: #$id_courrier"; 
			
			//////////////////////////
			// Determiner le destinataire ou la liste destinataire
			if($is_a_test = email_valide($email_test)) {
				// courrier à destination adresse email de test
				$str_log .= " TO: $email_test (TEST)"; 
			} 
			else if($id_liste > 0) {
				// courrier à destination des abonnés d'une liste
				$total_abonnes = spiplistes_listes_nb_abonnes_compter($id_liste);
				$str_log .= " to: ID_LISTE #$id_liste ($total_abonnes users)"; 
	
				$pied_page_html = spiplistes_pied_de_page_liste($id_liste);

				$lang = spiplistes_listes_langue($id_liste);

				if($lang != '') {
					$GLOBALS['spip_lang'] = $lang;
				}
				
				if(!$email_envoi = spiplistes_listes_email_emetteur($id_liste)) {
					$str_log .= " [ERROR] ID_LISTE #id_liste or email MISSING"; 
					spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_STATUT_ERREUR);
					// quitte while() principal
					break;
				}
			}
			else {
				// erreur dans un script d'appel ? Ou url ? Ou base erreur ?
				$str_log .= " [ERROR] MISSING PARAMS (id_liste AND email_test)";
				spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_STATUT_ERREUR);
				// quitte while() principal
				break;
			}
			
			//////////////////////////////
			// email emetteur
			$email_webmaster = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
			$from = email_valide($email_envoi) ? $email_envoi : $email_webmaster;
		
			$is_from_valide = email_valide($from);         
		
			////////////////////////////////////		  
			// Prepare la version texte
			$objet_texte = spiplistes_courrier_version_texte($objet_html);
			$page_texte = ($message_texte !='') ? $message_texte : spiplistes_courrier_version_texte($page_html);
			$pied_page_texte = spiplistes_courrier_version_texte($pied_page_html);
			
			////////////////////////////////////		  
			// Ajoute lien tete de courrier
			if($opt_lien_en_tete_courrier && ($opt_lien_en_tete_courrier == 'oui') && !empty($lien_patron)) {
				$url_courrier = generer_url_public('courrier', "id_courrier=$id_courrier");
				$lien_courrier_html = spiplistes_lien_courrier_html_get($lien_patron, $url_courrier);
				$lien_courrier_texte = spiplistes_lien_courrier_texte_get($lien_patron, $lien_courrier_html, $url_courrier);
				$page_html = $lien_courrier_html . $page_html;
				$page_texte = $lien_courrier_texte . $page_texte;
			}

			////////////////////////////////////		  
			// La petite ligne du renvoi du cookie pour modifier son abonnement
			$pied_rappel_html = _T('spiplistes:Cliquez_ici_pour_modifier_votre_abonnement');
			$pied_rappel_texte = _T('spiplistes:abonnement_mail_text');
			
			if($GLOBALS['meta']['spiplistes_charset_envoi'] != $GLOBALS['meta']['charset']){
				include_spip('inc/charsets');
				foreach(array('objet_html', 'objet_texte', 'page_html', 'page_texte', 'pied_page_html', 'pied_page_texte'
					, 'pied_rappel_html', 'pied_rappel_texte', 'tampon_html', 'tampon_texte') as $key) {
					if(!empty($$key)) {
						$$key = spiplistes_translate_2_charset($$key,$GLOBALS['meta']['spiplistes_charset_envoi']);
					}
				}
			}
			
			// corrige les liens relatifs (celui de texte a déjà été corrigé par la trieuse (cron)
			foreach(array('pied_page_html', 'pied_page_texte'
				, 'pied_rappel_html', 'pied_rappel_texte', 'tampon_html', 'tampon_texte') as $key) {
				if(!empty($$key)) {
					$$key = liens_absolus($$key);
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
		
			$str_log .= " REPLY-TO: ".$from;
			
			if($total_abonnes) {
		
				$limit = intval($GLOBALS['meta']['spiplistes_lots']); // nombre de messages envoyes par boucles.	
				
				spiplistes_log("MEL: titre: $titre, total_abos: $total_abonnes, limit: $limit", _SPIPLISTES_LOG_DEBUG);

				if($is_a_test) {
					$sql_adresses_dest = spip_query("SELECT id_auteur,nom,email FROM spip_auteurs WHERE email="._q($email_test)." LIMIT 1");
				}
				else {
					// Pour mémo: les étiquettes sont crées par la trieuse
					// ou directement en backoffice pour les envois de test
					
					// Traitement d'une liasse d'étiquettes
					// un id pour ce processus (le tampon est unique par liasse)
					$id_process = intval(substr(creer_uniqid(),0,5));
					
					// un coup de tampon sur les etiquettes 
					// des courriers qui vont partir
					spiplistes_auteurs_courriers_modifier(
						array(
							  'etat' => sql_quote($id_process))
							, "etat=".sql_quote('')." AND id_courrier=".sql_quote($id_courrier)." LIMIT $limit"
					);
					
					// prendre la liasse des courriers etiquettés et tamponnés
					$sql_adresses_dest = sql_select(
						  array('a.nom', 'a.id_auteur', 'a.email')
						, array('spip_auteurs AS a', 'spip_auteurs_courriers AS b')
						, array(
							"etat=".sql_quote($id_process)
							, "a.id_auteur=b.id_auteur"
							, "b.id_courrier=".sql_quote($id_courrier)
							)
						, array('a.email')
					);
				}
					
				$nb_destinataires = sql_count($sql_adresses_dest);
//spiplistes_log("MEL: nb destinataires: $nb_destinataires", _SPIPLISTES_LOG_DEBUG);
				if($nb_destinataires > 0) {
		
					//envoyer le lot d'emails selectionnes (la liasse)
					while($adresse = sql_fetch($sql_adresses_dest)) {
						$str_temp = " ";
						$id_auteur = intval($adresse['id_auteur']);
						$nom_auteur = $adresse['nom'];
						$email = $adresse['email'];

						// Marquer le debut de l'envoi
						if(!intval($date_debut_envoi)) {
							spiplistes_courrier_modifier ($id_courrier, array('date_debut_envoi' => "NOW()"), false);
						}
				
						$format_abo = spiplistes_format_abo_demande($id_auteur);
							
						$str_temp .= $nom_auteur."(".$format_abo.") - $email";
						$total++;
						unset ($cookie);
		
						if(($format_abo=='html') || ($format_abo=='texte')) {
							$cookie = creer_uniqid();
							spiplistes_auteurs_cookie_oubli_updateq($cookie, $email);
		
							if($is_from_valide) {
								$_url = generer_url_public('abonnement','d='.$cookie);
								// le &amp; semble poser problème sur certains MUA. A suivre...
								$_url = preg_replace(',(&amp;),','&', $_url);
								switch($format_abo) {
									case 'html':
										$body =
											"<html>\n\n<body>\n\n"
											. $page_html
											. $pied_page_html
											. "<a href=\"$_url\">".$pied_rappel_html."</a>\n\n</body></html>"
											. $tampon_html
											;
										break;
									case 'texte':
										$body =
											$page_texte ."\n\n"
											. $pied_page_texte
											. str_replace("&amp;", "&", $pied_rappel_texte). " " . $_url."\n\n"
											. $tampon_texte
											;
										break;
								}

								$email_a_envoyer[$format_abo]->Body = $body;
								$email_a_envoyer[$format_abo]->SetAddress($email, $nom_auteur);
								
								// envoie le mail																
								if(($opt_simuler_envoi == "oui") ? true : $email_a_envoyer[$format_abo]->send()) {
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
						} else {  
							$nb_emails_non_envoyes++; 
							$str_temp .= " "._T('spiplistes:pas_abonne_en_ce_moment');
						} /* fin abo*/
						spiplistes_log("MEL: ".$str_temp);
					} // fin while
					
					// supprime la liasse de la queue d'envois
spiplistes_log("MEL: supprimer queue $id_process", _SPIPLISTES_LOG_DEBUG);
					spiplistes_auteurs_courriers_supprimer("etat=".sql_quote($id_process));
					
					// si c'est un test on repasse le courrier en redac
					if($is_a_test) {
						spiplistes_courrier_modifier(
							$id_courrier
							, array(
								'email_test' => ''
								, 'total_abonnes' => 0
								, 'statut' => _SPIPLISTES_STATUT_REDAC
							)						
						);
						spiplistes_log('MEL: repasse document en statut redac', _SPIPLISTES_LOG_DEBUG);
					}
					$email_a_envoyer['texte']->SmtpClose();
					$email_a_envoyer['html']->SmtpClose();
				} // end if 
			}
			else {
				//aucun destinataire connu pour ce message
//spiplistes_log("MEL: "._T('spiplistes:erreur_sans_destinataire')."---"._T('spiplistes:envoi_annule'), _SPIPLISTES_LOG_DEBUG);
				spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_STATUT_IGNORE);
				spiplistes_courrier_supprimer_queue_envois('id_courrier', $id_courrier);
				$str_log .= " END #$id_courrier";
				// 
				break;
			}

			if(!$is_a_test) {
				// faire le bilan apres l'envoi d'un lot
				$sql_set_array = array(
					  'nb_emails_envoyes' => "(nb_emails_envoyes + $nb_emails_envoyes)"
					, 'nb_emails_texte' => "(nb_emails_texte + ".$nb_emails['texte'].")"
					, 'nb_emails_html' => "(nb_emails_html + ".$nb_emails['html'].")"
				);
				if($nb_emails_echec) {
					$sql_set_array['nb_emails_echec'] = "(nb_emails_echec + $nb_emails_echec)";
				}
				if($nb_emails_non_envoyes) {
					$sql_set_array['nb_emails_non_envoyes'] = "(nb_emails_non_envoyes + $nb_emails_non_envoyes)";
				}

				$str_log .= " (HTML: ".$nb_emails['html'].") (TEXT: ".$nb_emails['texte'].") (NONE: $nb_emails_non_envoyes)";

				///////////////////////
				// si courrier pas terminé, redemande la main au CRON, sinon nettoyage.
				if($t = spiplistes_courriers_en_queue_compter("id_courrier=".sql_quote($id_courrier))) {
					$str_log .= " LEFT $t"; 
					$meleuse_statut = "-1";
				}
				else {
					$statut = ($type == _SPIPLISTES_TYPE_NEWSLETTER) ? _SPIPLISTES_STATUT_PUBLIE : _SPIPLISTES_STATUT_AUTO;
spiplistes_log("MEL: nouveau statut $statut", _SPIPLISTES_LOG_DEBUG);
					$sql_set_array['statut'] = sql_quote($statut);
					$sql_set_array['date_fin_envoi'] = "NOW()";
					$str_log .= " END #$id_courrier";
					$meleuse_statut = "1";
				}
				spiplistes_courrier_modifier($id_courrier, $sql_set_array, false);
			}
		} // end while()
	} // end if()
	else {
		$str_log .= " NO JOBS"; 
	}

	spiplistes_log($str_log);

	if(($ii = spiplistes_courriers_en_cours_compter()) > 0) {
	// il en reste après la meleuse ? Signale au CRON tache non terminée
		spiplistes_log("MEL: courriers prets au depart ($ii) !", _SPIPLISTES_LOG_DEBUG);
		$meleuse_statut = "-1";
	}
	
	return($meleuse_statut);
} // end spiplistes_meleuse()

/*
*/
function spiplistes_listes_langue ($id_liste) {
	if(($id_liste = intval($id_liste)) > 0) {
		return(
			sql_getfetsel(
				'lang'
				, "spip_listes"
				, "id_liste=".sql_quote($id_liste)." LIMIT 1"
			)
		);
	}
	return(false);
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