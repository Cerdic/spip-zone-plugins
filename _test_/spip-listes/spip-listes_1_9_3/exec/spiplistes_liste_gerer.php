<?php

// exec/spiplistes_liste_gerer.php
// _SPIPLISTES_EXEC_LISTE_GERER

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
/* d'adaptation dans un but specifique. Reportez-vous � la Licence Publique Generale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

	// Precision sur la table spip_listes:
	// 'date': date d'envoi souhaitee
	// 'maj': date d'envoi du courrier mis a� jour par cron.
	// 'description': (pas utilise au 20071006)
	// 'texte': description affichee dans formulaire abonnement
	
function exec_spiplistes_liste_gerer () {

	include_spip('inc/mots');
	include_spip('inc/lang');
	include_spip('base/spiplistes_tables');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_dater_envoi');
	include_spip('inc/spiplistes_naviguer_paniers');
	
	global $meta
		, $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_lang_left,$spip_lang_right
		;

	// initialise les variables postees par le formulaire
	foreach(array(
		'new'	// nouvelle liste si 'oui'
		, 'id_liste'// si modif dans l'editeur
		, 'btn_liste_edit', 'titre', 'texte', 'pied_page' // renvoyes par l'editeur
		, 'btn_modifier_diffusion', 'changer_lang', 'statut' // local
		, 'btn_modifier_replyto', 'email_envoi' // local
		, 'btn_modifier_courrier_auto', 'message_auto' // local
			, 'titre_message', 'patron', 'periode', 'envoyer_maintenant'
			, 'jour', 'mois', 'annee', 'heure', 'minute'
			, 'auto_mois'
		, 'btn_patron_pied', 'btn_grand_patron' // boites gauches
		, 'btn_valider_forcer_abos', 'forcer_abo'
		, 'btn_supprimer_liste' //local
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_liste', 'periode') as $key) {
		$$key = intval($$key);
	}
	foreach(array('titre', 'texte', 'pied_page') as $key) {
		$$key = trim(corriger_caracteres($$key));
	}
	$lang = $changer_lang;

	$cherche_auteur = _request('cherche_auteur'); // ??
	$debut = _request('debut'); // ??


	$envoyer_maintenant = ($envoyer_maintenant == 'oui');
	
	$boite_pour_confirmer_envoi_maintenant = "";
	
	$page_result = "";

	if(!$id_liste) {
	//////////////////////////////////////////////////////
	// Creer une liste
	////
		// admin lambda peut creer une liste
		$flag_editable = ($connect_statut == "0minirezo");
		
		if ($btn_liste_edit && ($new=='oui')) {
			
			if ($titre=='') {
				$titre = _T('spiplistes:liste_sans_titre');
			}
			
			$pied_page = spiplistes_pied_de_page_liste(0, $GLOBALS['spip_lang']);
			
			spip_query("INSERT INTO spip_listes (statut, lang, titre, texte, pied_page) 
				VALUES ('"._SPIPLISTES_PRIVATE_LIST."','".$GLOBALS['spip_lang']."',"._q($titre).","._q($texte).","._q($pied_page).")");
			$id_liste = spip_insert_id();
			//Auteur de la liste (moderateur)
			spip_query("DELETE FROM spip_auteurs_mod_listes WHERE id_liste = "._q($id_liste));
			spip_query("INSERT INTO spip_auteurs_mod_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
			//abonne le moderateur a� sa liste
			spip_query("DELETE FROM spip_auteurs_listes WHERE id_liste = "._q($id_liste));
			spip_query("INSERT INTO spip_auteurs_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
		} 
		spiplistes_log("LISTE ID #$id_liste added by ID_AUTEUR #$connect_id_auteur");
	}
	else if($id_liste > 0) {
	//////////////////////////////////////////////////////
	// Modifier une liste
	////
		// les supers-admins et le moderateur seuls peuvent modifier la liste
		$id_mod_liste = spiplistes_mod_listes_get_id_auteur($id_liste);
		$flag_editable = ($connect_toutes_rubriques || ($connect_id_auteur == $id_mod_liste));

		if($flag_editable) {
//spiplistes_log("LISTE MODIF: flag_editable <<", _SPIPLISTES_LOG_DEBUG);
		
			// Recupere les donnees de la liste courante pour optimiser l'update
			$sql_select = "statut,titre,date,lang";
			$sql_result = sql_select($sql_select, "spip_listes", "id_liste=".sql_quote($id_liste), "", "", "1");
			if($row = sql_fetch($sql_result)) {
				foreach(explode(",", $sql_select) as $key) {
					$current_liste[$key] = $row[$key];
				}
			}
			
			///////////////////////////////////
			// Les modifications (sql_upadteq)
			// A noter, ne pas pr�parer les valeurs par sql_quote()
			//  sql_upadteq() s'en occupe
			
			$sql_champs = array();

			// Retour de l'editeur ?
			if($btn_liste_edit) {
				$titre = corriger_caracteres($titre);
				$texte = corriger_caracteres($texte);
				if(empty($titre)) {
					$titre = filtrer_entites(_T('spiplistes:Nouvelle_liste_de_diffusion'));
				}
				$sql_champs['titre'] = $titre;
				$sql_champs['texte'] = $texte;
			}
			
			// Modifier le grand patron ?
			if($btn_grand_patron && $patron) {
				$sql_champs['patron'] = $patron;
			}
			
			// Modifier patron de pied ?
			if($btn_patron_pied && $patron) {
//spiplistes_log("LISTE MODIF: de la liste <<$id_liste $patron", _SPIPLISTES_LOG_DEBUG);
				$pied_page = spiplistes_pied_page_html_get($patron);
				$sql_champs['pied_page'] = $pied_page;
			}
			
			// Modifier diffusion ?
			if($btn_modifier_diffusion) {
//spiplistes_log("LISTE MODIF: btn_modifier_diffusion <<$statut", _SPIPLISTES_LOG_DEBUG);
				// Modifier le statut ?
				if(in_array($statut, explode(";", _SPIPLISTES_LISTES_STATUTS)) && ($statut!=$current_liste['statut'])) {
					$sql_champs['statut'] = $statut;
					// si la liste passe en privee, retire les invites
					if($statut == _SPIPLISTES_PRIVATE_LIST) {
						$auteur_statut = '6forum';
						spip_query("DELETE FROM spip_auteurs_listes
							WHERE id_auteur IN (SELECT id_auteur FROM spip_auteurs WHERE statut='$auteur_statut')");
						spiplistes_log(" AUTEURS ($auteur_statut) REMOVED FROM LISTE $id_liste ($statut) BY ID_AUTEUR #$connect_id_auteur");
					}
				}
				// Modifier la langue ?
				if(!empty($lang) && ($lang!=$current_liste['lang'])) {
//spiplistes_log("LISTE MODIF: btn_modifier_diffusion $lang", _SPIPLISTES_LOG_DEBUG);
					$sql_champs['lang'] = $lang;
				}
			}
			
			// Modifier l'adresse email de reponse ?
			if($btn_modifier_replyto && email_valide($email_envoi) && ($email_envoi!=$current_liste['email_envoi'])) {
				$sql_champs['email_envoi'] = $email_envoi;
			}

			////////////////////////////////////
			// Modifier message_auto ?
			if($btn_modifier_courrier_auto) {
//spiplistes_log("LISTE MODIF: btn_modifier_courrier_auto <<", _SPIPLISTES_LOG_DEBUG);
				$titre_message = $titre_message ; // attention propre -> <p>
				if(
					($message_auto == 'oui')
					&& ($envoyer_maintenant
						|| ($envoyer_quand = spiplistes_formate_date_form($annee, $mois, $jour, $heure, $minute)) 
						|| $auto_mois)
					) {
					$sql_champs['message_auto'] = 'oui';
					$sql_champs['titre_message'] = $titre_message;

					if(time() > strtotime($envoyer_quand)) {
					// envoi dans le passe est considere comme envoyer maintenant
						$envoyer_maintenant = true;
						$date_depuis = $envoyer_quand ;
						$envoyer_quand = false;
					}
					
					if($envoyer_maintenant) {
						$boite_pour_confirmer_envoi_maintenant = ""
							. debut_cadre_couleur('', true)
							// formulaire de confirmation envoi
							. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTES_LISTE)."' method='post'>"
							. "<p style='text-align:center;font-weight:bold;' class='verdana2'>". _T('spiplistes:Confirmez_envoi_liste')	. "</p>"
							. "<input type='hidden' name='id_liste' value='$id_liste' />"
							. "<input type='hidden' name='periode' value='$periode' />"
							. "<input type='hidden' name='auto_mois' value='$auto_mois' />"
							. "<input type='hidden' name='titre_message' value=\"$titre_message\" />"
							. "<div style='text-align:right;'><input type='submit' name='btn_confirmer_envoi_maintenant' value='"._T('spiplistes:Envoyer_ce_courrier')."' class='fondo' /></div>\n"
							. "</form>"
							. fin_cadre_couleur(true)
							;
						if($date_depuis){
							$sql_champs['maj'] = $date_depuis;
							$sql_champs['periode'] = $periode;
						}
						$date_prevue = __mysql_date_time(time());
						
					}
					else if($envoyer_quand) {
						$sql_champs['date'] = $envoyer_quand;
						$sql_champs['periode'] = $periode;
					}
					
					$sql_champs['statut'] = 
						($auto_mois)
						? _SPIPLISTES_MONTHLY_LIST
						: _SPIPLISTES_PRIVATE_LIST
						;
				}
				else if($message_auto == 'non') {
					$sql_champs['message_auto'] = 'non';
					$sql_champs['titre_message'] = '';
					$sql_champs['date'] = '';
					$sql_champs['periode'] = 0;
				}
			} // end if($btn_modifier_courrier_auto)
			
			// Enregistre les modifs pour cette liste
			if(count($sql_champs)) {
				$sql_result = sql_updateq("spip_listes", $sql_champs, "id_liste=".sql_quote($id_liste)." LIMIT 1");
			}
			
			// Forcer les abonnements
			if($btn_valider_forcer_abos && $forcer_abo && in_array($forcer_abo, array('tous', 'auteurs', '6forum', 'aucun'))) {
				include_spip("inc/spiplistes_listes_forcer_abonnement");
				if(spiplistes_listes_forcer_abonnement ($id_liste, $forcer_abo) ===  false) {
					$page_result .= __boite_alerte(_T('spiplistes:Forcer_abonnement_erreur', true));
				}
			}
			
		} // end if($flag_editable)
	}

	//////////////////////////////////////////////////////
	// Recharge les donnees la liste
	$result = spip_query("SELECT * FROM spip_listes WHERE id_liste="._q($id_liste)." LIMIT 1");

	if($row = spip_fetch_array($result)) {
		foreach(array(
		// initialise les variables du resultat SQL
			'id_liste', 'titre', 'texte'
			, 'titre_message', 'pied_page', 'date', 'statut', 'maj'
			, 'email_envoi', 'message_auto', 'periode', 'patron', 'lang'
			) as $key) {
			$$key = $row[$key];
		}
	}

	// les supers-admins et le moderateur seuls peuvent modifier la liste
	$id_mod_liste = spiplistes_mod_listes_get_id_auteur($id_liste);
	$flag_editable = ($connect_toutes_rubriques || ($connect_id_auteur == $id_mod_liste));

	$titre_message = ($titre_message=='') ? $titre._T('spiplistes:_de_').$meta['nom_site'] : $titre_message;

	$nb_abonnes = spiplistes_nb_abonnes_count ($id_liste);

	// preparation des boutons 
	if($flag_editable) {
		// Propose de modifier la liste 
		$gros_bouton_modifier = 
			icone (
				_T('spiplistes:Modifier_cette_liste') // legende bouton
				, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_EDIT,'id_liste='.$id_liste) // lien
				, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif" // image du fond
				, "edit.gif" // image de la fonction. Ici, le crayon
				, '' // alignement
				, false // pas echo, demande retour
				)
			;
		// Propose de supprimer la liste 
		$gros_bouton_supprimer = 
			icone (
					_T('spiplistes:Supprimer_cette_liste')
					, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "btn_supprimer_liste=$id_liste&id_liste=$id_liste")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'poubelle_msg.gif'
					, ""
					, "right"
					, false
					)
			;
	}
	else {
		$gros_bouton_modifier = $gros_bouton_supprimer = "";
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:spip_listes');
	// Permet entre autres d'ajouter les classes � la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "liste_gerer";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	// la gestion des listes de courriers est reservee aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. spiplistes_onglets(_SPIPLISTES_RUBRIQUE, $titre_page, true)
		. debut_gauche($rubrique, true)
		. spiplistes_boite_info_id(_T('spiplistes:liste_numero'), $id_liste, true)
		. spiplistes_naviguer_paniers_listes(_T('spiplistes:aller_aux_listes_'), true)
		. spiplistes_boite_patron($flag_editable, $id_liste, _SPIPLISTES_EXEC_LISTE_GERER, 'btn_grand_patron'
			, _SPIPLISTES_PATRONS_DIR, _T('spiplistes:Patron_grand_')
			, ($patron ? $patron : "")
			, $patron, true)
		. spiplistes_boite_patron($flag_editable, $id_liste, _SPIPLISTES_EXEC_LISTE_GERER, 'btn_patron_pied'
			, _SPIPLISTES_PATRONS_PIED_DIR, _T('spiplistes:Patron_de_pied_')
			, (($ii = strlen($pied_page)) ? _T('taille_octets',array('taille'=>$ii)) : "")
			, ($ii==0), true)
		. creer_colonne_droite($rubrique, true)
		. spiplistes_boite_raccourcis(true)
		//. spiplistes_boite_autocron(true) // ne pas g�ner l'�dition
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		;

	changer_typo('','liste'.$id_liste);

	// message alerte et demande de confirmation si supprimer liste
	if(($btn_supprimer_liste > 0) && ($btn_supprimer_liste == $id_liste)) {
		$page_result .= ""
			. __boite_alerte (_T('spiplistes:Attention_suppression_liste')."<br />"._T('spiplistes:Confirmez_requete'), true)
			. "<form name='form_suppr_liste' id='form_suppr_liste' method='post' action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTES_LISTE, "")."'>\n"
			. "<div class='verdana2' style='text-align:right;'>\n"
			. "<input type='hidden' name='id_liste' value='$id_liste' />\n"
   		. "<label>"._T('spiplistes:Confirmer_la_suppression_de_la_liste')."# $id_liste : \n"
   		. "<input class='fondo' type='submit' name='btn_supprimer_liste_confirme' value='"._T('bouton_valider')."' /></label>\n"
			. "</div>\n"
			. "</form>\n"
			. "<br />\n"
		;
	}

	$page_result .= ""
		. debut_cadre_relief("", true)
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		. "<tr><td valign='top'>\n"
		. spiplistes_gros_titre(spiplistes_bullet_titre_liste('puce', $statut, '', true)." ".$titre, '', true)
		. "</td>"
		. "<td rowspan='2'>"
		// le gros bouton modifier si besoin
		. $gros_bouton_modifier
		. "</td></tr>\n"
		. "<tr><td width='100%'>\n"
		. "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaa; ' class='verdana1 spip_small'>\n"
		. propre($texte."~")
		. "</div>\n"
		. "</td>\n"
		. "</tr></table>\n"
		;

	
	//////////////////////////////////////////////////////
	// Modifier le statut de la liste
	//
	$page_result .= ""
	// en javascript !
		. "
	<script type='text/javascript'><!--
	var alerter_modif_statut = false;
	function change_bouton(selObj){
		var selection=selObj.options[selObj.selectedIndex].value;
		switch(selection) {
			case '"._SPIPLISTES_PRIVATE_LIST."':
				if(!alerter_modif_statut) { 
					alert('".__texte_html_2_iso(_T('spiplistes:Attention_action_retire_invites'), $GLOBALS['meta']['charset'], true)."'); 
					alerter_modif_statut=true; 
				}
				document.img_statut.src='".spiplistes_items_get_item("puce", _SPIPLISTES_PRIVATE_LIST)."';
				break;
			case '"._SPIPLISTES_PUBLIC_LIST."':
				document.img_statut.src='".spiplistes_items_get_item("puce", _SPIPLISTES_PUBLIC_LIST)."';
				break;
			case '"._SPIPLISTES_TRASH_LIST."':
				document.img_statut.src='".spiplistes_items_get_item("puce", _SPIPLISTES_TRASH_LIST)."';
				break;
		}
	}
	// --></script>"
		;

	$email_defaut = entites_html($meta['email_webmaster']);
	$email_envoi = (email_valide($email_envoi)) ? $email_envoi : $email_defaut ;

	$page_result .= ""
		//. debut_cadre_relief("racine-site-24.gif", true)
		. debut_cadre_relief("racine-site-24.gif", true, '', _T('spiplistes:Diffusion').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "diffusion"))
		//
		////////////////////////////
		// Formulaire diffusion
		.	(
			($flag_editable)
			? ""
				. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste")."' method='post'>\n"
				. "<input type='hidden' name='exec' value='listes' />\n"
				. "<input type='hidden' name='id_liste' value='$id_liste' />\n"
			: ""
			)
		. "<span class='verdana2'>". _T('spiplistes:Cette_liste_est')." : "
		. 	spiplistes_bullet_titre_liste('puce', $statut, 'img_statut', true)."</span>\n"
		.	(
			($flag_editable)
			? ""
				. "<select class='verdana2' name='statut' size='1' class='fondl' onchange='change_bouton(this)'>\n"
				. "<option" . mySel(_SPIPLISTES_PRIVATE_LIST, $statut) ." style='background-color: white'>"._T('spiplistes:statut_interne')."\n"
				. "<option" . mySel(_SPIPLISTES_PUBLIC_LIST, $statut) . " style='background-color: #B4E8C5'>"._T('spiplistes:statut_publique')."\n"
				. "<option" . mySel(_SPIPLISTES_TRASH_LIST, $statut) . " style='background:url("._DIR_IMG_PACK."rayures-sup.gif)'>"._T('texte_statut_poubelle')."\n"
				. "</select>\n"
			: "<span class='verdana2' style='font-weight:bold;'>".spiplistes_items_get_item("alt", $statut)."</span>\n"
			)
		. "<div style='margin:10px 0px 10px 0px'>\n"
		.	(
			($flag_editable)
			? ""
				. "<label class='verdana2' for='changer_lang'>"._T('info_multi_herit')." : </label>\n"
				. "<select name='changer_lang'  class='fondl' id='changer_lang'>\n"
				. liste_options_langues('changer_lang', $lang , _T('spiplistes:langue'),'', '')
				. "</select>\n"
			: ""
				. "<span class='verdana2'>". _T('info_multi_herit')." : "
				. "<span class='verdana2' style='font-weight:bold;'>".traduire_nom_langue($lang)."</span>\n"
			)
		. "</div>\n"
		.	(
			($flag_editable)
			? ""
				. "<div style='text-align:right;'><input type='submit' name='btn_modifier_diffusion' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
				. "</form>\n"
			: ""
			)
		. fin_cadre_relief(true)
		//
		////////////////////////////
		// Formulaire adresse email pour le reply-to
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply_to-24.png", true, '', _T('spiplistes:adresse_de_reponse').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "replyto"))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste")."' method='post'>\n"
		. "<p class='verdana2'>\n"
		. _T('spiplistes:adresse_mail_retour').":<br />\n"
		.	(
			($flag_editable)
			?	""
				. "<blockquote class='verdana2'><em>"._T('spiplistes:adresse')."</em></blockquote></p>\n"
				. "<div style='text-align:center'>\n"
				. "<input type='text' name='email_envoi' value=\"".$email_envoi."\" size='40' class='fondl' /></div>\n"
				. ($id_liste ? "<input type='hidden' name='id_liste' value='$id_liste' />" : "")
				. "<div style='text-align:right;'><input type='submit' name='btn_modifier_replyto' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
			: "<p style='font-weight:bold;text-align:center;'>$email_envoi</p>\n"
			)
		. "</form>\n"
		. fin_cadre_relief(true)
		//
		////////////////////////////
		// Formulaire programmer un courrier automatique
		. "<a name='form-programmer' id='form-programmer'></a>\n"
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_timer.png", true, '', _T('spiplistes:messages_auto').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "temporiser"))
		.	(
				(empty($patron))
				? __boite_alerte(_T('spiplistes:Patron_manquant', true))
				: ""
			)
		. $boite_pour_confirmer_envoi_maintenant
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste")."#form-programmer' method='post'>\n"
		. "<table border='0' cellspacing='1' cellpadding='3' width='100%'>\n"
		. "<tr><td background='"._DIR_IMG_PACK."rien.gif' align='$spip_lang_left' class='verdana2'>\n"
		;
	if(empty($patron)) {
		$page_result .= "<p class='verdana2'>"._T('spiplistes:Patron_manquant')."</p>\n";
	}
	if ($message_auto != "oui")
		$page_result .= "<p class='verdana2'>"._T('spiplistes:Pas_de_courrier_auto_programme')."</p>\n";
	else {
		$page_result .= ""
			// petite ligne d'info si envoi programme
			. "<p class='verdana2'>"._T('spiplistes:sujet_courrier_auto')."<br />\n"
			. "<span class='spip_large'> ".$titre_message."</span></p>\n"
			. "<p class='verdana2'>"
			.	(	
					($statut == _SPIPLISTES_MONTHLY_LIST)
					?	"<strong>" . _T('spiplistes:Liste_diffusee_le_premier_de_chaque_mois') . "</strong><br />"
					:	(
						($periode > 0)
						? _T('spiplistes:Periodicite_:')._T('spiplistes:Tous_les')."  <strong>".$periode."</strong>  " 
							. spiplistes_singulier_pluriel_str_get($periode, _T('spiplistes:jour'), _T('spiplistes:jours'), false)
						: "<strong>"._T('spiplistes:Pas_de_periodicite')."</strong><br />"._T('spiplistes:Ce_courrier_ne_sera_envoye_qu_une_fois')
						)."<br />"
				)
			.	(
				(intval($maj))
				? _T('spiplistes:Dernier_envoi_le_:') . " <strong>" . affdate_heure($maj) . "</strong>"
					.	(
						($last =  round((time() - strtotime($maj)) / _SPIPLISTES_TIME_1_DAY))
							? " (".spiplistes_singulier_pluriel_str_get($last, _T('spiplistes:jour'), _T('spiplistes:jours')).")"
							: ""
					). "<br />"
				: ""
				)
			.	(
				($date_prevue || (intval($date) && (time() < strtotime($date))))
				? _T('spiplistes:prochain_envoi_prevu')." : <strong>" . affdate_heure($date_prevue ? $date_prevue : $date) . "</strong>"
					.	(
						(!$date_prevue && ($next = round((strtotime($date) - time()) / _SPIPLISTES_TIME_1_DAY)))
							? " (".spiplistes_singulier_pluriel_str_get($next, _T('spiplistes:jour'), _T('spiplistes:jours')).")"
							: ""
						)
				: ""
				)
			. "</p>\n"
			;
		if($btn_modifier_courrier_auto) {
			$page_result .= ""
				. "<p class='verdana2'>"._T('spiplistes:date_act')."<br />"
				. _T('spiplistes:env_esquel')." <em>".$patron."</em>"
				. "</p>\n"
				;
		}
	}
	$date_debut_envoi = (!empty($date_prevue) ? $date_prevue : (($date && intval($date)) ? $date : __mysql_date_time(time())));

	$page_result .= ""
		. "<tr><td background='"._DIR_IMG_PACK."rien.gif' align='$spip_lang_left' class='verdana2'>"
		. "<input type='radio' name='message_auto' value='oui' id='auto_oui' "
			. ((empty($patron) || (!$flag_editable)) ? " disabled='disabled' " : "")
			. ($auto_checked = ($message_auto=='oui' ? "checked='checked'" : ""))
			. " onchange=\"jQuery('#auto_oui_detail').show();\" />"
		. "<label for='auto_oui' ".($auto_checked ? "style='font-weight:bold;'" : "").">"._T('spiplistes:prog_env')."</label>\n"
		. "<div id='auto_oui_detail' ".((empty($patron) || (!$flag_editable)) ? "style='display:none;'" : "").">"
		. "<ul style='list-style-type:none;'>\n"
		. '<li>'._T('spiplistes:message_sujet').': <input type="text" name="titre_message" value="'.$titre_message.'" size="50" class="fondl" /> </li>'."\n"
		//
		// chrono debut de mois
		. "
<script type='text/javascript'><!--
	function auto_mois_switch(c) {
		
		jQuery('#auto_oui').click();
		if(c.checked) {
				jQuery('#periode_jours').hide();
		}
		else {
				jQuery('#periode_jours').show();
		}
		
	}
--></script>		"
		// checkbo message mensuel
		. "<li><input type='checkbox' name='auto_mois' value='oui' id='auto_mois' onchange=\"auto_mois_switch(this);\" "
			. (($statut == _SPIPLISTES_MONTHLY_LIST) ? "checked='checked'" : "")
			. " />\n"
		. "<label for='auto_mois'>"._T('spiplistes:En_debut_de_mois')."</label></li>\n"
		// 
		. "<li id='periode_jours'>"._T('spiplistes:Tous_les')." <input type='text' name='periode' value='".$periode."' size='4' class='fondl' /> "._T('info_jours')."</li>\n"
		. "<li>"._T('spiplistes:A_partir_de')." : <br />\n"
		//
		. spiplistes_dater_envoi($id_liste, true, $statut, $date_debut_envoi, 'btn_changer_date', false)."</li>\n"
		.	(
			(!$envoyer_maintenant)
			? " <li><input type='checkbox' class='checkbox' name='envoyer_maintenant' value='oui' id='em' class='fondl' "
				. " onchange=\"jQuery('#auto_oui').click();\" />"
				. "<label for='em'>"._T('spiplistes:env_maint')."</label></li>\n"
			: ""
			)
		. "</ul></div>\n"
		;
	$checked = ($message_auto=='non') ? "checked='checked'" : "";
	$page_result .= ""
		. "<br /><input type='radio' name='message_auto' value='non' id='auto_non' "
		. ((empty($patron) || (!$flag_editable)) ? " disabled='disabled' " : "")
		. $checked
		. " onchange=\"jQuery('#auto_oui_detail').hide();\" />"
		. ($checked?"<strong>":"")
		. " <label for='auto_non'>"._T('spiplistes:prog_env_non')."</label> "
		. ($checked?"</strong>":"")
		.	(
			($message_auto=='non')
			? "<script type='text/javascript'><!--
	jQuery('#auto_oui_detail').hide();
	--></script>"
			: ""
			)
		. "</td></tr>\n"
		. "<tr><td style='text-align:$spip_lang_right;'>"
		. 	(
			($id_liste)
			? "<input type='hidden' name='id_liste' value='$id_liste' />"
			: ""
			)
		.	(
			($new)
			? "<input type='hidden' name='new' value='$new' />"
			: ""
			)
		// bouton de validation
		. ((!empty($patron) && $flag_editable) ? "<input type='submit' name='btn_modifier_courrier_auto' value='"._T('bouton_valider')."' class='fondo' />" : "")
		. "</td></tr>"
		. "</table>\n"
		. "</form>"
		. fin_cadre_relief(true)
		;
	
	$page_result .= ""
		. fin_cadre_relief(true)
		;
		
	$editer_auteurs = charger_fonction('editer_auteurs','inc');
	
	$page_result .= ""
		//////////////////////////
		// Liste des abonnes
		// Appliquer les modifications sur les abonnes
		. "<a name='auteurs'></a>"
	/*
	echo $editer_auteurs('liste', $id_liste, $flag_editable, _request('cherche_auteur'), _request('ids'), 
		_T('spiplistes:liste_des_abonnes'),
		'listes',
		_SPIPLISTES_EXEC_ABONNE_EDIT);
*/
		. $editer_auteurs(
			'liste'	// $type
			, $id_liste // $id
			, $flag_editable 
			, _request('cherche_auteur') //$cherche_auteur
			, _request('ids')	// $ids
			, _T('spiplistes:abon') // $titre_boite
			//, 'listes' // $script_edit_objet
			//, 'abonne_edit'
			)
		;
		
	// le super-admin peut abonner en masse
	if($connect_toutes_rubriques) {
		$page_result .= ""
			. "\n<!-- forcer abo -->\n"
			. debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."abonner-24.png", true, '', _T('spiplistes:Forcer_les_abonnement_liste').__plugin_aide("forcerliste"))."\n"
			. "<p class='verdana2'>\n"
			. _T('spiplistes:Forcer_abonnement_desc')."<br />\n"
			. "<blockquote class='verdana2'><em>"
			. _T('spiplistes:Forcer_abonnement_aide', array('lien_retour' => generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE)))
			. "</em></blockquote></p>\n"
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste#auteurs")."' id='form_forcer_abo' name='form_forcer_abo' method='post'>\n"
			. debut_cadre_relief("", true)."\n"
			//
			//////////////////////////
			// propose de forcer les membres sauf invites si la liste est privee
			.	(
					($statut==_SPIPLISTES_PRIVATE_LIST)
					? "<div class='verdana2'><input type='radio' name='forcer_abo' value='auteurs' id='forcer_abo_tous' />\n"
						. "<label for='forcer_abo_tous'>"._T('spiplistes:Abonner_tous_les_inscrits_prives')."</label></div>\n"
					: ""
				)
			//
			// propose de forcer les invites si la liste est publique ou periodique
			.	(
					(($statut!=_SPIPLISTES_PRIVATE_LIST) && ($statut!=_SPIPLISTES_TRASH_LIST))
					? "<div class='verdana2'><input type='radio' name='forcer_abo' value='6forum' id='forcer_abo_6forum' />\n"
						. "<label for='forcer_abo_6forum'>"._T('spiplistes:Abonner_tous_les_invites_public')."</label></div>\n"
					: ""
				)
			. (
				($nb_abonnes)
				? "<hr />\n"
					. "<div class='verdana2'><input type='radio' name='forcer_abo' value='aucun' id='forcer_desabo' />\n"
					. "<label for='forcer_desabo'>"._T('spiplistes:Forcer_desabonner_tous_les_inscrits')."</label></div>\n"
				: ""
				)
			. fin_cadre_relief(true)."\n"
			. "<div style='text-align:right;'><input type='submit' name='btn_valider_forcer_abos' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
			. "</form>\n"
			. fin_cadre_enfonce (true)."\n"
		;
	}
	//
	$page_result .= ""
		. $gros_bouton_supprimer
		;
	
	echo($page_result);

	echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();

} // end exec_spiplistes_liste_gerer()


/*
	donne contenu pied_page au format html (CP-20071014)
	lien_patron: nom du tampon (fichier, sans extension)
*/
function spiplistes_pied_page_html_get ($pied_patron, $lang = "") {
	if(empty($lang)) $lang = $GLOBALS['spip_lang'];
	$contexte_patron = array('lang'=>$lang);
	include_spip('public/assembler');
	$result = recuperer_fond(_SPIPLISTES_PATRONS_PIED_DIR.$pied_patron, $contexte_patron);
	return($result);
} // end spiplistes_pied_page_html_get()


/******************************************************************************************/
/* SPIP-listes est un systa�me de gestion de listes d'abonnes et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a� la Licence Publique Geneale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir rea�u une copie de la Licence Publique Generale GNU                    */
/* en ma�me temps que ce programme ; si ce n'est pas le cas, ecrivez a� la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

?>