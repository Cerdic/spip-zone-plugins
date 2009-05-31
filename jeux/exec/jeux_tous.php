<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_tous(){
	$par = _request('par');
	($par =='') ? $par='date' : $par = $par;

	jeux_debut_page(_T("jeux:liste_jeux"));

	jeux_compat_boite('debut_gauche');
	echo boite_infos_accueil();

	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
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