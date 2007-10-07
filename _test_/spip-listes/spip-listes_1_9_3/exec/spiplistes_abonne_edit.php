<?php
/******************************************************************************************/
/* SPIP-listes est un syst�me de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/

// _SPIPLISTES_EXEC_ABONNE_EDIT

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Formulaire �dition d'un abonn�
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_abonne_edit () {

	include_spip('inc/presentation');
	include_spip('inc/affichage');
	include_spip('inc/spiplistes_lister_courriers_listes');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// initialise les variables post�es par le formulaire
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
	// Modifie format si demand�
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
				spiplistes_desabonner($id_auteur);
				break;
			}
	}
	
	//////////////////////////////////////////////////////
	// Recharge les donn�es de l'auteur
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
	if($nb_listes_abo = spiplistes_nb_abonnes_count('toutes', $id_auteur)) {
		$format_abo = 'suspend';
	}

	//////////////////////////////////////////////////////
	// pr�paration du bouton 
		// Propose de supprimer l'auteur invit� 
	$gros_bouton_supprimer = 
		($id_auteur && $flag_editable && ($statut=='6forum'))
		? icone (
				_T('spiplistes:Supprime_ce_contact')
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
	
	// la gestion des abonn�s est r�serv�e aux admins et � l'auteur
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
		. (strlen($bio) ? "<blockquote class='spip' style='padding:1em;'>".propre($bio)."</blockquote>\n" : "")
		. "<br />\n"
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png', true, '', _T('spiplistes:Format_de_reception_:'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT)."' method='post'>\n"
		. "<p class='verdana2'>"._T('spiplistes:Format_de_reception_desc')."</p>\n"
		. debut_cadre_relief('', true)
		. "<table width='100%'  border='0' cellspacing='0' cellpadding='0'><tr>"
		. "<td class='verdana2'>"._T('spiplistes:Format_de_reception_:')."</td>\n"
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
		// r�silier les abonnements
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
		. spiplistes_afficher_en_liste(_T('spiplistes:abonne_listes'), _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png', 'abonnements', '', '', 'position')
		//
		. $gros_bouton_supprimer
		;
	
	echo($page_result);
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
}

/******************************************************************************************/
/* SPIP-listes est un syst�me de gestion de listes d'abonn�s et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/
?>