<?php
// _SPIPLISTES_EXEC_COURRIER_EDIT
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
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Formulaire de cr�ation de courrier
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

	foreach(array('btn_courrier_apercu') as $key) {
		$$key = _request($key);
	}

	if($id_courrier > 0) {
	///////////////////////////
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
		else {
			$id_courrier = false;
		}
	}

	// l'�dition du courrier est r�serv�e aux super-admins 
	// ou au cr�ateur du courrier
	$flag_editable = (($connect_statut == "0minirezo") && ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur)));

	if($flag_editable) {
		if(!$id_courrier) {
		///////////////////////////
		// si pas de ID courrier, c'est une cr�ation
			$statut = _SPIPLISTES_STATUT_REDAC; 
			$type = 'nl';
			$new = 'oui';
			$titre = _T('spiplistes:Nouveau_courrier');
		}
	
		$gros_bouton_retour =
			($id_courrier)
			? icone(
				_T('spiplistes:retour_link')
				, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER, "id_courrier=$id_courrier")
				, spiplistes_items_get_item('icon', $statut)
				, "rien.gif"
				, ""
				, false
				)
			: ""
			;
	}
	
//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	if(!flag_editable) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));

	debut_gauche();
	spiplistes_boite_info_id(_T('spiplistes:Courrier_numero_:'), $id_courrier, false);
	spiplistes_boite_raccourcis();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	$page_result .= ""
		. debut_cadre_formulaire('', true)
		. "<a name='haut_block' id='haut_block'></a>"
		// 
		// bloc titre
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>"
		. "<tr width='100%'>"
		. "<td>"
		. $gros_bouton_retour
		. "</td>"
		. "<td><img src='"._DIR_IMG_PACK."/rien.gif' width='10'></td>\n"
		. "<td width='100%'>"
		. ($id_courrier ? _T('spiplistes:Modifier_un_courrier_:') : _T('spiplistes:Creer_un_courrier_:') )."<br />\n"
		. gros_titre($titre, '', false)
		. "</td>"
		. "</tr></table>"
		. "<hr />"
		//
		. "<p>"._T('spiplistes:alerte_edit')."</p>\n"
		. "<br />\n"
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'stock_insert-slide.gif', true)
	   . "<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_REDAC,"id_message=$id_courrier")."'>"._T('spiplistes:Generer')."</a>"
		. fin_cadre_relief(true)
		. "<br />"
		//
		// d�but formulaire
		. "<form id='choppe_patron-1' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,"id_courrier=$id_courrier")."' method='post' name='formulaire'>"
		. "<input type='hidden' name='modifier_message' value=\"oui\" />"
		. "<input type='hidden' name='id_courrier' value='$id_courrier' />"
		//
		// choisir patron
		
		//
		// bloc sujet
		. "<label for='sujet_courrier'>"._T('spiplistes:sujet_courrier')."</label>\n"
		. "<input id='sujet_courrier' type='text' class='formo' name='titre' value=\"$titre\" size='40' />"
		. "<br />"
		// 
		// bloc texte
		. "<label for='texte_courrier'>"._T('spiplistes:texte_courrier')."</label>\n"
		. afficher_barre('document.formulaire.texte')
		. "<textarea id='texte_courrier' name='texte' ".$GLOBALS['browser_caret']." class='formo' rows='20' cols='40' wrap=soft>"
		. $texte
		. "</textarea>\n"
		. (!$id_courrier ? "<input type='hidden' name='new' value=\"oui\" />" : "")
		//
		// boutons apercu/valider (en cours CP-20071010)
		//	($btn_courrier_apercu)
		//		? "<p style='position:relative;'>"
		//		. "<input type='submit' name='btn_courrier_apercu' value='"._T('Apercu')."' class='fondo' />\n"
		//		. "<input style='position:absolute;right:0;' type='submit' name='btn_courrier_valider' value='"._T('bouton_valider')."' class='fondo' />"
		//		. "</p>\n"
		. "<p style='text-align:right;'><input type='submit' name='btn_courrier_valider' value='"._T('bouton_valider')."' class='fondo' /></p>"
		//
		// fin formulaire
		. "</form>"
		. fin_cadre_formulaire(true)
		;
	
	echo($page_result);

	// COURRIER EDIT FIN ---------------------------------------------------------------

	echo __plugin_html_signature(true), fin_gauche(), fin_page();
	
}
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
?>
