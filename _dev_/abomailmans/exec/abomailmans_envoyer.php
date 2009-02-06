<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
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
		echo icone_horizontale (_T("abomailmans:icone_envoyer_mail_liste"), generer_url_ecrire("abomailmans_envoyer",""), "../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/configure_mail.png", "",false);
	echo fin_boite_info(true);

	echo debut_droite("",true);

	$liste_templates = find_all_in_path("templates/","[.]html$");
	
	$templates = "";
	foreach($liste_templates as $titre_option) {
		$titre_option = basename($titre_option,".html");
		$value_option = "templates/$titre_option";
		$templates .= "<option value='".$value_option."'>".$titre_option."</option>\n";
	}
	
	echo recuperer_fond("prive/abomailman_envoyer",array("templates"=>$templates, "texte"=>$texte));

	echo fin_gauche(), fin_page();
}
?>