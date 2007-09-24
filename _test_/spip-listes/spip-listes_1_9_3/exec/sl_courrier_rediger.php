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

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_sl_courrier_rediger(){

	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/affichage');
	include_spip('base/spip-listes');
	include_spip('public/assembler');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;
	
	$type = _request('type');
	$id_courrier = _request('id_message');

	/* Crée le courrier par simple appel de la page. Pas bon.
		Doit être validé par exec/gerer_courrier
	*/
	if (_request('new') == "oui") { 
		$statut = 'redac'; 
		$type = 'nl'; 
		$result = spip_query("INSERT INTO spip_courriers (titre, date, statut, type, id_auteur) VALUES ("._q(_T('texte_nouveau_message')).", NOW(),"._q($statut).","._q($type).","._q($connect_id_auteur).")"); 
		$id_courrier = spip_insert_id(); 
	}

	// COURRIER REDIGER: Redaction d'un courrier ------------------------------------------

	$result = spip_query("SELECT * FROM spip_courriers WHERE id_courrier="._q($id_courrier));
	if ($row = spip_fetch_array($result)) {
		$id_courrier = $row['id_courrier'];
		$date_heure = $row["date"];
		$titre = entites_html($row["titre"]);
		$texte = entites_html($row["texte"]);
		$type = $row["type"];
		$statut = $row["statut"];
		$expediteur = $row["id_auteur"];
		if (!($expediteur == $connect_id_auteur OR ($type == 'nl' AND $connect_statut == '0minirezo'))) 
			die();
	}

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la création d'un courrier est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spiplistes_boite_raccourcis();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	if ($type == 'nl') $le_type = _T('spiplistes:email_collec');
	$liste_patrons = spiplistes_liste_des_patrons("patrons/");

	// Formulaire adapté de abomailman () // MaZiaR - NetAktiv	// tech@netaktiv.com
	$page_result = ""
		. debut_cadre_formulaire(_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_insert-slide.gif', true)
		. "<p><span style='font-family:Verdana,Arial,Sans,sans-serif;color:green;font-size:120%'><strong>$le_type</strong></span></p>"
		. "<p class='verdana2' style='margin-bottom:10px;font-family:Verdana,Arial,Sans,sans-serif;color:red;'>"._T('spiplistes:alerte_edit')."</p>"
		. "<br /><br />"
		. "<div id=\"ajax-loader\" align=\"right\"><img src=\""._DIR_PLUGIN_SPIPLISTES_IMG_PACK."ajax_indicator.gif\" /></div>"
		. "<div class='verdana2' id='envoyer'>"
		
		. "<form method='post' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE)."'"
		.		" style='border: 0px; margin: 0px;' id='template' name='template'>"
		. "<input name=\"id_courrier\" id=\"id_courrier\" type=\"hidden\" value=\"$id_courrier\" />\n"
		. "<br/><strong><label for='template'>"._T("Choisir un patron")."</label></strong><br/>"
		. "<select name='template' class='formo'>"
		;
	foreach($liste_patrons as $titre_option) {
		$page_result .= "<option value='".$titre_option."'>".$titre_option."</option>\n";
	}
	$page_result .= "</select><br />";
		
	$page_result .= ""
		. "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />"
		. '<script src="'.url_absolue(find_in_path('javascript/datepicker.js')).'" type="text/javascript"></script>'
		. '<script src="'.url_absolue(find_in_path('javascript/jquery-dom.js')).'" type="text/javascript"></script>'
		. "\n\n<script type=\"text/javascript\"><!-- \n$(document).ready(function(){ \n $.datePicker.setDateFormat('yyyy-mm-dd');\n"
		. unicode2charset(charset2unicode(recuperer_fond('formulaires/date_picker_init'),'html'))
		. " \n $('input.date-picker').datePicker({startDate:'01/01/1900'});\n }); \n //--></script> "
		//
		// sélecteur de langues
		. "<div style='float:right;width:50%;'>"
		. "<label for='lang'>"._T('spiplistes:Langue_du_courrier_:')."</label><br />\n"
		. "<select name='lang'  class='fondo' id='lang'>\n"
		. liste_options_langues('changer_lang')
		. "</select>\n"
		. "</div>\n"
		//
		// la date
		. "<label for=\"date\">Contenu a partir de cette date</label><br />\n"
		. "<input name=\"date\" id=\"date\" class=\"date-picker\"  /><br /><br /><br />\n"
		. "<strong><label for='sujet'>"._T("Et lister les articles de la rubrique")."</label></strong>"
		. "<br />"
		//
		// sélecteur de rubriques
		. "<select name=\"id_rubrique\"  class='formo'>"
		. "<option value=\"\"></option>"
		. spiplistes_arbo_rubriques(0)
		. "</select><br />"
		//
		// sélecteur des mots-clés
		. "<strong><label for='sujet'>"._T("Et lister les articles du mot cl&eacute;")."</label></strong>"
		. "<br />"
		. "<select name=\"id_mot\"  CLASS='formo'>"
		. "<option value=\"\"></option>"
		;
	$rqt_gmc = spip_query ("SELECT id_groupe, titre FROM spip_groupes_mots WHERE articles='oui'");
	while ($row = spip_fetch_array($rqt_gmc)) {
		$id_groupe = $row['id_groupe'];
		$titre = $row['titre'];
		$page_result .= "<option value='' disabled=\"disabled\">". supprimer_numero (typo($titre)) . "</option>";
		$rqt_mc = spip_query ("SELECT id_mot, titre FROM spip_mots WHERE id_groupe='".$id_groupe."'");
		while ($row = spip_fetch_array($rqt_mc)) {
			$id_mot = $row['id_mot'];
			$titre = $row['titre'];
			$page_result .= "<option value='".$id_mot ."'>--". supprimer_numero (typo($titre)) . "</option>";
		}
	}
	$page_result .= ""
		. "</select><br />"
		//
		// champ du titre (sujet du courrier)
		. "<strong><label for='sujet'>"._T("Sujet du courrier")."</label></strong> "._T('info_obligatoire_02')
		. "<br />"
		. "<input type='text' name='sujet' id='sujet' CLASS='formo' value=\"\" size='40'$js_titre /><br />\n"
		. "<strong><label for='message'>"._T("Introduction &agrave; votre courrier, avant le contenu issu du site")."</label></strong>"
		. "<br />"
		. afficher_barre('document.template.message')
		. "<textarea id='text_area' name='message' ".$GLOBALS['browser_caret']." class='formo' rows='5' cols='40' wrap=soft>"
		. ''
		. "</textarea>\n"
		. "<p class='verdana2' style='text-align:right;'>"
		. "<input type='submit' name='Valider' value='"._T('Apercu')."' class='fondo' /></div>\n"
		. "</form>"
		. "</div>\n"
		. fin_cadre_formulaire(true)
		// 
		// le bloc pour aperçu
		. "<div id='apercu'></div>"
		;
	
	echo($page_result);
	
	// COURRIER REDIGER: FIN ---------------------------------------------------------------

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
