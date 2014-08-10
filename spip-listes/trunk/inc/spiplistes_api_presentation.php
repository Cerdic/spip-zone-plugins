<?php
/**
 * Fonctions d'affichage et de presentation dans l'espace prive
 * 
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

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
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/spiplistes_api_journal');

/**
 * Retourne la puce qui va bien
 * @return string|null
 */
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

/**
 * Renvoie un element de definition courriers/listes
 * (icone, puce, alternate text, etc.)
 * voir spiplistes_mes_options, tableau $spiplistes_items
 * @global array $spiplistes_items
 * @return string|null
 */
function spiplistes_items_get_item($item, $statut) {
	$spiplistes_items=get_spiplistes_items();
	
	if(isset($spiplistes_items[$statut]) 
		&& isset($spiplistes_items[$statut][$item])
	) {
		return ($spiplistes_items[$statut][$item]);
	}
	else {
		return($spiplistes_items['default'][$item]);
	}
}

/**
 * @return string
 */
function spiplistes_gros_titre ($titre, $logo='', $return = false) {

	$aff = ($return === false);
	$size = 24;
	if (preg_match("/-([0-9]{1,3})[.](gif|png)$/i",$logo,$match))
		$size = $match[1];
	$icone = http_img_pack($logo, $alt, "width='$size' height='$size'");
	
	$r = gros_titre($titre, $icone, $aff);
	if($return) return($r);
}

/**
 * @version CP:20080322
 * @param string $statut
 * @return string
 */
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

/**
 * Bouton Valider pour les formulaires en /exec/
 * @version CP-20080323
 * @param string $name
 * @param string $value
 * @param bool $reset
 * @global string $spip_lang_right
 * @staticvar string $submit_value Titre du bouton
 * @staticvar string $reset_value Titre lien reset
 * @return string
 */
function spiplistes_form_bouton_valider ($name, $value = "", $reset = false) {
	global $spip_lang_right;
	static $submit_value, $reset_value;
	if(!$submit_value) { 
		$submit_value = _T('bouton_valider');
	}
	if(!$reset_value) { 
		$reset_value = _T('spiplistes:retablir');
	}
	$value = (!empty($value) ? $value : $submit_value);
	$reset = 
		$reset
		? "<input type='reset' name='reset_".$name."' value=\"".$reset_value."\" class='fondo' />\n"
		: ""
		;
	$result = ""
		. "<div class='verdana2' style='margin-top:1ex;text-align:$spip_lang_right;'>\n"
		. $reset
		. "<input type='submit' id='$name' name='$name' value='".$value."' class='fondo' />\n"
		. "</div>\n"
		;
	return($result);
}

/**
 * Debut de formulaire HTML
 * @version CP-20080323
 * @param string $action
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @param string $post
 * @return string|null
 */
function spiplistes_form_debut ($action = '#', $return = false, $method = 'post') {
	$result = "<form action='".$action."' method='$method'>\n";
	if($return) return($result);
	else echo($result);
}

/**
 * Fin de formulaire HTML
 * @version CP-20080323
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @return string|null
 */
function spiplistes_form_fin ($return = false) {
	$result = "</form>\n";
	if($return) return($result);
	else echo($result);
}

/**
 * Debut de fieldset de formulaire HTML
 * @version CP-20080323
 * @param string $legend
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @return string|null
 */
function spiplistes_form_fieldset_debut ($legend = "", $return = false) {
	if(!empty($legend)) {
		$legend = "<legend style='padding:0 1ex;'>".$legend."</legend>\n";
	}
	$result = "<fieldset class='verdana2'>".$legend;
	if($return) return($result);
	else echo($result);
}

/**
 * Fin de fieldset de formulaire HTML
 * @version CP-20080323
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @return string|null
 */
function spiplistes_form_fieldset_fin ($return = false) {
	$result = "</fieldset>\n";
	if($return) return($result);
	else echo($result);
}

/**
 * @version CP-20080323
 * @param string $description
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @return string|null
 */
function spiplistes_form_description ($description, $return = false) {
	$result = spiplistes_form_message($description, $return);
	if($return) return($result);
}

/**
 * @version CP-20080323
 * @param string $description
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @return string|null
 */
