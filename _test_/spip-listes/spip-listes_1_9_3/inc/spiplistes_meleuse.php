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

if (!defined("_ECRIRE_INC_VERSION")) return;

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
//return(0); //pour debuguer uniquement
spiplistes_log("spiplistes_meleuse()", _SPIPLISTES_LOG_DEBUG);
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
		, 'opt_ajout_tampon_editeur', 'tampon_patron'
		, 'opt_personnaliser_courrier'
		, 'opt_log_voir_destinataire'
		) as $key) {
		$$key = spiplistes_pref_lire($key);
	}

	$nb_etiquettes = spiplistes_courriers_en_queue_compter("etat=".sql_quote(''));
	
	$log_voir_destinataire = ($opt_log_voir_destinataire == "oui");
	
	$prefix_log = "MEL: ";
	$str_log = "MEL:";
	
	// si meleuse suspendue, signale en log 
	if($opt_suspendre_meleuse == 'oui') {
		spiplistes_log($prefix_log."SUSPEND MODE !!!");
		return(0 - $last_time);
	}

	if($nb_etiquettes) {

spiplistes_log($prefix_log.$nb_etiquettes." job(s), distribution...", _SPIPLISTES_LOG_DEBUG);
		
		// signale en log si mode simulation
		if($opt_simuler_envoi == 'oui') {
			spiplistes_log($prefix_log."SIMULATION MODE !!!");
		}

		$nomsite = $GLOBALS['meta']['nom_site'];
		$urlsite = $GLOBALS['meta']['adresse_site'];

		// prepare le tampon editeur
		if(
			($opt_ajout_tampon_editeur == 'oui')
			&& !empty($tampon_patron)
		) {
			$tampon_html = spiplistes_tampon_html_get($tampon_patron);
			$tampon_texte = spiplistes_courrier_tampon_texte($tampon_patron, $tampon_html);
		}
		else {
			$tampon_html = $tampon_texte = "";
		}
		
		// prendre la premiere etiquette sur le tas et traiter son courrier
		$sql_courrier_select = array(
			'titre', 'texte', 'message_texte', 'type'
			, 'id_courrier', 'id_liste', 'email_test', 'total_abonnes', 'date_debut_envoi'
			);
		if($id_courrier = 
			intval(spiplistes_courriers_en_queue_premier('id_courrier', "etat=".sql_quote('')))
		) {
			$sql_courrier_a_traiter = spiplistes_courriers_casier_premier(
				  $sql_courrier_select
				, "id_courrier=".sql_quote($id_courrier)
			);
spiplistes_log($prefix_log."etiquette en cours pour id_courrier #$id_courrier", _SPIPLISTES_LOG_DEBUG);
		} else {
			// un vieux bug dans une ancienne version, eradique depuis (j'espere ;-)
spiplistes_log($prefix_log."premiere etiquette en erreur. id_courier = 0. Supprimer cette etiquette manuellement !");
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
			// le total de chaque sera ajoute en fin de session
			$nb_emails_envoyes =
				$nb_emails_echec = 
				$nb_emails_non_envoyes = 
				$nb_emails['texte'] = 
				$nb_emails['html'] = 0
				;
			
			$pied_page_html = "" ;
			
			$str_log .= " id_courrier #$id_courrier"; 
			
			//////////////////////////
			// Determiner email de l emetteur
			if($is_a_test = email_valide($email_test)) {
				// courrier a destination adresse email de test
				$str_log .= " TO: $email_test (TEST)";
			} 
			else if($id_liste > 0) {
				// courrier a destination des abonnes d'une liste
				$total_abonnes = spiplistes_listes_nb_abonnes_compter($id_liste);
				$str_log .= " TO id_liste #$id_liste ($total_abonnes users)"; 
	
				$pied_page_html = spiplistes_pied_de_page_liste($id_liste);

				$lang = spiplistes_listes_langue($id_liste);

				if($lang != '') {
					$GLOBALS['spip_lang'] = $lang;
				}
				
				if(!$email_envoi = spiplistes_listes_email_emetteur($id_liste)) {
					$str_log .= " [ERROR] ID_LISTE #id_liste or from email MISSING"; 
					spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_COURRIER_STATUT_ERREUR);
					// quitte while() principal
					break;
				}
			}
			else {
				// erreur dans un script d'appel ? Ou url ? Ou base erreur ?
				$str_log .= " [ERROR] MISSING PARAMS (id_liste AND email_test)";
				spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_COURRIER_STATUT_ERREUR);
				// quitte while() principal
				break;
			}
			
			//////////////////////////////
			// email emetteur
			
			$from = $email_envoi;
			if($is_from_valide = email_valide($from)) {
				$fromname = extraire_multi($GLOBALS['meta']['nom_site']);
				if ($GLOBALS['meta']['spiplistes_charset_envoi']!=$GLOBALS['meta']['charset']){
					include_spip('inc/charsets');
					$fromname = unicode2charset(charset2unicode($fromname),$GLOBALS['meta']['spiplistes_charset_envoi']);
				}
				$from = $fromname." <$from>";
			}
