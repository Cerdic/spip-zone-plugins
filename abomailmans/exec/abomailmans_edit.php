<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id$
*/


include_spip('inc/abomailmans');

function exec_abomailmans_edit(){

	$id_abomailman = intval(_request('id_abomailman'));
	$retour = _request('retour');

	if ($retour)
		$retour = urldecode($retour);
	
	include_spip("inc/presentation");

	//
	// Affichage de la page
	//
	if($id_abomailman){
		$titre = sql_getfetsel("titre","spip_abomailmans","id_abomailman=$id_abomailman");
	}else{
		$titre = _T('abomailmans:icone_ajouter_liste');
	}
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("&laquo; $titre &raquo;", "documents", "abomailmans", "");
	echo debut_gauche("",true);
	echo debut_boite_info(true);
		echo icone_horizontale(_T("icone_retour"), generer_url_ecrire("abomailmans_tous",""), _DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif", "",false);
		echo icone_horizontale(_T("abomailmans:icone_ajouter_liste"), generer_url_ecrire("abomailmans_tous",""), _DIR_PLUGIN_ABOMAILMANS."img_pack/configure_mail.png", "",false);
		
	echo fin_boite_info(true);
	echo debut_droite("",true);
	
	//
	// Icones retour
	//
	if ($retour) {
		$icone_retour = icone_inline(_T('icone_retour'), $retour, _DIR_PLUGIN_ABOMAILMANS."img_pack/mailman.gif", "rien.gif",'right');
	}

	//
	// Edition des donnees du formulaire
	//
	echo recuperer_fond('prive/abomailman_creation_liste',array('id_abomailman'=>$id_abomailman,'retour'=>$retour,'icone_retour'=> $icone_retour));

	echo fin_gauche(), fin_page();
}
?>