function spiplistes_form_description_alert ($description, $return = false) {
	$result = spiplistes_form_message($description, $return, "message-alerte");
	if($return) return($result);
}

/**
 * @version CP-20080323
 * @param string $message
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @param string $class
 * @return string|null
 */
function spiplistes_form_message ($message, $return = false, $class = '') {
	$result = "";
	if(!empty($message)) {
		$result = "<p class='verdana2 $class'>".$message."</p>\n";
	}
	if($return) return($result);
	else echo($result);
}

/**
 * Retourne un element HTML
 * 
 * @param string $name
 * @param string $value
 * @param string $label
 * @param bool $checked si case a cocher cochee
 * @param bool $return Imprimer si TRUE (utilise echo())
 * @param bool $div Envelopper le resultat si TRUE 
 * @version CP-20080502
 * @return string|null
*/
function spiplistes_form_input_item ($type, $name, $value, $label, $checked, $return = false, $div = true, $id = false) {
	$result = ""
		. "<label class='verdana2'>"
		. "<input type='$type' id='".($id ? $id : $name)."' name='$name' value='$value' ".($checked ? "checked='checked'" : "")."/>"
		. $label
		. "</label>\n"
		;
	if($div) {
		$result = "<div>\n".$result."</div>\n";
	}
	if($return) return($result);
	else echo($result);
}

/**
 * Retourne un element HTML case a cocher
 *
 * {@link spiplistes_form_input_item()}
 * @param string $name
 * @param string $value
 * @param string $label
 * @param bool $checked case a cocher cochee
 * @param bool $return Imprimer si TRUE (utilise echo())
 * @param bool $div Envelopper le resultat si TRUE 
 * @version CP-20080323
 * @return string|null
*/
function spiplistes_form_input_checkbox ($name, $value, $label, $checked, $return = false, $div = true) {
	$result = spiplistes_form_input_item('checkbox', $name, $value, $label, $checked, $return, $div);
	if($return) return($result);
}

/**
 * @version CP-20080502
 * @param string $name
 * @param string $value
 * @param string $label
 * @param bool $checked
 * @param bool $return Retourne value si FALSE, sinon echo ()
 * @param bool $div
 * @return string|null
 */
function spiplistes_form_input_radio ($name, $value, $label, $checked, $return = false, $div = true) {
	static $id;
	$id++;
	$result = spiplistes_form_input_item('radio', $name, $value, $label, $checked, $return, $div, $name."_".$id);
	if($return) return($result);
}


/**
 * @version SPIP-Listes-V: CP:20070923
 */
function spiplistes_boite_raccourcis ($return = false) {
	// initialise les options
	foreach(array(
			'opt_console_debug'
	) as $key) {
		$$key = spiplistes_pref_lire($key);
	}
	
    return recuperer_fond("prive/inclure/boite_raccourcis", array('opt_console_debug' => $opt_console_debug, 'ip_serveur' => $_SERVER['SERVER_ADDR']));

}


function spiplistes_boite_info_spiplistes($return=false) {
	$result = ""
		// colonne gauche boite info
		. "<br />"
		. boite_ouvrir("",'info')
		. _T('spiplistes:_aide')
		. boite_fermer()
		;
	if($return) return($result);
	else echo($result);
}

// Pour construire des menu avec SELECTED
// http://doc.spip.org/@mySel
function mySel($varaut,$variable, $option = NULL) {
	$res = ' value="'.$varaut.'"' . (($variable==$varaut) ? ' selected="selected"' : '');

	return  (!isset($option) ? $res : "<option$res>$option</option>\n");
}




//CP-20080508 
function spiplistes_bouton_block_depliable ($titre = "", $deplie = true, $nom_block = "", $icone = "") {
	if(empty($titre)) {
		$titre = _T("info_sans_titre");
	}
	include_spip('inc/layer');
	$result = bouton_block_depliable($titre, $deplie, $nom_block);
	return($result);
}

// construit la boite de selection patrons (CP-20071012)
function spiplistes_boite_selection_patrons ($patron="", $return=false
						, $chemin="patrons/", $select_nom="patron"
						, $size_select=10, $width='34ex') {
	global $couleur_claire;
	$result = "";
	// va chercher la liste des patrons
	$liste_patrons = spiplistes_liste_des_patrons ($chemin);
	// boite de selection du patron
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
			!preg_match("=\.[a-z][a-z]\.html$=", $value)
			&& !preg_match("=_texte\.html$=", $value)
			&& !preg_match("=_texte\.[a-z][a-z]\.html$=", $value)
			) {
			$result[] = basename($value, ".html");
		}
	}
	sort($result);
	return($result);
}