spiplistes_log("email_envoi : " . $email_envoi, _SPIPLISTES_LOG_DEBUG);	
spiplistes_log("From : " . $from, _SPIPLISTES_LOG_DEBUG);	
		
			////////////////////////////////////
			// Prepare la version texte
			$objet_texte = spiplistes_courrier_version_texte($objet_html);
			$page_texte = ($message_texte !='') ? $message_texte : spiplistes_courrier_version_texte($page_html);
			$pied_page_texte = spiplistes_courrier_version_texte($pied_page_html);
			
			////////////////////////////////////
			// Ajoute lien tete de courrier
			if(
				($opt_lien_en_tete_courrier == 'oui') 
				&& !empty($lien_patron)
			) {
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
			
			// transcrire le contenu
			if($GLOBALS['meta']['spiplistes_charset_envoi'] != $GLOBALS['meta']['charset']){
				include_spip('inc/charsets');
				foreach(array('objet_html', 'objet_texte', 'page_html', 'page_texte', 'pied_page_html', 'pied_page_texte'
					, 'pied_rappel_html', 'pied_rappel_texte', 'tampon_html', 'tampon_texte') as $key) {
					if(!empty($$key)) {
						$$key = spiplistes_translate_2_charset(
							$$key
							, $GLOBALS['meta']['spiplistes_charset_envoi']
							, (strpos($key, 'texte') === false)
							);
					}
				}
			}
			
			// corrige les liens relatifs (celui de texte a deja ete corrige par la trieuse (cron)
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
				
				if($is_a_test) {
					$sql_adresses_dest = sql_select('id_auteur,nom,email', "spip_auteurs"
						, "email=".sql_quote($email_test)." LIMIT 1");
				}
				else {
					// Pour memo: les etiquettes sont creees par la trieuse
					// ou directement en backoffice 
					// - pour les envois de test
					// - pour les envoyer maintenant des courriers
					
					// Traitement d'une liasse d'etiquettes
					// un id pour ce processus (le tampon est unique par liasse)
					$id_process = intval(substr(creer_uniqid(),0,5));
spiplistes_log($prefix_log."PROCESS #$id_process", _SPIPLISTES_LOG_DEBUG);
			
					// un coup de tampon sur les etiquettes 
					// des courriers qui vont partir
					spiplistes_courriers_en_queue_modifier(
						array(
							  'etat' => sql_quote($id_process))
							, "etat=".sql_quote('')." AND id_courrier=".sql_quote($id_courrier)." LIMIT $limit"
					);
					
					// prendre la liasse des etiquettes tamponnees
					$sql_adresses_dest = sql_select(
						  array('a.nom', 'a.id_auteur', 'a.email')
						, array('spip_auteurs AS a', 'spip_auteurs_courriers AS b')
						, array(
							"etat=".sql_quote($id_process)
							, "a.id_auteur=b.id_auteur"
							, "b.id_courrier=".sql_quote($id_courrier)
							)
						, 'a.email'
					);
				}
					
				$nb_destinataires = sql_count($sql_adresses_dest);
spiplistes_log($prefix_log."nb etiquettes a traiter: $nb_destinataires", _SPIPLISTES_LOG_DEBUG);
				if($nb_destinataires > 0) {

spiplistes_log($prefix_log."total_abos: $total_abonnes, en_cour: $nb_destinataires, limit: $limit"
	, _SPIPLISTES_LOG_DEBUG);


					// replacer les compteurs
					if($row = sql_fetch(sql_select(
						"nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html"
						, "spip_courriers"
						, "id_courrier=".sql_quote($id_courrier)
						, '', '', 1
						))
					) {
						$nb_emails_envoyes = intval($row['nb_emails_envoyes']);
						$nb_emails_echec = intval($row['nb_emails_echec']);
						$nb_emails_non_envoyes = intval($row['nb_emails_non_envoyes']);
						$nb_emails['texte'] = intval($row['nb_emails_texte']);
						$nb_emails['html'] = intval($row['nb_emails_html']);
					}
					
					//envoyer le lot d'emails selectionne' (la liasse)
					while($adresse = sql_fetch($sql_adresses_dest)) {

						if($log_voir_destinataire) {
							$str_temp = "";
						}

						$id_auteur = intval($adresse['id_auteur']);
						$nom_auteur = $adresse['nom'];
						$email = $adresse['email'];

						// Marquer le debut de l'envoi
						if(!intval($date_debut_envoi)) {
							spiplistes_courrier_modifier ($id_courrier, array('date_debut_envoi' => "NOW()"), false);
						}
				
						$format_abo = spiplistes_format_abo_demande($id_auteur);

						$total++;
						if($log_voir_destinataire) {
							$str_temp .= $nom_auteur."(".$format_abo.") - $email";
						}
						unset ($cookie);
		
						if(($format_abo=='html') || ($format_abo=='texte')) {
							$cookie = creer_uniqid();
							spiplistes_auteurs_cookie_oubli_updateq($cookie, $email);
		
							if($is_from_valide) {
								$_url = generer_url_public('abonnement','d='.$cookie);
								
								if($opt_personnaliser_courrier == 'oui') {
									$page_html = spiplistes_personnaliser_courrier($page_html, $id_auteur);
									$page_texte = spiplistes_personnaliser_courrier($page_texte, $id_auteur);
								}
								
								// le &amp; semble poser probleme sur certains MUA. A suivre...
								$_url = preg_replace(',(&amp;),','&', $_url);
								switch($format_abo) {
									case 'html':
										$email_a_envoyer[$format_abo]->Body =
											"<html>\n\n<body>\n\n"
											. $page_html
											. $pied_page_html
											. "<a href=\"$_url\">".$pied_rappel_html."</a>\n\n</body></html>"
											. $tampon_html
											;
										/* la version alternative texte */
										$email_a_envoyer[$format_abo]->AltBody = 
											$page_texte ."\n\n"
											. $pied_page_texte
											. str_replace("&amp;", "&", $pied_rappel_texte). " " . $_url."\n\n"
											. $tampon_texte
											;
										break;
									case 'texte':
										$email_a_envoyer[$format_abo]->Body =
											$page_texte ."\n\n"
											. $pied_page_texte
											. str_replace("&amp;", "&", $pied_rappel_texte). " " . $_url."\n\n"
											. $tampon_texte
											;
										break;
								}

								$email_a_envoyer[$format_abo]->SetAddress($email, $nom_auteur);
								// envoie le mail																
								if(($opt_simuler_envoi == "oui") ? true : $email_a_envoyer[$format_abo]->send()) {
									$nb_emails_envoyes++;
									$nb_emails[$format_abo]++;
									if($log_voir_destinataire) {
										$str_temp .= "  [OK]";
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
								$str_temp .= " "._T('spiplistes:msg_abonne_sans_format');
							}
							// prevenir qu'il manque le format
							spiplistes_log($prefix_log." destination format MISSING FOR ID_AUTEUR $id_auteur");
						} /* fin abo*/
						
						if($log_voir_destinataire) {
							spiplistes_log($prefix_log.$str_temp);
						}
						
					} // fin while
					
					// supprime la liasse de la queue d'envois
spiplistes_log($prefix_log."envoi OK. Supprimer queue $id_process", _SPIPLISTES_LOG_DEBUG);
					spiplistes_courriers_en_queue_supprimer("etat=".sql_quote($id_process));
					
					// si c'est un test on repasse le courrier en redac
					if($is_a_test) {
						spiplistes_courrier_modifier(
							$id_courrier
							, array(
								'email_test' => ''
								, 'total_abonnes' => 0
								, 'statut' => _SPIPLISTES_COURRIER_STATUT_REDAC
							)						
						);
						spiplistes_log($prefix_log."repasse document en statut redac", _SPIPLISTES_LOG_DEBUG);
					}
					$email_a_envoyer['texte']->SmtpClose();
					$email_a_envoyer['html']->SmtpClose();
				} // end if 
			}
			else {
				//aucun destinataire connu pour ce message
//spiplistes_log($prefix_log._T('spiplistes:erreur_sans_destinataire')."---"._T('spiplistes:envoi_annule'), _SPIPLISTES_LOG_DEBUG);
				spiplistes_courrier_statut_modifier($id_courrier, _SPIPLISTES_COURRIER_STATUT_IGNORE);
				spiplistes_courrier_supprimer_queue_envois('id_courrier', $id_courrier);
				$str_log .= " END #$id_courrier";
				// 
				break;
			}

			if(!$is_a_test) {
				// faire le bilan apres l'envoi d'un lot
				$sql_set_array = array(
					  'nb_emails_envoyes' => $nb_emails_envoyes
					, 'nb_emails_texte' => $nb_emails['texte']
					, 'nb_emails_html' => $nb_emails['html']
				);
				if($nb_emails_echec) {
					$sql_set_array['nb_emails_echec'] = $nb_emails_echec;
				}
				if($nb_emails_non_envoyes) {
					$sql_set_array['nb_emails_non_envoyes'] = $nb_emails_non_envoyes;
				}

				$str_log .= " (HTML: ".$nb_emails['html'].") (TEXT: ".$nb_emails['texte'].") (NONE: $nb_emails_non_envoyes)";

				///////////////////////
				// si courrier pas termine, redemande la main au CRON, sinon nettoyage.
				if($t = spiplistes_courriers_en_queue_compter("id_courrier=".sql_quote($id_courrier))) {
					$str_log .= " LEFT $t jobs"; 
				}
				else {
					$statut = ($type == _SPIPLISTES_COURRIER_TYPE_NEWSLETTER) ? _SPIPLISTES_COURRIER_STATUT_PUBLIE : _SPIPLISTES_COURRIER_STATUT_AUTO;
//spiplistes_log($prefix_log."nouveau statut $statut", _SPIPLISTES_LOG_DEBUG);
					$sql_set_array['statut'] = sql_quote($statut);
					$sql_set_array['date_fin_envoi'] = "NOW()";
					$str_log .= " END #$id_courrier";
				}
				spiplistes_courrier_modifier($id_courrier, $sql_set_array, false);
			}
		} // end while()
	} // end if($nb_etiquettes)
	else {
		$str_log .= " no job"; 
	}

	spiplistes_log($str_log);

	if(($ii = spiplistes_courriers_total_abonnes()) > 0) {
		// il en reste apres la meleuse ? Signale au CRON tache non terminee
		$nb_etiquettes = spiplistes_courriers_en_queue_compter("etat=".sql_quote(''));
		spiplistes_log($prefix_log."courriers prets au depart (".$nb_etiquettes."/".$ii.")");
		$last_time = -$last_time;
	}
	
	return($last_time);
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

