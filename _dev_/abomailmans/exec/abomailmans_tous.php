<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
*/


include_spip('inc/abomailmans');


function exec_abomailmans_tous(){
	include_spip("inc/presentation");
	
	_abomailmans_install();
	
	
	debut_page(_T("abomailmans:les_listes_mailmans"), "documents", "abomailmans");
	debut_gauche();
	debut_boite_info();
	echo _T("abomailmans:les_listes_mailmans");
	fin_boite_info();
	
	echo "<br/>";
	$result = spip_query("SELECT id_abomailman FROM spip_abomailmans");
	if (spip_num_rows($result)) {
		debut_boite_info();
			icone_horizontale (_T("abomailmans:icone_envoyer_mail_liste"), generer_url_ecrire("abomailmans_envoyer",""), "../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/configure_mail.png", "");
		fin_boite_info();
	}
	debut_droite();
	
	abomailmans_afficher_abomailmans(_T("abomailmans:les_listes_mailmans"),
		array(
		"SELECT"=>"abomailmans.*",
		"FROM" => "spip_abomailmans AS abomailmans",
		"JOIN" => "",
		"WHERE" => "",
		"GROUP BY" => "abomailmans.id_abomailman",
		"ORDER BY" => "titre"));
	
	echo "<br />\n";
	
	if (abomailmans_abomailman_editable()) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('abomailmans_edit', 'new=oui');
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone(_T("abomailmans:icone_ajouter_liste"), $link, "../"._DIR_PLUGIN_ABOMAILMANS. "/img_pack/mailman.gif", "creer.gif");
		echo "</div>";
	}
	
	
	
	fin_page();
}

?>
