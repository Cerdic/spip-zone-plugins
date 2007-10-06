<?php

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
	_SPIPLISTES_EXEC_COURRIER_GERER
	
	Affiche un courrier. 
	Le formulaire permet :
	- l'envoi sur mail de test
	- l'attachement d'une liste
	Dans les deux cas, le statut du courrier passe à _SPIPLISTES_STATUT_READY 
	(la meleuse prend en charge les courriers en statut _SPIPLISTES_STATUT_READY)
	
*/
function exec_spiplistes_courrier_gerer () {

	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/affichage');
	include_spip('base/spip-listes');
	include_spip('inc/spiplistes_destiner_envoi');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_ecran
		;

	// initialise les variables postées par le formulaire
	foreach(array(
		'type'
		, 'id_message' // devrait être id_courrier. A corriger dans les autres scripts et supprimer ici.
		, 'id_courrier'
		, 'modifier_message', 'titre', 'texte' // (formulaire edition) _SPIPLISTES_EXEC_COURRIER_EDIT
		, 'new' // idem
		, 'btn_changer_destination', 'radio_destination', 'email_test', 'id_liste' // (formulaire local) destinataire
		, 'change_statut' // (formulaire spiplistes_boite_autocron) 'publie' pour annuler envoi par boite autocron
		, 'btn_confirmer_envoi' // (formulaire local) confirmer envoi
		, 'supp_dest'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_message','id_courrier','id_liste') as $key) {
		$$key = intval($$key);
	}
	foreach(array('email_test','titre','texte') as $key) {
		$$key = trim($$key);
	}
	if($id_message>0) $id_courrier = $id_message;
	
	$page_result = $message_erreur = $str_destinataire = "";

	//////////////////////////////////////////////////////
	// Modification de courrier
	////
	// effectue les modifications demandées si retour local ou retour editeur
	if($id_courrier > 0) {
		
		if($btn_changer_destination) {
			if($radio_destination == 'email_test') {
			//////////////////////////////////////////////////////
			// demande d'envoi à mail de test (formulaire local)
				if(email_valide($email_test)) {
					if(spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs WHERE email='$email_test' LIMIT 1"))==0) {
					// vérifie si l'adresse est connue des auteurs
						// si inconnue, refuse d'envoyer
						$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_Adresse_email_inconnue'), true);
					}
					else {
						// Ok. Enregistre l'adresse et passe le courrier ready pour la meleuse
						spip_query("UPDATE spip_courriers SET email_test='$email_test',total_abonnes=1,id_liste=0 WHERE id_courrier=$id_courrier LIMIT 1");
						//passer le mail en pret a l envoi
						$change_statut = _SPIPLISTES_STATUT_READY;
						$str_destinataire = _T('spiplistes:email_adresse') . " : $email_test";
					}
				}
				else {
					$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_Adresse_email_invalide'), true);
				}
			} // end if($btn_envoi_test)
			else if($radio_destination == 'id_liste') {
			//////////////////////////////////////////////////////
			// demande d'envoi à une liste (formulaire local)
				if($id_liste > 0) {
					if(($nb_abos = spiplistes_nb_abonnes_count($id_liste)) > 0) {
						if($row = spip_fetch_array(spip_query ("SELECT titre FROM spip_listes WHERE id_liste = $id_liste LIMIT 1"))) {
							// va chercher le nom de la liste + nb abos
							$str_destinataire = _T('spiplistes:sur_liste') . " : <a href='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste")."'>".$row['titre']."</a>"
								. " " . spiplistes_nb_abonnes_liste_str_get($id_liste, $nb_abos);
							// Ok. Met à jour le panier
							spip_query("UPDATE spip_courriers SET email_test='',total_abonnes=$nb_abos,id_liste=$id_liste WHERE id_courrier=$id_courrier LIMIT 1");
							$change_statut = _SPIPLISTES_STATUT_READY;
						}
					}
					else {
						$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_liste_vide'), true);
					}
				}
			} // end if($radio_destination
		} // if($btn_changer_destination

		else if ($modifier_message == "oui") {
		// retour éditeur
			if(!empty($titre)) {
				spip_query("UPDATE spip_courriers SET titre="._q($titre).",texte="._q($texte)." WHERE id_courrier=$id_courrier LIMIT 1");	
			}
			else {
				$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
			}
		}
		
		else if($btn_confirmer_envoi 
			//&& ($statut == _SPIPLISTES_STATUT_READY)
			//&& (!empty($email_test) || ($id_liste > 0))
			&& ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur))
			) {
			$change_statut = _SPIPLISTES_STATUT_ENCOURS;
			spip_query("UPDATE spip_courriers SET statut='$change_statut' WHERE id_courrier=$id_courrier LIMIT 1");
			spiplistes_supprime_liste_envois($id_courrier);
			// passe le courrier à la méleuse
			spiplistes_remplir_liste_envois($id_courrier,$id_liste);
			spiplistes_log("SEND ID_COURRIER #$id_courrier ON ID_LISTE #$id_liste BY ID_AUTEUR #$connect_id_auteur");
		}

		// FIN DES MODIFICATIONS

		// Ok. recharge les données pour compléter le formulaire
		$sql_select = "titre,texte,email_test,statut";
		if($row = spip_fetch_array(spip_query("SELECT $sql_select FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 1"))) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
		}
	} // end if($id_courrier > 0)
	//////////////////////////////////////////////////////
	// Nouveau courrier
	////
	else if($new == 'oui') {
	// retour éditeur. Création du courrier
		if(!empty($titre)) {
			$statut = _SPIPLISTES_STATUT_REDAC;
			$type = 'nl';
			$result = spip_query("INSERT INTO spip_courriers (titre,texte,date,statut,type,id_auteur) 
				VALUES ("._q($titre).","._q($texte).",NOW(),'$statut','$type',"._q($connect_id_auteur).")"); 
			$id_courrier = spip_insert_id(); 
		}
		else {
			$message_erreur .= __boite_alerte (_T('spiplistes:Erreur_courrier_titre_vide'), true);
		}
	}

	//////////////////////////////////////////////////////
	// recharge le courrier pour édition
	if($id_courrier > 0) {
		
		$sql_select_tmp = "email_test,date,titre,texte,message_texte,type,statut,date_debut_envoi,date_fin_envoi";
		$sql_select_int = "id_liste,id_auteur,total_abonnes,nb_emails_envoyes,nb_emails_echec,nb_emails_non_envoyes,nb_emails_texte,nb_emails_html";
		$sql_select_str = "titre";
		$sql_select = $sql_select_int.",".$sql_select_str.",".$sql_select_tmp;
		
		if($row = spip_fetch_array(spip_query("SELECT $sql_select FROM spip_courriers WHERE id_courrier="._q($id_courrier)." LIMIT 1"))) {
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
				$titre = spiplistes_propre($titre);
				$texte = spiplistes_propre($texte);
				spip_query("UPDATE spip_courriers SET titre="._q($titre).",texte="._q($texte).",statut='$change_statut' WHERE id_courrier=$id_courrier LIMIT 1");
				spiplistes_log("ID_COURRIER #$id_courrier MODIFIED TO $change_statut BY ID_AUTEUR #$connect_id_auteur");
				$statut = $change_statut;
			}
			else if($change_statut == _SPIPLISTES_STATUT_STOPE){
				spiplistes_supprime_liste_envois($id_courrier);
				spiplistes_log("ID_COURRIER #$id_courrier CANCELLED BY ID_AUTEUR #$connect_id_auteur");
			}
			/* futur
			else if($change_statut == _SPIPLISTES_STATUT_BREAK) {
				// si envoi annulé par spiplistes_boite_autocron, stope les envois en cours
				spip_query("SELECT id_courrier,statut FROM spip_courriers WHERE statut='".."'")
				spiplistes_log("BREAK BY ID_AUTEUR #$connect_id_auteur");
			}
			*/
			
			// prépare le texte texte seul
			if(!in_array($statut, array(
					  _SPIPLISTES_STATUT_REDAC
					, _SPIPLISTES_STATUT_READY
					, _SPIPLISTES_STATUT_PUBLIE
					, _SPIPLISTES_STATUT_STOPE
					))
				) {
				$texte = spiplistes_propre($texte);
			}
			if(!empty($message_texte)){
				$alt_message_texte = _T('spiplistes:calcul_patron');
			}
			else{
				$alt_message_texte = _T('spiplistes:calcul_html');
				$message_texte = version_texte($texte);
			}
			// construit la boite de sélection destinataire
			$boite_selection_destinataire = (($statut==_SPIPLISTES_STATUT_REDAC) || ($statut==_SPIPLISTES_STATUT_READY))
				? spiplistes_destiner_envoi($id_courrier, $id_liste, true, $statut, $type, 'btn_changer_destination', $email_test)
				: ""
				;
		}
	}

	
	//////////////////////////////////////////////////////
	// préparation des boutons si droits
	$gros_bouton_modifier = 
		$gros_bouton_supprimer = 
		$gros_bouton_arreter_envoi = ""
		;
	if($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur)) {
		
		if(($statut == _SPIPLISTES_STATUT_REDAC) || ($statut == _SPIPLISTES_STATUT_READY)) {
		// Le courrier peut-être modifié si en préparation 
			$gros_bouton_modifier = 
				icone (
					_T('spiplistes:Modifier_ce_courrier') // légende bouton
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'id_courrier='.$id_courrier) // lien
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_mail.gif" // image du fond
					, "edit.gif" // image de la fonction. Ici, le crayon
					, '' // alignement
					, false // pas echo, demande retour
					)
				;
		}
		
		if($statut != _SPIPLISTES_STATUT_PUBLIE) {
		// Le courrier peut-être supprimé s'il n'a pas été publié
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
		// L'envoi d'un courrier en cours peut être stoppé
			$gros_bouton_arreter_envoi = 
				icone (
					_T('spiplistes:Arreter_envoi')
					// si arreter envoi, passe la main à exec/spip_listes
					, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE, "btn_arreter_envoi=$id_courrier")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_redac-24.png"
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stop-top-right-24.png"
					, "right"
					, false
					)
				. fin_cadre_relief(true)
				;
		}
	}

	/////////////////////
	// prépare le message statut du courrier
	if($id_courrier > 0) {
		$le_type = _T('spiplistes:message_type');
		
		$str_statut_courrier = "";
		switch($statut) {
			case _SPIPLISTES_STATUT_REDAC:
				$str_statut_courrier = _T('spiplistes:message_en_cours')."<br />"._T('spiplistes:modif_envoi');
				break;
			case _SPIPLISTES_STATUT_READY:
				if($row = spip_fetch_array(spip_query ("SELECT titre FROM spip_listes WHERE id_liste = $id_liste LIMIT 1"))) {
					$str_destinataire = 
						(!empty($email_test))
						? _T('spiplistes:email_adresse') . " : $email_test"
						: _T('spiplistes:sur_liste') . " : <a href='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste")."'>".$row['titre']."</a>"
									. " " . spiplistes_nb_abonnes_liste_str_get($id_liste)
						;
				}
				$str_statut_courrier = ""
					. "<p class='verdana2'>"._T('spiplistes:message_presque_envoye') . "<br />"
					. $str_destinataire . "<br />"
					._T('spiplistes:confirme_envoi')
					// formulaire de confirmation envoi
					. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE,"id_courrier=$id_courrier")."' method='post'>"
					. "<p style='text-align:center;'>"
					. "<input type='hidden' name='id_liste' value='$id_liste' />"
					. "<input type='hidden' name='id_courrier' value='$id_courrier' />"
					. "<input type='submit' name='btn_confirmer_envoi' value='"._T('spiplistes:Envoyer_ce_courrier')."' class='fondo' />"
					. "</p>"
					. "</form>"
					;
				break;
			case _SPIPLISTES_STATUT_ENCOURS:
				$str_statut_courrier = ""
					. _T('spiplistes:envoi_program')."<br />"._T('spiplistes:a_destination').$str_destinataire."<br /><br />"
					. "<a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a>"
					;
				break;
			case _SPIPLISTES_STATUT_PUBLIE:
				$str_statut_courrier = ""
					. "<span>"
					. "<strong>"._T('spiplistes:message_arch')."</strong></span>"
					. "<ul>"
					. " <li>"._T('spiplistes:envoyer_a').$str_destinataire."</li>"
					. " <li>"._T('spiplistes:envoi_date').$date."</li>"
					. " <ul>"
					. "  <li>"._T('spiplistes:envoi_debut').$date_debut_envoi."</li>"
					. "  <li>"._T('spiplistes:envoi_fin').$date_fin_envoi."</li>"
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
		}
		if(!empty($str_statut_courrier)) {
			$str_statut_courrier = "<div class='verdana2'>".$str_statut_courrier."</div>";
		}

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la gestion des listes de courriers est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));

	debut_gauche();
	spiplistes_boite_info_id(_T('spiplistes:Courrier_numero_:'), $id_courrier, false);
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

		
	/////////////////////
	// construction du ventre
		$page_result = ""
			. $message_erreur
			. debut_cadre_relief(spiplistes_items_get_item('icon', $statut), true)
			. "<table width='100%'  border='0' cellspacing='0' cellpadding='0'>"
			. "<tr>"
			. "<td>".gros_titre($titre, spiplistes_items_get_item('puce', $statut), false)."</td>"
			. "<td rowspan='2' style='vertical-align:top;width:90px;'>"
				// si besoin, l'un de ces deux boutons apparaît
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
			. $boite_selection_destinataire
			. "<br />"
			//
			// boite courrier au format html
			. debut_cadre_couleur('', true)
			. _T('spiplistes:version_html')." <a href='".generer_url_ecrire('courrier_preview','id_message='.$id_courrier)."' title=\""._T('spiplistes:plein_ecran')."\"><small>(+)</small></a><br />\n"
			. "<iframe style='background-color:#fff;border:1px solid #000;'"
				. " src='".generer_url_ecrire('courrier_preview','id_message='.$id_courrier)."' width='100%' height='500'></iframe>\n"
			. fin_cadre_couleur(true)
			//
			// boite courrier au format texte seul
			. debut_cadre_couleur('', true)
			. _T('spiplistes:version_texte')." <a href='#' title='$alt_message_texte'><small>(?)</small></a><br />"
			. "<textarea readonly='readonly' name='texte' rows='".(($spip_ecran == "large") ? 28 : 20)."' class='formo' cols='40' wrap='soft'>"
			. $message_texte
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
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
}
/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'abonn� et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
?>
