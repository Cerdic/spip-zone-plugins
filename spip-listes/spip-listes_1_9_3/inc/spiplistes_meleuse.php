<?php

// inc/spiplistes_meleuse.php

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
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');

/*
	Prend dans le panier des courriers a envoyer (spip_courriers) les encours
	- formate le titre, texte pour l'envoi
	
	les etiquettes sont dans la queue d'envois (spip_auteurs_courriers)
	- id_auteur (pour reprendre l'adresse mail de id_auteur)
	- id_courrier (le courrier a dupliquer/envoyer)
	
	la queue (spip_auteurs_courriers) a ete remplie par cron_spiplistes_cron()
	se sert de la queue pour ventiler les envois par lots

	le courrier (spip_courriers) doit avoir date <= time() et statut 'encour'
	si email_test, la meleuse envoie le courrier a email_test, 
		supprime email_test du courrier 
		et repositionne le statut du courrier en 'redac'
	si pas email_test mais id_liste, 
		regarde la queue d'envois (spip_auteurs_courriers) 
		et passe le statut du courrier (spip_courriers) a :
			'publie' si type == 'nl' (newsletter)
			'auto' si type == 'auto' (liste programmee)
		et envoie les courriers precises aux abonnes de cette liste
		et supprime l'identifiant du courrier dans la queue d'envois (spip_auteurs_courriers)

	renvoie:
	- nul, si la tache n'a pas a etre effectuee
	- positif, si la tache a ete effectuee
	- negatif, si la tache doit etre poursuivie ou recommencee

*/
	
