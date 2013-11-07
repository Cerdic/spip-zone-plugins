<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

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

/*
	Formulaire edition d'un abonne
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_abonne_edit () {

	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_lister_courriers_listes');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// initialise les variables postees par le formulaire
	foreach(array(
		'id_auteur'
		, 'btn_confirmer_format', 'modif_abo'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_auteur') as $key) {
		$$key = intval($$key);
	}

	$flag_editable = (
		($id_auteur > 0) 
		&& (
			(($connect_statut == "0minirezo") && $connect_toutes_rubriques)
			|| ($connect_id_auteur == $id_auteur)
			)
		);
	
	//////////////////////////////////////////////////////
	// Modifie format si demande
	if($flag_editable && $btn_confirmer_format) {
		switch($modif_abo) {
			case 'html':
			case 'texte':
				spiplistes_format_abo_modifier($id_auteur, $modif_abo);
				break;
			case 'suspend':
				spiplistes_format_abo_modifier($id_auteur, 'non');
				break;
			case 'non':
				spiplistes_abonnements_desabonner_statut($id_auteur, explode(";", _SPIPLISTES_LISTES_STATUTS_TOUS));
				break;
			}
	}

	//////////////////////////////////////////////////////
	// Recharge les donnees de l'auteur
	if($id_auteur > 0) {
	
		$sql_select = "nom,bio,email,nom_site,url_site,login,pass,statut,pgp,messagerie,imessage,low_sec";
		$sql_result = sql_select($sql_select, "spip_auteurs", "id_auteur=".sql_quote($id_auteur)." LIMIT 1");

		if($row = sql_fetch($sql_result)) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			$format_id_auteur = spiplistes_format_abo_demande($id_auteur);
		} else {
			$id_auteur = 0;
			$format_id_auteur = false;
		}
	}
	$format_abo = spiplistes_format_abo_demande($id_auteur);

	//////////////////////////////////////////////////////
	// preparation du bouton 
		// Propose de supprimer l'auteur invite 
	$gros_bouton_modifier = 
		($flag_editable)
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
		// Propose de supprimer l'auteur invite 
	$gros_bouton_supprimer = 
		($id_auteur && $flag_editable && ($statut=='6forum'))
		? icone (
				_T('spiplistes:supprimer_ce_contact')
				, generer_action_auteur(_SPIPLISTES_ACTION_SUPPRIMER_ABONNER, $id_auteur, generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE,'',false,true))
				, 'redacteurs-24.gif'
				, "supprimer.gif"
				, "right"
				, false
				)
		: ""
		;

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:edition_dun_abonne');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "abonne_edit";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . $titre_page, $rubrique, $sous_rubrique));
	
	// la gestion des abonnes est reservee aux admins et a l'auteur
	if(!$flag_editable) {
		// erreur sur appel de page ?
		if(!($id_auteur > 0)) {
			die (spiplistes_terminer_page_donnee_manquante() . fin_page());
		}
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ""
		. '<br class="debut-page" />'.PHP_EOL
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. spiplistes_boite_info_id(_T('titre_cadre_numero_auteur'), $id_auteur, true, 'id_auteur')
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;
	
	$page_result .= ""
		. debut_cadre_relief(spiplistes_get_icone_auteur($statut), true)
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		. "<tr><td valign='top'>\n"
		. spiplistes_gros_titre($nom, '', true)
		. "<br />\n"
		;
	if(strlen($email) || strlen($nom_site)) {			
		$page_result .= ""
			. "<span class='verdana3'>"
			. (strlen($email) ? _T('email_2')."<strong><a href='mailto:$email'>$email</a></strong><br />\n" : "")
			. (strlen($nom_site) ? _T('info_site_2')."<strong><a href='$url_site'>$nom_site</a></strong>" : "")
			. "</span><br />\n"
			;
	} 
	
	// Si adresse mail, permettre l'abonnement
	if(strlen($email)) {
		$message_alert_abo = 
			(!$format_id_auteur)
			? spiplistes_form_description_alert(_T('spiplistes:abonne_sans_format'), true)
			: ""
			;
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
			. spiplistes_form_debut(generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT), true)
			. spiplistes_form_description(_T('spiplistes:format_de_reception_desc'), true)
			. $message_alert_abo
			. debut_cadre_relief('', true)
			. "<table width='100%'  border='0' cellspacing='0' cellpadding='0'><tr>"
			. "<td class='verdana2'>"._T('spiplistes:format_de_reception')."</td>\n"
			. "<td>"
			. spiplistes_form_input_radio ('modif_abo', 'html', _T('spiplistes:html')
				, ($format_abo == 'html'), true, false)
			. "</td>\n"
			. "<td>"
			. spiplistes_form_input_radio ('modif_abo', 'texte', _T('spiplistes:texte')
				, ($format_abo == 'texte'), true, false)
			. "</td>\n"
			. "</tr></table>\n"
			. fin_cadre_relief(true)
			// suspendre les abonnements
			. debut_cadre_relief('', true)
			. spiplistes_form_input_radio ('modif_abo', 'suspend', _T('spiplistes:desabonner_temporaire')
				, ($modif_abo == 'suspend'), true, false)
			. fin_cadre_relief(true)
			// resilier les abonnements
			. debut_cadre_relief('', true)
			. spiplistes_form_input_radio ('modif_abo', 'non', _T('spiplistes:desabonner_definitif')
				, (($format_abo == 'non') && ($modif_abo != 'suspend')), true, false)
			. fin_cadre_relief(true)
			. "<input type='hidden' name='id_auteur' value=$id_auteur >\n"
			. spiplistes_form_bouton_valider('btn_confirmer_format')
			. spiplistes_form_fin(true)
			. fin_cadre_relief(true)
			;
	} else {
		$page_result .= ""
			. "</td><td></td></tr></table>\n"
			. "<p>"._T('spiplistes:adresse_mail_obligatoire')."</p>\n"
			. "<p><a href='".generer_url_ecrire("auteur_infos","id_auteur=$id_auteur&edit=oui")."'>"
				._T('spiplistes:editer_fiche_abonne')."</a></p>\n"
			;
	}
	
	$page_result .=
		fin_cadre_relief(true)
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
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();
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
