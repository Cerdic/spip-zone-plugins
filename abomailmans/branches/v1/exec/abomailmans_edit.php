<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");
include_spip('inc/abomailmans');

function exec_abomailmans_edit(){

	$id_abomailman = intval(_request('id_abo'));
	$retour = _request('retour');

	if ($retour)
		$retour = urldecode($retour);

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
	
	if(!autoriser($id_abomailman?'modifier' : 'creer', 'abomailmans', $id_abomailman)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
	
	echo debut_gauche("",true);
	echo debut_boite_info(true);
		echo icone_horizontale(_T("icone_retour"), generer_url_ecrire("abomailmans_tous",""), _DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif", "",false);
		echo icone_horizontale(_T("abomailmans:icone_ajouter_liste"), generer_url_ecrire("abomailmans_edit","new=oui"), _DIR_PLUGIN_ABOMAILMANS."img_pack/configure_mail.png", "creer.gif",false);
		
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
	}
	echo fin_gauche(),
	fin_page();
}
?>