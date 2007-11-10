<?php
// _SPIPLISTES_EXEC_COURRIER_EDIT
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

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_courrier_edit(){

	include_spip('inc/barre');
	include_spip('base/spiplistes_tables');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('public/assembler');
	include_spip('inc/spiplistes_naviguer_paniers');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_ecran
		, $compteur_block
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

	// l'édition du courrier est réservée aux super-admins 
	// ou aux admin créateur du courrier
	$flag_editable = (($connect_statut == "0minirezo") 
		&& ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur) || !$id_courrier));

	if($flag_editable) {
		if(!$id_courrier) {
		///////////////////////////
		// si pas de ID courrier, c'est une création
			$statut = _SPIPLISTES_STATUT_REDAC; 
			$type = 'nl';
			$new = 'oui';
			$titre = _T('spiplistes:Nouveau_courrier');
			$clearonfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}
		else {
			$clearonfocus = "";
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
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	if(!$flag_editable) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));

	debut_gauche();
	spiplistes_boite_info_id(_T('spiplistes:Courrier_numero_:'), $id_courrier, false);
	spiplistes_naviguer_paniers_courriers(_T('spiplistes:Aller_au_panier'));
	spiplistes_boite_raccourcis();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	$page_result = ""
		// le bloc pour aperçu
		. "<div id='apercu-courrier' style='clear:both;tex-align:center'></div>\n"
		//
		. debut_cadre_formulaire('', true)
		. "<a name='haut-block' id='haut-block'></a>\n"
		// 
		//
		// bloc titre
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		. "<tr width='100%'>"
		. "<td>"
		. $gros_bouton_retour
		. "</td>"
		. "<td><img src='"._DIR_IMG_PACK."/rien.gif' width='10'></td>\n"
		. "<td width='100%'>"
		. ($id_courrier ? _T('spiplistes:Modifier_un_courrier_:') : _T('spiplistes:Creer_un_courrier_:') )."<br />\n"
		. spiplistes_gros_titre($titre, '', false)
		. "</td>\n"
		. "</tr></table>\n"
		. "<hr />\n"
		//
		// début formulaire
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,"id_courrier=$id_courrier")
			."' method='post' name='formulaire_courrier_edit' id='formulaire_courrier_edit'>\n"
		. "<input type='hidden' name='modifier_message' value=\"oui\" />\n"
		. "<input type='hidden' name='id_courrier' value='$id_courrier' />\n"
		//
		// bloc sujet
		. "<label for='sujet_courrier'>"._T('spiplistes:sujet_courrier').":</label>\n"
		. "<input id='sujet_courrier' type='text' class='formo' name='titre' value=\"$titre\" size='40' $clearonfocus />\n"
		. "<p style='margin-bottom:1.75em;'>"._T('spiplistes:Courrier_edit_desc')."</p>\n"
		//
		// générer le contenu
		// Reprise du Formulaire adapté de abomailman () // MaZiaR - NetAktiv	// tech@netaktiv.com
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'stock_insert-slide.gif', true)
		. bouton_block_invisible(md5(_T('spiplistes:charger_patron')))
		. "<a href=\"javascript:swap_couche('$compteur_block', '$spip_lang_rtl');\">"._T('spiplistes:Generer_le_contenu')."</a>\n"
		. debut_block_invisible(md5(_T('spiplistes:charger_patron')))
		// 
		. "<div id='ajax-loader' align='right'><img src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."ajax_indicator.gif' /></div>\n"
		//sélection du patron
		. "<label class='verdana2' style='font-weight:bold;display:block;margin-top:1em;' for='patron'>"._T('spiplistes:Choisir_un_patron').":</label>\n"
		. spiplistes_boite_selection_patrons ("", true, _SPIPLISTES_PATRONS_DIR, "patron", 1, "100%")."<br />\n"
		//
		//
		. "<div id='boite-2-cols' style='margin:1em 0;padding:0;vertical-align:top;width:100%;height:3em;'>\n" 
		// la date
		// sélecteur de date
		// nota: les scripts js sont appelés dans header_prive
		. "<div id='col-gauche' style='width:50%;height:3em;float:left;'>\n"
		. "<script type='text/javascript'><!-- \n$(document).ready(function(){ \n $.datePicker.setDateFormat('yyyy-mm-dd');\n"
		. unicode2charset(charset2unicode(recuperer_fond('formulaires/date_picker_init'),'html'))
		. " \n $('input.date-picker').datePicker({startDate:'01/01/1900'});\n }); \n //--></script>\n"
		. "<label class='verdana2' for='date'>"._T('spiplistes:Contenu_a_partir_de_date_').":</label><br />\n"
		. "<input name='date' id='date' class='date-picker' style='font-size:11px;' />\n"
		. "</div>\n"
		// sélecteur de langues
		. "<label class='verdana2' for='lang'>"._T('spiplistes:Langue_du_courrier_').":</label><br />\n"
		. "<select name='lang' class='fondo' id='lang'>\n"
		. liste_options_langues('changer_lang')
		. "</select>\n"
		. "</div>\n" // fin boite-2-cols
		//
		// sélecteur de rubriques
		. "<label class='verdana2' for='ajouter_rubrique'>"._T('spiplistes:Lister_articles_de_rubrique').":</label>\n"
		. "<select name='id_rubrique' id='ajouter_rubrique' class='formo'>\n"
		. "<option value=''></option>\n"
		. spiplistes_arbo_rubriques(0)
		. "</select>\n"
		. "<br />\n"
		//
		// sélecteur des mots-clés
		. "<label class='verdana2' for='ajouter_motcle'>"._T('spiplistes:Lister_articles_mot_cle').":</label>\n"
		. "<select name='id_mot' id='ajouter_motcle' class='formo'>\n"
		. "<option value=''></option>\n"
		;
	$rqt_gmc = spip_query ("SELECT id_groupe,titre FROM spip_groupes_mots WHERE articles='oui'");
	while ($row = spip_fetch_array($rqt_gmc)) {
		$id_groupe = intval($row['id_groupe']);
		$titre = $row['titre'];
		$page_result .= "<option value='' disabled='disabled'>". supprimer_numero (typo($titre)) . "</option>\n";
		$rqt_mc = spip_query ("SELECT id_mot,titre FROM spip_mots WHERE id_groupe=$id_groupe");
		while ($row = spip_fetch_array($rqt_mc)) {
			$id_mot = intval($row['id_mot']);
			$titre = supprimer_numero (typo($row['titre']));
			$page_result .= "<option value='$id_mot'>--$titre</option>\n";
		}
	}
	$page_result .= ""
		. "</select><br />\n"
		// texte introduction
		. "<label class='verdana2' style='display:block;' for='text_area'>"._T('spiplistes:Introduction_du_courrier_').":</label>\n"
		. afficher_barre('document.formulaire_courrier_edit.message')
		. "<textarea id='text_area' name='message' ".$GLOBALS['browser_caret']." rows='5' cols='40' wrap='soft' style='width:100%;'>\n"
		. "</textarea>\n"
		//
		. "<p class='verdana2'>\n"
			. _T('spiplistes:Cliquez_Generer_desc', array('titre_bouton'=>_T('spiplistes:Generer_Apercu'), 'titre_champ_texte'=>_T('spiplistes:texte_courrier')))
			. "</p>\n"
		. "<p class='verdana2' style='text-align:right;'>\n"
		. "<input type='submit' name='Valider' value='"._T('spiplistes:Generer_Apercu')."' class='fondo' /></p>\n"
		. fin_block() // fin_block_invisible
		. fin_cadre_relief(true)
		. "<br />\n"
		//
		// bloc texte
		. "<label for='texte_courrier'>"._T('spiplistes:texte_courrier')."</label>\n"
		. afficher_barre('document.formulaire_courrier_edit.texte')
		. "<textarea id='texte_courrier' name='texte' ".$GLOBALS['browser_caret']." class='formo' rows='20' cols='40' wrap=soft>\n"
		. $texte
		. "</textarea>\n"
		. (!$id_courrier ? "<input type='hidden' name='new' value=\"oui\" />\n" : "")
		//
		. "<p style='text-align:right;'>\n"
		. "<input type='submit' onclick='this.value=\"oui\";' id='btn_courrier_edit' name='btn_courrier_valider' value='"._T('bouton_valider')."' class='fondo' /></p>\n"
		//
		// fin formulaire
		. "</form>\n"
		. fin_cadre_formulaire(true)
		;
	
	echo($page_result);

	// COURRIER EDIT FIN ---------------------------------------------------------------

	echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();
	
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
