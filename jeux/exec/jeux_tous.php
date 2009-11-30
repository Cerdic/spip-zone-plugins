<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_tous(){
	$par = _request('par');
	($par =='') ? $par='date' : $par = $par;
    $commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("jeux:liste_jeux"));

	echo debut_gauche('',true);
	echo boite_infos_accueil();

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:liste_jeux"), '', false);
	
	debut_cadre('liste');
	include_spip('public/assembler');
	echo recuperer_fond('fonds/jeux_tous', array('par'=>$par));
	fin_cadre('liste');
	
	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}


?>