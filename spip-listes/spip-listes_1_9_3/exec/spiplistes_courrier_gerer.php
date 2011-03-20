<?php

// exec/spiplistes_courrier_gerer.php

// _SPIPLISTES_EXEC_COURRIER_GERER
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
	Affiche un courrier. 
	Le formulaire permet :
	- l'envoi sur mail de test
	- l'envoi sur les destinataires d'une liste
	- le passage en mode edition
	- la duplication d'un courrier archive, le passe en mode redac	
*/

function exec_spiplistes_courrier_gerer () {

	include_spip('inc/barre');
	include_spip('inc/documents');
	include_spip('base/spiplistes_tables');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_courrier');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_destiner_envoi');
	include_spip('inc/spiplistes_naviguer_paniers');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_ecran
		;
	
	$eol = "\n";

	// initialise les variables postees par le formulaire
	foreach(array(
		'type'
		, 'id_courrier'
		, 'btn_courrier_valider', 'titre', 'message', 'message_texte' // (formulaire edition) _SPIPLISTES_EXEC_COURRIER_EDIT
		, 'new' // idem
		, 'btn_changer_destination', 'radio_destination', 'email_test', 'id_liste' // (formulaire local) destinataire
		, 'change_statut' // (formulaire spiplistes_boite_autocron) 'publie' pour annuler envoi par boite autocron
		, 'btn_dupliquer_courrier' // (formulaire local) dupliquer le courrier
		, 'supp_dest'
		, 'id_temp' // pour recuperer les documents joints
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_courrier', 'id_liste', 'btn_dupliquer_courrier') as $key) {
		$$key = intval($$key);
	}
	foreach(array('email_test','titre','message','message_texte') as $key) {
		$$key = trim($$key);
	}
	$texte = $message;

	$page_result = $message_erreur = $str_destinataire =
		$boite_confirme_envoi = '';

	$flag_admin = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;
	$flag_moderateur = count($listes_moderees = spiplistes_mod_listes_id_auteur($connect_id_auteur));
	$flag_createur = ($id_courrier && ($connect_id_auteur == spiplistes_courrier_id_auteur_get($id_courrier)));

	// l'edition du courrier est reservee...
	$flag_autorise = (
		// aux super-admins 
		$flag_admin
		// ou a un moderateur
		|| $flag_moderateur
		// ou au createur du courrier
		|| $flag_createur
	);

	if($flag_autorise) {
		// Modification de courrier
	
		if($btn_dupliquer_courrier > 0) {
			$id_courrier = $btn_dupliquer_courrier;
		}

		// effectue les modifications demandees si retour local ou retour editeur
		if($id_courrier > 0) {

			if($btn_dupliquer_courrier > 0)
			{
				if($row = sql_fetsel('titre,texte', 'spip_courriers', 'id_courrier='.sql_quote($id_courrier),'','',1))
				{
					$titre = typo($row['titre']);
					
					$texte = typo($row['texte']);
					//
					// @see: http://www.spip-contrib.net/SPIP-Listes#comment441566
					//$texte = typo($row['message_texte']);
					
					$str_log = "id_courrier #$id_courrier";
					$statut = _SPIPLISTES_COURRIER_STATUT_REDAC;
					$type = _SPIPLISTES_COURRIER_TYPE_NEWSLETTER;
					$id_courrier = sql_insert(
						'spip_courriers'
						, "(titre,texte,message_texte,date,statut,type,id_auteur)"
						, "(".sql_quote($titre).",".sql_quote($texte).",".sql_quote($message_texte)
							.",NOW(),".sql_quote($statut).",".sql_quote($type).",".sql_quote($connect_id_auteur).")"
					);
					spiplistes_log("$str_log DUPLICATED TO #$id_courrier BY ID_AUTEUR #$connect_id_auteur");
				}
				else {
					spiplistes_log("ERR: DUPLICATION FROM id_courrier #$id_courrier (missing ?)");
				}
			}
			
			if($btn_changer_destination) {
				if($radio_destination == 'email_test') {
					
					// demande d'envoi au mail de test (retour formulaire local)
					if(email_valide($email_test)) {
						if(!($id_auteur_test = spiplistes_idauteur_depuis_email($email_test))) {
							// verifie si l'adresse est dans la table des auteurs
							// si inconnue, refuse d'envoyer
							$message_erreur .= spiplistes_boite_alerte (_T('spiplistes:Erreur_Adresse_email_inconnue'), true);
						}
						else {
							
							$format_abo = spiplistes_format_abo_demande($id_auteur_test);

							/*
							 * meme le compte qui veut recevoir un test doit avoir
							 * un format de reception
							 */
							if(
								in_array($format_abo, spiplistes_formats_autorises())
								&& ($format_abo != 'non')
							) {
								// Ok. Enregistre l'adresse test
								spiplistes_courrier_modifier(
									$id_courrier
									, array(
										  'email_test' => $email_test
										, 'total_abonnes' => 1
										, 'id_liste' => ($id_liste = 0)
										, 'statut' => ($change_statut = _SPIPLISTES_COURRIER_STATUT_READY)
										)
								);
							}
							else {
								$message_erreur .= spiplistes_boite_alerte (_T('spiplistes:destinataire_sans_format_alert'), true);
							}
							
							$str_destinataire = _T('spiplistes:email_adresse') . " : $email_test";
						}
					}
					else {
						$message_erreur .= spiplistes_boite_alerte (_T('spiplistes:Erreur_Adresse_email_invalide'), true);
					}
				} // end if($radio_destination == 'email_test')
				
				else if($radio_destination == 'id_liste') {
					// demande d'envoi a une liste (retour formulaire local)
					if($id_liste > 0) {
						if(
							($nb_abos = spiplistes_listes_nb_abonnes_compter($id_liste)) > 0
						) {
							$str_destinataire = ""
								. _T('spiplistes:sur_liste') 
								. " : <a href='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste")."'>"
								. spiplistes_listes_liste_fetsel($id_liste, 'titre')
								. "</a>"
								. " " . spiplistes_nb_abonnes_liste_str_get($id_liste, $nb_abos)
								;
							spiplistes_courrier_modifier(
								$id_courrier
								, array(
									  'email_test' => ""
									, 'total_abonnes' => $nb_abos
									, 'id_liste' => $id_liste
									, 'statut' => ($change_statut = _SPIPLISTES_COURRIER_STATUT_READY)
								)
							);
						}
						else {
							$message_erreur .= spiplistes_boite_alerte (_T('spiplistes:Erreur_liste_vide'), true);
						}
					}
				} // end if($radio_destination
			} // if($btn_changer_destination
	
			else if($btn_courrier_valider) {
				// retour editeur local
				if(!empty($titre)) {
					$sql_set = array(
							  'titre' => $titre
							, 'texte' => $texte
							, 'message_texte' => $message_texte
						);
					spiplistes_courrier_modifier($id_courrier, $sql_set);
					spiplistes_courrier_attacher_documents($id_courrier, $id_temp);
				}
				else {
					$message_erreur .= spiplistes_boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
				}
			}
			// FIN DES MODIFICATIONS
		}
					
		// Ok. recharge les donnees pour completer le formulaire
		$sql_select_array = array('titre', 'texte', 'email_test', 'statut');
		if($row = spiplistes_courriers_premier($id_courrier, $sql_select_array)) {
			foreach($sql_select_array as $key) {
				$$key = $row[$key];
			}
		} // end if($id_courrier > 0)
	}  // end if($flag_autorise)


	//////////////////////////////////////////////////////
	// Nouveau courrier
	////
	if(($connect_statut == "0minirezo") && ($new == 'oui')) {
		// retour editeur. Creation du courrier
		if(!empty($titre)) {
			$statut = _SPIPLISTES_COURRIER_STATUT_REDAC;
			$type = _SPIPLISTES_COURRIER_TYPE_NEWSLETTER;
			$id_courrier = sql_insert(
				'spip_courriers'
				, "(titre,texte,message_texte,date,statut,type,id_auteur)"
				, "(".sql_quote($titre).",".sql_quote($texte).",".sql_quote($message_texte)
					.",NOW(),".sql_quote($statut).",".sql_quote($type).",".sql_quote($connect_id_auteur).")"
			);
			spiplistes_courrier_attacher_documents($id_courrier, $id_temp);
		}
		else {
			$message_erreur .= spiplistes_boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
		}
	}

	//////////////////////////////////////////////////////
	// recharge le courrier pour edition
	if($id_courrier > 0) {
		
		$sql_select_tmp = "email_test,date,titre,texte,message_texte,type,statut,date_debut_envoi,date_fin_envoi";
		$sql_select_int = "id_liste,id_auteur,total_abonnes,nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html";
		$sql_select_str = "titre";
		$sql_select = $sql_select_int.",".$sql_select_str.",".$sql_select_tmp;
		
		if($row = sql_fetsel($sql_select, "spip_courriers", "id_courrier=".sql_quote($id_courrier), '', '', 1)) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			foreach(explode(",",$sql_select_int) as $key) {
				$$key = intval($$key);
			}
			foreach(explode(",",$sql_select_str) as $key) {
				$$key = typo($$key);
			}

			if($change_statut == _SPIPLISTES_COURRIER_STATUT_READY) {
				//$titre = propre($titre); // pas de propre ici, ca fait un <p> </p>
				// Le statut n'est modifie ici, mais 
				// par courrier_casier en retour de ce formulaire
				
				// $texte = spiplistes_courrier_propre($texte);
				$texte = spiplistes_texte_propre ($texte);
				
				spiplistes_courrier_modifier(
					$id_courrier
					, array(
						'titre' => $titre
						, 'texte' => $texte
					)
				);
				spiplistes_log("ID_COURRIER #$id_courrier titre,texte MODIFIED BY ID_AUTEUR #$connect_id_auteur");
				$statut = $change_statut;
			}
			else if($change_statut == _SPIPLISTES_COURRIER_STATUT_STOPE){
				spiplistes_courrier_supprimer_queue_envois('id_courrier', $id_courrier);
				spiplistes_log("ID_COURRIER #$id_courrier CANCELLED BY ID_AUTEUR #$connect_id_auteur");
			}
			
			// prepare le texte texte seul
			if(!in_array($statut, array(
					  _SPIPLISTES_COURRIER_STATUT_REDAC
					, _SPIPLISTES_COURRIER_STATUT_READY
					, _SPIPLISTES_COURRIER_STATUT_PUBLIE
					, _SPIPLISTES_COURRIER_STATUT_STOPE
					))
			) {
				
				//$texte = spiplistes_courrier_propre($texte);
				$texte = spiplistes_texte_propre ($texte);
			}
			if(!empty($message_texte)){
				$alt_message_texte = _T('spiplistes:calcul_patron');
			}
			else{
				$alt_message_texte = _T('spiplistes:calcul_html');
				$message_texte = spiplistes_courrier_version_texte($texte);
			}
			// construit la boite de selection destinataire
			$boite_selection_destinataire = 
				(($statut==_SPIPLISTES_COURRIER_STATUT_REDAC) || ($statut==_SPIPLISTES_COURRIER_STATUT_READY))
				? spiplistes_destiner_envoi($id_courrier, $id_liste
						, $flag_admin
						, $flag_moderateur
						, $listes_moderees
						, $statut, $type, 'btn_changer_destination', $email_test)
				: ""
				;
		}
	} // end if()

	//////////////////////////////////////////////////////
	// preparation des boutons si droits
	$gros_bouton_modifier = 
		$gros_bouton_dupliquer = 
		$gros_bouton_supprimer = 
		$gros_bouton_arreter_envoi = '';
	

	if($flag_autorise) {
		
		if(($statut == _SPIPLISTES_COURRIER_STATUT_REDAC) || ($statut == _SPIPLISTES_COURRIER_STATUT_READY)) {
		// Le courrier peut-etre modifie si en preparation 
			$gros_bouton_modifier = "<!-- bouton modifier -->\n" .
				icone (
					_T('spiplistes:Modifier_ce_courrier') // legende bouton
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'id_courrier='.$id_courrier) // lien
					, spiplistes_items_get_item('icon', $statut) // image du fond
					, "edit.gif" // image de la fonction. Ici, le crayon
					, '' // alignement
					, false // pas echo, demande retour
					) 
					. $eol
				;
			
		}
		
		// Le courrier peut-etre supprime si obsolete
		if(in_array($statut, array(_SPIPLISTES_COURRIER_STATUT_REDAC
								   , _SPIPLISTES_COURRIER_STATUT_PUBLIE
								   , _SPIPLISTES_COURRIER_STATUT_AUTO
								   , _SPIPLISTES_COURRIER_STATUT_VIDE
								   , _SPIPLISTES_COURRIER_STATUT_IGNORE
								   , _SPIPLISTES_COURRIER_STATUT_STOPE
								   , _SPIPLISTES_COURRIER_STATUT_ERREUR))
					) {
			$gros_bouton_supprimer = 
				'<div style="margin-top:1ex">'
				. icone (
					_T('spiplistes:Supprimer_ce_courrier')
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE, 'btn_supprimer_courrier='.$id_courrier)
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'poubelle_msg.gif'
					, ''
					, 'right'
					, false
					)
				. '</div>'.$eol
				;
		}
		// Un courrier publie ou stoppe peut-etre duplique pour edition
		// on revient sur cette page avec le contenu recupere
		if(in_array($statut, array(_SPIPLISTES_COURRIER_STATUT_PUBLIE
								   , _SPIPLISTES_COURRIER_STATUT_AUTO
								   , _SPIPLISTES_COURRIER_STATUT_STOPE))
					) {
			$gros_bouton_dupliquer = 
				"<div style='margin-top:1ex;'>"
				. icone (
					_T('spiplistes:dupliquer_ce_courrier')
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER, "btn_dupliquer_courrier=$id_courrier")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'stock_mail.gif'
					, _DIR_IMG_PACK."creer.gif"
					, "right"
					, false
					)
				. "</div>\n"
				;
		}
		
		if($statut == _SPIPLISTES_COURRIER_STATUT_ENCOURS) {
		// L'envoi d'un courrier en cours peut etre stoppe
			$gros_bouton_arreter_envoi = 
				icone (
					_T('spiplistes:Arreter_envoi')
					// si arreter envoi, passe la main a exec/spiplistes_courriers_casier
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE, "btn_arreter_envoi=$id_courrier")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_redac-24.png"
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stop-top-right-24.png"
					, "right"
					, false
					)
				. fin_cadre_relief(true)
				;
		}
		if($statut == _SPIPLISTES_COURRIER_STATUT_READY) {
			if(!$id_liste && !$id_auteur_test) {
				// normalement, la validation est locale, mais si l'utilisateur
				// part sur un casier, le retour ici est incomplet...
				// cas particulier d'un appel d'un courrier ready a partir des casiers
				// il faut recreer $id_auteur_test si id_liste == 0
				if(!($id_auteur_test = spiplistes_idauteur_depuis_email($email_test))) {
					spiplistes_log("ERR: id_auteur_test #$id_auteur_test (id_auteur missing ?)");
				}
			}
			if(($id_liste > 0) || ($id_auteur_test > 0)) {
				$boite_confirme_envoi = 
					  debut_cadre_couleur('', true)
					// formulaire de confirmation envoi
					// renvoie sur la page des casiers
					. '<form action="'
						. generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE,'id_courrier='.$id_courrier)
						. '" method="post">'.$eol
					. '<p style="text-align:center;font-weight:bold;" class="verdana2">'
						. _T('spiplistes:confirme_envoi')
						. '</p>'.$eol
					. '<input type="hidden" name="id_liste" value="'.$id_liste.'" />'.$eol
					. '<input type="hidden" name="id_courrier" value="'.$id_courrier.'" />'.$eol
					. '<input type="hidden" name="id_auteur_test" value="'.$id_auteur_test.'" />'.$eol
					. '<div style="text-align:left;">'.$eol
					. '<input type="submit" name="btn_annuler_envoi" value="'
						. _T('spiplistes:annuler_envoi').'" class="fondo" style="float:left" />'.$eol
					. '<div style="text-align:right;width:100%">'.$eol
					. '<input type="submit" name="btn_confirmer_envoi" value="'
						. _T('spiplistes:Envoyer_ce_courrier').'" class="fondo" />'.$eol
					. '</div>'.$eol
					. '</div>'.$eol
					. '</form>'
					. fin_cadre_couleur(true)
					;
			}
		}
	}

	/////////////////////
	// prepare le message statut du courrier
	if($id_courrier > 0) {
		$le_type = _T('spiplistes:message_type');
		
		if($statut != _SPIPLISTES_COURRIER_STATUT_REDAC) {
			if(!empty($email_test)) {
				$str_destinataire = _T('spiplistes:email_adresse') . " : <span style='font-weight:bold;color:gray;'>$email_test</span>";
			}
			else {
				if($row = sql_fetsel('titre', 'spip_listes', "id_liste=".sql_quote($id_liste), '', '', 1)) {
					$str_destinataire = ""
						. _T('spiplistes:Liste_de_destination') 
						. " : <a href='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste")."'>"
							. typo($row['titre']) . "</a>"
						. " " . spiplistes_nb_abonnes_liste_str_get($id_liste)
						;
				}
				else {
					$str_destinataire = _T('spiplistes:Courriers_sans_liste');
				}
			}
		} //
		
		$str_statut_courrier = "";
		
		switch($statut) {
			case _SPIPLISTES_COURRIER_STATUT_REDAC:
				$str_statut_courrier = _T('spiplistes:message_en_cours')."<br />"
				.	(
					($flag_autorise)
					? _T('spiplistes:modif_envoi')
					: ""
					)
				;
				break;
			case _SPIPLISTES_COURRIER_STATUT_READY:
				$str_statut_courrier = ""
					. _T('spiplistes:message_presque_envoye') . "<br />"
					. $str_destinataire . "<br />\n"
					;
				break;
			case _SPIPLISTES_COURRIER_STATUT_ENCOURS:
				$str_statut_courrier = ""
					. _T('spiplistes:message_en_cours')."<br />$str_destinataire<br /><br />"
					//. "<a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a>"
					;
				break;
			case _SPIPLISTES_COURRIER_STATUT_PUBLIE:
			case _SPIPLISTES_COURRIER_STATUT_AUTO:
				$str_statut_courrier = ""
					. "<span>"
					. "<strong>"._T('spiplistes:message_arch')."</strong></span>"
					. "<ul>"
					. " <li>$str_destinataire</li>"
					. " <li>"._T('spiplistes:envoi_date').affdate_heure($date)."</li>"
					. " <ul>"
					. "  <li>"._T('spiplistes:envoi_debut').affdate_heure($date_debut_envoi)."</li>"
					. "  <li>"._T('spiplistes:envoi_fin').affdate_heure($date_fin_envoi)."</li>"
					. " </ul>"
					. " <li>"._T('spiplistes:nbre_abonnes').$total_abonnes."</li>"
					. " <ul>"
					. "  <li>"._T('spiplistes:format_html__n', array('n' => $nb_emails_html))."</li>"
					. "  <li>"._T('spiplistes:format_texte__n', array('n' => $nb_emails_texte))."</li>"
					. "  <li>"._T('spiplistes:desabonnes')." : ".$nb_emails_non_envoyes."</li>"
					. " </ul>"
					. " <li>"._T('spiplistes:erreur_envoi').$nb_emails_echec."</li>"
					. "</ul>"
					;
		} // end switch()
		if(!empty($str_statut_courrier)) {
			$str_statut_courrier = "<span class='verdana2'>".$str_statut_courrier."</span>";
		}
	} // end if()
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:edition_du_courrier');
	// Permet entre autres d'ajouter les classes a la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "courrier_gerer";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . trim($titre_page), $rubrique, $sous_rubrique));

	// la gestion des listes de courriers est reservee aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ""
		. "<br /><br /><br />\n"
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. spiplistes_boite_info_id(_T('spiplistes:Courrier_numero_'), $id_courrier, true)
		. spiplistes_naviguer_paniers_courriers(_T('spiplistes:aller_au_panier_'), true)
		//. $boite_documents
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
			//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron()
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;
		
	if($id_courrier > 0) {
	/////////////////////
	// construction du ventre
		$page_result .= "\n<!-- construction du ventre -->\n"
			. $message_erreur
			. debut_cadre_relief(spiplistes_items_get_item('icon', $statut), true)
			. "<table width='100%'  border='0' cellspacing='0' cellpadding='0'>"
			. "<tr>"
			. "<td>".spiplistes_gros_titre($titre, spiplistes_items_get_item('puce', $statut), true)."</td>"
			. "<td rowspan='2' style='vertical-align:top;width:90px;'>"
				// si besoin, l'un de ces trois boutons apparait
				. $gros_bouton_modifier
				. $gros_bouton_arreter_envoi
				. $gros_bouton_dupliquer
				."</td>"
			. "</tr>\n"
			. "<tr> "
			. "<td>"
			. "<p class='verdana2' style='font-size:120%;color:red;font-weight:bold;'>$le_type</p>\n"
			. "<p class='verdana2'>$str_statut_courrier</p>\n"
			. "</td>"
			. "</tr>\n"
			. "</table>"
			. $boite_confirme_envoi
			. $boite_selection_destinataire
			. "<br />\n"
			;

		function spiplistes_generer_oeil ($params) {
			return(
				"&nbsp;<a href='"
				. generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE, $params)
				. "' title='"._T('spiplistes:Apercu_plein_ecran')."' target='_blank'>\n"
				. spiplistes_icone_oeil() . "</a>"
			);
		}

		// previsu
		$params = "id_courrier=$id_courrier&id_liste=$id_liste";
		$oeil_html = spiplistes_generer_oeil($params. "&lire_base=oui&plein_ecran=oui");
		$oeil_texte = spiplistes_generer_oeil($params . "&lire_base=oui&plein_ecran=oui&format=texte");
			
		$page_result .= ""
			. debut_cadre_couleur('', true)
			. "<form id='choppe_patron-1' action='$form_action' method='post' name='choppe_patron-1'>\n"
			. "<div id='previsu-html' class='switch-previsu'>\n"
			. _T('spiplistes:version_html') . $oeil_html
				. " / " . "<a href='javascript:jQuery(this).switch_previsu()'>" 
				. _T('spiplistes:version_texte') . $oeil_texte
			. "<div>\n"
			. "<iframe class='previsu-edit'"
				. " src='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE, $params . "&lire_base=oui")
				. "' width='100%' height='500'></iframe>\n"
			. "</div>\n"
			. "</div>\n" // fin id='previsu-html
			. "<div id='previsu-texte' class='switch-previsu' style='display:none;'>\n"
			. "<a href='javascript:jQuery(this).switch_previsu()'>" . _T('spiplistes:version_html') . "</a>\n"
				. $oeil_html
				. " / " 
				. _T('spiplistes:version_texte') . "</a> $oeil_texte\n"
			. "<div>\n"
			//. "<pre>"
			. "<iframe class='previsu-edit'"
				. " src='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE, $params . "&format=texte&lire_base=oui")
				."' width='100%' height='500'></iframe>\n"
			//. "</pre>"
			. "</div>\n"
			. "</div>\n" // fin id='previsu-texte
			. "</form>\n"
			. fin_cadre_couleur(true)
			
			//
			// fin de la boite
			. fin_cadre_relief(true)
			//
			. $gros_bouton_supprimer
			;
	} // end if
	else {
		$page_result .= 
			(empty($message_erreur))
			? spiplistes_boite_alerte (_T('spiplistes:Erreur_courrier_introuvable'), true)
			: $message_erreur
			;
	}

	echo($page_result);
	
	// GERER COURRIER: FIN DE PAGE
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();

} // end function exec_spiplistes_courrier_gerer ()

function spiplistes_icone_oeil () {
	return("<img src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."oeil-16.png' alt='' width='16' height='16' border='0' />");
}

/* retourne l'id auteur depuis l'email */
function spiplistes_idauteur_depuis_email ($email) {
	if($email = email_valide($email)) {
		return(sql_getfetsel("id_auteur", "spip_auteurs"
			, "email=".sql_quote($email)." AND statut<>".sql_quote("5poubelle"))
		);
	}
	return(false);
}


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
?>