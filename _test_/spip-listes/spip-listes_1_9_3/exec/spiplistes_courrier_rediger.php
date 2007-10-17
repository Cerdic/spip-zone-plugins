<?php
// _SPIPLISTES_EXEC_COURRIER_REDAC
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

// ce script ne sert plus (CP-20071011)
// le formulaire a été intégré dans courrier_edit

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_courrier_rediger () {

	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/spiplistes_api');
	include_spip('public/assembler');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;
	
	$id_courrier = intval(_request('id_courrier'));

	// COURRIER REDIGER: Redaction d'un courrier ------------------------------------------

	if($id_courrier > 0) {
	///////////////////////////
	// initialise les variables
		$sql_select = "";
		$result = spip_query("SELECT * FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 1");
		if ($row = spip_fetch_array($result)) {
			$date_heure = $row["date"];
			$titre = entites_html($row["titre"]);
			$texte = entites_html($row["texte"]);
			$type = $row["type"];
			$statut = $row["statut"];
			$id_auteur = $row["id_auteur"];
		}
		else {
			$id_courrier = false;
		}
	}

	if(!$id_courrier) {
	///////////////////////////
	// nouveau courrier
		$statut = _SPIPLISTES_STATUT_REDAC; 
		$type = 'nl';
		$id_courrier = 0;
	}

	if ($type == 'nl') $le_type = _T('spiplistes:email_collec');
	$liste_patrons = spiplistes_liste_des_patrons("patrons/");

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la création d'un courrier est réservée aux admins ou à l'auteur du courrier
	if(($connect_statut != "0minirezo") || ($id_auteur && ($id_auteur != $connect_id_auteur))) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spiplistes_boite_raccourcis();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	// Formulaire adapté de abomailman () // MaZiaR - NetAktiv	// tech@netaktiv.com
	
	$page_result = ""
		. "<a name='haut-block' id='haut-block'></a>"
		// 
		// le bloc pour aperçu
		. "<div id='apercu-courrier' style='clear:both;tex-align:center'></div>"
		//
		. debut_cadre_formulaire(_DIR_PLUGIN_SPIPLISTES_IMG_PACK.'stock_insert-slide.gif', true)
		. "<p><span style='font-family:Verdana,Arial,Sans,sans-serif;color:green;font-size:120%'><strong>$le_type</strong></span></p>"
		. "<p class='verdana2' style='margin-bottom:10px;font-family:Verdana,Arial,Sans,sans-serif;color:red;'>"._T('spiplistes:alerte_edit')."</p>"
		. "<br /><br />"
		. "<div id='ajax-loader' align='right'><img src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."ajax_indicator.gif' /></div>"
		. "<div class='verdana2' id='envoyer'>\n"
		//
		// début formulaire
		// nota: url action est forcé par javascript
		. "<form method='post' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_PREVUE."#haut-block")."'"
		.		" style='border: 0px; margin: 0px;' id='template' name='template'>"
		. "<input name='id_courrier' id='id_courrier' type='hidden' value='$id_courrier' />\n"
		. "<br />\n"
		//
		// sélecteur patron
		. "<strong><label for='patron'>"._T('spiplistes:Choisir_un_patron').":</label></strong><br />"
		. "<select name='patron' id='patron' class='formo'>"
		;
	foreach($liste_patrons as $titre_option) {
		$page_result .= "<option value='".$titre_option."'>".$titre_option."</option>\n";
	}
	$page_result .= ""
		. "</select>"
		. "<br />\n"
		//
		// sélecteur de date
		// nota: les scripts js sont appelés dans header_prive
		. "<script type='text/javascript'><!-- \n$(document).ready(function(){ \n $.datePicker.setDateFormat('yyyy-mm-dd');\n"
		. unicode2charset(charset2unicode(recuperer_fond('formulaires/date_picker_init'),'html'))
		. " \n $('input.date-picker').datePicker({startDate:'01/01/1900'});\n }); \n //--></script>\n"
		//
		// sélecteur de langues
		. "<div style='float:right;width:50%;'>"
		. "<label for='lang'>"._T('spiplistes:Langue_du_courrier_').":</label><br />\n"
		. "<select name='lang' class='fondo' id='lang'>\n"
		. liste_options_langues('changer_lang')
		. "</select>\n"
		. "</div>\n"
		//
		// la date
		. "<label for='date'>Contenu a partir de cette date</label><br />\n"
		. "<input name='date' id='date' class='date-picker'  /><br /><br /><br />\n"
		//
		// sélecteur de rubriques
		. "<label for='ajouter_rubrique' style='font-weight:bold;'>"._T('spiplistes:Lister_articles_de_rubrique').":</label>"
		. "<select name='id_rubrique' id='ajouter_rubrique' class='formo'>"
		. "<option value=''></option>"
		. spiplistes_arbo_rubriques(0)
		. "</select>"
		. "<br />\n"
		//
		// sélecteur des mots-clés
		. "<label for='ajouter_motcle' style='font-weight:bold;'>"._T('spiplistes:Lister_articles_mot_cle').":</label>"
		. "<select name='id_mot' id='ajouter_motcle' class='formo'>"
		. "<option value=''></option>"
		;
	$rqt_gmc = spip_query ("SELECT id_groupe,titre FROM spip_groupes_mots WHERE articles='oui'");
	while ($row = spip_fetch_array($rqt_gmc)) {
		$id_groupe = intval($row['id_groupe']);
		$titre = $row['titre'];
		$page_result .= "<option value='' disabled='disabled'>". supprimer_numero (typo($titre)) . "</option>";
		$rqt_mc = spip_query ("SELECT id_mot,titre FROM spip_mots WHERE id_groupe=$id_groupe");
		while ($row = spip_fetch_array($rqt_mc)) {
			$id_mot = intval($row['id_mot']);
			$titre = supprimer_numero (typo($row['titre']));
			$page_result .= "<option value='$id_mot'>--$titre</option>";
		}
	}
	$page_result .= ""
		. "</select><br />\n"
		//
		// champ du titre (sujet du courrier)
		. "<label style='font-weight:bold;' for='sujet'>"._T("Sujet du courrier")."</label> "._T('info_obligatoire_02').":"
		. "<br />"
		. "<input type='text' name='sujet' id='sujet' class='formo' value='' size='40'$js_titre /><br />\n"
		. "<label style='font-weight:bold;' for='text_area'>"._T("Introduction &agrave; votre courrier, avant le contenu issu du site")."</label>"
		. "<br />"
		. afficher_barre('document.template.message')
		. "<textarea id='text_area' name='message' ".$GLOBALS['browser_caret']." class='formo' rows='5' cols='40' wrap=soft>"
		. ""
		. "</textarea>\n"
		. "<p class='verdana2' style='text-align:right;'>"
		. "<input type='submit' name='Valider' value='"._T('spiplistes:Apercu')."' class='fondo' /></p>\n"
		. "</form>\n"
		. "</div>\n"
		. fin_cadre_formulaire(true)
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
