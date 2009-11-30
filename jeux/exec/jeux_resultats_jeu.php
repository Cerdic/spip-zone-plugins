<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_resultats_jeu(){
	$id_jeu	= _request('id_jeu');
	$par = _request('par');
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$requete = jeux_fetsel('id_jeu,type_jeu,titre_prive', 'spip_jeux', "id_jeu=$id_jeu");
	$id_jeu = $requete['id_jeu'];
	$type_jeu = $requete['type_jeu'];
	$titre_prive = $requete['titre_prive'];
	if(!$id_jeu){
		echo $commencer_page(_T("jeux:pas_de_jeu"));
		echo gros_titre(_T("jeux:pas_de_jeu"), '', false), fin_page();
		return;
	}
	echo $commencer_page(_T("jeux:resultats_jeu",array('id'=>$id_jeu,'nom'=>$type_jeu)));

	echo debut_gauche('',true);

	echo boite_infos_jeu($id_jeu);
	echo boite_infos_accueil($id_jeu);
	
	echo creer_colonne_droite('',true);
	echo debut_droite('',true);
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:resultats_jeu", array('id'=>$id_jeu,'nom'=>$type_jeu)), '', false);
	$titre_prive = $titre_prive==''?_T('jeux:sans_titre_prive'):propre($titre_prive);
	echo "<div style='font-weight:bold'>$titre_prive</div><br />";
	echo "<div class='nettoyeur'></div>";
	debut_cadre('liste');
	include_spip('public/assembler');
	echo recuperer_fond('fonds/resultats', array('id_jeu'=>$id_jeu,'par'=>$par));
	fin_cadre('liste');
	
	fin_cadre_relief();
	echo jeux_navigation_pagination();
	echo fin_gauche(), fin_page();
}


?>
