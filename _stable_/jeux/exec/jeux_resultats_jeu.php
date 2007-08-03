<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_jeux_resultats_jeu(){
	$id_jeu 	= _request('id_jeu');
	
	$requete	= spip_fetch_array(spip_query("SELECT id_jeu FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$id_jeu		= $requete['id_jeu'];
	if(!$id_jeu){
		debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"));
		fin_page();
		return;
		}
	debut_page(_T("jeux:resultats_jeu",array('id'=>$id_jeu)));
			
	debut_gauche();
	
	
	
	
	
		
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:resultats_jeu",array('id'=>$id_jeu)));
	
	include_spip('public/assembler');
	debut_cadre('liste');
	echo recuperer_fond('fond/resultats_jeu_detail',array('id_jeu'=>$id_jeu));
	fin_cadre();
	
	fin_cadre_relief();
	fin_gauche();
	fin_page();
	}


?>