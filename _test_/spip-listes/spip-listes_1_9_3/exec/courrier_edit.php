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

function exec_courrier_edit(){

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
	echo "<p style='margin-bottom:10px;font-family:Verdana,Arial,Sans,sans-serif;color:red;'>"._T('vous pouvez modifier le HTML de ce courrier ou bien le regenerer')."</p><br /><br />";

	echo debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_insert-slide.gif');

   echo "<a href='./?exec=sl_courrier_rediger&id_message=$id_courrier'>Regenerer</a>" ;

	echo fin_cadre_relief();

	echo "<br />";

	echo "<form id='choppe_patron-1' action='".generer_url_ecrire("gerer_courrier","id_message=$id_courrier")."' method='post' name='choppe_patron-1'>";
	echo "<input type='hidden' name='modifier_message' value=\"oui\" />";
	echo "<input type='hidden' name='id_message' value=\"$id_courrier\" />";
	if(!intval($id_courrier))
		echo "<input type='hidden' name='new' value=\"oui\" />";

	echo _T('spiplistes:sujet_courrier');

	echo "<input type='text' class='formo' name='titre' value=\"$titre\" size='40' />";
	echo "<br />";
	echo "<br />";
	echo _T('spiplistes:texte_courrier');
	echo aide ("raccourcis");
	echo "<br />";
	echo afficher_barre('document.formulaire.texte');
	echo "<textarea id='text_area' name='texte' ".$GLOBALS['browser_caret']." class='formo' rows='20' cols='40' wrap=soft>";
	echo $texte;
	echo "</textarea>\n";

	echo "<p align='right'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
	echo "</form>";

	// MODE EDIT FIN ---------------------------------------------------------------

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