function spiplistes_meleuse ($last_time) { 

	//spiplistes_debug_log('spiplistes_meleuse()');
	
	include_spip('inc/meta');
	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/acces');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_courrier');
	
	include_once(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_mail.inc.php');

	// initialise les options (preferences)
	foreach(array(
		'opt_simuler_envoi'
		, 'opt_suspendre_meleuse'
		, 'opt_lien_en_tete_courrier', 'lien_patron'
		, 'opt_ajout_pied_courrier', 'pied_patron'
		, 'opt_ajout_tampon_editeur'
		, 'opt_personnaliser_courrier'
		, 'opt_log_voir_destinataire'
		) as $key) {
		$$key = spiplistes_pref_lire($key);
	}
	
	$sql_vide = sql_quote('');
	$nb_etiquettes = spiplistes_courriers_en_queue_compter('etat='.$sql_vide);
	
	
	$prefix_log = _SPIPLISTES_PREFIX_LOG;
	
	// si meleuse suspendue, signale en log 
	if($opt_suspendre_meleuse == 'oui') {
		spiplistes_log($prefix_log.'SUSPEND MODE !!!');
		return(0 - $last_time);
	}

	if($nb_etiquettes) {
		
		$eol = "\n";
		$eol2 =$eol.$eol;
		$body_html_debut = '<html>'.$eol2.'<body style="margin:0;padding:0;">'.$eol2;
		$body_html_fin = $eol2.'</body></html>';
		$charset_spip = $GLOBALS['meta']['charset'];
		$charset_dest = $GLOBALS['meta']['spiplistes_charset_envoi'];

		spiplistes_log($prefix_log.$nb_etiquettes.' job(s), distribution...');
		
		$log_voir_destinataire = ($opt_log_voir_destinataire == 'oui');
		$simuler_envoi = ($opt_simuler_envoi == 'oui');
		
		// signale en log si mode simulation
		if($simuler_envoi) {
			spiplistes_log($prefix_log.'SIMULATION MODE !!!');
		}

		// prepare le tampon editeur
		if($opt_ajout_tampon_editeur == 'oui')
		{
			list($tampon_html, $tampon_texte) = spiplistes_tampon_assembler_patron();
		}
		else {
			$tampon_html = $tampon_texte = '';
		}
		
		// prendre la premiere etiquette sur le tas et traiter son courrier
		$sql_courrier_select = array(
			'titre', 'texte', 'message_texte', 'type'
			, 'id_courrier', 'id_liste', 'email_test', 'total_abonnes', 'date_debut_envoi'
			);
		if($id_courrier = 
			intval(spiplistes_courriers_en_queue_premier('id_courrier', 'etat='.$sql_vide))
		) {
			$sql_courrier_a_traiter = spiplistes_courriers_casier_premier(
				  $sql_courrier_select
				, 'id_courrier='.sql_quote($id_courrier)
			);
			spiplistes_debug_log ($prefix_log.'etiquette en cours pour id_courrier #'.$id_courrier);
		} else {
			// un vieux bug dans une ancienne version, eradique depuis (j'espere ;-)
			//spiplistes_log($prefix_log."premiere etiquette en erreur. id_courier = 0. Supprimer cette etiquette manuellement !");
			spiplistes_log(_T('spiplistes:erreur_queue_supprimer_courrier'
							  , array('s' => $prefix_log))
						   );
		}
		
		// boucle (sur LIMIT 1) pour pouvoir sortir par break si erreur
		while($row = sql_fetch($sql_courrier_a_traiter)) {
		
			foreach($sql_courrier_select as $key) {
				$$key = $row[$key];
			}
			foreach(array('id_courrier','id_liste','total_abonnes') as $key) {
				$$key = intval($$key);
			}
			// objet (subject) ne peut pas être en html ?!
			// sauf pour le webmail (et encore)
			$objet_html = filtrer_entites(typo(spiplistes_calculer_balise_titre(extraire_multi($titre))));
			$page_html = stripslashes($texte);
			$message_texte = stripslashes($message_texte);
			
			$nb_emails = array();
			
			// compteur pour la session uniquement
			// le total de chaque sera ajoute en fin de session
			$nb_emails_envoyes =
				$nb_emails_echec = 
				$nb_emails_non_envoyes = 
				$nb_emails['texte'] = 
				$nb_emails['html'] = 0
				;
			
			$str_log = 'id_courrier #'.$id_courrier;
			
			//////////////////////////
			// Determiner email de l emetteur
			if($is_a_test = email_valide($email_test)) {
				// courrier a destination adresse email de test
				$str_log .= ' TO: '.$email_test.' (TEST)';
			} 
			else if($id_liste > 0) {
				// courrier a destination des abonnes d'une liste
				$total_abonnes = spiplistes_listes_nb_abonnes_compter($id_liste);
				$str_log .= ' TO id_liste #'.$id_liste.' ('.$total_abonnes.' users)';
	
				$lang = spiplistes_listes_langue($id_liste);

				if($lang != '') {
					$GLOBALS['spip_lang'] = $lang;
				}
				
				$contexte = array('lang' => $lang);
				
				list($pied_html, $pied_texte) = spiplistes_pied_page_assembler_patron($id_liste, $lang);
			}
			else {
				// erreur dans un script d'appel ? Ou url ? Ou base erreur ?
				$str_log .= ' [ERROR] MISSING PARAMS (id_liste AND email_test)';
				spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_COURRIER_STATUT_ERREUR);
				// quitte while() principal
				break;
			}
			
			//////////////////////////////
			// email emetteur
			$email_envoi = spiplistes_listes_email_emetteur($id_liste);
			if(!$is_a_test && !($email_envoi)) { 
				$str_log .= ' [ERROR] ID_LISTE #'.$id_liste.' or from email MISSING'; 
				spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_COURRIER_STATUT_ERREUR);
				// quitte while() principal
				break;
			}
			$from = $email_envoi;
			if($from_valide = email_valide($from)) {
				if(strpos($from, '<') === false) {
					$fromname = spiplistes_nom_site_texte ($lang);
					$fromname = extraire_multi($GLOBALS['meta']['nom_site']);
					if ($charset_dest!=$charset_spip)
					{
						include_spip('inc/charsets');
						$fromname = unicode2charset(charset2unicode($fromname),$charset_dest);
					}
				}
			}
			else {
				spiplistes_log('[ERROR] from address incorrect: '.$from);
				if($is_a_test) {
					spiplistes_courriers_statut_redac ($id_courrier);
				}
				// break; // garder pour incrementer les erreurs des listes
			}
			
			$email_reply_to = spiplistes_pref_lire_defaut('email_reply_to', $from);
			
			$return_path = spiplistes_pref_lire_defaut('email_return_path_defaut', $from);
			
			////////////////////////////////////
			// Prepare la version texte
			$objet_texte = $titre;
			$page_texte = ($message_texte !='')
				? $message_texte
				: spiplistes_courrier_version_texte($page_html)
				;
			
			////////////////////////////////////
			// Ajoute lien tete de courrier
			if(
				($opt_lien_en_tete_courrier == 'oui') 
				&& !empty($lien_patron)
			) {
				list($lien_html, $lien_texte) = spiplistes_courriers_assembler_patron (
					_SPIPLISTES_PATRONS_TETE_DIR . $lien_patron
					, array('id_courrier' => $id_courrier
							, 'lang' => $lang)
					);
				$page_html = $lien_html . $page_html;
				$page_texte = $lien_texte . $page_texte;
			}

			////////////////////////////////////
			// La petite ligne du renvoi du cookie pour modifier son abonnement
			//$pied_rappel_html = _T('spiplistes:modif_abonnement_html');
			//$pied_rappel_texte = _T('spiplistes:modif_abonnement_text');
			
			// transcrire le contenu
			if($charset_dest != $charset_spip){
				include_spip('inc/charsets');
				foreach(array(
					  'objet_html', 'objet_texte'
					, 'page_html', 'page_texte'
					, 'pied_html', 'pied_texte'
					//, 'pied_rappel_html', 'pied_rappel_texte'
					, 'tampon_html', 'tampon_texte') as $key) 
				{
					if(!empty($$key)) {
						$$key = spiplistes_translate_2_charset(
							$$key
							, $charset_dest
							, (strpos($key, 'texte') === false)
							);
					}
				}
			}
			
			// corrige les liens relatifs (celui de texte a deja ete corrige par la trieuse (cron)
			foreach(array('pied_html', 'pied_texte'
				//, 'pied_rappel_html', 'pied_rappel_texte'
				, 'tampon_html', 'tampon_texte') as $key) {
				if(!empty($$key)) {
					$$key = spiplistes_liens_absolus ($$key);
				}
			}
			
			$email_a_envoyer = array();
			$email_a_envoyer['texte'] = new phpMail('', $objet_texte, ''
													, $page_texte, $charset_dest);
			$email_a_envoyer['texte']->From = $from ; 
			if($fromname) $email_a_envoyer['texte']->FromName = $fromname ;
			// Errors-To:,    Non-standard @see: http://www.ietf.org/rfc/rfc2076.txt
			//$email_a_envoyer['texte']->AddCustomHeader('Errors-To: '.$return_path); 
			$email_a_envoyer['texte']->AddCustomHeader('Reply-To: '.$email_reply_to); 
			$email_a_envoyer['texte']->AddCustomHeader('Return-Path: '.$return_path); 
			$email_a_envoyer['texte']->SMTPKeepAlive = true;

			//$email_a_envoyer['html'] = new phpMail('', $objet_html, $page_html, $page_texte, $charset_dest);
			$email_a_envoyer['html'] = new phpMail(''
												   , $objet_html
												   , $page_html
												   , $page_texte
												   , $charset_dest
												   );
			$email_a_envoyer['html']->From = $from ; 
			if($fromname) {
				$email_a_envoyer['html']->FromName = $fromname ;
			}
			//$email_a_envoyer['html']->AddCustomHeader('Errors-To: '.$return_path); 
			$email_a_envoyer['html']->AddCustomHeader('Reply-To: '.$email_reply_to); 
			$email_a_envoyer['html']->AddCustomHeader('Return-Path: '.$return_path); 	
			$email_a_envoyer['html']->SMTPKeepAlive = true;
		
			$str_log .= ' REPLY-TO: '.$email_reply_to.' RETURN-PATH: '.$return_path;
			
			if($total_abonnes) {
		
				$limit = intval($GLOBALS['meta']['spiplistes_lots']); // nombre de messages envoyes par boucles.	
				
				if($is_a_test) {
					$sql_adresses_dest = sql_select('id_auteur,nom,email', 'spip_auteurs'
						, 'email='.sql_quote($email_test).' LIMIT 1');
				}
				else {
					// Pour memo: les etiquettes sont creees par la trieuse
					// ou directement en backoffice 
					// - pour les envois de test
					// - pour les envoyer maintenant des courriers
					
					// Traitement d'une liasse d'etiquettes
					// un id pour ce processus (le tampon est unique par liasse)
					$id_process = intval(substr(creer_uniqid(),0,5));
					$prefix_log .= '['.$id_process.'] ';
			
					// un coup de tampon sur les etiquettes 
					// des courriers qui vont partir
					spiplistes_courriers_en_queue_modifier(
						array(
							  'etat' => sql_quote($id_process))
							, 'etat='.$sql_vide.' AND id_courrier='.sql_quote($id_courrier).' LIMIT '.$limit
					);
					
					// prendre la liasse des etiquettes tamponnees
					$sql_adresses_dest = sql_select(
						  array('a.nom', 'a.id_auteur', 'a.email')
						, array('spip_auteurs AS a', 'spip_auteurs_courriers AS b')
						, array(
							'etat='.sql_quote($id_process)
							, 'a.id_auteur=b.id_auteur'
							, 'b.id_courrier='.sql_quote($id_courrier)
							)
						, 'a.email'
					);
				}
					
				$nb_destinataires = sql_count($sql_adresses_dest);
				spiplistes_log($prefix_log.'nb etiquettes a traiter: '.$nb_destinataires);
				if($nb_destinataires > 0) {

					spiplistes_debug_log($prefix_log.'total_abos: '.$total_abonnes.', en cours: '.$nb_destinataires.', limit: '.$limit);

/*
// CP:20100215: inutile de compter AVANT
// si process en //, le chiffre est faux
					// replacer les compteurs
					if($row = sql_fetch(sql_select(
						"nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html"
						, 'spip_courriers'
						, 'id_courrier='.sql_quote($id_courrier)
						, '', '', 1
						))
					) {
						$nb_emails_envoyes = intval($row['nb_emails_envoyes']);
						$nb_emails_echec = intval($row['nb_emails_echec']);
						$nb_emails_non_envoyes = intval($row['nb_emails_non_envoyes']);
						$nb_emails['texte'] = intval($row['nb_emails_texte']);
						$nb_emails['html'] = intval($row['nb_emails_html']);
					}
*/

					//envoyer le lot d'emails selectionne' (la liasse)
					while($adresse = sql_fetch($sql_adresses_dest)) {

						if($log_voir_destinataire) {
							$str_temp = '';
						}

						$id_auteur = intval($adresse['id_auteur']);
						$nom_auteur = $adresse['nom'];
						$email = $adresse['email'];

						// Marquer le debut de l'envoi
						if(!intval($date_debut_envoi)) {
							spiplistes_courrier_modifier ($id_courrier, array('date_debut_envoi' => 'NOW()'), false);
						}
				
						$format_abo = spiplistes_format_abo_demande($id_auteur);

						$total++;
						if($log_voir_destinataire) {
							$str_temp .= $nom_auteur.'('.$format_abo.') - '.$email;
						}
						unset ($cookie);
		
						if(($format_abo=='html') || ($format_abo=='texte')) {
							$cookie = creer_uniqid();
							spiplistes_auteurs_cookie_oubli_updateq($cookie, $email);
		
							if($from_valide) {
								//$_url = generer_url_public('abonnement','d='.$cookie);
								
								if($opt_personnaliser_courrier == 'oui') {
									list($ventre_html, $ventre_texte) = spiplistes_personnaliser_courrier(
																			$page_html
																			, $page_texte
																			, $id_auteur
																			, $format_abo
																		);
								}
								else {
									$ventre_html = $page_html;
									$ventre_texte = $page_texte;
								}
								// le &amp; semble poser probleme sur certains MUA. A suivre...
								//$_url = preg_replace(',(&amp;),','&', $_url);
								
								// Pour le moment (27/03/2011), un seul patron connu
								$lien_rappel = 'lien_standard';
								
								list($pied_rappel_html, $pied_rappel_texte) = spiplistes_courriers_assembler_patron (
									_SPIPLISTES_PATRONS_LIEN_DIR . $lien_rappel
									, array('id_courrier' => $id_courrier
											, 'id_liste' => $id_liste
											, '_url' => generer_url_public()
											, 'lang' => $lang
											, 'd' => $cookie
											)
								);
								
								switch($format_abo) {
									case 'html':
										// Si on ne trouve pas les tags HTML alors on les ajoutes
										if (FALSE === strpos($ventre_html, '</html>')) {
											$email_a_envoyer[$format_abo]->Body =
												  $body_html_debut . $eol
												. $ventre_html . $eol
												. $pied_html . $eol
												//. '<a href="'.$_url.'">'.$pied_rappel_html.'</a>'
												. $pied_rappel_html . $eol
												. $tampon_html . $eol
												. $body_html_fin
												;										
										} else {
											// Si on trouve les tags HTML cela veut dire que l'auteur
											// veut pouvoir gerer lui meme la partie <head> ainsi que le lien de desabonnement
											// donc on ne prend en compte que la partie ventre_html.
											$tags_perso = array('http://%URL_ABONNEMENT%' => generer_url_public('abonnement','d='.$cookie),);
											$email_a_envoyer[$format_abo]->Body = str_replace(array_keys($tags_perso), array_values($tags_perso), $ventre_html);
										}
										// la version alternative texte 
										$email_a_envoyer[$format_abo]->AltBody = 
											$ventre_texte .$eol2
											. $pied_texte . $eol2
											//. str_replace('&amp;', '&', $pied_rappel_texte). ' ' . $_url.$eol2
											. $pied_rappel_texte . $eol2
											. $tampon_texte
											;
										break;
									case 'texte':
										$email_a_envoyer[$format_abo]->Body =
											$ventre_texte .$eol2
											. $pied_texte
											. str_replace('&amp;', '&', $pied_rappel_texte). ' ' . $_url.$eol2
											. $tampon_texte
											;
										break;
								}

								$email_a_envoyer[$format_abo]->SetAddress($email, $nom_auteur);
								// envoie le mail																
								if($simuler_envoi || $email_a_envoyer[$format_abo]->send()) {
									$nb_emails_envoyes++;
									$nb_emails[$format_abo]++;
									if($log_voir_destinataire) {
										$str_temp .= '  [OK]';
									}
								}
								else {
									$nb_emails_echec++;
									if($log_voir_destinataire) {
										$str_temp .= _T('spiplistes:erreur_mail');
									}
								}
							}
							else {
								$nb_emails_echec++;
								if($log_voir_destinataire) {
									$str_temp .= _T('spiplistes:sans_adresse');
								}
							} 
							
						} // end if(($format_abo=='html') || ($format_abo=='texte'))
						else {  
							$nb_emails_non_envoyes++; 
							if($log_voir_destinataire) {
								$str_temp .= ' '._T('spiplistes:msg_abonne_sans_format');
							}
							// prevenir qu'il manque le format
							spiplistes_log($prefix_log.' destination format MISSING FOR ID_AUTEUR #'.$id_auteur);
						} /* fin abo*/
						
						if($log_voir_destinataire) {
							spiplistes_log($prefix_log.$str_temp);
						}
						
					} // fin while
					
					// supprime la liasse de la queue d'envois
					spiplistes_debug_log($prefix_log."envoi OK. Supprimer queue $id_process");
					spiplistes_courriers_en_queue_supprimer('etat='.sql_quote($id_process));
					
					// si c'est un test on repasse le courrier en redac
					if($is_a_test) {
						spiplistes_courriers_statut_redac ($id_courrier);
					}
					$email_a_envoyer['texte']->SmtpClose();
					$email_a_envoyer['html']->SmtpClose();
				} // end if 
			}
			else {
				//aucun destinataire connu pour ce message
				spiplistes_debug_log($prefix_log._T('spiplistes:erreur_sans_destinataire')
									 . '---' . _T('spiplistes:envoi_annule')
									 );
				spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_COURRIER_STATUT_IGNORE);
				spiplistes_courrier_supprimer_queue_envois('id_courrier', $id_courrier);
				$str_log .= ' END #'.$id_courrier;
				// 
				break;
			}

			if(!$is_a_test) {
				// faire le bilan apres l'envoi d'un lot
				$sql_set_array = array(
					  'nb_emails_envoyes' => sql_quote('nb_emails_envoyes').'+'.$nb_emails_envoyes
					, 'nb_emails_texte' => sql_quote('nb_emails_texte').'+'.$nb_emails['texte']
					, 'nb_emails_html' => sql_quote('nb_emails_html').'+'.$nb_emails['html']
				);
				if($nb_emails_echec) {
					$sql_set_array['nb_emails_echec'] = sql_quote('nb_emails_echec').'+'.$nb_emails_echec;
				}
				if($nb_emails_non_envoyes) {
					$sql_set_array['nb_emails_non_envoyes'] = sql_quote('nb_emails_non_envoyes').'+'.$nb_emails_non_envoyes;
				}

				spiplistes_log($prefix_log.$str_log);
				
				$str_log = spiplistes_trace_compteur ($id_courrier
												   , $nb_emails_envoyes
												   , $nb_emails['html']
												   , $nb_emails['texte']
												   , $nb_emails_non_envoyes
												   , $nb_emails_echec
												   , 'SESSION');

				// si courrier pas termine, redemande la main au CRON, sinon nettoyage.
				if($t = spiplistes_courriers_en_queue_compter('id_courrier='.sql_quote($id_courrier))) {
					$str_log .= ' LEFT '.$t.' jobs'; 
				}
				else {
					$statut = ($type == _SPIPLISTES_COURRIER_TYPE_NEWSLETTER) ? _SPIPLISTES_COURRIER_STATUT_PUBLIE : _SPIPLISTES_COURRIER_STATUT_AUTO;
					spiplistes_debug_log($prefix_log."nouveau statut $statut");
					$sql_set_array['statut'] = sql_quote($statut);
					$sql_set_array['date_fin_envoi'] = 'NOW()';
					$str_log .= ' END #'.$id_courrier;
				}
				spiplistes_courrier_modifier($id_courrier, $sql_set_array, false);
				
				// placer en log le suivi des compteurs si mode debug
				if (spiplistes_debug_log())
				{					
					if ($row = sql_fetch(sql_select(
						'nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html'
						, 'spip_courriers'
						, 'id_courrier='.sql_quote($id_courrier)
						, '', '', 1
						))
					) {
						spiplistes_log($prefix_log.$str_log);
						
						$str_log = spiplistes_trace_compteur ($id_courrier
												   , $row['nb_emails_envoyes']
												   , $row['nb_emails_html']
												   , $row['nb_emails_texte']
												   , $row['nb_emails_non_envoyes']
												   , $row['nb_emails_echec']
												   , 'FROM_DB')
												. ' END #'.$id_courrier;
												;
					}
				}
				
			}
		} // end while()
	} // end if($nb_etiquettes)
	else {
		$str_log = 'no job'; 
	}

	spiplistes_log($prefix_log.$str_log);

	if(($ii = spiplistes_courriers_total_abonnes()) > 0) {
		// il en reste apres la meleuse ? Signale au CRON tache non terminee
		$nb_etiquettes = spiplistes_courriers_en_queue_compter('etat='.$sql_vide);
		spiplistes_log($prefix_log.'courriers prets au depart ('.$nb_etiquettes.'/'.$ii.')');
		$last_time = -$last_time;
	}
	
	return($last_time);
} // end spiplistes_meleuse()



