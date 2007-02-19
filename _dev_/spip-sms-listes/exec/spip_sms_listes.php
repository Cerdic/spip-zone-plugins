<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/smslist_affichage');

function exec_spip_sms_listes() {
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	if (!autoriser('administrer','smslist')) {
		echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "smslist");
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}
	
	// Admin Spip-sms-listes
	echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "smslist");
	echo smslist_onglets("smslist", "Spip-SMS-Listes");
	
	echo debut_gauche("smslist",true);
	echo smslist_raccourcis();
	echo creer_colonne_droite();

	echo debut_droite("smslist",true);
	
	///
	$messages_vus = '';
	echo spiplistes_afficher_en_liste(_T('spiplistes:aff_encours'), _DIR_PLUGIN_SPIPLISTES.'img_pack/24_send-receive.gif', 'messages', 'encour', '', 'position') ;
	echo spiplistes_afficher_en_liste(_T('spiplistes:aff_redac'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'messages', 'redac', '', 'position') ;
	
	
	// afficher les messages auto
	echo spiplistes_afficher_pile_messages();
	
	echo "<br /><br />";

	echo spiplistes_afficher_en_liste(_T('spiplistes:messages_auto_envoye'),_DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'messages', 'auto', '', 'position') ;
	echo spiplistes_afficher_en_liste(_T('spiplistes:aff_envoye'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'messages', 'publie', '', 'position') ;
	
	
	// MODE HISTORIQUE FIN ---------------------------------------------------------
	
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