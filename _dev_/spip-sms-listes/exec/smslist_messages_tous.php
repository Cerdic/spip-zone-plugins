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
include_spip('base/forms_base_api');
include_spip('public/assembler');

function exec_smslist_messages_tous() {
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	if (!autoriser('administrer','smslist')) {
		echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "smslist");
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}
	
	// Admin Spip-sms-listes
	echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "messages");
	
	echo debut_gauche("smslist",true);
	
	echo smslist_barre_nav_gauche('gerer_messages');

	echo creer_colonne_droite();

	echo debut_droite("smslist",true);
	
	include_spip("exec/template/tables_affichage");
	$liste = Forms_liste_tables('smslist_message');
	echo affichage_donnees_tous_corps('smslist_message',reset($liste));

	echo fin_gauche(), fin_page();
}

?>