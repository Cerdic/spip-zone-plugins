<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * Inspire de Spip-Listes
 * $Id$
*/

include_spip('inc/barre');

function exec_abomailmans_envoyer(){

	//
	// Affichage de la page
	//
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("abomailmans:envoyer_mailmans"), "documents", "abomailmans", "");

	echo debut_gauche("",true);
	echo debut_boite_info(true);
		echo icone_horizontale (_T("abomailmans:icone_ajouter_liste"), generer_url_ecrire("abomailmans_tous",""), "../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/configure_mail.png", "",false);
	echo fin_boite_info(true);

	echo debut_droite("",true);
	
	echo recuperer_fond("prive/abomailman_envoyer");

	echo fin_gauche(), fin_page();
}
?>