// complete les dates chiffres (jour, heure, etc.)
// de retour du formulaire pour les dates et renvoie une date formatee correcte
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
	$connect_id_auteur = intval($GLOBALS['connect_id_auteur']);
	
	$result = "";
	// n'apparait que si super_admin et pas sur la page de config (doublon de form)
	if(autoriser('webmestre','','',$connect_id_auteur)) {
		if(_request('exec')!=_SPIPLISTES_EXEC_CONFIGURE) {
			$result = ""
				. "<!-- bouton annulation option -->\n"
				. "<form id='id_form_$option' method='post' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE)."'"
					. " style='margin:0.5em 0;text-align:center;'>\n"
				. "<input type='hidden' name='$option' id='id_$option' value='$value' />\n"
				. "<label for='id_$option' style='display:none;'>$titre option</label>\n"
				. "<input type='submit' name='Submit' value='$titre' id='Submit' class='fondo' />\n"
				. "</form>\n"
				;
		}
		else {
			$result = ""
				. "<p class='verdana2'>"._T('spiplistes:utilisez_formulaire_ci_contre')."</p>\n"
				;
		}
	}
	return($result);
}

// Petite boite info pour l'autocron (CP-20071018)
function spiplistes_boite_autocron_info ($icone = "", $return = false, $titre_boite = '', $bouton = "", $texte = "", $nom_option = "", $icone_alerte = false) {
	$result = ""
		. "<div class='spip-alert'>\n"
		. debut_cadre_couleur_foncee($icone, true, '', $titre_boite)
		. ($icone_alerte ? "<div style='text-align:center;'><img alt='' src='$icone_alerte' /></div>" : "")
		. ($texte ? "<p class='verdana2' style='margin:0;'>$texte</p>\n" : "")
		. ($bouton ? spiplistes_boite_autocron_form($bouton, $nom_option, 'non') : "")
		. fin_cadre_couleur(true)
		. "</div>\n"
		;
	if($return) return($result);
	else echo($result);
}

/*
 * boite info sur les simulation et les envois en cours
 * @return la boite autocron, chaine html
 */