/*
 * CP-20090426
 * Petite fonction pour remplacer les url_site
 * Ex., pour url_site = "foo.bar" :
 * _HREF_AUTEUR_URL_SITE_ = "href='http://foo.bar'"
 * _AUTEUR_URL_SITE_ = "foo.bar"
 */
function spiplistes_personnaliser_courrier_urls ($txt, $url) {

	// commencer par href (voir patron details_auteurs pour exemple)
	if(!empty($url)) {
		$txt = preg_replace("@(_HREF_AUTEUR_URL_SITE_)@"
							, " href='" . ((!preg_match(',^https?://.+$,', $url)) ? "http://" : "") . $url . "'"
							, $txt);
	}
	// et url_site seul
	$txt = preg_replace("@(_AUTEUR_URL_SITE_)@", $url, $txt);
	return($txt);
}

/**
 * CP-20080608 :: personnalisation du courrier
 * recherche/remplace les tags _AUTEUR_CLE_ en masse dans le corps du message.
 * (toutes les cles presentes dans la table *_auteur sont utilisables)
 * @return array
 */
function spiplistes_personnaliser_courrier ($page_html, $page_texte, $id_auteur, $format_abo) {

	$result_html = $result_texte = "";
	
	//if($auteur = sql_fetsel("*", 'spip_auteurs', "id_auteur=".sql_quote($id_auteur), '','', 1)) {
	if ($auteur = spiplistes_auteurs_auteur_select ('*', 'id_auteur='.sql_quote($id_auteur)))
	{
		$ii = 0;
		$pattern = array();
		$replace = array();
		krsort($auteur);
		foreach($auteur as $key => $val) {
			if($key == "url_site") continue;
			$pattern[$ii] = ",(_AUTEUR_" . strtoupper($key) .")_,";
			$replace[$ii] = $auteur[$key];
			$ii++;
		}
		$url = trim($auteur['url_site']);
		
		if($format_abo == 'html') {
			$result_html = preg_replace($pattern, $replace, $page_html);
			
			// traiter url_site a part (href et corrige' par l'assembleur ou un filtre en amont)
			$result_html = spiplistes_personnaliser_courrier_urls ($result_html, $url);
		}
		
		$result_texte = preg_replace($pattern, $replace, $page_texte);
		$result_texte = spiplistes_personnaliser_courrier_urls($result_texte, $url);
		
		spiplistes_debug_log(_SPIPLISTES_PREFIX_LOG.'personnalisation du courrier pour id_auteur #'.$id_auteur);
	} 
	return(array($result_html, $result_texte));
}


/**
 * Repasse un courrier en mode redac (en general, un test d'envoi)
 * @return 
 * @param $id_courrier int
 */
function spiplistes_courriers_statut_redac ($id_courrier) {
	spiplistes_courrier_modifier(
		$id_courrier
		, array(
			'email_test' => ''
			, 'total_abonnes' => 0
			, 'statut' => _SPIPLISTES_COURRIER_STATUT_REDAC
		)						
	);
	spiplistes_debug_log(_SPIPLISTES_PREFIX_LOG.'repasse document en statut redac');
	return(true);
}

/**
 * petite ligne pour trace dans le log
 * @return string
*/
function spiplistes_trace_compteur ($id, $sent, $html, $text, $none, $echec, $type='TOTAL')
{
	$str = $type.': id_courrier #'.$id
		// nombre total de courrier transmis
		.' SENT: '.$sent
		// dont aux formats
		.' (HTML: '.$html.', TEXT: '.$text
		// dont ceux sans format pour le destinataire
		.', NONE: '.$none
		// et ceux en echec
		.', ECHEC: '.$echec
		.')';
	return($str);
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
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
