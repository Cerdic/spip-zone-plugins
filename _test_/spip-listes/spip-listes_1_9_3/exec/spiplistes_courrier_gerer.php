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
	Affiche un courrier. 
	Le formulaire permet :
	- l'envoi sur mail de test
	- l'attachement d'une liste
	Dans les deux cas, le statut du courrier passe a  _SPIPLISTES_STATUT_READY 
	(la meleuse prend en charge les courriers en statut _SPIPLISTES_STATUT_READY)
	
*/
function exec_spiplistes_courrier_gerer () {

	include_spip('inc/barre');
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

	// initialise les variables postees par le formulaire
	foreach(array(
		'type'
		, 'id_courrier'
		, 'btn_courrier_valider', 'titre', 'texte' // (formulaire edition) _SPIPLISTES_EXEC_COURRIER_EDIT
		, 'new' // idem
		, 'btn_changer_destination', 'radio_destination', 'email_test', 'id_liste' // (formulaire local) destinataire
		, 'change_statut' // (formulaire spiplistes_boite_autocron) 'publie' pour annuler envoi par boite autocron
		, 'btn_confirmer_envoi' // (formulaire local) confirmer envoi
		, 'supp_dest'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_courrier', 'id_liste') as $key) {
		$$key = intval($$key);
	}
	foreach(array('email_test','titre','texte') as $key) {
		$$key = trim($$key);
	}
			
	$page_result = $message_erreur = $str_destinataire = "";

	// l'edition du courrier est reservee aux super-admins 
	// ou aux admin createur du courrier
	$flag_editable = (($connect_statut == "0minirezo") 
		&& ($connect_toutes_rubriques 
			|| ($connect_id_auteur == spiplistes_courrier_id_auteur_get($id_courrier)) || !$id_courrier));

	if($flag_editable) {
		// Modification de courrier

		// effectue les modifications demandees si retour local ou retour editeur
		if($id_courrier > 0) {
			
			if($btn_changer_destination) {
				if($radio_destination == 'email_test') {
					// demande d'envoi a  mail de test (retour formulaire local)
					if(email_valide($email_test)) {
						if(!($id_auteur_test = spiplistes_idauteur_depuis_email($email_test))) {
							// verifie si l'adresse est dans la table des auteurs
							// si inconnue, refuse d'envoyer
							$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_Adresse_email_inconnue'), true);
						}
						else {
							// Ok. Enregistre l'adresse test
							spiplistes_courrier_modifier(
								$id_courrier
								, array(
									  'email_test' => $email_test
									, 'total_abonnes' => 1
									, 'id_liste' => ($id_liste = 0)
									, 'statut' => ($change_statut = _SPIPLISTES_STATUT_READY)
									)
							);
							$str_destinataire = _T('spiplistes:email_adresse') . " : $email_test";
						}
					}
					else {
						$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_Adresse_email_invalide'), true);
					}
				} // end if($btn_envoi_test)
				
				else if($radio_destination == 'id_liste') {
					// demande d'envoi a  une liste (retour formulaire local)
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
									, 'statut' => ($change_statut = _SPIPLISTES_STATUT_READY)
								)
							);
						}
						else {
							$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_liste_vide'), true);
						}
					}
				} // end if($radio_destination
			} // if($btn_changer_destination
	
			else if($btn_courrier_valider) {
				// retour editeur local
				if(!empty($titre)) {
					spiplistes_courrier_modifier(
						$id_courrier
						, array(
							  'titre' => $titre
							, 'texte' => $texte
						)
					);
				}
				else {
					$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
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
	}  // end if($flag_editable)


	//////////////////////////////////////////////////////
	// Nouveau courrier
	////
	if(($connect_statut == "0minirezo") && ($new == 'oui')) {
	// retour editeur. Creation du courrier
		if(!empty($titre)) {
			$statut = _SPIPLISTES_STATUT_REDAC;
			$type = 'nl';
			$result = spip_query("INSERT INTO spip_courriers (titre,texte,date,statut,type,id_auteur) 
				VALUES (".sql_quote($titre).",".sql_quote($texte).",NOW(),'$statut','$type',".sql_quote($connect_id_auteur).")"); 
			$id_courrier = spip_insert_id(); 
		}
		else {
			$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
		}
	}

	//////////////////////////////////////////////////////
	// recharge le courrier pour edition
	if($id_courrier > 0) {
		
		$sql_select_tmp = "email_test,date,titre,texte,message_texte,type,statut,date_debut_envoi,date_fin_envoi";
		$sql_select_int = "id_liste,id_auteur,total_abonnes,nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html";
		$sql_select_str = "titre";
		$sql_select = $sql_select_int.",".$sql_select_str.",".$sql_select_tmp;
		
		if($row = sql_fetch(sql_select($sql_select, "spip_courriers", "id_courrier=".sql_quote($id_courrier), '', '', 1))) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			foreach(explode(",",$sql_select_int) as $key) {
				$$key = intval($$key);
			}
			foreach(explode(",",$sql_select_str) as $key) {
				$$key = typo($$key);
			}

			if($change_statut == _SPIPLISTES_STATUT_READY) {
				//$titre = propre($titre); // pas de propre ici, ca fait un <p> </p>
				// Le statut n'est modifié ici, mais 
				// par courrier_casier en retour de ce formulaire
				$texte = spiplistes_courrier_propre($texte);
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
			else if($change_statut == _SPIPLISTES_STATUT_STOPE){
				spiplistes_courrier_supprimer_queue_envois('id_courrier', $id_courrier);
				spiplistes_log("ID_COURRIER #$id_courrier CANCELLED BY ID_AUTEUR #$connect_id_auteur");
			}
			
			// prepare le texte texte seul
			if(!in_array($statut, array(
					  _SPIPLISTES_STATUT_REDAC
					, _SPIPLISTES_STATUT_READY
					, _SPIPLISTES_STATUT_PUBLIE
					, _SPIPLISTES_STATUT_STOPE
					))
				) {
				$texte = spiplistes_courrier_propre($texte);
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
				(($statut==_SPIPLISTES_STATUT_REDAC) || ($statut==_SPIPLISTES_STATUT_READY))
				? spiplistes_destiner_envoi($id_courrier, $id_liste, true, $statut, $type, 'btn_changer_destination', $email_test)
				: ""
				;
		}
	}

	//////////////////////////////////////////////////////
	// preparation des boutons si droits
	$gros_bouton_modifier = 
		$gros_bouton_supprimer = 
		$gros_bouton_arreter_envoi = ""
		;
	$flag_editable = (
		(($connect_statut == "0minirezo") && ($connect_toutes_rubriques)) 
		|| ($connect_id_auteur == $id_auteur));
	
	if($flag_editable) {
		
		if(($statut == _SPIPLISTES_STATUT_REDAC) || ($statut == _SPIPLISTES_STATUT_READY)) {
		// Le courrier peut-etre modifie si en preparation 
			$gros_bouton_modifier = 
				icone (
					_T('spiplistes:Modifier_ce_courrier') // legende bouton
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'id_courrier='.$id_courrier) // lien
					, spiplistes_items_get_item('icon', $statut) // image du fond
					, "edit.gif" // image de la fonction. Ici, le crayon
					, '' // alignement
					, false // pas echo, demande retour
					)
				;
		}
		
		if($statut != _SPIPLISTES_STATUT_PUBLIE) {
		// Le courrier peut-etre supprime s'il n'a pas ete publie
			$gros_bouton_supprimer = 
				"<div style='margin-top:1ex;'>"
				. icone (
					_T('spiplistes:Supprimer_ce_courrier')
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE, "btn_supprimer_courrier=$id_courrier")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'poubelle_msg.gif'
					, ""
					, "right"
					, false
					)
				. "</div>\n"
				;
		}
	
		if($statut == _SPIPLISTES_STATUT_ENCOURS) {
		// L'envoi d'un courrier en cours peut etre stoppe
			$gros_bouton_arreter_envoi = 
				icone (
					_T('spiplistes:Arreter_envoi')
					// si arreter envoi, passe la main a exec/spiplistes_courriers_casier
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE, "btn_arreter_envoi=$id_courrier")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_redac-24.png"
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stop-top-right-24.png"
					, "right"
					, false
					)
				. fin_cadre_relief(true)
				;
		}
		$boite_confirme_envoi = 
			($statut == _SPIPLISTES_STATUT_READY)
			? ""
				. debut_cadre_couleur('', true)
				// formulaire de confirmation envoi
				// renvoie sur la page des casiers
				. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE,"id_courrier=$id_courrier")."' method='post'>"
				. "<p style='text-align:center;font-weight:bold;' class='verdana2'>"
				. _T('spiplistes:confirme_envoi')
				. "</p>"
				. "<input type='hidden' name='id_liste' value='$id_liste' />"
				. "<input type='hidden' name='id_courrier' value='$id_courrier' />"
				. "<input type='hidden' name='id_auteur_test' value='$id_auteur_test' />"
				. "<div style='text-align:right;'><input type='submit' name='btn_confirmer_envoi' value='"._T('spiplistes:Envoyer_ce_courrier')."' class='fondo' /></div>\n"
				. "</form>"
				. fin_cadre_couleur(true)
			: ""
			;
	}

	/////////////////////
	// prepare le message statut du courrier
	if($id_courrier > 0) {
		$le_type = _T('spiplistes:message_type');
		
		if($statut != _SPIPLISTES_STATUT_REDAC) {
			if(!empty($email_test)) {
				$str_destinataire = _T('spiplistes:email_adresse') . " : <span style='font-weight:bold;color:gray;'>$email_test</span>";
			}
			else {
				if($row = sql_fetch(sql_select('titre', 'spip_listes', "id_liste=".sql_quote($id_liste), '', '', 1))) {
					$str_destinataire = ""
						. _T('spiplistes:Liste_de_destination') 
						. " : <a href='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste")."'>".$row['titre']."</a>"
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
			case _SPIPLISTES_STATUT_REDAC:
				$str_statut_courrier = _T('spiplistes:message_en_cours')."<br />"
				.	(
					($flag_editable)
					? _T('spiplistes:modif_envoi')
					: ""
					)
				;
				break;
			case _SPIPLISTES_STATUT_READY:
				$str_statut_courrier = ""
					. "<p class='verdana2'>"._T('spiplistes:message_presque_envoye') . "<br />"
					. $str_destinataire . "<br />"
					;
				break;
			case _SPIPLISTES_STATUT_ENCOURS:
				$str_statut_courrier = ""
					. _T('spiplistes:message_en_cours')."<br />$str_destinataire<br /><br />"
					//. "<a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a>"
					;
				break;
			case _SPIPLISTES_STATUT_PUBLIE:
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
					. "  <li>"._T('spiplistes:format_html').$nb_emails_html."</li>"
					. "  <li>"._T('spiplistes:format_texte').$nb_emails_texte."</li>"
					. "  <li>"._T('spiplistes:desabonnes').": ".$nb_emails_non_envoyes."</li>"
					. " </ul>"
					. " <li>"._T('spiplistes:erreur_envoi').$nb_emails_echec."</li>"
					. "</ul>"
					;
		} // end switch()
		if(!empty($str_statut_courrier)) {
			$str_statut_courrier = "<div class='verdana2'>".$str_statut_courrier."</div>";
		}
	} // end if()
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:spip_listes');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "courrier_gerer";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	// la gestion des listes de courriers est reservee aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ""
		. spiplistes_onglets(_SPIPLISTES_RUBRIQUE, $titre_page, true)
		. debut_gauche($rubrique, true)
		. spiplistes_boite_info_id(_T('spiplistes:Courrier_numero_:'), $id_courrier, true)
		. spiplistes_naviguer_paniers_courriers(_T('spiplistes:aller_au_panier_'), true)
		. creer_colonne_droite($rubrique, true)
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron()
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		;
		
	if($id_courrier > 0) {
	/////////////////////
	// construction du ventre
		$page_result .= ""
			. $message_erreur
			. debut_cadre_relief(spiplistes_items_get_item('icon', $statut), true)
			. "<table width='100%'  border='0' cellspacing='0' cellpadding='0'>"
			. "<tr>"
			. "<td>".spiplistes_gros_titre($titre, spiplistes_items_get_item('puce', $statut), true)."</td>"
			. "<td rowspan='2' style='vertical-align:top;width:90px;'>"
				// si besoin, l'un de ces deux boutons apparait
				. $gros_bouton_modifier
				. $gros_bouton_arreter_envoi
				."</td>"
			. "</tr>"
			. "<tr> "
			. "<td>"
			. "<p class='verdana2' style='font-size:120%;color:red;font-weight:bold;'>$le_type</p>"
			. "<p class='verdana2'>$str_statut_courrier</p>"
			. "</td>"
			. "</tr>"
			. "</table>"
			. $boite_confirme_envoi
			. $boite_selection_destinataire
			. "<br />"
			//
			// boite courrier au format html
			. debut_cadre_couleur('', true)
			. _T('spiplistes:version_html')
			. "&nbsp;<a href='"
				. generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE,"id_courrier=$id_courrier&id_liste=$id_liste&lire_base=oui&plein_ecran=oui")
				. "' title='"._T('spiplistes:Apercu_plein_ecran')."' target='_blank'>\n"
			. spiplistes_icone_oeil() . "</a><br />\n"
			. "<iframe style='background-color:#fff;border:1px solid #000;'"
				. " src='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE,"id_courrier=$id_courrier&lire_base=oui")
				."' width='100%' height='500'></iframe>\n"
			. fin_cadre_couleur(true)
			//
			// boite courrier au format texte seul
			. debut_cadre_couleur('', true)
			. _T('spiplistes:version_texte')
			. "&nbsp;<a href='"
				. generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE,"id_courrier=$id_courrier&id_liste=$id_liste&lire_base=oui&format=texte&plein_ecran=oui")
				."' title='"._T('spiplistes:Apercu_plein_ecran')." ($alt_message_texte)' target='_blank'>\n"
			. spiplistes_icone_oeil() . "</a><br />\n"
			. "<textarea readonly='readonly' name='texte' rows='".(($spip_ecran == "large") ? 28 : 20)."' class='formo' cols='40' wrap='soft'>"
			. spiplistes_courrier_version_texte(propre($message_texte))
			. "</textarea>\n"
			. fin_cadre_couleur(true)
			//
			// fin de la boite
			. fin_cadre_relief(true)
			//
			. $gros_bouton_supprimer
			;
	} // end if
	else {
		$page_result .= ""
			. __boite_alerte (_T('spiplistes:Erreur_courrier_introuvable'), true)
			;
	}

	echo($page_result);
	
	// GERER COURRIER: FIN DE PAGE
	
	echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();

} // end function exec_spiplistes_courrier_gerer ()

function spiplistes_icone_oeil () {
	return("<img src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."oeil-16.png' alt='' width:'16' height='16' border='0' />");
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
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>