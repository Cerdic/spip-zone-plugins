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

function exec_listes_dist(){

	include_spip('inc/presentation');
	include_spip('inc/mots');
	include_spip('inc/lang');
	include_spip('inc/affichage');
	include_spip('base/spip-listes');
	
	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_lang_left,$spip_lang_right
		;

	// initialise les variables postées par le formulaire
	foreach(array(
		'new'	// nouvelle liste si 'oui'
		, 'id_liste'// si modif dans l'éditeur
		, 'titre', 'texte' // modif ou new renvoyés par l'éditeur
		, 'pied_page', 'changer_lang', 'statut_nouv', 'btn_modifier_replyto', 'email_envoi', 'btn_modifier_diffusion' // modif ou new
		, 'jour', 'mois', 'annee', 'heure', 'minute'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_liste') as $key) {
		$$key = intval($$key);
	}
	foreach(array('titre', 'texte', 'pied_page') as $key) {
		$$key = trim($$key);
	}

	$lang = (!empty($changer_lang)) ? $changer_lang : $GLOBALS['spip_lang'] ;
	
	$cherche_auteur = _request('cherche_auteur');
	
	$Valider_auto = _request('Valider_auto');
	$auto = _request('auto');
	$changer_extra = _request('changer_extra');
	$patron = _request('patron');
	$periode = _request('periode');
	$sujet_message = _request('sujet_message');
	$envoyer_direct = _request('envoyer_direct');
	
	$debut = _request('debut');
 
	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 

	if ($id_liste==0) {
	//////////////////////////////////////////////////////
	// Creer une liste
	////
		if ($new=='oui') {
			if ($titre=='') $titre = _T('spiplistes:liste_sans_titre');
	
			spip_query("INSERT INTO spip_listes (statut, date, lang, titre, texte) 
				VALUES ('"._SPIPLISTES_PRIVATE_LIST."', NOW(),"._q($langue).","._q($titre).","._q($texte).")");
			$id_liste = spip_insert_id();
			//Auteur de la liste (moderateur)
			spip_query("DELETE FROM spip_auteurs_mod_listes WHERE id_liste = "._q($id_liste));
			spip_query("INSERT INTO spip_auteurs_mod_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
			//abonner le moderateur a sa liste
			spip_query("DELETE FROM spip_auteurs_listes WHERE id_liste = "._q($id_liste));
			spip_query("INSERT INTO spip_auteurs_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
		} 
		spiplistes_log("LISTE ID #$id_liste added by ID_AUTEUR #$connect_id_auteur");
		// supprime l'id pour éviter de passer en mode modif
	}
	else if($id_liste > 0) {
	//////////////////////////////////////////////////////
	// Modifier une liste (retour d'éditeur)
	////
		// les supers-admins et le moderateur seuls peuvent modifier la liste
		$id_mod_liste = spiplistes_mod_listes_get_id_auteur($id_liste);
		$flag_editable = ($connect_toutes_rubriques || ($connect_id_auteur == $id_mod_liste));

		if($flag_editable) {
			$sql_query = "";
			// récupère les données de la liste actuelle pour optimiser l'update
			$sql_select = "statut,titre,maj";
			if($row = spip_fetch_array(spip_query("SELECT ".$sql_select." FROM spip_listes WHERE id_liste=$id_liste LIMIT 1"))) {
				foreach(explode(",", $sql_select) as $key) {
					$current_liste[$key] = $row[$key];
				}
			}
			
			if(in_array($statut_nouv, explode(";", _SPIPLISTES_LISTES_STATUTS)) && ($statut_nouv!=$current_liste['statut'])) {
				$sql_query .= " statut='$statut_nouv',";
			}
			
			// Modifier l'adresse email 
			if($btn_modifier_replyto && email_valide($email_envoi) && ($email_envoi!=$current_liste['email_envoi'])) {
				$sql_query .= " email_envoi="._q($email_envoi).",";
			}
			
			//modifier la date (à venir, ne figure pas dans cette version CP:20070922)
			if ($jour) {
				$mois = intval(_request('mois'));
				if (($annee=intval(_request('annee'))) == "0000") $mois = "00";
				if ($mois == "00") $jour = "00";
				$result = spip_query("UPDATE spip_listes SET date='$annee-$mois-$jour' WHERE id_liste="._q($id_liste));
			}

			// Enregistrer les modifs sur la liste
		
			if ($titre && !$ajout_forum && $flag_editable) {
				$titre = corriger_caracteres($titre);
				$descriptif = corriger_caracteres($descriptif);
				$texte = corriger_caracteres($texte);
				$pied_page = corriger_caracteres($pied_page);
				
				$result = spip_query("UPDATE spip_listes SET titre="._q($titre).",descriptif="._q($descriptif).",texte="._q($texte).",pied_page="._q($pied_page)." WHERE id_liste="._q($id_liste));
				// afficher le nouveau titre dans la barre de fenetre
				$titre_article = $titre;
			}
		
			if($changer_lang) // à revoir aussi
				$result = spip_query("UPDATE spip_listes SET lang="._q($changer_lang)." WHERE id_liste="._q($id_liste));
		
			// prendre en compte les modifs sur le message auto // à revoir aussi
			if($Valider_auto){
				if($auto == "oui"){
					$result = spip_query("UPDATE spip_listes SET message_auto='oui' WHERE id_liste="._q($id_liste));
					if($maj=="0000-00-00 00:00:00"){
						$result = spip_query("UPDATE spip_listes SET maj=NOW() WHERE id_liste="._q($id_liste));
					}
				}
				elseif ($auto == "non"){
					$result = spip_query("UPDATE spip_listes SET message_auto='non', maj='0000-00-00 00:00:00' WHERE id_liste="._q($id_liste));
				}

				if(($changer_extra == "oui") AND ($auto == "oui") ){
					$result = spip_query("UPDATE spip_listes SET patron="._q($patron).", periode="._q($periode).", titre_message="._q($sujet_message)." WHERE id_liste="._q($id_liste));
					if($envoyer_direct){
						$majnouv = (time() - ($periode * 3600*24));
						$result = spip_query("UPDATE spip_listes SET maj=FROM_UNIXTIME($majnouv), periode="._q($periode)." WHERE id_liste="._q($id_liste));
					}
				}
			}
		
			$sql_query = rtrim($sql_query, ",");
			
			if(!empty($sql_query)) {
				spip_query("UPDATE spip_listes SET $sql_query WHERE id_liste=$id_liste LIMIT 1");
			}
		}
	}

	//
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
					, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_VUE, "btn_supprimer_liste=$id_liste&id_liste=$id_liste")
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
					alert('".__texte_html_2_iso(_T('spiplistes:Attention_modifie_liste_abonnes'), $GLOBALS['meta']['charset'], true)."'); 
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

	// tous les admins ont la possibilité de modifier le statut
	if ($connect_statut == '0minirezo' ) {
		$page_result .= ""
			//. debut_cadre_relief("racine-site-24.gif", true)
			. debut_cadre_relief("racine-site-24.gif", true, '', _T('spiplistes:Diffusion').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "diffusion"))
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_VUE,"id_liste=$id_liste")."' method='get'>"
			. "<input type='hidden' name='exec' value='listes' />"
			. "<input type='hidden' name='id_liste' value='$id_liste' />"
			. "<strong>"._T('spiplistes:Cette_liste_est').": </strong> "
			. 	spiplistes_bullet_titre_liste($titre, $statut, true, 'img_statut')
			. "<select name='statut_nouv' size='1' class='fondl' onChange='change_bouton(this)'>"
			. "<option" . mySel(_SPIPLISTES_PRIVATE_LIST, $statut_article) ." style='background-color: white'>"._T('spiplistes:statut_interne')."\n"
			. "<option" . mySel(_SPIPLISTES_PUBLIC_LIST, $statut_article) . " style='background-color: #B4E8C5'>"._T('spiplistes:statut_publique')."\n"
			. "<option" . mySel(_SPIPLISTES_TRASH_LIST, $statut_article) . " style='background:url("._DIR_IMG_PACK."rayures-sup.gif)'>"._T('texte_statut_poubelle')."\n"
			. "</select>"
			. " \n"
			. "<input type='submit' name='Modifier' value='"._T('bouton_modifier')."' class='fondo' />"
			. aide ("artstatut")
			. "<div style='margin:10px 0px 10px 0px'>"
			. menu_langues('changer_lang', $lang , _T('spiplistes:langue'),'', '')
			. "</div>"
			//regler email d'envoi de la liste
			.	( 
				($id_liste)
				? "<input type='hidden' name='id_liste' value='$id_liste' />"
				: "<input type='hidden' name='new' value='$new' />"
				)
			. "</form>"
			. fin_cadre_relief(true)
			;

			//
			// Formulaire adresse email pour le reply-to
		$email_defaut = entites_html(lire_meta("email_webmaster"));
		$email_envoi = (email_valide($email_envoi)) ? $email_envoi : $email_defaut ;
		$page_result .= ""
			. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply_to-24.png", true, '', _T('spiplistes:adresse_de_reponse').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "replyto"))
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_VUE,"id_liste=$id_liste")."' method='post'>\n"
			. "<p class='verdana2'>\n"
			. _T('spiplistes:adresse_mail_retour')."<br />\n"
			. "<blockquote class='verdana2'><em>"._T('spiplistes:adresse')."</em></blockquote></p>\n"
			. "<div style='text-align:center'>\n"
			. "<input type='text' name='email_envoi' value=\"".$email_envoi."\" size='40' class='fondl' /></div>\n"
			. ($id_liste ? "<input type='hidden' name='id_liste' value='$id_liste' />" : "")
			. "<div style='text-align:right;'><input type='submit' name='btn_modifier_replyto' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
			. "</form>\n"
			. fin_cadre_relief(true)
			;
		
		// programmer un courrier automatique
		$auto_checked = ($message_auto=='oui')?"checked='checked'":"";
		$page_result .= ""
			. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_timer.png", true, '', _T('spiplistes:messages_auto').__plugin_aide(_SPIPLISTES_EXEC_AIDE, "temporiser"))
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_VUE,"id_liste=$id_liste")."' method='post'>"
			. "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">"
			. "<tr><td background='"._DIR_IMG_PACK."/rien.gif' align='$spip_lang_left' class='verdana2'>"
			;
		if ($message_auto != "oui")
			$page_result .= _T('spiplistes:non_program');
		else {
			$dernier_envoi =  strtotime($maj_nouv)  ;
			$sablier = (time() - $dernier_envoi) ;
			$proch = round(  (( (24*3600*$periode) - $sablier) / (3600*24)) ) ;
			$last = round(  ($sablier / (3600*24)) ) ;
			if(($changer_extra == "oui") && ($auto == "oui")) {
				$page_result .= "<h2>"._T('spiplistes:date_act')."</h2>";
			}
			$page_result .= ""
				. "<h3>"._T('spiplistes:sujet_courrier_auto').$titre_message."</h3>"
				. _T('spiplistes:env_esquel')." <em>".$patron."</em> "
				. "<br />"._T('spiplistes:Tous_les')."  <strong>".$periode."</strong>  "._T('info_jours')
				. "<br />"._T('spiplistes:dernier_envoi')." <strong>$last</strong> "._T('spiplistes:jours')."<br />"
				;
			if($proch != 0) {
				$page_result .= "<br />"._T('spiplistes:prochain_envoi_prevu_dans')."<strong>$proch</strong> "._T('spiplistes:jours')."<br />";
			}
			else {
				$page_result .= "<br />"._T('spiplistes:prochain_envoi_aujd')."<br />";
			}
		}
		$sujet_message = ($titre_message=='') ? $titre." "._T('zxml_de')." ".$nomsite : $titre_message ;
		$page_result .= ""
			. "<tr><td background='"._DIR_IMG_PACK."/rien.gif' align='$spip_lang_left' class='verdana2'>"
			. "<input type='radio' name='auto' value='oui' id='auto_oui' "
			. $auto_checked
			. " onchange=\"jQuery('#auto_oui_detail').show();\" />"
			. ($auto_checked?"<strong>":"")
			. "<label for='auto_oui'>"._T('spiplistes:prog_env')."</label>"
			. ($auto_checked?"</strong>":"")
			. "<input type='hidden' name='changer_extra' value='oui'>"
			. "<div id='auto_oui_detail'>"
			. "<ul style='list-style-type:none;'>"
			. "<li>"._T('spiplistes:message_sujet').": <input type='titre_message' name='sujet_message' value='".$sujet_message."' size='50' class='fondl' /> </li>"
			. "<li>"._T('spiplistes:squel')
			;
		$liste_patrons = find_all_in_path("patrons/","[.]html$");
		$page_result .= "<select name='patron'>";
		foreach($liste_patrons as $titre_option) {
			$titre_option = basename($titre_option,".html");
			$selected ="";
			if ($patron == $titre_option)
				$selected = "selected='selected";
			$page_result .= "<option ".$selected." value='".$titre_option."'>".$titre_option."</option>\n";
		}
		$page_result .= ""
			. "</select>"
			. "</li>"
			. "<li>"._T('spiplistes:Tous_les')." <input type='text' name='periode' value='".$periode."' size='4' class='fondl' /> "._T('info_jours')."</li>"
			.	(
				(!$envoyer_direct)
				? " <li><input type='checkbox' class='checkbox' name='envoyer_direct' id='box' class='fondl' /><label for='box'>"._T('spiplistes:env_maint')."</label></li>"
				: ""
				)
			. "</ul></div>"
			;
		$checked = ($message_auto=='non')?"checked='checked'":"";
		$page_result .= ""
			. "<br /><input type='radio' name='auto' value='non' id='auto_non' "
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
			. "<input type='submit' name='Valider_auto' value='"._T('bouton_valider')."' class='fondo' />"
			. "</td></tr>"
			. "</table>\n"
			. "</form>"
			. fin_cadre_relief(true)
			;
	} // end if ($connect_statut == '0minirezo' )
	
	$page_result .= ""
		. fin_cadre_relief(true)
		;
		
	echo($page_result);
	

	//////////////////////////
	// Liste des abonnes
	// Appliquer les modifications sur les abonnes
	echo "<a name='auteurs'></a>";
	$editer_auteurs = charger_fonction('editer_auteurs','inc');
	echo $editer_auteurs('liste', $id_liste, $flag_editable, _request('cherche_auteur'), _request('ids'), 
		_T('spiplistes:liste_des_abonnes'),
		'listes',
		_SPIPLISTES_EXEC_ABONNE_EDIT);

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