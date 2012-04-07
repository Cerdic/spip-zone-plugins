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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/distant');
include_spip('inc/affichage');
include_spip('inc/meta');
include_spip('inc/config');


function spiplistes_configurer() {
	if ($abonnement_config = _request('abonnement_config'))
		ecrire_meta('abonnement_config', $abonnement_config);
	
	if ($adresse_defaut = _request('email_defaut') AND email_valide($adresse_defaut)) {
		ecrire_meta('email_defaut', $adresse_defaut);
	}
	
	if ($smtp_server = _request('smtp_server'))
		ecrire_meta('smtp_server', $smtp_server);

	if ($smtp_login = _request('smtp_login'))
		ecrire_meta('smtp_login', $smtp_login);
	
	if ($smtp_pass = _request('smtp_pass'))
		ecrire_meta('smtp_pass', $smtp_pass);
	
	if ($smtp_port = _request('smtp_port'))
		ecrire_meta('smtp_port', $smtp_port);
	
	if ($mailer_smtp = _request('mailer_smtp'))
		ecrire_meta('mailer_smtp', $mailer_smtp);
	
	if ($smtp_identification = _request('smtp_identification'))
		ecrire_meta('smtp_identification', $smtp_identification);
	
	if ($smtp_sender = _request('smtp_sender'))
		ecrire_meta('smtp_sender', $smtp_sender);

	ecrire_metas();
}