//CP-20080608 :: personnalisation du courrier
// recherche/remplace les tags _AUTEUR_CLE_ en masse dans le corps du message.
// (toutes les cles presentes dans la table *_auteur sont utilisables)
function spiplistes_personnaliser_courrier ($corps, $id_auteur) {

	if($auteur = sql_fetsel("*", 'spip_auteurs', "id_auteur=".sql_quote($id_auteur), '','', 1)) {
		$ii = 0;
		$pattern = array();
		$replace = array();
		krsort($auteur);
		foreach($auteur as $key => $val) {
			$pattern[$ii] = ",(_AUTEUR_" . strtoupper($key) .")_,";
			$replace[$ii] = $auteur[$key];
			$ii++;
		}
		$corps = preg_replace($pattern, $replace, $corps);
		spiplistes_log($prefix_log."personnalisation du courrier pour $id_auteur", _SPIPLISTES_LOG_DEBUG);
	} 
	return($corps);
}

/*
 * complete caracteres manquants dans HTML -> ISO
 * @return la chaine transcrite
 * @param $texte le texte a transcrire
 * @param $charset le charset souhaite'. Normalement 'iso-8859-1' (voir page de config)
 * @param $is_html flag. Pour ne pas transcrire completement la version html
 * @see http://fr.wikipedia.org/wiki/ISO_8859-1
 * @see http://www.w3.org/TR/html401/sgml/entities.html
 */
