<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * Inspire de Spip-Listes
 * $Id$
*/

include_spip('inc/abomailmans');
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

	$rubriques = abomailman_arbo_rubriques(0);
	
	$mots = "";
	$groupes_mots = sql_select("id_groupe, titre","spip_groupes_mots","tables_liees LIKE '%articles%'");
	while ($row = sql_fetch($groupes_mots)) {
		$id_groupe = $row['id_groupe'];
		$titre = $row['titre'];
		$mots .= "<option value='' disabled=\"disabled\">". supprimer_numero(typo($titre)) . "</option>";
		$mots_query = sql_select("id_mot, titre","spip_mots","id_groupe='".$id_groupe."'");
		while ($row = sql_fetch($mots_query)) {
			$id_mot = $row['id_mot'];
			$titre = $row['titre'];
			$mots .= "<option value='".$id_mot ."'>--". supprimer_numero (typo($titre)) . "</option>";
		}
	}
	
	echo recuperer_fond("prive/abomailman_envoyer",array("templates"=>$templates, "rubriques"=>$rubriques, "mots"=>$mots, "texte"=>$texte));

	echo fin_gauche(), fin_page();
}
?>