function exec_config(){

	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur,$couleur_foncee;
	
	$reinitialiser_config = _request('reinitialiser_config');
	$Valider_reinit = _request('Valider_reinit');

	$nomsite=$GLOBALS['meta']['nom_site']; 
	$urlsite=$GLOBALS['meta']['adresse_site']; 


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

	// MODE CONFIG: Configuration de spip-listes -----------------------------------

	spiplistes_configurer();

	$config = $GLOBALS['meta']['abonnement_config'] ;

	echo debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:mode_inscription'));
	echo "<form action='".generer_url_ecrire('config')."' method='post'>";
	echo "<input type='hidden' name='changer_config' value='oui' />";
	
	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";
	
	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";

	$texte1 = '' ;
	$texte2 = '' ;
	($config == 'simple' ) ? $texte1 = "checked"  : $texte2 = "checked" ;

	echo "<input type='radio' name='abonnement_config' value='simple' $texte1 id='statut_simple' />";
	echo "<label for='statut_simple'>"._T('spiplistes:abonnement_simple')."</label> ";
	echo "<p><input type='radio' name='abonnement_config' value='membre' $texte2 id='statut_membre' />";
	echo "<label for='statut_membre'>"._T('spiplistes:abonnement_code_acces')."</label> ";
	echo "</td></tr>";
	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
	echo "</td></tr>";
	echo "</table>\n";
	
	echo "</form>";
	echo fin_cadre_relief();

	echo "<form action='".generer_url_ecrire('config')."' method='post'>";

	echo '<br />';
	echo debut_cadre_relief("", false, "", _T('spiplistes:email_envoi'));

	echo debut_cadre_trait_couleur("", false, "", _T('spiplistes:adresse_envoi'));
	$adresse_defaut = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
	echo "<input type='text' name='email_defaut' value='".$adresse_defaut."' size='30' CLASS='formo' />";
	
	echo fin_cadre_trait_couleur();

	echo debut_cadre_trait_couleur("", false, "", _T('spiplistes:methode_envoi'));

	echo _T('spiplistes:pas_sur');

	$mailer_smtp = $GLOBALS['meta']['mailer_smtp'];

	echo bouton_radio("mailer_smtp", "non", _T('spiplistes:php_mail'), $mailer_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');");
	echo "<br />";
	echo bouton_radio("mailer_smtp", "oui", _T('spiplistes:smtp'), $mailer_smtp == "oui", "changeVisible(this.checked, 'smtp', 'block', 'none');");

	if ($mailer_smtp == "oui") $style = "display: block;";
	else $style = "display: none;";
	echo "<div id='smtp' style='$style'>";
	echo "<ul>";
	echo "<li>"._T('spiplistes:smtp_hote')." <input type='text' name='smtp_server' value='".$GLOBALS['meta']['smtp_server']."' size='30' class='formo' />";
	echo "<li>"._T('spiplistes:smtp_port')." <input type='text' name='smtp_port' value='".$GLOBALS['meta']['smtp_port']."' size='4' class='fondl' />";
	echo "<li>"._T('spiplistes:spip_ident');

	$smtp_identification = $GLOBALS['meta']['smtp_identification'];

	echo bouton_radio("smtp_identification", "oui", _T('item_oui'), $smtp_identification == "oui", "changeVisible(this.checked, 'smtp-auth', 'block', 'none');");
	echo "&nbsp;";
	echo bouton_radio("smtp_identification", "non", _T('item_non'), $smtp_identification == "non", "changeVisible(this.checked, 'smtp-auth', 'none', 'block');");

	if ($smtp_identification == "oui") $style = "display: block;";
	else $style = "display: none;";
	echo "<div id='smtp-auth' style='$style'>";
	echo "<ul>";
	echo "<li>"._T('item_login')." <input type='text' name='smtp_login' value='".$GLOBALS['meta']['smtp_login']."' size='30' CLASS='formo' />";
	echo "<li>"._T('entree_passe_ldap')." <input type='password' name='smtp_pass' value='".$GLOBALS['meta']['smtp_pass']."' size='30' CLASS='formo' />";
	echo "</ul>";
	echo "</div>";

	echo "</ul>";
	echo "</div>";

	echo "<br />";
	echo fin_cadre_trait_couleur();

	if ($mailer_smtp == "oui") $style = "display: block;";
	else $style = "display: none;" ;
	echo "<div style='$style'>";
	echo debut_cadre_relief("", false, "", _T('spiplistes:adresse_smtp'));
	echo "<p style='margin:10px'>"._T('spiplistes:adresse_smtp')."</p>";
	echo "<input type='text' name='smtp_sender' value=\"".$GLOBALS['meta']['smtp_sender']."\" class='formo' />";
	echo fin_cadre_relief();
	echo "</div>\n";

	echo "<input type='submit' name='valid_smtp' value='"._T('bouton_valider')."' class='fondo' style='float:right' />";
	echo "<hr style='clear:both;visibility:hidden' />";

	echo "</form>";	

	echo fin_cadre_relief();
	
	if (($reinitialiser_config == 'oui' AND $Valider_reinit)) {
		ecrire_meta('spiplistes_lots' , _request('spiplistes_lots')) ;
		ecrire_meta('spiplistes_charset_envoi' , _request('spiplistes_charset_envoi')) ;
		ecrire_metas();
	}

	echo debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:tableau_bord'));
	echo "<form action='".generer_url_ecrire('config')."' method='post'>";
	echo "<input type='hidden' name='reinitialiser_config' value='oui' />";
	echo "<label for='spiplistes_lots'>"._T('spiplistes:nombre_lot')."</label>" ;
	echo "<input type='text' name='spiplistes_lots' value=\"".$GLOBALS['meta']['spiplistes_lots']."\" class='formo' />";
	echo "<label for='spiplistes_charset_envoi'>"._T('spiplistes:envoi_charset')."</label>" ;
	echo "<input type='text' name='spiplistes_charset_envoi' value=\"".$GLOBALS['meta']['spiplistes_charset_envoi']."\" class='formo' />";

	echo "<input type='submit' name='Valider_reinit' value='"._T('spiplistes:reinitialiser')."' class='fondo' style='float:right' />";
	echo "<hr style='clear:both;visibility:hidden' />";
	echo "</form>";
	echo fin_cadre_relief();


	
function sl_console_lit_log($logname){
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

if(_request('logs')=="oui"){
echo "<a name='logs'></a>";
echo debut_cadre_relief("", false, "", "Logs");
echo "<div style='width:98%;overflow:auto'>";
echo "<pre>".sl_console_lit_log("spiplistes")."</pre>";
echo "</div>";
echo fin_cadre_relief();
}else{
echo "<a href='".generer_url_ecrire('config','logs=oui#logs')."'>Logs</a>";
}

	// MODE CONFIG FIN -------------------------------------------------------------

	//$spiplistes_version = "SPIP-listes 1.9b2";
	echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."</p>" ;

	echo fin_gauche(), fin_page();
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