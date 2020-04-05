<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * Inspire de Spip-Listes
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/barre');

function exec_abomailmans_envoyer(){

	//
	// Affichage de la page
	//
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("abomailmans:envoyer_mailmans"), "documents", "abomailmans", "");

		if(!autoriser($id_abomailman?'modifier' : 'creer', 'abomailmans', $id_abomailman)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
	echo debut_gauche("",true);
	echo debut_boite_info(true);
		echo icone_horizontale(_T("icone_retour"), generer_url_ecrire("abomailmans_tous",""), _DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif", "",false);

		echo fin_boite_info(true);

	echo debut_droite("",true);
	
	echo recuperer_fond("prive/abomailman_envoyer");
	}

	echo fin_gauche(), fin_page();
}
?>