<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/abomailmans');

function exec_abomailmans_tous(){
	include_spip("inc/presentation");
	global $couleur_claire;
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("abomailmans:les_listes_mailmans"), "documents", "abomailmans", "");
		if(!autoriser($id_abomailman?'modifier' : 'creer', 'abomailmans', $id_abomailman)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
	echo debut_gauche("",true);
	echo debut_boite_info(true);
	echo _T("abomailmans:les_listes_mailmans");
	echo fin_boite_info(true);
	
	 
	$result = sql_count(sql_select("id_abomailman","spip_abomailmans"));
	if ($result>0) {
		echo debut_boite_info(true);
			echo icone_horizontale(_T("abomailmans:icone_envoyer_mail_liste"), generer_url_ecrire("abomailmans_envoyer",""),find_in_path("img_pack/forward.png"), "",false);
		echo fin_boite_info(true);
	}
	echo debut_droite("",true);
	
	// L'icone de creation de liste
	$link=generer_url_ecrire('abomailmans_edit', 'new=oui');
	$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
	$icone = icone(_T("abomailmans:icone_ajouter_liste"), $link, _DIR_PLUGIN_ABOMAILMANS. "/img_pack/mailman.gif", "creer.gif");
	
	echo recuperer_fond('prive/abomailman_afficher_abomailmans',array("couleur_claire"=>$couleur_claire,'icone' => $icone));
	}
	
	echo fin_gauche(), fin_page();
}

?>
