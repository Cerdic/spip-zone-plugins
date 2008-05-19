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
/* d'adaptation dans un but specifique. Reportez-vous � la Licence Publique Generale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/presentation');

/*
	Les fonctions affichage et pr�sentation dans l'espace priv�
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

// renvoie un �l�ment de d�finition courriers/listes (icone, puce, alternate text, etc.)
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
		$ze_logo = ""; // semble ne plus �tre utilis� dans exec/*
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

//CP-20080508 
function spiplistes_bouton_block_depliable ($titre = "", $deplie = true, $nom_block = "", $icone = "") {
	if(empty($titre)) {
		$titre = _T("info_sans_titre");
	}
	include_spip('inc/layer');
	if(spiplistes_spip_est_inferieur_193()) {
		$f = ($deplie ? "bouton_block_visible" : "bouton_block_invisible");
		$result = $f($nom_block, $icone);
	} else {
		$result = bouton_block_depliable($titre, $deplie, $nom_block);
	}
	return($result);
}

// construit la boite de selection patrons (CP-20071012)
function spiplistes_boite_selection_patrons ($patron="", $return=false, $chemin="patrons/", $select_nom="patron", $size_select=10, $width='34ex') {
	global $couleur_claire;
	$result = "";
	// va chercher la liste des patrons
	$liste_patrons = spiplistes_liste_des_patrons ($chemin);
	// boite de s�lection du patron
	$result  .= "<select style='width:$width;'  name='". $select_nom . "' class='verdana1' size='" . $size_select . "'>\n";
	// par defaut, selectionne le premier
	$selected = (empty($title_selected) ? "selected='selected'" : ""); 
	foreach($liste_patrons as $titre_option) {
		$selected =
			($titre_option == $patron)
			? " selected='selected' style='background:$couleur_claire;' "
			: ""
			;
		$result .= "<option $selected value='" . $titre_option . "'>" . $titre_option . "</option>\n";
		if (!empty($selected)) {
			$selected = "";
		}
	}
	$result  .= "</select>\n";

	if($return) return($result);
	else echo($result);
}



// From SPIP-Listes-V: CP:20070923
function spiplistes_boite_patron ($flag_editable, $id_liste
	, $exec_retour, $nom_bouton_valider, $chemin_patrons, $titre_boite = ""
	, $msg_patron = false, $patron = "", $return = false) {
	// bloc s�lection patron
	$result = ""
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."patron-24.png", true)
		. "<div class='verdana1' style='text-align: center;'>\n"
		;
	$titre_boite = "<strong>$titre_boite</strong>\n";
	
	if($flag_editable) {
	// inclusion du script de gestion des layers de SPIP
		if(($patron === true) || (is_string($patron) && empty($patron))) {
			$result  .= ""
				. spiplistes_bouton_block_depliable ($titre_boite, true, md5($nom_bouton_valider))
				. (spiplistes_spip_est_inferieur_193() ? $titre_boite : "")
				. debut_block_visible(md5($nom_bouton_valider))
				;
		}
		else {
			$result  .= ""
				. spiplistes_bouton_block_depliable ($titre_boite, false, md5($nom_bouton_valider))
				. (spiplistes_spip_est_inferieur_193() ? $titre_boite : "")
				. debut_block_invisible(md5($nom_bouton_valider))
				;
		}
	}
	else {
		$result  .= $titre_boite;
	}
	if($flag_editable) {
		$result .= "\n"
			. "<form action='".generer_url_ecrire($exec_retour, "id_liste=$id_liste")."' method='post' style='margin:1ex;'>\n"
			. spiplistes_boite_selection_patrons ($patron, true, $chemin_patrons)
			. "<div style='margin-top:1em;text-align:right;'><input type='submit' name='$nom_bouton_valider' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
			. "</form>\n"
			. fin_block()
			;
	}
	else {
	}
	$result .= "\n"
		. "<div style='text-align:center'>\n"
		. ($msg_patron ? $msg_patron : "<span style='color:gray;'>&lt;"._T('spiplistes:aucun')."&gt;</span>\n")
		. "</div>\n"
		. "</div>\n"
		. fin_cadre_relief(true);
		;

	if($return) return($result);
	else echo($result);
}

// boite information avec juste titre et id
// A placer dans cadre gauche (ex.: exec/spiplistes_listes)
// si $id_objet (par exemple: 'id_auteur') va chercher le logo de l'objet
function spiplistes_boite_info_id ($titre, $id, $return = true, $id_objet = false) {
	global $spip_display;
	$result = "";
	if($id) {
		$logo = "";
		if($id_objet && ($spip_display != 4)) {
			include_spip("inc/iconifier");
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($id, $id_objet, 'on')) {
				list($img, $clic) = decrire_logo($id_objet,'on',$id, 170, 170, $logo, $texteon, $script);
				$logo = "<div style='text-align: center;margin:1em 0;'>$img</div>";
			}
			else {
				$logo = "";
			}
		}
		$result = 
			debut_boite_info(true)
			. "\n<div style='font-weight: bold; text-align: center; text-transform: uppercase;' class='verdana1 spip_xx-small'>"
			.  $titre
			. "<br /><span class='spip_xx-large'>"
			. $id
			. "</span></div>"
			. $logo
			. fin_boite_info(true)
			. "<br />"
		;
	}
	if($return) return($result);
	else echo($result);
}

// renvoie liste des patrons en excluant les sous-versions (texte, lang) (CP-20071012)
function spiplistes_liste_des_patrons ($chemin) {
	$liste_patrons = find_all_in_path($chemin, "[.]html$");
	$result = array();
	foreach($liste_patrons as $key => $value) {
		if (
			!ereg("_[a-z][a-z].html$", $value)
			&& !ereg("_texte.html$", $value)
			&& !ereg("_[a-z][a-z]_texte.html$", $value)
			) {
			$result[] = basename($value, ".html");
		}
	}
	sort($result);
	return($result);
}

// compl�te les dates chiffres (jour, heure, etc.)
// de retour du formulaire pour les dates et renvoie une date format�e correcte
function spiplistes_formate_date_form($annee, $mois, $jour, $heure, $minute) {
	if(!empty($jour) && !empty($mois) && !empty($annee) && (intval($heure) >= 0) && (intval($minute) >= 0)) {
		foreach(array('mois', 'jour', 'heure', 'minute') as $k) {
			if($$k < 10) {
				$$k = str_pad($$k, 2, "0", STR_PAD_LEFT);
			}
		}
		return($annee."-".$mois."-".$jour." ".$heure.":".$minute.":00");
	}
	return(false);
}

// Petit formulaire dans la boite autocron (CP-20071018)
function spiplistes_boite_autocron_form($titre, $option, $value) {
	global $connect_id_auteur;
	$result = "";
	// n'appara�t que si super_admin et pas sur la page de config (doublon de form)
	if($connect_id_auteur == 1) {
		if(_request('exec')!=_SPIPLISTES_EXEC_CONFIGURE) {
			$result = ""
				. "<!-- bouton annulation option -->\n"
				. "<form name='form_$option' id='id_form_$option' method='post' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE)."'"
					. " style='margin:0.5em 0;text-align:center;'>\n"
				. "<input type='hidden' name='$option' id='id_$option' value='$value' />\n"
				. "<label for='id_$option' style='display:none;'>$titre option</label>\n"
				. "<input type='submit' name='Submit' value='$titre' id='Submit' class='fondo' />\n"
				. "</form>\n"
				;
		}
		else {
			$result = ""
				. "<p class='verdana2'>"._T('spiplistes:Utilisez_formulaire')."</p>\n"
				;
		}
	}
	return($result);
}

// Petite boite info pour l'autocron (CP-20071018)
function spiplistes_boite_autocron_info ($icone = "", $return = false, $titre_boite = '', $bouton = "", $texte = "", $nom_option = "", $icone_alerte = false) {
	$result = ""
		. debut_cadre_couleur($icone, $return, $fonction, $titre_boite)
		. ($icone_alerte ? "<div style='text-align:center;'><img alt='' src='$icone_alerte' border='0' /></div>" : "")
		. ($texte ? "<p class='verdana2' style='margin:0;'>$texte</p>\n" : "")
		. ($bouton ? spiplistes_boite_autocron_form($bouton, $nom_option, 'non') : "")
		. fin_cadre_couleur($return)
		;
	if($return) return($result);
	else echo($result);
}

// la boite autocron apparait en backoffice sur la colone droite
// si courrier en cours d'envoi
function spiplistes_boite_autocron () { 
	@define('_SPIP_LISTE_SEND_THREADS',1);
	
	global $connect_id_auteur;
	
	// initialise les options
	foreach(array(
		'opt_suspendre_trieuse'
		, 'opt_suspendre_meleuse'
		, 'opt_simuler_envoi'
		) as $key) {
		$$key = spiplistes_pref_lire($key);
	}

	$result = "";
	
	// Informe sur l'�tat de la trieuse
	if($opt_suspendre_trieuse == 'oui') {
		if(_request('opt_suspendre_trieuse')=='non') {
			if($connect_id_auteur == 1) {
				__plugin_ecrire_key_in_serialized_meta ('opt_suspendre_trieuse', $opt_suspendre_trieuse = 'non', _SPIPLISTES_META_PREFERENCES);
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:Trieuse_reactivee')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_timer.gif", true
				, _T('spiplistes:trieuse_suspendue'), _T('bouton_annuler')
				, _T('spiplistes:trieuse_suspendue_info'), 'opt_suspendre_trieuse', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	// Informe sur l'�tat de la meleuse
	if($opt_suspendre_meleuse == 'oui') {
		if(_request('opt_suspendre_meleuse')=='non') {
			if($connect_id_auteur == 1) {
				__plugin_ecrire_key_in_serialized_meta ('opt_suspendre_meleuse', $opt_suspendre_meleuse = 'non', _SPIPLISTES_META_PREFERENCES);
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:Meleuse_reactivee')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_envoyer-24.png", true
				, _T('spiplistes:meleuse_suspendue'), _T('bouton_annuler')
				, _T('spiplistes:meleuse_suspendue_info'), 'opt_suspendre_meleuse', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	// Informe si mode simulation en cours
	if($opt_simuler_envoi == 'oui') {
		if(_request('opt_simuler_envoi')=='non') {
			if($connect_id_auteur == 1) {
				__plugin_ecrire_key_in_serialized_meta ('opt_simuler_envoi', $opt_simuler_envoi = 'non', _SPIPLISTES_META_PREFERENCES);
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:simulation_desactive')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_envoyer-24.png", true
				, _T('spiplistes:Mode_simulation'), _T('bouton_annuler')
				, _T('spiplistes:mode_simulation_info'), 'opt_simuler_envoi', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	include_spip('genie/spiplistes_cron');
	if($ii = cron_spiplistes_cron($time) > 0) { 
		// le CRON n'a rien a faire. Pas de boite autocron
		return($result);
	}
	
	$nb_etiquettes = spiplistes_courriers_en_queue_compter("etat=".sql_quote(''));
	$nb_abos_encour = spiplistes_courriers_en_cours_compter();

	if($nb_etiquettes > 0) {
		$result .= ""
			. "<br />"
			. debut_boite_info(true)
			. "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:envoi_en_cours')."</div>"
			. "<div style='padding : 10px;text-align:center'><img alt='' src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_distribution-48.gif' /></div>"
			. "<div id='meleuse'>"
			.	(
					($nb_abos_encour)
					?	""
						. "<p align='center' id='envoi_statut'>"._T('spiplistes:envoi_en_cours')." "
						. "<strong id='envois_restants'>$nb_etiquettes</strong>/<span id='envois_total'>$nb_abos_encour</span> "
						. "(<span id='envois_restant_pourcent'>"
						. round($nb_etiquettes/$nb_abos_encour*100)."</span>%)</p>"
					:	""
				)
			// message si simulation d'envoi	
			.	(
					($opt_simuler_envoi == 'oui') 
					? "<div style='color:white;background-color:red;text-align:center;line-height:1.4em;'>"._T('spiplistes:mode_simulation')."</div>\n" 
				: ""
				)
			;
		
		$href = generer_action_auteur('spiplistes_envoi_lot','envoyer');

		for ($i=0;$i<_SPIP_LISTE_SEND_THREADS;$i++) {
			$result .= "<span id='proc$i' class='processus' name='$href'></span>";
		}
		if (_request('exec')==_SPIPLISTES_EXEC_COURRIERS_LISTE) {
			$result .= "<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE)."' id='redirect_after'></a>";
		}
		$result .= ""
			. "</div>"
			. "<script><!--
		var target = $('#envois_restants');
		var total = $('#envois_total').html();
		var target_pc = $('#envois_restant_pourcent');
		function redirect_fin(){
			redirect = $('#redirect_after');
			if (redirect.length>0){
				href = redirect.attr('href');
				setTimeout('document.location.href = \"'+href+'\"',0);
			}
		}
		jQuery.fn.runProcessus = function(url) {
			var proc=this;
			var href=url;
			$(target).load(url,function(data){
				restant = $(target).html();
				pourcent=Math.round(restant/total*100);
				$(target_pc).html(pourcent);
				if (Math.round(restant)>0)
					$(proc).runProcessus(href);
				else
					redirect_fin();
			});
		}
		$('span.processus').each(function(){
			var href = $(this).attr('name');
			$(this).html(ajax_image_searching).runProcessus(href);
			//run_processus($(this).attr('id'));
		});
		//--></script>"
			. "<p class='verdana2'>"._T('spiplistes:texte_boite_en_cours')."</p>" 
			. fin_boite_info(true)
			;
	}
	return($result);
}

// adapt� de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com
 

// Afficher l'arbo
function  spiplistes_arbo_rubriques($id_rubrique,  $rslt_id_rubrique="") {
	global $ran;
	$ran ++;
	
	$marge="&nbsp;&nbsp;&nbsp;|";
	for ($g=0;$g<$ran;$g++) {
		if (($ran-1)==0) {
			$marge="&bull;";
		}
		else {
			$marge .="-"; 
		}
	}
	$marge .="&nbsp;";

	$rqt_rubriques = spip_query ("SELECT id_rubrique, id_parent, titre FROM spip_rubriques WHERE id_parent='".$id_rubrique."'");
	while ($row = spip_fetch_array($rqt_rubriques)) {
		$id_rubrique = $row['id_rubrique'];
		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$arbo .="<option value='".$id_rubrique."'>" . $marge  . supprimer_numero (typo($titre)) . "</option>";
		$arbo .= spiplistes_arbo_rubriques($id_rubrique,   $rslt_id_parent);
	}
	
	return $arbo;
	
}
// Nombre d'abonnes a une liste, chaine html
function spiplistes_nb_abonnes_liste_str_get ($id_liste) {
	$nb_abos = spiplistes_listes_nb_abonnes_compter($id_liste);
	$result =
		($nb_abos)
		? "(" . spiplistes_singulier_pluriel_str_get($nb_abos, _T('spiplistes:nb_abonnes_sing'), _T('spiplistes:nb_abonnes_plur')) . ")"
		: _T('spiplistes:sans_abonne')
		;
	return ($result);
}

// CP-20080510
function spiplistes_titre_boite_info ($titre = "") {
	global $spip_display, $spip_lang_left;
	$result =
		(!empty($titre))
		?
			"<h3 style='padding-$spip_lang_left:3px;text-align:$spip_lang_left;border-bottom:1px solid #444;margin:0;' class='verdana2'>"
			. $titre
			. "</h3>\n"
		: ""
		;
	return($result);
}

// termine page si la donn�e n'existe pas dans la base
function spiplistes_terminer_page_donnee_manquante ($return = true) {
	spiplistes_terminer_page_message (_T('spiplistes:Pas_de_donnees'), $return);
}


// termine la page (en affichant message ou retour)
function spiplistes_terminer_page_message ($message) {
	$result = "<p>$message</p>";
	if($return) return($result);
	else echo($result);
}

// termine la page (� employer qd droits insuffisants)
function spiplistes_terminer_page_non_autorisee ($return = true) {
	spiplistes_terminer_page_message (_T('spiplistes:acces_a_la_page'), $return);
}


//
?>