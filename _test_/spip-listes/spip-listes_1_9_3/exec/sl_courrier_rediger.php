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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/barre');
include_spip('inc/affichage');
include_spip('base/spip-listes');
include_spip('public/assembler');



function exec_sl_courrier_rediger(){

	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;
	$type = _request('type');
	$id_courrier = _request('id_message');

	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 

	if (_request('new') == "oui") { 
		$statut = 'redac'; 
		$type = 'nl'; 
		$result = spip_query("INSERT INTO spip_courriers (titre, date, statut, type, id_auteur) VALUES ("._q(_T('texte_nouveau_message')).", NOW(),"._q($statut).","._q($type).","._q($connect_id_auteur).")"); 
		$id_courrier = spip_insert_id(); 
	}

	// Admin SPIP-Listes
	echo debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	if ($connect_statut != "0minirezo" ) {
		echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
		echo fin_page();
		exit;
	}

	if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
		$statut_auteur=$statut;
		spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));
	}

	debut_gauche();
	spip_listes_raccourcis();
	creer_colonne_droite();

	debut_droite("messagerie");

	// MODE EDIT: Redaction d'un courrier ------------------------------------------

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

	if ($type == 'nl') $le_type = _T('spiplistes:email_collec');

	echo "<p><span style='font-family:Verdana,Arial,Sans,sans-serif;color:green;font-size:120%'><b>$le_type</b></span></p>";
	echo "<p style='margin-bottom:10px;font-family:Verdana,Arial,Sans,sans-serif;color:red;'>"._T('spiplistes:alerte_edit')."</p><br /><br />";

	echo debut_cadre_formulaire(_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_insert-slide.gif');

	
	// adapté de abomailman ()
	// MaZiaR - NetAktiv
	// tech@netaktiv.com

	
	$liste_patrons = find_all_in_path("patrons/","[.]html$");
		echo "<div id=\"ajax-loader\" align=\"right\"><img src=\""._DIR_PLUGIN_SPIPLISTES. "/img_pack/ajax_indicator.gif\" /></div>";
		echo "<div class='verdana2' id='envoyer'>";
	
		echo "<form method='POST' action='./?exec=sl_courrier_previsu' style='border: 0px; margin: 0px;' id='template' name='template'>";
		echo "<input name=\"id_courrier\" id=\"id_courrier\" type=\"hidden\" value=\"$id_courrier\" /><br /><br /><br />\n";

		echo "<br/><strong><label for='template'>"._T("Choisir un patron")."</label></strong><br/>";
		echo "<select name='template'  CLASS='formo'>";
		
		foreach($liste_patrons as $key => $val) {
			if(ereg("_texte",$val)) unset ($liste_patrons[$key]) ;
		}

		
		foreach($liste_patrons as $titre_option) {
			$titre_option = basename($titre_option,".html");		
			echo "<option value='".$titre_option."'>".$titre_option."</option>\n";
		}
		echo "</select><br />";
		
		
		
			
		echo "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />";
	echo '<script src="'.url_absolue(find_in_path('javascript/datepicker.js')).'" type="text/javascript"></script>';
	echo '<script src="'.url_absolue(find_in_path('javascript/jquery-dom.js')).'" type="text/javascript"></script>';

	echo "\n\n<script type=\"text/javascript\"><!-- \n$(document).ready(function(){ \n $.datePicker.setDateFormat('yyyy-mm-dd');\n"
	  . unicode2charset(charset2unicode(recuperer_fond('formulaires/date_picker_init'),'html'))
	  . " \n $('input.date-picker').datePicker({startDate:'01/01/1900'});\n }); \n //--></script> ";
	
	echo "<div style=\"float:right;width:50%\"><label for=\"lang\">Langue du courrier</label><br />\n";
		echo "<input name=\"lang\" /></div>\n";
	
	
 		echo "<label for=\"date\">Contenu a partir de cette date</label><br />\n";
		echo "<input name=\"date\" id=\"date\" class=\"date-picker\"  /><br /><br /><br />\n";



		echo "<strong><label for='sujet'>"._T("Et lister les articles de la rubrique")."</label></strong>";
		echo "<br />";
		
		echo "<select name=\"id_rubrique\"  CLASS='formo'>";
		echo "<option value=\"\"></option>";
		echo spiplistes_arbo_rubriques(0);
		echo "</select><br />";


		echo "<strong><label for='sujet'>"._T("Et lister les articles du mot cl&eacute;")."</label></strong>";
		echo "<br />";
		
		echo "<select name=\"id_mot\"  CLASS='formo'>";
		echo "<option value=\"\"></option>";
		$rqt_gmc = spip_query ("SELECT id_groupe, titre FROM spip_groupes_mots WHERE articles='oui'");
		while ($row = spip_fetch_array($rqt_gmc)) {
		$id_groupe = $row['id_groupe'];
		$titre = $row['titre'];
			echo "<option value='' disabled=\"disabled\">". supprimer_numero (typo($titre)) . "</option>";

			$rqt_mc = spip_query ("SELECT id_mot, titre FROM spip_mots WHERE id_groupe='".$id_groupe."'");

			while ($row = spip_fetch_array($rqt_mc)) {
				$id_mot = $row['id_mot'];
				$titre = $row['titre'];
				echo "<option value='".$id_mot ."'>--". supprimer_numero (typo($titre)) . "</option>";
			}
		
		}
		echo "</select><br />";

		echo "<strong><label for='sujet'>"._T("Sujet du courrier")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		
		echo "<input type='text' name='sujet' id='sujet' CLASS='formo' value=\"\" size='40'$js_titre /><br />\n";

		echo "<strong><label for='message'>"._T("Introduction &agrave; votre courrier, avant le contenu issu du site")."</label></strong>";
		echo aide ("raccourcis");
		echo "<br />";
		echo afficher_barre('document.template.message');
		echo "<textarea id='text_area' name='message' ".$GLOBALS['browser_caret']." class='formo' rows='5' cols='40' wrap=soft>";
		echo '' ;
		echo "</textarea>\n";
		echo "<div align='right'>";
		echo "<input type='submit' name='Valider' value='"._T('Apercu')."' class='fondo'></div>\n";
		echo "</form>";
		echo "</div>\n";
	

	fin_cadre_formulaire();
		
		echo "<div id='apercu'></div>";
	
	
	
	// MODE EDIT FIN ---------------------------------------------------------------

	echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."</p>" ;
	echo fin_gauche(), fin_page();

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
