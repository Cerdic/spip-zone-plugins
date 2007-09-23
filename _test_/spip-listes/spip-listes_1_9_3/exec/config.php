<?php

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_config () {

	include_spip('inc/presentation');
	include_spip('inc/distant');
	include_spip('inc/affichage');
	include_spip('inc/meta');
	include_spip('inc/config');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $couleur_foncee
		, $spip_lang_right
		;
	
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
	
	// initialise les variables postées par le formulaire
	foreach(array_merge(
		array(
			'abonnement_valider', 'abonnement_config', 'param_reinitialise', 'param_valider'
		)
		, $keys_param_valider) as $key) {
		$$key = _request($key);
	}

	$doit_ecrire_metas = false;
	
	if($abonnement_valider && $abonnement_config) {
		ecrire_meta('abonnement_config', $abonnement_config);
		$doit_ecrire_metas = true;
	}

	if($param_valider) {
		foreach($keys_param_valider as $key) {
			if(($key != 'email_defaut') || email_valide($email_defaut)) {
				ecrire_meta($key, $$key);
				$doit_ecrire_metas = true;
			}
		}
	}
	
	if($doit_ecrire_metas) {
		ecrire_metas();
	}

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la configuration spiplistes est réservée aux supers-admins 
	if(!(($connect_statut == "0minirezo") && ($connect_toutes_rubriques))) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	echo "<br /><br /><br />\n";
	gros_titre(_T('titre_page_config_contenu'));
	echo barre_onglets("configuration", "spiplistes");

	debut_gauche();
	__plugin_boite_meta_info();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");

	$page_result = "";

	$checked1 = $checked2 = "";
	($GLOBALS['meta']['abonnement_config'] == 'simple') ? $checked1 = "checked='checked'"  : $checked2 = "checked='checked'" ;

	// Mode d'inscription des visiteurs
	$page_result .= ""
		. debut_cadre_trait_couleur("redacteurs-24.gif", true, "", _T('spiplistes:mode_inscription'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)."' method='post'>\n"
		. "<p class='verdana2'>\n"
		. "<input type='radio' name='abonnement_config' value='simple' $checked1 id='statut_simple' />\n"
		. "<label for='statut_simple'>"._T('spiplistes:abonnement_simple')."</label>"
		. "</p>\n"
		. "<p class='verdana2'>"
		. "<input type='radio' name='abonnement_config' value='membre' $checked2 id='statut_membre' />\n"
		. "<label for='statut_membre'>"._T('spiplistes:abonnement_code_acces')."</label>"
		. "</p>\n"
		. "<p class='verdana2' style='text-align:$spip_lang_right;'>\n"
		. "<label for='abonnement_valider' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' id='abonnement_valider' name='abonnement_valider' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</p>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	// Paramétrages des envois
	$adresse_defaut = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
	$mailer_smtp = (isset($GLOBALS['meta']['mailer_smtp']) && ($GLOBALS['meta']['mailer_smtp']=='oui')) ? "oui" : "non";
	$smtp_port = (isset($GLOBALS['meta']['smtp_port']) && (!empty($GLOBALS['meta']['smtp_port']))) ? $GLOBALS['meta']['smtp_port'] : "25";
	$smtp_server = (isset($GLOBALS['meta']['smtp_server']) && (!empty($GLOBALS['meta']['smtp_server']))) ? $GLOBALS['meta']['smtp_server'] : "localhost";
	$smtp_sender = (email_valide($GLOBALS['meta']['smtp_sender'])) ? $GLOBALS['meta']['smtp_sender'] : $GLOBALS['meta']['email_webmaster'];

	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_envoyer-24.png", true, "", _T('spiplistes:email_envoi'))
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)."' method='post'>\n"
		//
		// adresse email de retour (reply-to)
		. debut_cadre_relief("", true, "", _T('spiplistes:adresse_envoi'))
		. "<input type='text' name='email_defaut' value='".$adresse_defaut."' size='30' class='forml' />\n"
		. fin_cadre_relief(true)
		//
		// Méthode d'envoi 
		. debut_cadre_relief("", true, "", _T('spiplistes:methode_envoi'))
		. "<span  class='verdana2'>\n"
		. _T('spiplistes:pas_sur')
		. bouton_radio("mailer_smtp", "non", _T('spiplistes:php_mail'), $mailer_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');")
		. "<br />\n"
		. bouton_radio("mailer_smtp", "oui", _T('spiplistes:smtp'), $mailer_smtp == "oui"
			, "changeVisible(this.checked, 'smtp', 'block', 'none');")
		. "</span>\n"
		//
		// si 'smtp', affiche bloc de paramétrage
		. "<div id='smtp' style='display:".(($mailer_smtp == "oui") ? "block;" : "none;")."'>\n"
		. "<ul class='verdana2' style='list-style: none;'>\n"
		. "<li>"._T('spiplistes:smtp_hote')." : <input type='text' name='smtp_server' value='$smtp_server' size='30' class='forml' /></li>\n"
		. "<li>"._T('spiplistes:smtp_port')." : <input type='text' name='smtp_port' value='$smtp_port' size='4' class='fondl' /></li>\n"
		. "<li>"
			. "<label for='smtp_sender'>"._T('spiplistes:adresse_smtp')." : \n"
			. "<input type='text' id='smtp_sender' name='smtp_sender' value=\"$smtp_sender\" class='formo' /></p>\n"
		. "</li>\n"
		. "<li>"._T('spiplistes:spip_ident')." : "
		. bouton_radio("smtp_identification", "oui", _T('item_oui'), $smtp_identification == "oui", "changeVisible(this.checked, 'smtp-auth', 'block', 'none');")
		. "&nbsp;"
		. bouton_radio("smtp_identification", "non", _T('item_non'), $smtp_identification == "non", "changeVisible(this.checked, 'smtp-auth', 'none', 'block');")."</li>"
		. "<div id='smtp-auth' style='display:".(($smtp_identification == "oui") ? "block;" : "none;" )."'>\n"
		. "<ul class='verdana2' style='list-style: none;'>\n"
		. "<li>"
			. "<label for='smtp_login'>"._T('item_login')." : </label>"
			. "<input type='text' id='smtp_login' name='smtp_login' value='".$GLOBALS['meta']['smtp_login']."' size='30' class='fondl' />\n"
		. "</li>\n"
		. "<li>"
			. "<label for='smtp_pass'>"._T('entree_passe_ldap')." : </label>"
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
		//
		// Boutons de reinit/reset/validation
		. "<p style='text-align:right;' class='verdana2'>\n"
		/* CP: bouton à mettre en place après modif base/spiplistes_init.php (pas encore installé, voir SPIP-Listes-V)
		. "<label for='p_reinit' style='display:none;'>"._T('spiplistes:reinitialiser')."</label>\n"
		. "<input type='submit' name='param_reinitialise' value='"._T('spiplistes:reinitialiser')."' id='p_reinit' class='fondo' />&nbsp;"
		*/
		. "<label for='p_reset' style='display:none;'>"._T('spiplistes:Retablir')."</label>\n"
		. "<input type='reset' name='param_reset' value='"._T('spiplistes:Retablir')."' class='fondo' id='p_reset' class='fondo' style='display:inline' />&nbsp;"
		. "<label for='p_valid' style='display:none;'>"._T('bouton_valider')."</label>\n"
		. "<input type='submit' name='param_valider' value='"._T('bouton_valider')."' class='fondo' style='display:inline' />\n"
		. "</p>\n"
		//
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	// lien sur logs ou logs
	$page_result .=
		(_request('logs')=="oui")
		?
			""
			. "<a name='logs'></a>"
			. debut_cadre_relief("", true, "", "Logs")
			. "<div style='width:98%;overflow:auto'>"
			. "<pre>".spiplistes_console_lit_log("spiplistes")."</pre>"
			. "</div>"
			. fin_cadre_relief(true)
		:
			"<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE,'logs=oui#logs')."'>Logs</a>"
		;
	
	// Fin de la page
	echo($page_result);
	echo __plugin_html_signature(true), fin_gauche(), fin_page();
	
} // exec_config()

function spiplistes_console_lit_log($logname){
	$files = preg_files(defined('_DIR_TMP')?_DIR_TMP:_DIR_SESSION ,"$logname\.log(\.[0-9])?");
	krsort($files);

	$log = "";
	foreach($files as $nom){
		if (lire_fichier($nom,$contenu))
			$log.=$contenu;
	}
	$contenu = explode("<br />",nl2br($contenu));
	
	$out = "";
	$maxlines = 40;
	while ($contenu && $maxlines--){
		$out .= array_pop($contenu)."\n";
	}
	return $out;
}


/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'abonn� et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
?>