function spiplistes_boite_autocron () { 

	//nombre de processus d'envoi simultanes
	if (!defined('_SPIP_LISTE_SEND_THREADS')) {
		define('_SPIP_LISTE_SEND_THREADS', 1);
	}
	
	$connect_id_auteur = intval($GLOBALS['connect_id_auteur']);
	
	// initialise les options
	foreach(array(
		'opt_suspendre_trieuse'
		, 'opt_suspendre_meleuse'
		, 'opt_simuler_envoi'
		) as $key) {
		$$key = spiplistes_pref_lire($key);
	}

	$result = "";
	
	// Informe sur l'etat de la trieuse
	if($opt_suspendre_trieuse == 'oui') {
		if(_request('opt_suspendre_trieuse')=='non') {
			if(autoriser('webmestre','','',$connect_id_auteur)) {
				spiplistes_ecrire_key_in_serialized_meta ('opt_suspendre_trieuse', $opt_suspendre_trieuse = 'non', _SPIPLISTES_META_PREFERENCES);
				//spiplistes_ecrire_metas();
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:trieuse_reactivee')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info("stock_timer.gif", true
				, _T('spiplistes:trieuse_suspendue'), _T('bouton_annuler')
				, _T('spiplistes:trieuse_suspendue_info'), 'opt_suspendre_trieuse', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	// Informe sur l'etat de la meleuse
	if($opt_suspendre_meleuse == 'oui') {
		if(_request('opt_suspendre_meleuse')=='non') {
			if(autoriser('webmestre','','',$connect_id_auteur)) {
				spiplistes_ecrire_key_in_serialized_meta ('opt_suspendre_meleuse', $opt_suspendre_meleuse = 'non', _SPIPLISTES_META_PREFERENCES);
				//spiplistes_ecrire_metas();
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:meleuse_reactivee')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info("courriers_envoyer-24.png", true
				, _T('spiplistes:meleuse_suspendue'), _T('bouton_annuler')
				, _T('spiplistes:meleuse_suspendue_info'), 'opt_suspendre_meleuse', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	// Informe si mode simulation en cours
	if($opt_simuler_envoi == 'oui') {
		if(_request('opt_simuler_envoi')=='non') {
			if(autoriser('webmestre','','',$connect_id_auteur)) {
				spiplistes_ecrire_key_in_serialized_meta ('opt_simuler_envoi', $opt_simuler_envoi = 'non', _SPIPLISTES_META_PREFERENCES);
				//spiplistes_ecrire_metas();
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:simulation_desactive')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info("courriers_envoyer-24.png", true
				, _T('spiplistes:mode_simulation'), _T('bouton_annuler')
				, _T('spiplistes:mode_simulation_info'), 'opt_simuler_envoi', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	include_spip('genie/spiplistes_cron');
	$time = time();
	$time = cron_spiplistes_cron($time);

	if($time > 0) { 
		// le CRON n'a rien a faire. Pas de boite autocron
		return($result);
	}

	$nb_etiquettes = spiplistes_courriers_en_queue_compter("etat=".sql_quote(''));
	$nb_total_abonnes = spiplistes_courriers_total_abonnes();

	if(($nb_etiquettes > 0) && ($nb_total_abonnes > 0)) {
		$result .= ""
			. "<br />"
			. debut_boite_info(true)
			. "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:envoi_en_cours')."</div>"
			. "<div style='padding : 10px;text-align:center'><img alt='' src='"."courriers_distribution-48.gif' /></div>"
			. "<div id='meleuse'>"
			.	(
					($nb_total_abonnes)
					?	""
						. "<p align='center' id='envoi_statut'>"._T('spiplistes:envoi_en_cours')." "
						. "<strong id='envois_restants'>$nb_etiquettes</strong>/<span id='envois_total'>$nb_total_abonnes</span> "
						. "(<span id='envois_restant_pourcent'>"
						. round($nb_etiquettes / $nb_total_abonnes * 100)."</span>%)</p>"
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

		for ($i = 0; $i < _SPIP_LISTE_SEND_THREADS; $i++) {
			$result .= "<span id='proc$i' class='processus' name='$href'></span>";
		}
		$result .= ""
			. "<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE)."' id='redirect_after'></a>"
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
			var proc = this;
			var href = url;
			$(target).load(url,function(data){
				restant = $(target).html();
				pourcent = Math.round(restant/total*100);
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
		//-->
		</script>"
			. "<p class='verdana2'>"._T('spiplistes:texte_boite_en_cours')."</p>" 
			. fin_boite_info(true)
			;
	}
	return($result);
}

// adapte de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com


// CP-20080528
// Renvoie l'arborescence des rubriques
function spiplistes_arbo_rubriques ($id_rubrique = 0, $ran = 0) {
	
	// une seule rqt pour recuperer toutes les rubriques
	$sql_result = sql_select("id_rubrique,id_parent,titre", "spip_rubriques");
	$rubriques_array = array();
	// empile les rubriques
	while($row = sql_fetch($sql_result)) {
		$rubriques_array[] = array(
			'id_rubrique' => intval($row['id_rubrique'])
			, 'id_parent' => intval($row['id_parent'])
			, 'titre' => supprimer_numero(typo($row['titre']))
		);
	}
	// renvoie la liste sous forme de option de select (html)
	return(
		"<!-- liste des rubriques -->\n"
		. spiplistes_arbo_rubriques_sub($rubriques_array)
	);
}

// sous-fonction de spiplistes_arbo_rubriques()
// recursive
function spiplistes_arbo_rubriques_sub ($rubriques_array, $id_parent = 0, $ran = 0) {
	$result = "";
	$marge =
		($ran)
		? "&nbsp;&nbsp;&nbsp;|".str_repeat("--", $ran)
		: "&bull;"
		;
	foreach($rubriques_array as $rubrique) {
		if($rubrique['id_parent'] == $id_parent) {
			$result .= ""
				. "<option value='" . $rubrique['id_rubrique'] . "'>" . $marge . "&nbsp;" . $rubrique['titre'] . "</option>\n"
				. spiplistes_arbo_rubriques_sub($rubriques_array, $rubrique['id_rubrique'], $ran + 1)
				;
		}
	}
	return($result);
}

// Nombre d'abonnes a une liste, chaine html
function spiplistes_nb_abonnes_liste_str_get ($id_liste, $nb_abos = false, $html = false, $texte = false) {
	if($nb_abos === false) {
		list($nb_abos, $html, $texte) = spiplistes_listes_nb_abonnes_compter($id_liste, true);
	} 
	$absents = $nb_abos - ($html + $texte);
	$result =
		($nb_abos)
		?	""
			. spiplistes_singulier_pluriel_str_get($nb_abos, _T('spiplistes:nb_abonnes_sing'), _T('spiplistes:nb_abonnes_plur')) 
			.	(
				$absents 
				? _T('spiplistes:_dont_n_sans_format_reception', array('n' => $absents))
				: ""
				)
		: _T('spiplistes:sans_abonne')
		;
	return ($result);
}

/** 
 * Nombre de destinataires d'une liste, chaine html
 * @version CP-20081126
 * @param int $nb
 * @return string
 */
function spiplistes_nb_destinataire_str_get ($nb) {
	$result = ($nb > 0)
			? spiplistes_singulier_pluriel_str_get(
				$nb
				, _T('spiplistes:nb_destinataire_sing')
				, _T('spiplistes:nb_destinataire_plur')
				)
			: _T('spiplistes:aucun_destinataire')
			;
	return($result);
}

/**
 * Nombre de listes, chaine html
 * @version CP-20081126
 * @param int $nb
 * @return string
 */
function spiplistes_nb_listes_str_get ($nb) {
	if(!$nb) return ("");
	$result = 
		($nb == 1)
		? _T('spiplistes:1_liste', array('n' => $nb))
		: _T('spiplistes:n_listes', array('n' => $nb))
		;
	return($result);
}

/**
 * Nombre de moderateurs d'une liste, chaine html
 * @version CP-20080610
 * @param int|bool $nb
 * @return string
 */
function spiplistes_nb_moderateurs_liste_str_get ($nb) {
	$result = ""
		. "("
		.	(
			($nb)
			? spiplistes_singulier_pluriel_str_get(
				$nb
				, _T('spiplistes:nb_moderateur_sing'), _T('spiplistes:nb_moderateur_plur')) 
			: _T('spiplistes:sans_moderateur')
			)
		. ")"
		;
	return ($result);
}

/**
 * @version CP-20080510
 * @param string $titre
 * @return string
 */
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

/**
 * termine page si la donnee n'existe pas dans la base
 * @param bool $return
 * @return string
 */
function spiplistes_terminer_page_donnee_manquante ($return = true) {
	spiplistes_terminer_page_message (_T('spiplistes:pas_de_donnees'), $return);
}

/**
 * termine la page (en affichant message ou retour)
 * @param string $message
 * @return string
 */
function spiplistes_terminer_page_message ($message) {
	$result = '<p>'.$message.'</p>'.PHP_EOL;
	if($return) return($result);
	else echo($result);
}

/**
 * termine la page (a employer quand droits insuffisants)
 * @param bool $return
 * @return string
 */
function spiplistes_terminer_page_non_autorisee ($return = true) {
	spiplistes_terminer_page_message (_T('avis_non_acces_page'), $return);
}

/**
 * @param string $id
 * @return string
 */
function spiplistes_debut_block_visible ($id="") {
	if(!function_exists('debut_block_visible')) {
		include_spip('inc/layer');
		return(debut_block_depliable(true,$id));
	}
	return(debut_block_visible($id));
}

/**
 * @param string $id
 * @return string
 */
function spiplistes_debut_block_invisible ($id="") {
	if(!function_exists('debut_block_invisible')) {
		include_spip('inc/layer');
		return debut_block_depliable(false,$id);
	}
	return(debut_block_invisible($id));
}

/**
 * Renvoie tableau de listes valides avec nb abonnes
 * du style :
 *   array(
 *     array(
 *       $id_liste
 *       , $titre  // titre de la liste
 *       , $nb_abos
 *     )
 *   , ...
 *   )
 * @version CP-20080430
 * @return array|null
 */
function spiplistes_listes_lister_abos () {
	$sql_select = array('l.id_liste', 'l.titre', 'COUNT(a.id_auteur) AS nb_abos');
	$sql_from = "spip_listes as l LEFT JOIN spip_auteurs_listes AS a ON l.id_liste=a.id_liste";
	$sql_where = "l.statut=".implode(" OR l.statut=", array_map("sql_quote", explode(";", _SPIPLISTES_LISTES_STATUTS_OK)));
	$sql_group = 'l.id_liste';
	if($sql_result = sql_select($sql_select, $sql_from, $sql_where, $sql_group)) {
		$result = array();
		while($row = sql_fetch($sql_result)) {
			$result[] = $row;
		}
		return($result);
	}
	return(NULL);
}

/**
 * CP-20081112
 * deux fonctions issues de fmp3
 * - fmp3_envelopper_script()
 * - fmp3_compacter_script()
 * 
 * enveloppe un script javascript pour insertion dans le code de la page
 */
function spiplistes_envelopper_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		switch($format) {
			case 'css':
				$source = "\n<style type='text/css'>\n<!--\n" 
					. $source
					. "\n-->\n</style>";
				break;
			case 'js':
				$source = "\n<script type='text/javascript'>\n//<![CDATA[\n" 
					. $source
					. "\n//]]>\n</script>";
				break;
			default:
				$source = "\n\n<!-- erreur envelopper: format inconnu [$format] -->\n\n";
		}
	}
	return($source);
} // end spiplistes_envelopper_script()

/**
 * Complement des deux 'compacte'. supprimer les espaces en trop.
 * 
 * @return string
 */ 
function spiplistes_compacter_script ($source, $format) {
	$source = trim($source);
	if(!empty($source)) {
		$source = compacte($source, $format);
		$source = preg_replace(",/\*.*\*/,Ums","",$source); // pas de commentaires
		$source = preg_replace('=[[:space:]]+=', ' ', $source); // reduire les espaces
	}
	return($source);
} // end spiplistes_compacter_script()

/**
 * Donne les infos systemes du plugin.
 * 
 * @return string
 */ 
function spiplistes_plugin_get_infos($plug) {
	if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
		$get_infos = charger_fonction('get_infos','plugins');
		$infos = $get_infos($plug);
	}
	else {
		$infos = plugin_get_infos($plug);
	}
	return($infos);
}

/**
 * Petite signature du plugin
 *
 * Signature affichee en bas de formulaire en espace prive.
 * @param $prefix string prefix du plugin
 * @param $html bool si true, renvoyer le resultat au format html
 * @param $verifier_svn si true
 * @return string petite signature de plugin (version plugin, version base, version jquery)
 */
function spiplistes_html_signature ($prefix, $html = true, $verifier_svn = false) {
	
	$info = spiplistes_plugin_get_infos(spiplistes_get_meta_dir($prefix));
	$nom = typo($info['nom']);
	$version = typo($info['version']);
	//$base_version = typo($info['version_base']); // cache ?
	$base_version = spiplistes_current_version_base_get($prefix);
	$svnrevision = spiplistes_current_svnrevision_get($prefix, $verifier_svn);
	$revision = "";
	
	if($html) {
		$version = (($version) ? " <span style='color:gray;'>".$version : "")
			. (($svnrevision) ? "-".$svnrevision : "")
			. "</span>"
			;
		$base_version = (($base_version) ? " <span style='color:#66c;'>&lt;".$base_version."&gt;</span>" : "");
	}
	$result = ''
		. $nom
		. ' ' . $version
		. ' ' . $base_version
		;
	if($html) {
		$result = "<p class='verdana1 spip_xx-small' style='font-weight:bold;'>$result\n"
		. "<script type='text/javascript'>\n"
		. "//<![CDATA[\n"
		. "document.write(' <span style=\'color:green\'>jQuery ' + jQuery.fn.jquery + '</span>')"
		. "//]]>\n"
		. "</script>\n"
		. "<noscript>\n"
		. "<span style='color:red'>" . _T('spiplistes:jquery_inactif') . "</span>"
		. "</noscript>\n"
		. "</p>\n";
	}
	return($result);
} // end spiplistes_html_signature()

/**
 * Le numero de revision svn
 * @param $prefix prefix du plugin
 * @param $s si true, va chercher le numero dans le repertoire du plugin
 * @return string
 */
function spiplistes_current_svnrevision_get ($prefix, $verifier_svn) {
	static $svn_revision = false;
	if(!empty($prefix)) {
		// lire directement dans plugin.xml (eviter le cache ?)
		$dir_plugin = _DIR_PLUGINS.spiplistes_get_meta_dir($prefix);
		// cherche si sur version svn
		if(!$result = version_svn_courante($dir_plugin)) {
			// mefiance: il faut que svn/entries soit a jour (svn update sur le poste de travail/serveur !)
			// si pas de svn/entries, lire l'attribut dans plugin.xml
			$file = $dir_plugin."/paquet.xml";
			$result = spiplistes_svn_revision_read($file);
		}
		if($verifier_svn && !$svn_revision) {
			// verifier les fichiers inclus (gourmand et peut-etre trompeur si fichier fantome ?)
			// ne verifier que sur deux niveaux (PLUGIN_ROOT et ses repertoires directs, pas en dessous)
			define("_PGL_SVN_LIRE_EXTENSIONS", "css|html|js|php|xml");
			$script_files = array();
			if(is_dir($dir_plugin) && ($dh = opendir($dir_plugin))) {
				while (($file = readdir($dh)) !== false) {
					if($file[0] == ".") continue;
					if(is_dir($dir_plugin_sub = $dir_plugin."/".$file) && ($dh_s = opendir($dir_plugin_sub))) {
						while (($file = readdir($dh_s)) !== false) {
							if($file[0] == ".") continue;
							if(preg_match('=\.('._PGL_SVN_LIRE_EXTENSIONS.')$=i', $file)) {
								$script_files[] = $dir_plugin_sub."/".$file;
							}
						}
						closedir($dh_s);
					}
					else if(preg_match('=\.('._PGL_SVN_LIRE_EXTENSIONS.')$=i', $file)) {
						$script_files[] = $dir_plugin."/".$file;
					}
				}
				closedir($dh);
			}
			foreach($script_files as $file) {
				if(!$ii = spiplistes_svn_revision_read ($file)) {	continue; }
				$result = max($ii, $result);
			}
		}
		if(!empty($result) && (intval($result) > 0)) return($result);
	}
	return(false);
} // end spiplistes_current_svnrevision_get()

/**
 * lire le fichier, en esperant trouver le mot cle svn dans les $buf_size premiers caracteres
 * @return le numero de revision svn
 * @param $filename
 * @param $buf_size
 */
function spiplistes_svn_revision_read ($filename, $buf_size = 2048) {
	if($fh = fopen($filename, "rb")) {
		$buf = fread($fh, $buf_size);
		fclose($fh);
		if($buf = strstr($buf, "$"."LastChanged"."Revision:")) {
			$revision = preg_replace('=^\$LastChanged'.'Revision: ([0-9]+) \$.*$=s', '${1}', $buf, 1);
			if(strval(intval($revision)) == $revision) { 
				return($revision);
			}
		}
	}
	return (false);
} // end spiplistes_svn_revision_read()

/**
 * @return Renvoie ou affiche une boite d'alerte
 */
function spiplistes_boite_alerte ($message, $return = false) {
	$result = ""
		. debut_boite_alerte()
		.  http_img_pack("warning.gif", _T('info_avertissement'), 
				 "style='width:48px;height:48px;float:right;margin:5px;'")
		. "<span class='message-alerte'>$message</span>\n"
		. fin_boite_alerte()
		. "\n<br />"
		;
	if($return) return($result);
	else echo($result);
}

 /**
  * Un petit bloc info sur le plugin
  * @param string $prefix
  * @return string
  */
function spiplistes_boite_meta_info ($prefix) {
	include_spip('inc/meta');
	$result = false;
	if(!empty($prefix)) {
		$meta_info = spiplistes_get_meta_infos($prefix); // dir et version
		$info = spiplistes_plugin_get_infos($meta_info['dir']);
		$icon = 
			(isset($info['icon']))
			? "<div "
				. " style='width:64px;height:64px;"
					. "margin:0 auto 1em;"
					. "background: url(". _DIR_PLUGINS.$meta_info['dir']."/".trim($info['icon']).") no-repeat center center;overflow: hidden;'"
				. " title='Logotype plugin $prefix'>"
				. "</div>\n"
			: ""
			;
		if(isset($info['etat']) && $info['etat']) {
			if($info['etat'] == 'stable') {
			// en version stable, ne sort plus les infos de debug
				foreach(array('description','lien','auteur') as $key) {
					if(isset($info[$key]) && !isset($meta_info[$key])) {
						$meta_info[$key] = $info[$key];
					}
				}
				$result .= spiplistes_boite_meta_info_liste($meta_info, true) // nom, etat, dir, version, description, lien, auteur
					;
			}
			else {
			// un peu plus d'info en mode test et dev
				$result .= 
					spiplistes_boite_meta_info_liste($meta_info, true) // nom, etat, dir, version
					. spiplistes_boite_meta_info_liste($info, ($info['etat']=='dev'))  // et tout ce qu'on a en magasin
					;
		}
		}
		if(!empty($result)) {
			$result = ""
				. debut_cadre_relief('plugin-24.gif', true, '', _T($prefix.':'.$prefix))
				. $icon
				. $result
				. fin_cadre_relief(true)
				;
		}
	}
	return($result);
} // spiplistes_boite_meta_info()

/**
 * Petite boite info pour la page de configuration du plugin
 *
 * Apparait sur la gauche de la page. Affiche infos de configuration.
 * @param array $array
 * @param bool $recursive
 * @global string $spip_lang_left
 * @return string
 */
function spiplistes_boite_meta_info_liste($array, $recursive = false) {
	global $spip_lang_left;
	$result = '';
	if(is_array($array)) {
		
		$eviter = array(
			'version' // inutile. deja affiche' en bas de page
			, 'version_base' // idem
			, 'nom' // deja en titre de boite
			, 'filemtime', 'icon', 'prefix' // sans interet
			
		);
		
		foreach($array as $key => $value) { 
			if(!in_array($key, $eviter)) {
				$sub_result = '';
				if(is_array($value)) {
					if($recursive) {
						$sub_result = spiplistes_boite_meta_info_liste($value);
					}
				}
				else {
					$sub_result = propre($value);
				}
				if(!empty($sub_result)) {
					$result .= "<li><span style='font-weight:bold;'>$key</span> : $sub_result</li>\n";
				}
			}
		}
		if(!empty($result)) {
			$result = "<ul style='margin:0;padding:0 1ex;list-style: none;text-align: $spip_lang_left;' class='verdana2 meta-info-liste'>$result</ul>";
		}
	}
	return($result);
}

/**
 * Petit bouton aide a placer a droite du titre de bloc
 * @param string $fichier_exec_aide 
 * @param string $aide
 * @param bool $return
 * @return string|null
 */
function spiplistes_plugin_aide ($fichier_exec_aide, $aide='', $return=true) {
	include_spip('inc/minipres');
	include_spip('inc/aider');
	global $spip_lang
		, $spip_lang_rtl
		, $spip_display
		;
	if (!$aide || $spip_display == 4) return;
	
	$t = _T('titre_image_aide');
	$result = ""
	. "\n&nbsp;&nbsp;<a class='aide'\nhref='"
	. generer_url_ecrire($fichier_exec_aide, "var_lang=$spip_lang")
	. (
		(!empty($aide)) 
		? "#$aide" 
		: ""
		)
	. "'"
	. " onclick=\"javascript:window.open(this.href,'spip_aide', 'scrollbars=yes, resizable=yes, width=740, height=580'); return false;\">\n"
	. http_img_pack(
		"aide".aide_lang_dir($spip_lang,$spip_lang_rtl).".gif"
		, _T('info_image_aide')
		, " title=\"$t\" class='aide'"
		)
	. "</a>"
	;
	
	if($return) return($result);
	else echo($result);
} // spiplistes_plugin_aide()

/**
 * @param string $date 
 * @return string date, chaine html
 */
function spiplistes_affdate ($date) {
	$result = "";
	if($date) {
		$proch = round((strtotime($date) - time()) / _SPIPLISTES_TIME_1_DAY);
		$result = 
			(
			($proch > 1)
			? affdate_jourcourt($date)
			: "<strong>"._T($proch ? 'date_demain' : 'date_aujourdhui')."</strong>"
			)
			. "<br />".heures_minutes($date)
			;
	}
	return($result);
}