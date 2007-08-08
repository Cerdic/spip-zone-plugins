<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_tous(){
	$par = _request('par');
	($par =='') ? $par='date' : $par = $par;
	
	debut_page(_T("jeux:jeux_tous"));
			
	debut_gauche();
	boite_infos_accueil();
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:jeux_tous"));
	
	include_spip('public/assembler');
	debut_cadre('liste');
	echo recuperer_fond('fonds/jeux_tous', array('par'=>$par));
	fin_cadre('liste');
	
	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}


?>