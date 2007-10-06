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
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Formulaire de création de courrier
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_courrier_edit(){

	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/affichage');
	include_spip('inc/spiplistes_api');
	include_spip('public/assembler');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_ecran
		;
		
	$type = _request('type');
	$id_courrier = intval(_request('id_courrier'));

	if($id_courrier > 0) {
		// Edition /modification d'un courrier
		$sql_select = "titre,texte,type,statut,id_auteur";
		$result = spip_query("SELECT $sql_select FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 1");
		if ($row = spip_fetch_array($result)) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			$titre = entites_html($titre);
			$texte = entites_html($texte);
		}
	}
	else {
		// si pas de ID courrier, c'est une création
		$statut = _SPIPLISTES_STATUT_REDAC; 
		$type = 'nl';
		$new = 'oui';
		$titre = _T('spiplistes:Nouveau_courrier');
	}

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// l'édition du courrier est réservée aux super-admins 
	// ou à l'admin créateur du courrier
	if(!(
		($connect_statut == "0minirezo")
		&& ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur))
		)) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));

	debut_gauche();
	spiplistes_boite_info_id(_T('spiplistes:Courrier_numero_:'), $id_courrier, false);
	spiplistes_boite_raccourcis();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");


	$page_result .= ""
		. debut_cadre_formulaire('', true)
		. "<a name='haut_block' id='haut_block'></a>"
		. "<p>".($id_courrier ? _T('spiplistes:Modifier_un_courrier_:') : _T('spiplistes:Creer_un_courrier_:') )."<br />\n"
		. "<span style='font-size:150%;font-weight:bold;color:gray;'>$titre</span></p>\n"
		. "<hr />\n"
		. "<span style='font-size:80%;font-weight:bold;color:gray;'>"._T('spiplistes:alerte_edit')."</span><br />\n"
		. "<br />\n"
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'stock_insert-slide.gif', true)
	   . "<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_REDAC,"id_message=$id_courrier")."'>"._T('spiplistes:Generer')."</a>"
		. fin_cadre_relief(true)
		. "<br />"
		//
		. "<form id='choppe_patron-1' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,"id_courrier=$id_courrier")."' method='post' name='formulaire'>"
		. "<input type='hidden' name='modifier_message' value=\"oui\" />"
		. "<input type='hidden' name='id_courrier' value='$id_courrier' />"
		. _T('spiplistes:sujet_courrier')
		. "<input type='text' class='formo' name='titre' value=\"$titre\" size='40' />"
		. "<br />"
		. "<br />"
		. _T('spiplistes:texte_courrier')
		. aide ("raccourcis")
		. "<br />"
		. afficher_barre('document.formulaire.texte')
		. "<textarea id='text_area' name='texte' ".$GLOBALS['browser_caret']." class='formo' rows='20' cols='40' wrap=soft>"
		. $texte
		. "</textarea>\n"
		. (!$id_courrier ? "<input type='hidden' name='new' value=\"oui\" />" : "")
		. "<p align='right'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />"
		. "</form>"
		. fin_cadre_formulaire(true)
		;
	
	echo($page_result);

	// COURRIER EDIT FIN ---------------------------------------------------------------

	echo __plugin_html_signature(true), fin_gauche(), fin_page();
	
}
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
?>
