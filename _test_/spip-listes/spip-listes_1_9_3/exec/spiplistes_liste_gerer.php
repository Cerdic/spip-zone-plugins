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

	// Precision sur la table spip_listes:
	// 'date': date d'envoi souhaitee
	// 'maj': date d'envoi du courrier mis a� jour par cron.
	// 'description': (pas utilise au 20071006)
	// 'texte': description affichee dans formulaire abonnement
	
function exec_spiplistes_liste_gerer () {

	include_spip('inc/autoriser');
	include_spip('inc/mots');
	include_spip('inc/lang');
	include_spip('inc/editer_auteurs');
	include_spip('base/spiplistes_tables');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_dater_envoi');
	include_spip('inc/spiplistes_naviguer_paniers');
	include_spip('inc/spiplistes_afficher_auteurs');
	include_spip('inc/spiplistes_listes_selectionner_auteur');
	
	global $meta
		, $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_lang_left
		, $spip_lang_right
		, $couleur_claire
		;

	// initialise les variables postees par le formulaire
	foreach(array(
		'new'	// nouvelle liste si 'oui'
		, 'id_liste'// si modif dans l'editeur
		, 'btn_liste_edit', 'titre', 'texte', 'pied_page' // renvoyes par l'editeur
		, 'btn_modifier_diffusion', 'changer_lang', 'statut' // local
		, 'btn_modifier_replyto', 'email_envoi' // local
		, 'btn_modifier_courrier_auto', 'message_auto' // local
			, 'auto_chrono', 'auto_weekly', 'auto_mois'
			, 'titre_message', 'patron', 'periode', 'envoyer_maintenant'
			, 'jour', 'mois', 'annee', 'heure', 'minute'
		, 'btn_patron_pied', 'btn_grand_patron' // boites gauches
		, 'btn_valider_forcer_abos', 'forcer_abo', 'forcer_format_abo', 'forcer_format_reception'
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

	$cherche_auteur = _request('cherche_auteur');
	$debut = _request('debut');


	$envoyer_maintenant = ($envoyer_maintenant == 'oui');
	
	$boite_pour_confirmer_envoi_maintenant = 
		$grosse_boite_moderateurs = 
		$message_erreur =
		$page_result = "";

	if(!$id_liste) {
	//////////////////////////////////////////////////////
	// Creer une liste
	////
		// admin lambda peut creer une liste
		$flag_editable = ($connect_statut == "0minirezo");
		
		if ($btn_liste_edit && ($new=='oui')) {
			
			if ($titre == '') {
				$titre = _T('spiplistes:liste_sans_titre');
			}
			
			$pied_page = spiplistes_pied_de_page_liste(0, $GLOBALS['spip_lang']);
			
			$id_liste = spiplistes_listes_liste_creer(_SPIPLISTES_PRIVATE_LIST, $GLOBALS['spip_lang']
				, $titre, $texte, $pied_page);
		} 
		spiplistes_log("LISTE ID #$id_liste added by ID_AUTEUR #$connect_id_auteur");
	}
	else if($id_liste > 0) {
	//////////////////////////////////////////////////////
	// Modifier une liste
	////
		// les admins toutes rubriques et le moderateur seuls peuvent modifier la liste
		$flag_editable = autoriser('moderer', 'liste', $id_liste);

		if($flag_editable) {
		
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
			// A noter, ne pas preparer les valeurs par sql_quote()
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
				$pied_page = spiplistes_pied_page_html_get($patron);
				$sql_champs['pied_page'] = $pied_page;
			}
			
			// Modifier diffusion ?
			if($btn_modifier_diffusion) {
				// Modifier le statut ?
				if(in_array($statut, explode(";", _SPIPLISTES_LISTES_STATUTS_TOUS)) 
					&& ($statut!=$current_liste['statut'])
				) {
					$sql_champs['statut'] = $statut;
					// si la liste passe en privee, retire les invites
					if($statut == _SPIPLISTES_PRIVATE_LIST) {
						$auteur_statut = '6forum';
						spiplistes_abonnements_auteurs_supprimer($auteur_statut);
						spiplistes_log("AUTEURS ($auteur_statut) REMOVED FROM LISTE #$id_liste ($statut) BY ID_AUTEUR #$connect_id_auteur");
					}
				}
				// Modifier la langue ?
				if(!empty($lang) && ($lang!=$current_liste['lang'])) {
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
			
				$envoyer_quand = spiplistes_formate_date_form($annee, $mois, $jour, $heure, $minute);
			
				if(time() > strtotime($envoyer_quand)) {
				// envoi dans le passe est considere comme envoyer maintenant
					$envoyer_maintenant = true;
					$date_depuis = $envoyer_quand;
					$envoyer_quand = false;
				}
				
				if($envoyer_maintenant && ($message_auto != 'non')) {
					$boite_pour_confirmer_envoi_maintenant = ""
						. debut_cadre_couleur('', true)
						// formulaire de confirmation envoi
						. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_LISTES_LISTE), true)
						. "<p style='text-align:center;font-weight:bold;' class='verdana2'>"
						. _T('spiplistes:boite_confirmez_envoi_liste')	. "</p>"
						. "<input type='hidden' name='id_liste' value='$id_liste' />"
						. spiplistes_form_bouton_valider('btn_confirmer_envoi_maintenant')
						. spiplistes_form_fin(true)
						. fin_cadre_couleur(true)
						;
					$date_prevue = normaliser_date(time());
				}

				if($message_auto == 'oui') {
				
					$sql_champs['message_auto'] = 'oui';
					$sql_champs['titre_message'] = $titre_message;
					$sql_champs['date'] = (!$envoyer_maintenant) ? $envoyer_quand : '';
					
					switch($auto_chrono) {
						case 'auto_jour':
							$sql_champs['statut'] = _SPIPLISTES_DAILY_LIST;
							// force au minimum 1 jour
							$sql_champs['periode'] = (($periode > 0) ? $periode : 1);
							break;
						case 'auto_hebdo':
							if($auto_weekly == 'oui') {
								// debut de semaine ?
								$sql_champs['statut'] = _SPIPLISTES_WEEKLY_LIST;
								// corrige la date pour le lundi de la semaine
								$time = strtotime($envoyer_quand);
								$time = mktime(0,0,0,date("m", $time),date("d", $time)-date("w", $time)+1,date("Y", $time));
    							$envoyer_quand = date("Y-m-d H:i:s", $time);
	 							$sql_champs['date'] = $envoyer_quand;
							} else {
								$sql_champs['statut'] = _SPIPLISTES_HEBDO_LIST;
							}
							$sql_champs['periode'] = 0;
							break;
						case 'auto_mensuel':
							if($auto_mois == 'oui') {
								// debut du mois ?
								$sql_champs['statut'] = _SPIPLISTES_MONTHLY_LIST;
								// corrige la date, 1' du mois
								$envoyer_quand = substr($envoyer_quand, 0, 8)."01 00:00:00";
								$sql_champs['date'] = $envoyer_quand;
							} else {
								$sql_champs['statut'] = _SPIPLISTES_MENSUEL_LIST;
							}
							$sql_champs['periode'] = 0;
							break;
						case 'auto_an':
							$sql_champs['statut'] = _SPIPLISTES_YEARLY_LIST;
							$sql_champs['periode'] = 0;
							break;
					}
				}
				else if($message_auto == 'non') {
					$sql_champs['message_auto'] = 'non';
					$sql_champs['date'] = '';
					$sql_champs['periode'] = 0;
				}
			} // end if($btn_modifier_courrier_auto)
			
			// Enregistre les modifs pour cette liste
			if(count($sql_champs)) {
				sql_updateq("spip_listes", $sql_champs, "id_liste=".sql_quote($id_liste)." LIMIT 1");
			}
			
			// Forcer les abonnements
			if($btn_valider_forcer_abos && $forcer_abo && in_array($forcer_abo, array('tous', 'auteurs', '6forum', 'aucun'))) {
				
				$forcer_format_reception = 
					(($forcer_format_abo == 'oui') && in_array($forcer_format_reception, explode(';', _SPIPLISTES_FORMATS_ALLOWED)))
					? $forcer_format_reception
					: false
					;
				include_spip("inc/spiplistes_listes_forcer_abonnement");
				if(spiplistes_listes_forcer_abonnement ($id_liste, $forcer_abo, $forcer_format_reception) ===  false) {
					$message_erreur .= spiplistes_boite_alerte(_T('spiplistes:Forcer_abonnement_erreur'), true);
				}
			}
			
		} // end if($flag_editable)
	}

	//////////////////////////////////////////////////////
	// Recharge les donnees la liste

	$sql_select_array = array('id_liste', 'titre', 'texte'
			, 'titre_message', 'pied_page', 'date', 'statut', 'maj'
			, 'email_envoi', 'message_auto', 'periode', 'patron', 'lang');

	if($row = spiplistes_listes_liste_fetsel($id_liste, $sql_select_array)) {
		foreach($sql_select_array as $key) {
			// initialise les variables du resultat SQL
			$$key = $row[$key];
		}
	}

	// les supers-admins et le moderateur seuls peuvent modifier la liste
	$flag_editable = autoriser('moderer', 'liste', $id_liste);

	$titre_message = ($titre_message=='') ? $titre._T('spiplistes:_de_').$meta['nom_site'] : $titre_message;

	$nb_abonnes = spiplistes_listes_nb_abonnes_compter($id_liste);

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
			
		// la grosse boite des abonnes
		$tri = _request('tri') ? _request('tri') : 'nom';
		$boite_liste_abonnes = spiplistes_listes_boite_abonnements(
			$id_liste, $statut, $tri, $debut, _SPIPLISTES_EXEC_LISTE_GERER
			);
		$titre_boite = _T('spiplistes:abos_cette_liste');
		$nb = spiplistes_listes_nb_abonnes_compter($id_liste);
		$legend = _T('spiplistes:nbre_abonnes').$nb;
		$legend = "<small id='legend-abos1'>".spiplistes_nb_abonnes_liste_str_get($id_liste)."</small>";
		$grosse_boite_abonnements = ""
			. "<!-- boite abonnes/elligibles -->\n"
			. debut_cadre_enfonce("auteur-24.gif", true, "", $titre_boite)
			. spiplistes_bouton_block_depliable($legend
				, false, md5('abonnes_liste'))
			. (spiplistes_spip_est_inferieur_193() ? $legend : "")
			. spiplistes_debut_block_invisible(md5('abonnes_liste'))
			. debut_cadre_relief('', true)
			. $boite_liste_abonnes
			. fin_cadre_relief(true)
			. fin_block()
			. fin_cadre_enfonce(true)
			. "<!-- fin boite abonnes/elligibles -->\n"
			;

		// la grosse boite des moderateurs
		$boite_liste_moderateurs = spiplistes_listes_boite_moderateurs(
			$id_liste, _SPIPLISTES_EXEC_LISTE_GERER, 'mods-conteneur'
			);
		$titre_boite = _T('spiplistes:mods_cette_liste');
		$nb = spiplistes_mod_listes_compter($id_liste);
		$legend = _T('spiplistes:nbre_mods').$nb;
		$legend = "<small>".spiplistes_nb_moderateurs_liste_str_get($nb)."</small>";
		$grosse_boite_moderateurs = ""
			. "<!-- boite moderateurs -->\n"
			. debut_cadre_enfonce("redacteurs-24.gif", true, "", $titre_boite)
			. spiplistes_bouton_block_depliable($legend
				, false, md5('mods_liste'))
			. (spiplistes_spip_est_inferieur_193() ? $legend : "")
			. spiplistes_debut_block_invisible(md5('mods_liste'))
			. debut_cadre_relief('', true)
			. "<div id='mods-conteneur'>\n"
			. $boite_liste_moderateurs
			. "</div>\n"
			. fin_cadre_relief(true)
			. fin_block()
			. fin_cadre_enfonce(true)
			. "<!-- fin boite moderateurs -->\n"
			;

	}
	else {
		$gros_bouton_modifier = $gros_bouton_supprimer = $grosse_boite_abonnements = "";
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	// bloc jQuery pour interactivite du formulaire
	$page_result .= "
<script type='text/javascript'>
//<![CDATA[
	jQuery(document).ready(function() {
		var alerter_modif_statut = false;
		$('#change_statut').change(function() {
			switch($(this).val()) {
				case '"._SPIPLISTES_PRIVATE_LIST."':
					if(!alerter_modif_statut) { 
						alert('".spiplistes_texte_html_2_iso(_T('spiplistes:Attention_action_retire_invites'), $GLOBALS['meta']['charset'], true)."'); 
						alerter_modif_statut=true; 
					}
					$('#img_statut').attr('src','".spiplistes_items_get_item("puce", _SPIPLISTES_PRIVATE_LIST)."');
					break;
				case '"._SPIPLISTES_PUBLIC_LIST."':
					$('#img_statut').attr('src','".spiplistes_items_get_item("puce", _SPIPLISTES_PUBLIC_LIST)."');
					break;
				case '"._SPIPLISTES_TRASH_LIST."':
					$('#img_statut').attr('src','".spiplistes_items_get_item("puce", _SPIPLISTES_TRASH_LIST)."');
					break;
			}
		});
		// interactivite bloc planification
		$('#auto_oui').change(function(){
			$('#auto_oui_detail').toggle();
		});
		$('#auto_non').change(function(){
			$('#auto_oui_detail').toggle();
		});
		$('input[@name=auto_chrono]').change(function(){
			$('#auto_weekly').attr('checked',false);
			$('#auto_mois').attr('checked',false);
		});
		$('input[@name=periode]').focus(function(){
			$('#auto_weekly').attr('checked',false);
			$('#auto_mois').attr('checked',false);
			$('input[@name=auto_chrono][@value=auto_jour]').attr('checked','checked');
		});
		$('#auto_weekly').change(function(){
			$('input[@name=periode]').val('0');
			$('#auto_mois').attr('checked',false);
			$('input[@name=auto_chrono][@value=auto_hebdo]').attr('checked','checked');
		});
		$('#auto_mois').change(function(){
			$('input[@name=periode]').val('0');
			$('#auto_weekly').attr('checked',false);
			$('input[@name=auto_chrono][@value=auto_mensuel]').attr('checked','checked');
		});
		/*
		* forcer le format de reception ? 
		*/
		$('input[@name=forcer_abo]').click( function() { 
			if($(this).val() == 'aucun') {
				$('#forcer_format').hide();
			}
			else {
				$('#forcer_format').show();
			}
		});
		$('input[@name=forcer_format_reception]').click( function() { 
			$('#forcer_format_abo').attr('checked','checked');
		});
		// sparadra !
		// fait disparaitre la legende si deplie'
		var sparadra;
		$('#triangle1').click( function() { 
			if(!sparadra) {
				$('#legend-abos1').html('');
				sparadra = true;
			}
			else {
				$('#legend-abos1').html($('#legend-abos1-propre').html());
				sparadra = false;
			}
		});
	});
//]]>
</script>
		"
		;
	// fin bloc jQuery
		
	$titre_page = _T('spiplistes:gestion_dune_liste');
	// Permet entre autres d'ajouter les classes a la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "liste_gerer";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spip_listes') . " - " . $titre_page, $rubrique, $sous_rubrique));

	// la gestion des listes de courriers est reservee aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result .= ""
		. "<br /><br />\n"
		. spiplistes_gros_titre($titre_page, '', true)
		. spiplistes_onglets(_SPIPLISTES_RUBRIQUE, $sous_rubrique)
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
		. spiplistes_boite_autocron()
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		. $message_erreur
		;

	changer_typo('','liste'.$id_liste);

	// message alerte et demande de confirmation si supprimer liste
	if(($btn_supprimer_liste > 0) && ($btn_supprimer_liste == $id_liste)) {
		$page_result .= ""
			. spiplistes_boite_alerte (_T('spiplistes:Attention_suppression_liste')."<br />"._T('spiplistes:Confirmez_requete'), true)
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
	$email_defaut = entites_html($meta['email_webmaster']);
	$email_envoi = (email_valide($email_envoi)) ? $email_envoi : $email_defaut ;

	$page_result .= ""
		//. debut_cadre_relief("racine-site-24.gif", true)
		. debut_cadre_relief("racine-site-24.gif", true, '', _T('spiplistes:Diffusion').spiplistes_plugin_aide(_SPIPLISTES_EXEC_AIDE, "diffusion"))
		//
		////////////////////////////
		// Formulaire diffusion
		.	(
			($flag_editable)
			? ""
				. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste"), true)
				. "<input type='hidden' name='exec' value='listes' />\n"
				. "<input type='hidden' name='id_liste' value='$id_liste' />\n"
			: ""
			)
		. "<span class='verdana2'>". _T('spiplistes:Cette_liste_est')." : "
		. 	spiplistes_bullet_titre_liste('puce', $statut, 'img_statut', true)."</span>\n"
		.	(
			($flag_editable)
			? ""
				. "<select class='verdana2 fondl' name='statut' size='1' id='change_statut'>\n"
				. "<option" . mySel(_SPIPLISTES_PRIVATE_LIST, $statut) ." style='background-color: white'>"._T('spiplistes:statut_interne')."</option>\n"
				. "<option" . mySel(_SPIPLISTES_PUBLIC_LIST, $statut) . " style='background-color: #B4E8C5'>"._T('spiplistes:statut_publique')."</option>\n"
				. "<option" . mySel(_SPIPLISTES_TRASH_LIST, $statut) . " style='background:url("._DIR_IMG_PACK."rayures-sup.gif)'>"._T('texte_statut_poubelle')."</option>\n"
				. "</select>\n"
			: "<span class='verdana2' style='font-weight:bold;'>".spiplistes_items_get_item("alt", $statut)."</span>\n"
			)
		. "<div style='margin:10px 0px;'>\n"
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
				? spiplistes_form_bouton_valider('btn_modifier_diffusion')
					. spiplistes_form_fin(true)
				: ""
			)
		. fin_cadre_relief(true)
		;
		
		////////////////////////////
		// Formulaire adresse email pour le reply-to
	$page_result .= ""
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply_to-24.png", true, '', _T('spiplistes:adresse_de_reponse').spiplistes_plugin_aide(_SPIPLISTES_EXEC_AIDE, "replyto"))
		. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste"), true)
		. "<p class='verdana2'>\n"
		. _T('spiplistes:adresse_mail_retour').":<br />\n"
		.	(
			($flag_editable)
			?	""
				. ""._T('spiplistes:adresse')."</p>\n"
				. "<div style='text-align:center'>\n"
				. "<input type='text' name='email_envoi' value=\"".$email_envoi."\" size='40' class='fondl' /></div>\n"
				. spiplistes_form_bouton_valider('btn_modifier_replyto')
			: "</p><p style='font-weight:bold;text-align:center;'>$email_envoi</p>\n"
			)
		. spiplistes_form_fin(true)
		. fin_cadre_relief(true)
		;
		
		////////////////////////////
		// Formulaire planifier un courrier automatique
	$page_result .= ""
		. "<a name='form-programmer' id='form-programmer'></a>\n"
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_timer.png", true, '', _T('spiplistes:messages_auto')
			. spiplistes_plugin_aide(_SPIPLISTES_EXEC_AIDE, "temporiser"))
		;
	if(empty($patron)) {
		$page_result .= spiplistes_boite_alerte(_T('spiplistes:Patron_manquant'), true);
	}
	$page_result .= ""
		. $boite_pour_confirmer_envoi_maintenant
		. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste")."#form-programmer", true)
		. "<table border='0' cellspacing='1' cellpadding='3' width='100%'>\n"
		. "<tr><td align='$spip_lang_left' class='verdana2'>\n"
		;
	if ($message_auto != "oui") {
		$page_result .= "<div class='verdana2'>"._T('spiplistes:Pas_de_courrier_auto_programme')."</div>\n";
	}
	else {
		$page_result .= ""
			// petite ligne d'info si envoi programme
			. "<p class='verdana2'>"._T('spiplistes:sujet_courrier_auto')."<br />\n"
			. "<span class='spip_large'> ".$titre_message."</span></p>\n"
			. "<p class='verdana2'>"
			. spiplistes_items_get_item('alt', $statut)
			.	(	
					($statut == _SPIPLISTES_MONTHLY_LIST)
					?	"<strong>" . _T('spiplistes:Liste_diffusee_le_premier_de_chaque_mois') . "</strong><br />"
					:	""
				)
			.	(
					($periode > 0)
					? _T('spiplistes:Periodicite_:')._T('spiplistes:Tous_les')."  <strong>".$periode."</strong>  " 
						. spiplistes_singulier_pluriel_str_get($periode, _T('spiplistes:jour'), _T('spiplistes:jours'), false)
					: ""
				)
			.	(
					(!in_array($statut, explode(";", _SPIPLISTES_LISTES_STATUTS_PERIODIQUES)))
					? " <strong>"._T('spiplistes:Pas_de_periodicite')."</strong><br />"
						._T('spiplistes:Ce_courrier_ne_sera_envoye_qu_une_fois')
					: ""
				)
			.	"<br />"
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
	$date_debut_envoi = (!empty($date_prevue) ? $date_prevue : (($date && intval($date)) ? $date : normaliser_date(time())));

	$page_result .= ""
		. "</td>\n"
		. "</tr>\n"
		;
	if($flag_editable) {
		$page_result .= ""
			. "<tr><td align='$spip_lang_left' class='verdana2'>"
			. "<input type='radio' name='message_auto' value='oui' id='auto_oui' "
				. (empty($patron) ? " disabled='disabled' " : "")
				. ($auto_checked = ($message_auto=='oui' ? "checked='checked'" : ""))
				. " />"
			. "<label for='auto_oui' ".($auto_checked ? "style='font-weight:bold;'" : "").">"
				. _T('spiplistes:prog_env')."</label>\n"
			. "<div id='auto_oui_detail' "
				.((empty($patron) || !$auto_checked) ? "style='display:none;'" : "")
				.">"
			. "<ul style='list-style-type:none;'>\n"
			. "<li>"._T('spiplistes:message_sujet')
			. ': <input type="text" name="titre_message" value="'.$titre_message.'" size="50" class="fondl" /> </li>'."\n"
			;
			// 
			// chrono jour
			$ii = ($periode > 0) ? $periode : 1;
		$page_result .= ""
			. "<li style='margin-top:0.5em'>"
				. spiplistes_form_input_radio ('auto_chrono', 'auto_jour'
					, ''
					, ($statut == _SPIPLISTES_DAILY_LIST)
					, true, false
					)
				. _T('spiplistes:Tous_les')
				. " <input type='text' name='periode' value='".$ii."' size='4' maxlength='4' class='fondl' /> "
				. _T('info_jours')
				. "</li>\n"
			// chrono hebdo
			. "<li>"
				. spiplistes_form_input_radio ('auto_chrono', 'auto_hebdo'
					, _T('spiplistes:Toutes_les_semaines')
					, (($statut == _SPIPLISTES_HEBDO_LIST) || ($statut == _SPIPLISTES_WEEKLY_LIST))
					, true, false)
				. spiplistes_form_input_checkbox('auto_weekly', 'oui'
					, _T('spiplistes:en_debut_de_semaine'), ($statut == _SPIPLISTES_WEEKLY_LIST), true, false)
				. "</li>\n"
			// chrono mois
			. "<li>"
				. spiplistes_form_input_radio ('auto_chrono', 'auto_mensuel'
					, _T('spiplistes:Tous_les_mois')
					, (($statut == _SPIPLISTES_MENSUEL_LIST) || ($statut == _SPIPLISTES_MONTHLY_LIST))
					, true, false)
				. spiplistes_form_input_checkbox('auto_mois', 'oui'
					, _T('spiplistes:en_debut_de_mois'), ($statut == _SPIPLISTES_MONTHLY_LIST), true, false)
				. "</li>\n"
			// chrono annee
			. "<li>"
				. spiplistes_form_input_radio ('auto_chrono', 'auto_an'
					, _T('spiplistes:Tous_les_ans')
					, ($statut == _SPIPLISTES_YEARLY_LIST)
					, true, false)
				. "</li>\n"
			. "<li style='margin-top:0.5em'>"._T('spiplistes:A_partir_de')." : <br />\n"
			//
			. spiplistes_dater_envoi(
				'liste', $id_liste, $statut
				, $flag_editable
				, _T('spiplistes:date_expedition_')
				, $date_debut_envoi, 'btn_changer_date'
				, false
				)
			. "</li>\n"
			.	(
				(!$envoyer_maintenant)
				? " <li>"
					. spiplistes_form_input_checkbox('envoyer_maintenant', 'oui'
						, _T('spiplistes:env_maint'), false, true)
					. "</li>\n"
				: ""
				)
			. "</ul></div>\n"
			;
		$checked = ($message_auto=='non') ? "checked='checked'" : "";
		$class = $checked ? "class='bold'" : "";
		$disabled = (empty($patron) ? " disabled='disabled' " : "");
		$page_result .= ""
			. "<br /><input type='radio' name='message_auto' value='non' id='auto_non' $disabled $checked />"
			. "<span $class >"
			. " <label for='auto_non'>"._T('spiplistes:prog_env_non')."</label> "
			. "</span>\n"
			. "</td></tr>\n"
			;

		$page_result .= ""
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
			. (!empty($patron) 
				? spiplistes_form_bouton_valider('btn_modifier_courrier_auto', _T('bouton_valider'), true)
				: "")
			. "</td></tr>"
			;
	}
	$page_result .= ""
		. "</table>\n"
		. spiplistes_form_fin(true)
		. fin_cadre_relief(true)
		;
		// fin formulaire planifier
		
	$page_result .= ""
		. fin_cadre_relief(true)
		. $grosse_boite_abonnements
		. $grosse_boite_moderateurs
		;
	
	// le super-admin peut abonner en masse
	if($connect_toutes_rubriques) {
		$page_result .= ""
			. "\n<!-- forcer abo -->\n"
			. debut_cadre_enfonce(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."abonner-24.png", true, '', _T('spiplistes:forcer_les_abonnement_liste').spiplistes_plugin_aide("forcerliste"))."\n"
			. "<p class='verdana2'>\n"
			. _T('spiplistes:forcer_abonnement_desc')
			. "</p>\n"
			. "<p class='verdana2' style='margin-bottom:1em'><em>"
			. _T('spiplistes:forcer_abonnement_aide', array('lien_retour' => generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE)))
			. "</em></p>\n"
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste#auteurs")."' id='form_forcer_abo' name='form_forcer_abo' method='post'>\n"
			. debut_cadre_relief("", true)."\n"
			//
			//////////////////////////
			// propose de forcer les membres sauf invites si la liste est privee
			.	(
					($statut==_SPIPLISTES_PRIVATE_LIST)
					? "<div class='verdana2'><input type='radio' name='forcer_abo' value='auteurs' id='forcer_abo_tous' />\n"
						. "<label for='forcer_abo_tous'>"._T('spiplistes:Abonner_tous_les_inscrits_prives')."</label>"
						. "</div>\n"
						. spiplistes_boutons_forcer_format('forcer_format', _T('spiplistes:forcer_abonnements_nouveaux'))
					: ""
				)
			//
			// propose de forcer les invites si la liste est publique ou periodique
			.	(
					(($statut!=_SPIPLISTES_PRIVATE_LIST) && ($statut!=_SPIPLISTES_TRASH_LIST))
					? "<div class='verdana2'><input type='radio' name='forcer_abo' value='6forum' id='forcer_abo_6forum' />\n"
						. "<label for='forcer_abo_6forum'>"._T('spiplistes:Abonner_tous_les_invites_public')."</label></div>\n"
						. spiplistes_boutons_forcer_format('forcer_format', _T('spiplistes:forcer_abonnements_nouveaux'))
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

	echo spiplistes_html_signature(_SPIPLISTES_PREFIX), fin_gauche(), fin_page();

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

/*
 * Boite de confirmation pour forcer le format de reception
 * @return 
 * @param $id_bloc Object
 * @param $message Object[optional]
 */*/
function spiplistes_boutons_forcer_format ($id_bloc, $message = '') {
	if(!empty($message)) {
		$message = "<div>$message</div>\n";
	}
	return(
		"<div id='$id_bloc' class='verdana2' style='margin-left:2ex; display:none'>"
		. $message
		. "<label>"
		. "<input type='checkbox' name='forcer_format_abo' id='forcer_format_abo' value='oui' />"
		. _T('spiplistes:forcer_les_abonnements_au_format_')
		. "</label>\n"
		. "<label>"
		. "<input type='radio' name='forcer_format_reception' value='html' checked='checked' />" . _T('spiplistes:html')
		. "</label>\n"
		. "<label>"
		. "<input type='radio' name='forcer_format_reception' value='texte' />" . _T('spiplistes:texte')
		. "</label>\n"
		. "</div>\n"
	);
}

/*
 * mmmm.. (CP-20081115) semble bien lourd pour juste une boite d'alerte.
 * 
 * @return une chaine traduite de HTML en ISO
 * @param $string Object
 * @param $charset Object
 * @param $unspace Object[optional]
 */*/
function spiplistes_texte_html_2_iso($string, $charset, $unspace = false) {
	$charset = strtoupper($charset);
	// html_entity_decode a qq soucis avec UTF-8
	if($charset=="UTF-8" && (phpversion()<5)) {
		$string = spiplistes_html_entity_decode_utf8($string);
	}
	else {
		$string = html_entity_decode($string, ENT_QUOTES, $charset);
	}
	if($unspace) {
		// renvoie sans \r ou \n pour les boites d'alerte javascript
		$string = preg_replace("/([[:space:]])+/", " ", $string);
	}
	return ($string);
}

// http://fr.php.net/html_entity_decode
	// thank to: laurynas dot butkus at gmail dot com
function spiplistes_html_entity_decode_utf8($string)
{
	 static $trans_tbl;
	
	 // replace numeric entities
	 $string = preg_replace('~&#x([0-9a-f]+);~ei', 'spiplistes_code2utf(hexdec("\\1"))', $string);
	 $string = preg_replace('~&#([0-9]+);~e', 'spiplistes_code2utf(\\1)', $string);

	 // replace literal entities
	 if (!isset($trans_tbl))
	 {
		  $trans_tbl = array();
		 
		  foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
				$trans_tbl[$key] = utf8_encode($val);
	 }
	
	 return strtr($string, $trans_tbl);
} // spiplistes_html_entity_decode_utf8()

// Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
// thank to: akniep at rayo dot info
function spiplistes_code2utf($number)  {
	spiplistes_log("####");
	static $windows_illegals_chars;
	if($windows_illegals_chars === null) {
		$windows_illegals_chars = array(
			128 => 8364
            , 129 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 130 => 8218
            , 131 => 402
            , 132 => 8222
            , 133 => 8230
            , 134 => 8224
            , 135 => 8225
            , 136 => 710
            , 137 => 8240
            , 138 => 352
            , 139 => 8249
            , 140 => 338
            , 141 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 142 => 381
            , 143 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 144 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 145 => 8216
            , 146 => 8217
            , 147 => 8220
            , 148 => 8221
            , 149 => 8226
            , 150 => 8211
            , 151 => 8212
            , 152 => 732
            , 153 => 8482
            , 154 => 353
            , 155 => 8250
            , 156 => 339
            , 157 => 160 // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            , 158 => 382
            , 159 => 376
		);
	}
	
    if ($number < 0)
        return FALSE;
    if ($number < 128)
        return chr($number);
    // Removing / Replacing Windows Illegals Characters
    if ($number < 160) {
    	$number = $windows_illegals_chars[$number];
    }
   
    if ($number < 2048)
        return chr(($number >> 6) + 192) . chr(($number & 63) + 128);
    if ($number < 65536)
        return chr(($number >> 12) + 224) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
    if ($number < 2097152)
        return chr(($number >> 18) + 240) . chr((($number >> 12) & 63) + 128) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
   
    return(false);
} //spiplistes_code2utf()

// !(PHP 4 >= 4.3.0, PHP 5)
if(!function_exists("html_entity_decode")) {
	function html_entity_decode ($string, $quote_style = "", $charset = "")
	{
		// Remplace les entites numeriques
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// Remplace les entites 
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}
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