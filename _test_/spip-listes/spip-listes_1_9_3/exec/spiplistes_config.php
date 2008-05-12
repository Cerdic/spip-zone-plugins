<?php

// exec/spiplistes_config.php

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

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_config () {

	include_spip('inc/distant');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/meta');
	include_spip('inc/config');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $couleur_foncee
		, $spip_lang_right
		;

	spiplistes_log("CONFIGURE ID_AUTEUR #$connect_id_auteur <<");
	
	$keys_complement_courrier = array(
		'opt_lien_en_tete_courrier', 'lien_patron'
		, 'opt_ajout_tampon_editeur', 'tampon_patron'
		);
	$keys_complement_courrier = array_merge($keys_complement_courrier, $_tampon_cles = explode(",", _SPIPLISTES_TAMPON_CLES));
	$tampon_labels = array_flip($_tampon_cles);
	foreach($tampon_labels as $key=>$value) {
		$tampon_labels[$key] = _T('spiplistes:'.$key);
	}

	$keys_param_valider = array(
		'email_defaut'
		, 'smtp_server'
		, 'smtp_login'
		, 'smtp_pass'
		, 'smtp_port'
		, 'mailer_smtp'
		, 'smtp_identification'
		, 'smtp_sender'
		, 'spiplistes_lots'
		, 'spiplistes_charset_envoi'
		);
	
	$keys_opts_param_valider = array(
		'opt_simuler_envoi' // demande à la méleuse de simuler l'envoi du courrier
		, 'opt_suspendre_trieuse' // suspendre la trieuse. Les listes restent en attente
		, 'opt_suspendre_meleuse' // suspendre les envois de courriers
		);
			
	$keys_console_syslog = array(
		'opt_console_syslog' // permet d'envoyer le journal sur syslog
		);
			
	// initialise les variables postées par le formulaire
	foreach(array_merge(
		array(
			'abonnement_valider', 'abonnement_config', 'param_reinitialise'
			, 'btn_complement_courrier'
			, 'btn_param_valider'
			, 'btn_console_syslog'
			, 'voir_logs'
		)
		, $keys_complement_courrier
		, $keys_param_valider
		, $keys_opts_param_valider
		, $keys_console_syslog
		) as $key) {
		$$key = _request($key);
	}

	$doit_ecrire_metas = false;
	
	if($abonnement_valider && $abonnement_config) {
		ecrire_meta('abonnement_config', $abonnement_config);
		$doit_ecrire_metas = true;
	}

	if($btn_complement_courrier) {
		foreach($keys_complement_courrier as $key) {
			if(!empty($$key)) {
				__plugin_ecrire_key_in_serialized_meta ($key, $$key, _SPIPLISTES_META_PREFERENCES);
			} 
			else {
				__plugin_ecrire_key_in_serialized_meta ($key, null, _SPIPLISTES_META_PREFERENCES);
			}
		}
		$doit_ecrire_metas = true;
	}
	
	if($btn_param_valider) {
		foreach($keys_param_valider as $key) {
			if(($key != 'email_defaut') || email_valide($email_defaut)) {
				ecrire_meta($key, $$key);
			}
		}
		foreach($keys_opts_param_valider as $key) {
			if(!empty($$key)) {
				if(!isset($GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES])) {
					$GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES] = array();
				}
				__plugin_ecrire_key_in_serialized_meta ($key, $$key, _SPIPLISTES_META_PREFERENCES);
			} 
			else {
				__plugin_ecrire_key_in_serialized_meta ($key, null, _SPIPLISTES_META_PREFERENCES);
			}
		}
		$doit_ecrire_metas = true;
	}
		
	if($btn_console_syslog) {
		foreach($keys_console_syslog as $key) {
			if(!empty($$key)) {
				if(!isset($GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES])) {
					$GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES] = array();
				}
				__plugin_ecrire_key_in_serialized_meta ($key, $$key, _SPIPLISTES_META_PREFERENCES);
			} 
			else {
				__plugin_ecrire_key_in_serialized_meta ($key, null, _SPIPLISTES_META_PREFERENCES);
			}
		}
		$doit_ecrire_metas = true;
	}
	
	if(!__server_in_private_ip_adresses() 
		&& spiplistes_pref_lire('opt_console_syslog')
		// si pas sur réseau privé et option syslog validé,
		// retire l'option syslog (cas de copie de base du LAN sur celle du WAN)
		) {
		__plugin_ecrire_key_in_serialized_meta ($key, null, _SPIPLISTES_META_PREFERENCES);
		$doit_ecrire_metas = true;
	}
	
	if($doit_ecrire_metas) {
		spiplistes_ecrire_metas();
	}

	// Paramétrages des envois
	$adresse_defaut = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
	$smtp_identification = (isset($GLOBALS['meta']['smtp_identification']) && ($GLOBALS['meta']['smtp_identification']=='oui')) ? "oui" : "non";
	$mailer_smtp = (isset($GLOBALS['meta']['mailer_smtp']) && ($GLOBALS['meta']['mailer_smtp']=='oui')) ? "oui" : "non";
	$smtp_port = (isset($GLOBALS['meta']['smtp_port']) && (!empty($GLOBALS['meta']['smtp_port']))) ? $GLOBALS['meta']['smtp_port'] : "25";
	$smtp_server = (isset($GLOBALS['meta']['smtp_server']) && (!empty($GLOBALS['meta']['smtp_server']))) ? $GLOBALS['meta']['smtp_server'] : "localhost";
	$smtp_sender = (email_valide($GLOBALS['meta']['smtp_sender'])) ? $GLOBALS['meta']['smtp_sender'] : $GLOBALS['meta']['email_webmaster'];

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:spip_listes');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "configuration";
	$sous_rubrique = _SPIPLISTES_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));
	
	// la configuration spiplistes est réservée aux supers-admins 
	if(!(($connect_statut == "0minirezo") && ($connect_toutes_rubriques))) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. "<br /><br /><br />\n"
		. spiplistes_gros_titre(_T('titre_page_config_contenu'), '', true)
		. barre_onglets($rubrique, _SPIPLISTES_PREFIX)
		. debut_gauche($rubrique, true)
		. __plugin_boite_meta_info(_SPIPLISTES_PREFIX, true)
		. creer_colonne_droite($rubrique, true)
		. spiplistes_boite_autocron()
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		;

	//////////////////////////////////////////////////////
	// Boite Mode d'inscription des visiteurs
	$checked1 = $checked2 = "";
	($GLOBALS['meta']['abonnement_config'] == 'simple') ? $checked1 = "checked='checked'"  : $checked2 = "checked='checked'" ;
	$page_result .= ""
		. debut_cadre_trait_couleur("redacteurs-24.gif", true, "", _T('spiplistes:mode_inscription'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)."' method='post'>\n"
		. "<p class='verdana2'>\n"
		. "<input type='radio' name='abonnement_config' value='simple' $checked1 id='statut_simple' />\n"
		. "<label for='statut_simple'>"._T('spiplistes:abonnement_simple')."</label>\n"
		. "</p>\n"
		. "<p class='verdana2'>\n"
		. "<input type='radio' name='abonnement_config' value='membre' $checked2 id='statut_membre' />\n"
		. "<label for='statut_membre'>"._T('spiplistes:abonnement_code_acces')."</label>\n"
		. "</p>\n"
		// bouton de validation
		. "<div style='text-align:right;'><input type='submit' name='abonnement_valider' class='fondo' value='"._T('bouton_valider')."' /></div>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Boite parametrage complément du courrier
	$opt_lien_en_tete_courrier = (spiplistes_pref_lire('opt_lien_en_tete_courrier') == 'oui');
	$lien_patron = spiplistes_pref_lire('lien_patron');
	$opt_ajout_tampon_editeur = (spiplistes_pref_lire('opt_ajout_tampon_editeur') == 'oui');
	$tampon_patron = spiplistes_pref_lire('tampon_patron');
	foreach($_tampon_cles as $key) {
		$$key = spiplistes_pref_lire($key);
	}
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_complement-24.png", true, "", _T('spiplistes:Complement_des_courriers'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)."' method='post'>\n"
		//
		// ajout du renvoi de tete, lien courrier
		. debut_cadre_relief("", true, "", _T('spiplistes:Complement_lien_en_tete'))
		. "<p class='verdana2'>"._T('spiplistes:Complement_lien_en_tete_desc')."</p>"
   	. "<input type='checkbox' name='opt_lien_en_tete_courrier' value='oui' id='opt-lien-en-tete-courrier' "
			. (($opt_lien_en_tete_courrier) ? "checked='checked'" : "")
			. " />\n"
   	. "<label class='verdana2' for='opt-lien-en-tete-courrier'>"._T('spiplistes:Complement_ajouter_lien_en_tete')."</label>\n"
		//
		// lien courrier: boite de sélection
		. "<div id='div-lien-en-tete-courrier' style='".(!$opt_lien_en_tete_courrier ? "display:none;" : "")."margin-top:1em;'>"
   	. "<label class='verdana2' style='padding-left:2ex;' for='lien_patron'>"._T('spiplistes:Patron_du_lien').":</label>\n"
		. spiplistes_boite_selection_patrons($lien_patron, true, _SPIPLISTES_PATRONS_TETE_DIR, "lien_patron", 1)
		. "</div>\n" // fin bloc div-lien-en-tete-courrier
		. fin_cadre_relief(true)
		//
		// ajout tampon editeur
		. debut_cadre_relief("", true, "", _T('spiplistes:Complement_tampon_editeur'))
		. "<p class='verdana2'>"._T('spiplistes:Complement_tampon_editeur_desc')."</p>"
   	. "<input type='checkbox' name='opt_ajout_tampon_editeur' value='oui' id='opt-ajout-tampon-editeur' "
			. ($opt_ajout_tampon_editeur ? "checked='checked'" : "")
			. " />\n"
   	. "<label class='verdana2' for='opt-ajout-tampon-editeur'>"._T('spiplistes:Complement_tampon_editeur_label')."</label>\n"
		//
		// coordonnées editeur: bloc coordonnes_editeur
		. "<div id='div-ajout-tampon-editeur' style='".(!$opt_ajout_tampon_editeur ? "display:none;" : "")."margin-top:1em;'>"
		// tampon sélecteur
   	. "<label class='verdana2' style='padding-left:2ex;' for='tampon_patron'>"._T('spiplistes:Patron_du_tampon').":</label>\n"
		. spiplistes_boite_selection_patrons($tampon_patron, true, _SPIPLISTES_PATRONS_TAMPON_DIR, "tampon_patron", 1)
		. "<ul class='verdana2' style='list-style:none;padding-left:2ex;'>\n"
		;
		foreach($_tampon_cles as $key) {
			$page_result .= ""
				. "<li><label for='id_$key'>".$tampon_labels[$key].":</label>"
				. "<input type='text' name='$key' id='id_$key' size='40' class='forml' value=\"{$$key}\" /></li>\n"
				;
		}
	$page_result .= ""
		. "</lu>\n"
		. "</div>\n" // fin bloc div-ajout-tampon-editeur
		. fin_cadre_relief(true)
		//
		// bouton de validation
		. "<div style='text-align:right;'><input type='submit' name='btn_complement_courrier' class='fondo' value='"._T('bouton_valider')."' /></div>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// Boite parametrage envoi du courrier
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_envoyer-24.png", true, "", _T('spiplistes:Envoi_des_courriers'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)."' method='post'>\n"
		//
		// adresse email de retour (reply-to)
		. debut_cadre_relief("", true, "", _T('spiplistes:adresse_envoi'))
		. "<input type='text' name='email_defaut' value='".$adresse_defaut."' size='30' class='forml' />\n"
		. fin_cadre_relief(true)
		//
		// Méthode d'envoi 
		. debut_cadre_relief("", true, "", _T('spiplistes:methode_envoi'))
		. "<div  class='verdana2'>\n"
		. _T('spiplistes:pas_sur')
		. bouton_radio("mailer_smtp", "non", _T('spiplistes:php_mail'), $mailer_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');")
		. "<br />\n"
		. bouton_radio("mailer_smtp", "oui", _T('spiplistes:smtp'), $mailer_smtp == "oui"
			, "changeVisible(this.checked, 'smtp', 'block', 'none');")
		. "</div>\n"
		//
		// si 'smtp', affiche bloc de paramétrage
		. "<div id='smtp' style='display:".(($mailer_smtp == "oui") ? "block;" : "none;")."'>\n"
		. "<ul class='verdana2' style='list-style: none;'>\n"
		. "<li>"._T('spiplistes:smtp_hote')." : <input type='text' name='smtp_server' value='$smtp_server' size='30' class='forml' /></li>\n"
		. "<li>"._T('spiplistes:smtp_port')." : <input type='text' name='smtp_port' value='$smtp_port' size='4' class='fondl' /></li>\n"
		. "<li>"
			. "<label for='smtp_sender'>"._T('spiplistes:adresse_smtp')." : </label>\n"
			. "<input type='text' id='smtp_sender' name='smtp_sender' value=\"$smtp_sender\" class='formo' />"
		. "</li>\n"
		. "<li>"._T('spiplistes:spip_ident')." : "
		. bouton_radio("smtp_identification", "oui", _T('item_oui'), ($smtp_identification == "oui"), "changeVisible(this.checked, 'smtp-auth', 'block', 'none');")
		. "&nbsp;"
		. bouton_radio("smtp_identification", "non", _T('item_non'), ($smtp_identification == "non"), "changeVisible(this.checked, 'smtp-auth', 'none', 'block');")."</li>\n"
		. "<div id='smtp-auth' style='display:".(($smtp_identification == "oui") ? "block;" : "none;" )."'>\n"
		. "<ul class='verdana2' style='list-style: none;'>\n"
		. "<li>"
			. "<label for='smtp_login'>"._T('item_login')." : </label>\n"
			. "<input type='text' id='smtp_login' name='smtp_login' value='".$GLOBALS['meta']['smtp_login']."' size='30' class='fondl' />\n"
		. "</li>\n"
		. "<li>"
			. "<label for='smtp_pass'>"._T('entree_passe_ldap')." : </label>\n"
			. "<input type='password' id='smtp_pass' name='smtp_pass' value='".$GLOBALS['meta']['smtp_pass']."' size='30' class='fondl' />\n"
		. "</li>\n"
		. "</ul>\n"
		. "</div>\n"
		. "</div>\n"
		. fin_cadre_relief(true)
		//
		// le nombre de lots d'envois
		. debut_cadre_relief("", true, "", _T('spiplistes:Parametrer_la_meleuse'))
		. __boite_select_de_formulaire (
			__array_values_in_keys(explode(";", _SPIPLISTES_LOTS_PERMIS)), $GLOBALS['meta']['spiplistes_lots']
				, 'spiplistes_lots', 'spiplistes_lots'
				, 1, '', 'fondo', _T('spiplistes:nombre_lot')." : ", '', 'verdana2')
		. "<br />\n"
		//
		// sélection du charset d'envoi
		. __boite_select_de_formulaire (
			__array_values_in_keys(explode(";", _SPIPLISTES_CHARSETS_ALLOWED)), $GLOBALS['meta']['spiplistes_charset_envoi']
				, 'spiplistes_charset_envoi', 'spiplistes_charset_envoi'
				, 1, '', 'fondo', _T('spiplistes:Jeu_de_caracteres')." : ", '', 'verdana2')
		. fin_cadre_relief(true)
		;
		//
	// options simulation des envois, suspendre le tri, la meleuse
	$page_result .= ""
		. debut_cadre_relief("", true, "", _T('spiplistes:Mode_suspendre_trieuse'))
   	. "<input type='checkbox' name='opt_suspendre_trieuse' value='oui' id='opt_suspendre_trieuse' "
			. ((spiplistes_pref_lire('opt_suspendre_trieuse') == 'oui') ? "checked='checked'" : "") . " />\n"
   	. "<label class='verdana2' for='opt_suspendre_trieuse'>"._T('spiplistes:Suspendre_le_tri_des_listes')."</label>\n"
		. fin_cadre_relief(true)
		//
		. debut_cadre_relief("", true, "", _T('spiplistes:Mode_suspendre_meleuse'))
   	. "<input type='checkbox' name='opt_suspendre_meleuse' value='oui' id='opt_suspendre_meleuse' "
			. ((spiplistes_pref_lire('opt_suspendre_meleuse') == 'oui') ? "checked='checked'" : "") . " />\n"
   	. "<label class='verdana2' for='opt_suspendre_meleuse'>"._T('spiplistes:Suspendre_lenvoi_des_courriers')."</label>\n"
		. fin_cadre_relief(true)
		//
		. debut_cadre_relief("", true, "", _T('spiplistes:mode_simulation'))
   	. "<input type='checkbox' name='opt_simuler_envoi' value='oui' id='opt_simuler_envoi' "
			. ((spiplistes_pref_lire('opt_simuler_envoi') == "oui") ? "checked='checked'" : "") . " />\n"
   	. "<label class='verdana2' for='opt_simuler_envoi'>"._T('spiplistes:simuler_les_envois')."</label>\n"
		. fin_cadre_relief(true)
		//
		// Boutons de reinit/reset/validation
		. "<div style='text-align:right;'>"
		/* CP: bouton à mettre en place après modif base/spiplistes_init.php (pas encore installé, voir SPIP-Listes-V)
		. "<label for='p_reinit' style='display:none;'>"._T('spiplistes:reinitialiser')."</label>\n"
		. "<input type='submit' name='param_reinitialise' value='"._T('spiplistes:reinitialiser')."' id='p_reinit' class='fondo' />&nbsp;"
		*/
		. "<input type='reset' name='param_reset' value='"._T('spiplistes:Retablir')."' class='fondo' id='p_reset' class='fondo' style='display:inline' />&nbsp;"
		. "<input type='submit' name='btn_param_valider' class='fondo' value='"._T('bouton_valider')."' /></div>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	//////////////////////////////////////////////////////
	// La console
		$page_result .= ""
			. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."console-24.gif", true, "", _T('spiplistes:Console'))
			;
		// Paramétrer la console de debug/logs si sur LAN
		if(__server_in_private_ip_adresses()) {
			$page_result .= ""
				. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)."' method='post'>\n"
				//
				// ajout du renvoi de tete
				. debut_cadre_relief("", true, "", _T('spiplistes:Console_syslog'))
				. "<p class='verdana2'>"._T('spiplistes:Console_syslog_desc', array('IP_LAN' => $_SERVER['SERVER_ADDR']))."</p>\n"
				. "<input type='checkbox' name='opt_console_syslog' value='oui' id='opt_console_syslog' "
					. ((spiplistes_pref_lire('opt_console_syslog')) ? "checked='checked'" : "")
					. " />\n"
				. "<label class='verdana2' for='opt_console_syslog'>"._T('spiplistes:Console_syslog_texte')."</label>\n"
				. fin_cadre_relief(true)
				//
				// bouton de validation
				. "<div style='text-align:right;'><input type='submit' name='btn_console_syslog' class='fondo' value='"._T('bouton_valider')."' /></div>\n"
				. "</form>\n"
				;
		}
		// voir les journaux SPIP
		if(!spiplistes_pref_lire('opt_console_syslog')) {
		// si syslog non activé, on visualise les journaux de spip
			// lien sur logs ou affiche logs
			$page_result .= ""
				. "<a id='view-spiplistes-log' name='view-spiplistes-log' href='#view-spiplistes-log' class='verdana2'>"
					. _T('spiplistes:Voir_les_journaux_SPIPLISTES')
					. "</a>\n"
				. "<div id='view-spiplistes-log-box'></div>\n"
				;
		}
		$page_result .= ""
			. fin_cadre_trait_couleur(true)
			;
	
	// Fin de la page
	echo($page_result);
	echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();
	
} // exec_config()

?>