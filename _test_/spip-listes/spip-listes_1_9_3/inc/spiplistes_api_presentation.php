<?php

// inc/spiplistes_api_presentation.php
	
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
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/presentation');

/*
	Les fonctions affichage et présentation dans l'esapce privé
*/

function spiplistes_gros_titre($titre, $ze_logo='', $return = false) {
	if(version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) {
		$ze_logo = ""; // semble ne plus être utilisé dans exec/*
	}
	$aff = ($return === false);
	$r = gros_titre($titre, $ze_logo, $aff);
	if($return) return($r);
}

/*
	Les onglets dans la rubrique Edition (naviguer)
*/
function spiplistes_onglets ($rubrique, $onglet, $return = false) {

	$result = "";
	
	if ($rubrique == _SPIPLISTES_RUBRIQUE){
		$result = ""
			. "<br />"
			. debut_onglet()
			. onglet(_T('spiplistes:Casier_a_courriers'), generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_hyperlink-mail-and-news-24.gif")
			. onglet(_T('spiplistes:listes_de_diffusion_'), generer_url_ecrire(_SPIPLISTES_EXEC_LISTES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif")
			. onglet(_T('spiplistes:Suivi_des_abonnements'), generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."addressbook-24.gif")
			. fin_onglet()
		;
	}

	if($return) return($result);
	else echo($result);
} // end spiplistes_onglets()

// CP:20080322 :
function spiplistes_get_icone_auteur ($statut) {
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
	return($logo);
}

// CP-20080323
function spiplistes_form_bouton_valider ($name, $value, $reset = false, $return = false) {
	global $spip_lang_right;
	static $reset_value;
	if(!$reset_value) { 
		$reset_value = _T('spiplises:retablir');
	}
	$reset = 
		$reset
		? "<input type='reset' name='reset_".$name."' value='R&eacute;tablir' class='fondo' />\n"
		: ""
		;
	$result = ""
		. "<div class='verdana2' style='margin-top:1ex;text-align:$spip_lang_right;'>\n"
		. $reset
		. "<input type='submit' id='$name' name='$name' value='".$value."' class='fondo' />\n"
		. "</div>\n"
		;
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_fieldset_debut ($legend = "", $return = false) {
	if(!empty($legend)) {
		$legend = "<legend style='padding:0 1ex;'>".$legend."</legend>\n";
	}
	$result = "<fieldset class='verdana2'>".$legend;
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_fieldset_fin ($return = false) {
	$result = "</fieldset>\n";
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_debut ($action = '#', $method = 'post', $return = false) {
	$result = "<form action='".$action."' method='$method'>\n";
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_fieldset_legend_detail ($texte = '', $return = false) {
	$result = "";
	if(!empty($texte)) {
		$result = " <span class='spiplistes-legend-stitre'>(".$texte.")</span>";
	}
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_fin ($return = false) {
	$result = "</form>\n";
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_description ($description, $return = false) {
	$result = spiplistes_form_message($description, $return);
	if($return) return($result);
}

// CP-20080323
function spiplistes_form_message ($message, $return = false) {
	$result = "";
	if(!empty($message)) {
		$result = "<p class='verdana2'>".$message."</p>\n";
	}
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_input_checkbox ($name, $value, $label, $return = false) {
	$result = ""
		. "<div>"
		. "<label>"
		. "<input type='checkbox' id='$name' name='$name' value='$value' />"
		. $label
		. "</label>"
		. "</div>\n"
		;
	if($return) return($result);
	else echo($result);
}

?>