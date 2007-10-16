<?php
/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/

// _SPIPLISTES_EXEC_ABONNE_EDIT

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Formulaire édition d'un abonné
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_abonne_edit () {

	include_spip('inc/presentation');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_lister_courriers_listes');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// initialise les variables postées par le formulaire
	foreach(array(
		'id_auteur'
		, 'btn_confirmer_format', 'modif_abo'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_auteur') as $key) {
		$$key = intval($$key);
	}

	$flag_editable = (($connect_statut == "0minirezo") && $connect_toutes_rubriques);
	
	//////////////////////////////////////////////////////
	// Modifie format si demandé
	if($btn_confirmer_format) {
		switch($modif_abo) {
			case 'html':
			case 'texte':
				spiplistes_format_abo_modifier($id_auteur, $modif_abo);
				break;
			case 'suspendre':
				spiplistes_format_abo_modifier($id_auteur, 'non');
				break;
			case 'non':
				spiplistes_desabonner_listes_statut($id_auteur, array(_SPIPLISTES_PUBLIC_LIST, _SPIPLISTES_PRIVATE_LIST,_SPIPLISTES_MONTHLY_LIST));
				break;
			}
	}

	//////////////////////////////////////////////////////
	// Recharge les données de l'auteur
	if($id_auteur) {
	
		$sql_select = "nom,bio,email,nom_site,url_site,login,pass,statut,pgp,messagerie,imessage,low_sec";
		$sql_result = spip_query("SELECT $sql_select FROM spip_auteurs WHERE id_auteur=$id_auteur LIMIT 1");

		if($row = spip_fetch_array($sql_result)) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
		}
		else {
			$id_auteur = 0;
		}
	}
	$format_abo = spiplistes_format_abo_demande($id_auteur);

	//////////////////////////////////////////////////////
	// préparation du bouton 
		// Propose de supprimer l'auteur invité 
	$gros_bouton_modifier = 
		($id_auteur && $flag_editable)
		? icone (
				_T('admin_modifier_auteur')
				, generer_url_ecrire("auteur_infos", "id_auteur=$id_auteur&edit=oui")
				, 'redacteurs-24.gif'
				, "edit.gif"
				, "right"
				, false
				)
		: ""
		;
		// Propose de supprimer l'auteur invité 
	$gros_bouton_supprimer = 
		($id_auteur && $flag_editable && ($statut=='6forum'))
		? icone (
				_T('spiplistes:Supprimer_ce_contact')
				, generer_action_auteur(_SPIPLISTES_ACTION_SUPPRIMER_ABONNER, $id_auteur, generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE))
				, 'redacteurs-24.gif'
				, "supprimer.gif"
				, "right"
				, false
				)
		: ""
		;

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
	// la gestion des abonnés est réservée aux admins et à l'auteur
	if(($connect_statut != "0minirezo") && ($connect_id_auteur != $id_auteur)) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	// erreur ?
	if(!$id_auteur) {
		die (spiplistes_terminer_page_donnee_manquante() . fin_page());
	}
	
	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spiplistes_boite_info_id(_T('titre_cadre_numero_auteur'), $id_auteur, false, 'id_auteur');
	spiplistes_boite_raccourcis();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	switch($statut) {
		case "0minirezo":
			$logo = "redacteurs-admin-24.gif"; // jolie cravate
			break;
		case "5poubelle":
			$logo = "redacteurs-poubelle-24.gif";
			break;
		default:
			$logo = "redacteurs-24.gif";
			break;
	}
	
	$page_result = ""
		. debut_cadre_relief($logo, true)
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		. "<tr><td valign='top'>\n"
		. gros_titre($nom, '', false)
		. "<br />\n"
		;
	if (strlen($email) || strlen($nom_site)) {			
		$page_result .= ""
			. "<span class='verdana3'>"
			. (strlen($email) ? _T('email_2')."<strong><a href='mailto:$email'>$email</a></strong><br />\n" : "")
			. (strlen($nom_site) ? _T('info_site_2')."<strong><a href='$url_site'>$nom_site</a></strong>" : "")
			. "</span>\n"
			;
	}
	$page_result .= ""
		. "</td>"
		. "<td>"
		// le gros bouton modifier si besoin
		. $gros_bouton_modifier
		. "</td></tr>\n"
		. "<tr><td width='100%' colspan='2'>\n"
		. (strlen($bio) ? "<blockquote class='spip' style='padding:1em;'>".propre($bio)."</blockquote>\n" : "")
		. "</td>\n"
		. "</tr></table>\n"
		. "<br />\n"
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png', true, '', _T('spiplistes:format_de_reception').":")
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT)."' method='post'>\n"
		. "<p class='verdana2'>"._T('spiplistes:Format_de_reception_desc')."</p>\n"
		. debut_cadre_relief('', true)
		. "<table width='100%'  border='0' cellspacing='0' cellpadding='0'><tr>"
		. "<td class='verdana2'>"._T('spiplistes:format_de_reception').":</td>\n"
		. "<td>"
		. "<input name='modif_abo' ".(($format_abo == 'html')? 'checked=checked)': '')." value='html' type='radio' id='f_html' />\n"
		. "<label for='f_html' class='verdana2'>"._T('spiplistes:html')."</label>\n"
		. "</td>\n"
		. "<td>"
		. "<input name='modif_abo' ".(($format_abo == 'texte')? 'checked=\"checked\")' : '')." value='texte' type='radio' id='f_texte' />\n"
		. "<label for='f_texte' class='verdana2'>"._T('spiplistes:texte')."</label>\n"
		. "</td>\n"
		. "</tr></table>\n"
		. fin_cadre_relief(true)
		// suspendre les abonnements
		. debut_cadre_relief('', true)
		. "<input name='modif_abo' ".(($format_abo == 'suspend')? 'checked=\"checked\")' : '')." value='suspendre' type='radio' id='f_suspend' />"
		. "<label for='f_suspend' class='verdana2'>"._T('spiplistes:Desabonner_temporaire')."</label>\n"
		. fin_cadre_relief(true)
		// résilier les abonnements
		. debut_cadre_relief('', true)
		. "<input name='modif_abo' ".(($format_abo == 'non')? 'checked=\"checked\")' : '')." value='non' type='radio' id='f_non' />"
		. "<label for='f_non' class='verdana2'>"._T('spiplistes:Desabonner_definitif')."</label>\n"
		. fin_cadre_relief(true)
		. "<input type='hidden' name='id_auteur'  value=$id_auteur >\n"
		//
		// bouton validation
		. "<div style='text-align:right;'><input type='submit' name='btn_confirmer_format' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
		. "</form>\n"
		. fin_cadre_relief(true)
		. fin_cadre_relief(true)
		//
		. "<br />\n"
		// Liste des abonnements
		. spiplistes_lister_courriers_listes(
			_T('spiplistes:abonne_listes')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
			, 'abonnements'
			, ''
			, false
			, 'position'
			, _SPIPLISTES_EXEC_LISTE_GERER
			, $id_auteur
		)
		//
		. $gros_bouton_supprimer
		;
	
	echo($page_result);
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
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
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
?>