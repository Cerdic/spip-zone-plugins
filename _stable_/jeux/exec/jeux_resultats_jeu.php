<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_resultats_jeu(){
	$id_jeu 	= _request('id_jeu');
	$par = _request('par');
	($par == '') ? $par='resultat_court' : $par = $par;
	
	$requete	= spip_fetch_array(spip_query("SELECT id_jeu,type_jeu,titre_prive FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$id_jeu		= $requete['id_jeu'];
	$type_jeu		= $requete['type_jeu'];
	$titre_prive		= $requete['titre_prive'];
	if(!$id_jeu){
		jeux_debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"), '', false);
		fin_page();
		return;
		}
	jeux_debut_page(_T("jeux:resultats_jeu",array('id'=>$id_jeu,'nom'=>$type_jeu)));

	jeux_compat_boite('debut_gauche');

	boite_infos_jeu($id_jeu, $type_jeu);
	boite_infos_accueil();
	
	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:resultats_jeu", array('id'=>$id_jeu,'nom'=>$type_jeu)), '', false);
	$titre_prive = $titre_prive==''?_T('jeux:sans_titre_prive'):propre($titre_prive);
	echo "<div style='font-weight:bold'>$titre_prive</div><br />";
	echo "<div class='nettoyeur'></div>";
	include_spip('public/assembler');
	debut_cadre('liste');
	echo recuperer_fond('fonds/resultats_jeu_detail', array('id_jeu'=>$id_jeu,'par'=>$par));
	fin_cadre('liste');
	
	fin_cadre_relief();
	echo jeux_navigation_pagination();
	echo fin_gauche(), fin_page();
}


?>