function spiplistes_translate_2_charset ($texte, $charset='AUTO', $is_html = false) {
	
	$texte = charset2unicode($texte);
	$texte = unicode2charset($texte, $charset);
	if($charset != "utf-8") {
		$remplacements = array(
			"&#8217;" => "'"	// quote
			, "&#8220;" => '"' // guillemets
			, "&#8221;" => '"' // guillemets
			)
			;
		if(!$is_html) {
			$remplacements = array_merge(
				$remplacements
				, array(
							// Latin Extended
					  '&#338;' => "OE"  // OElig
					, '&#339;' => "oe"  // oelig
					, '&#352;' => "S"  // Scaron
					, '&#353;' => "s"  // scaron
					, '&#376;' => "Y"  // Yuml
						// General Punctuation
					, '&#8194;' => " " // ensp
					, '&#8195;' => " " // emsp
					, '&#8201;' => " " // thinsp
					, '&#8204;' => " " // zwnj
					, '&#8205;' => " " // zwj
					, '&#8206;' => " " // lrm
					, '&#8207;' => " " // rlm
					, '&#8211;' => "-" // ndash
					, '&#8212;' => "--" // mdash
					, '&#8216;' => "'" // lsquo
					, '&#8217;' => "'" // rsquo
					, '&#8218;' => "'" // sbquo
					, '&#8220;' => '"' // ldquo
					, '&#8221;' => '"' // rdquo
					, '&#8222;' => '"' // bdquo
					, '&#8224;' => "+" // dagger
					, '&#8225;' => "++" // Dagger
					, '&#8240;' => "0/00" // permil
					, '&#8249;' => "." // lsaquo
					, '&#8250;' => "." // rsaquo
						// sans oublier
					, '&#8364;' => "euros"  // euro
				)
			);
		}
		$texte = strtr($texte, $remplacements);
	}
	return($texte);
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
?>