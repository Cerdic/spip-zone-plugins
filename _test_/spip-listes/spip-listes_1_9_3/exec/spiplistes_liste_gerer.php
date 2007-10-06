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

// _SPIPLISTES_EXEC_LISTE_GERER

if (!defined("_ECRIRE_INC_VERSION")) return;

	// Précision sur la table spip_listes:
	// 'date': date d'envoi souhaitée
	// 'maj': date d'envoi du courrier mis à jour par cron.
	// 'description': (pas utilisé au 20071006)
	// 'texte': description affichée dans formulaire abonnement
	
function exec_spiplistes_liste_gerer () {

	include_spip('inc/presentation');
	include_spip('inc/mots');
	include_spip('inc/lang');
	include_spip('inc/affichage');
	include_spip('base/spip-listes');
	include_spip('inc/spiplistes_dater_envoi');
	
	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_lang_left,$spip_lang_right
		;

	// initialise les variables postées par le formulaire
	foreach(array(
		'new'	// nouvelle liste si 'oui'
		, 'id_liste'// si modif dans l'éditeur
		, 'btn_liste_edit', 'titre', 'texte', 'pied_page' // renvoyés par l'éditeur
		, 'btn_modifier_diffusion', 'changer_lang', 'statut' // local
		, 'btn_modifier_replyto', 'email_envoi' // local
		, 'btn_modifier_courrier_auto', 'message_auto' // local
			, 'patron', 'periode', 'envoyer_direct'
			, 'jour', 'mois', 'annee', 'heure', 'minute'
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
 
	if($id_liste==0) {
	//////////////////////////////////////////////////////
	// Creer une liste
	////
		if ($btn_liste_edit && ($new=='oui')) {
			if ($titre=='') $titre = _T('spiplistes:liste_sans_titre');
	
			spip_query("INSERT INTO spip_listes (statut, date, lang, titre, texte) 
				VALUES ('"._SPIPLISTES_PRIVATE_LIST."', NOW(),".$GLOBALS['spip_lang'].","._q($titre).","._q($texte).")");
			$id_liste = spip_insert_id();
			//Auteur de la liste (moderateur)
			spip_query("DELETE FROM spip_auteurs_mod_listes WHERE id_liste = "._q($id_liste));
			spip_query("INSERT INTO spip_auteurs_mod_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
			//abonne le moderateur à sa liste
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
		
			$sql_query = "";

			// Récupère les données de la liste courante pour optimiser l'update
			$sql_select = "statut,titre,date,lang";
			if($row = spip_fetch_array(spip_query("SELECT $sql_select FROM spip_listes WHERE id_liste=$id_liste LIMIT 1"))) {
				foreach(explode(",", $sql_select) as $key) {
					$current_liste[$key] = $row[$key];
				}
			}
			
			// Retour de l'éditeur ?
			if($btn_liste_edit) {
				$titre = corriger_caracteres($titre);
				$texte = corriger_caracteres($texte);
				if(empty($titre)) {
					$titre = filtrer_entites(_T('spiplistes:Nouvelle_liste_de_diffusion'));
				}
				$sql_query .= "titre="._q($titre).",texte="._q($texte).",";
			}
			
			// Modifier diffusion ?
			if($btn_modifier_diffusion) {
spiplistes_log("LISTE MODIF: btn_modifier_diffusion <<$statut", LOG_DEBUG);
				// Modifier le statut ?
				if(in_array($statut, explode(";", _SPIPLISTES_LISTES_STATUTS)) && ($statut!=$current_liste['statut'])) {
					$sql_query .= "statut='$statut',";
				}
				// Modifier la langue ?
				if(!empty($lang) && ($lang!=$current_liste['lang'])) {
spiplistes_log("LISTE MODIF: btn_modifier_diffusion $lang", LOG_DEBUG);
					$sql_query .= "lang='$lang',";
				}
			}
			
			// Modifier l'adresse email de réponse ?
			if($btn_modifier_replyto && email_valide($email_envoi) && ($email_envoi!=$current_liste['email_envoi'])) {
				$sql_query .= "email_envoi="._q($email_envoi).",";
			}

			// Modifier message_auto ?
			if($btn_modifier_courrier_auto){
spiplistes_log("LISTE MODIF: btn_modifier_courrier_auto", LOG_DEBUG);
				$sql_query = "";
				$titre_message = spiplistes_titre_propre($titre_message);
				switch($message_auto) {
					case 'oui':
						$sql_query .= "message_auto='oui',titre_message="._q($titre_message).",periode=$periode,";
						if(!empty($jour) && !empty($mois) && !empty($annee) && (intval($heure) >= 0) && (intval($minute) >= 0)) {
							$envoyer_quand = spiplistes_formate_date_form($annee, $mois, $jour, $heure, $minute);
							$sql_query .= "date='$envoyer_quand',";
						}
						if($auto_mois == 'oui') {
							$sql_query .= "statut='"._SPIPLISTES_MONTHLY_LIST."',";
						}
						break;
					case 'non':
						$sql_query .= "message_auto='non',titre_message='',date='',periode=0";
						break;
					case 'envoyer_maintenant':
						$sql_query .= "message_auto='non',date=NOW(),periode=0,"; 
						// la trieuse s'occupera du reste
						break;
				}
			}

			// Enregistre les modifs
			if(!empty($sql_query)) {
				$sql_query = rtrim($sql_query,",");
				$sql_query = "UPDATE spip_listes SET $sql_query WHERE id_liste=$id_liste LIMIT 1";
				$sql_result = spip_query($sql_query);
			}
		}
	}

	//////////////////////////////////////////////////////
	// Recharge les données la liste
	$result = spip_query("SELECT * FROM spip_listes WHERE id_liste="._q($id_liste)." LIMIT 1");

	if($row = spip_fetch_array($result)) {
		foreach(array(
		// initialise les variables du résultat SQL
			'id_liste', 'titre', 'texte'
			, 'titre_message', 'pied_page', 'date', 'statut', 'maj'
			, 'email_envoi', 'message_auto', 'periode', 'patron', 'lang'
			) as $key) {
			$$key = $row[$key];
		}
	}

	$titre_message = ($titre_message=='') ? $titre._T('spiplistes:_de_').lire_meta("nom_site") : $titre_message;

	$nb_abonnes = spiplistes_nb_abonnes_count ($id_liste);

	// préparation des boutons 
	if($flag_editable) {
		// Propose de modifier la liste 
		$gros_bouton_modifier = 
			($connect_toutes_rubriques)
			? icone (
				_T('spiplistes:Modifier_cette_liste') // légende bouton
				, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_EDIT,'id_liste='.$id_liste) // lien
				, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif" // image du fond
				, "edit.gif" // image de la fonction. Ici, le crayon
				, '' // alignement
				, false // pas echo, demande retour
				)
			: ""
			;
		// Propose de supprimer la liste 
		$gros_bouton_supprimer = 
			($connect_toutes_rubriques)
			// Conserve les archives postées
			? icone (
					_T('spiplistes:Supprimer_cette_liste')
					, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "btn_supprimer_liste=$id_liste&id_liste=$id_liste")
					, _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'poubelle_msg.gif'
					, ""
					, "right"
					, false
					)
			: ""
			;
	}
	else {
		$gros_bouton_modifier = $gros_bouton_supprimer = "";
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
	spiplistes_boite_info_id(_T('spiplistes:Liste_numero_:'), $id_liste, false);
	spiplistes_boite_raccourcis();
	spiplistes_boite_autocron();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	changer_typo('','liste'.$id_liste);

	$page_result = "";

	$page_result .= ""
		. debut_cadre_relief("", true)
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		. "<tr><td valign='top'>\n"
		. gros_titre(spiplistes_bullet_titre_liste($titre, $statut, true)." ".$titre, '', false)
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

	$email_defaut = entites_html(lire_meta("email_webmaster"));
	$email_envoi = (email_valide($email_envoi)) ? $email_envoi : $email_defaut ;

	$page_result .= ""
		//. debut_cadre_relief("racine-site-24.gif", true)
		. debut_cadre_relief("racine-site-24.gif", true, '', _T('spiplistes:Diffusion').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "diffusion"))
		//
		////////////////////////////
		// Formulaire diffusion
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste")."' method='post'>\n"
		. "<input type='hidden' name='exec' value='listes' />\n"
		. "<input type='hidden' name='id_liste' value='$id_liste' />\n"
		. "<span class='verdana2'>". _T('spiplistes:Cette_liste_est').": "
		. 	spiplistes_bullet_titre_liste($titre, $statut, true, 'img_statut')."</span>\n"
		. "<select class='verdana2' name='statut' size='1' class='fondl' onChange='change_bouton(this)'>\n"
		. "<option" . mySel(_SPIPLISTES_PRIVATE_LIST, $statut) ." style='background-color: white'>"._T('spiplistes:statut_interne')."\n"
		. "<option" . mySel(_SPIPLISTES_PUBLIC_LIST, $statut) . " style='background-color: #B4E8C5'>"._T('spiplistes:statut_publique')."\n"
		. "<option" . mySel(_SPIPLISTES_TRASH_LIST, $statut) . " style='background:url("._DIR_IMG_PACK."rayures-sup.gif)'>"._T('texte_statut_poubelle')."\n"
		. "</select>\n"
		. " \n"
		. "<div style='margin:10px 0px 10px 0px'>\n"
		. "<label class='verdana2' for='changer_lang'>"._T(info_multi_herit)." : </label>\n"
		. "<select name='changer_lang'  class='fondl' id='changer_lang'>\n"
		. liste_options_langues('changer_lang', $lang , _T('spiplistes:langue'),'', '')
		. "</select>\n"
		. "</div>\n"
		. "<div style='text-align:right;'><input type='submit' name='btn_modifier_diffusion' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
		. "</form>\n"
		. fin_cadre_relief(true)
		//
		////////////////////////////
		// Formulaire adresse email pour le reply-to
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply_to-24.png", true, '', _T('spiplistes:adresse_de_reponse').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "replyto"))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste")."' method='post'>\n"
		. "<p class='verdana2'>\n"
		. _T('spiplistes:adresse_mail_retour')."<br />\n"
		. "<blockquote class='verdana2'><em>"._T('spiplistes:adresse')."</em></blockquote></p>\n"
		. "<div style='text-align:center'>\n"
		. "<input type='text' name='email_envoi' value=\"".$email_envoi."\" size='40' class='fondl' /></div>\n"
		. ($id_liste ? "<input type='hidden' name='id_liste' value='$id_liste' />" : "")
		. "<div style='text-align:right;'><input type='submit' name='btn_modifier_replyto' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
		. "</form>\n"
		. fin_cadre_relief(true)
		//
		////////////////////////////
		// Formulaire programmer un courrier automatique
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_timer.png", true, '', _T('spiplistes:messages_auto').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "temporiser"))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=$id_liste")."' method='post'>\n"
		. "<table border='0' cellspacing='1' cellpadding='3' width='100%'>\n"
		. "<tr><td background='"._DIR_IMG_PACK."/rien.gif' align='$spip_lang_left' class='verdana2'>\n"
		;
	if ($message_auto != "oui")
		$page_result .= _T('spiplistes:Pas_de_courrier_auto_programme');
	else {
		$page_result .= ""
			// petite ligne d'info si envoi programmé
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
				(intval($date) && (time() < strtotime($date)))
				? _T('spiplistes:prochain_envoi_prevu')." : <strong>" . affdate_heure($date) . "</strong>"
					.	(
						($next = round((strtotime($date) - time()) / _SPIPLISTES_TIME_1_DAY))
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
	$date_debut_envoi = ($date && intval($date)) ? $date : __mysql_date_time(time());
	$page_result .= ""
		. "<tr><td background='"._DIR_IMG_PACK."/rien.gif' align='$spip_lang_left' class='verdana2'>"
		. "<input type='radio' name='message_auto' value='oui' id='auto_oui' "
		. ($auto_checked = ($message_auto=='oui' ? "checked='checked'" : ""))
		. " onchange=\"jQuery('#auto_oui_detail').show();\" />"
		. "<label for='auto_oui' ".($auto_checked ? "style='font-weight:bold;'" : "").">"._T('spiplistes:prog_env')."</label>\n"
		. "<div id='auto_oui_detail'>"
		. "<ul style='list-style-type:none;'>\n"
		. "<li>"._T('spiplistes:message_sujet').": <input type='text' name='titre_message' value='".$titre_message."' size='50' class='fondl' /> </li>\n"
		. "<li>"._T('spiplistes:squel')
		//
		. spiplistes_boite_selection_patrons ($patron, true, "patron", 1)
		//
		. "</li>\n"
		. "<li>"._T('spiplistes:Tous_les')." <input type='text' name='periode' value='".$periode."' size='4' class='fondl' /> "._T('info_jours')."</li>\n"
		. "<li>"._T('spiplistes:A_partir_de')." : <br />\n"
		//
		. spiplistes_dater_envoi($id_liste, true, $statut, $date_debut_envoi, 'btn_changer_date', false)."</li>\n"
		.	(
			(!$envoyer_direct)
			? " <li><input type='checkbox' class='checkbox' name='envoyer_direct' id='box' class='fondl' /><label for='box'>"._T('spiplistes:env_maint')."</label></li>\n"
			: ""
			)
		. "</ul></div>\n"
		;
	$checked = ($message_auto=='non')?"checked='checked'":"";
	$page_result .= ""
		. "<br /><input type='radio' name='message_auto' value='non' id='auto_non' "
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
		. "<input type='submit' name='btn_modifier_courrier_auto' value='"._T('bouton_valider')."' class='fondo' />"
		. "</td></tr>"
		. "</table>\n"
		. "</form>"
		. fin_cadre_relief(true)
		;
	
	$page_result .= ""
		. fin_cadre_relief(true)
		;
		
	echo($page_result);
	

	//////////////////////////
	// Liste des abonnes
	// Appliquer les modifications sur les abonnes
	echo "<a name='auteurs'></a>";
	$editer_auteurs = charger_fonction('editer_auteurs','inc');
	/*
	echo $editer_auteurs('liste', $id_liste, $flag_editable, _request('cherche_auteur'), _request('ids'), 
		_T('spiplistes:liste_des_abonnes'),
		'listes',
		_SPIPLISTES_EXEC_ABONNE_EDIT);
*/
	echo $editer_auteurs('liste',$id_liste,$flag_editable, _request('cherche_auteur'),_request('ids'), 
		_T('spiplistes:abon'),
		'listes',
		'abonne_edit');

	////
	// MODE EDIT LISTE FIN ---------------------------------------------------------

	echo __plugin_html_signature(true), fin_gauche(), fin_page();

}

function listes_edit_presentation($id_liste){
	return icone(_T('spiplistes:modifier_liste'), generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_EDIT,"id_liste=$id_liste"),_DIR_PLUGIN_SPIPLISTES."img_pack/reply-to-all-24.gif", "edit.gif");
}

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Généale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

?>