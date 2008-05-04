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
	Les fonctions affichage et présentation dans l'espace privé
*/

// retourne la puce qui va bien 
function spiplistes_bullet_titre_liste ($type, $statut, $id=false, $return=false) {
	$result = $img = $taille = "";
	switch($type) {
		case 'puce':
			$taille = "width='9' height='9'";
			break;
	}
	$img = spiplistes_items_get_item($type, $statut);
	$alt = spiplistes_items_get_item('alt', $statut);
	if($img) {
		$result = "<img src='$img' alt='".$alt."' ".(!empty($id) ? "id='$id'" : "")." $taille border='0' />\n";
	}
	if($return) return($result);
	else echo($result);
}

// renvoie un élément de définition courriers/listes (icone, puce, alternate text, etc.)
// voir spsiplites_mes_options, tableau $spiplistes_items
function spiplistes_items_get_item($item, $statut) {
	global $spiplistes_items;

	if(isset($spiplistes_items[$statut]) 
		&& isset($spiplistes_items[$statut][$item])
	) {
		return ($spiplistes_items[$statut][$item]);
	}
	else {
		return($spiplistes_items['default'][$item]);
	}
}

function spiplistes_gros_titre($titre, $ze_logo='', $return = false) {
	if(!spiplistes_spip_est_inferieur_193()) {
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
function spiplistes_form_debut ($action = '#', $return = false, $method = 'post') {
	$result = "<form action='".$action."' method='$method'>\n";
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
function spiplistes_fieldset_legend_detail ($texte = '', $return = false) {
	$result = "";
	if(!empty($texte)) {
		$result = " <span class='spiplistes-legend-stitre'>(".$texte.")</span>";
	}
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_description ($description, $return = false) {
	$result = spiplistes_form_message($description, $return);
	if($return) return($result);
}

// CP-20080323
function spiplistes_form_description_alert ($description, $return = false) {
	$result = spiplistes_form_message($description, $return, "message-alerte");
	if($return) return($result);
}

// CP-20080323
function spiplistes_form_message ($message, $return = false, $class = "") {
	$result = "";
	if(!empty($message)) {
		$result = "<p class='verdana2 $class'>".$message."</p>\n";
	}
	if($return) return($result);
	else echo($result);
}

// CP-20080502
function spiplistes_form_input_item ($type, $name, $value, $label, $checked, $return = false, $div = true, $id = false) {
	$result = ""
		. "<label class='verdana2'>"
		. "<input type='$type' id='".($id ? $id : $name)."' name='$name' value='$value'".($checked ? "checked='checked'" : "")."/>"
		. $label
		. "</label>\n"
		;
	if($div) {
		$result = "<div>\n".$result."</div>\n";
	}
	if($return) return($result);
	else echo($result);
}

// CP-20080323
function spiplistes_form_input_checkbox ($name, $value, $label, $checked, $return = false, $div = true) {
	$result = spiplistes_form_input_item('checkbox', $name, $value, $label, $checked, $return, $div);
	if($return) return($result);
}

// CP-20080502
function spiplistes_form_input_radio ($name, $value, $label, $checked, $return = false, $div = true) {
	static $id;
	$id++;
	$result = spiplistes_form_input_item('radio', $name, $value, $label, $checked, $return, $div, $name."_".$id);
	if($return) return($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_debut_raccourcis ($titre = "", $raccourcis = true, $return = false) {
  
  $result = ""
		. ($raccourcis ? creer_colonne_droite('', true) : "")
		. debut_cadre_enfonce('', true)
		. "<span class='verdana2' style='font-size:80%;text-transform: uppercase;font-weight:bold;'>$titre</span>"
		. "<br />"
		;
	if($return) return($result);
	else echo($result);
}


// From SPIP-Listes-V: CP:20070923
function spiplistes_fin_raccourcis ($return = false) {
	$result = ""
		. fin_cadre_enfonce(true)
		;
	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_boite_raccourcis ($return = false) {
	global $connect_id_auteur;
	
	$result = ""
		// Les raccourcis
		. spiplistes_debut_raccourcis(_T('titre_cadre_raccourcis'), true, true)
		. "<ul class='verdana2' style='list-style: none;padding:1ex;margin:0;'>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:Nouveau_courrier')
			, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'new=oui&type=nl')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_brouillon-24.png"
			,"creer.gif"
			,false
			)
		. "</li>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:Nouvelle_liste_de_diffusion')
			, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_EDIT,'new=oui')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif"
			,"creer.gif"
			,false
			)
		. "</li>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:import_export')
			, generer_url_ecrire(_SPIPLISTES_EXEC_IMPORT_EXPORT)
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."listes_inout.png"
			,""
			,false
			)
		. "</li>\n"
		;
	if($connect_id_auteur == 1) {
		$result .= ""
			. "<li>"
			. icone_horizontale(
				_T('titre_admin_tech')
				, generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE)
				, "administration-24.gif"
				,""
				,false
				)
			. "</li>\n"
			;
	}
	$result .= ""
		. "<!-- aide en ligne -->\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:aide_en_ligne')
			, generer_url_ecrire(_SPIPLISTES_EXEC_AIDE)
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."aide-24.png"
			, ""
			, false
			, " onclick=\"javascript:window.open(this.href,'spip_aide', 'scrollbars=yes, resizable=yes, width=740, height=580'); return false;\" "
			)
		. "</li>\n"
		;
	$result .= ""
		. "</ul>\n"
		. spiplistes_fin_raccourcis(true)
		;
	
	if($return) return($result);
	else echo($result);
}

function spiplistes_boite_info_spiplistes($return=false) {
	$result = ""
		// colonne gauche boite info
		. "<br />"
		. debut_boite_info(true)
		. _T('spiplistes:_aide')
		. fin_boite_info(true)
		;
	if($return) return($result);
	else echo($result);
}

//
?>