<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_jeux_voir(){
	$id_jeu 	= _request('id_jeu');
	
	$requete	= spip_fetch_array(spip_query("SELECT contenu,id_jeu,date FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$contenu 	= $requete['contenu'];
	$id_jeu		= $requete['id_jeu'];
	$date		= $requete['date'];
	
	
	if(!$id_jeu){
		debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"));
		fin_page();
		return;
		}
	
	debut_page(_T("jeux:jeu_numero",array('id'=>$id_jeu)));
			
	debut_gauche();
	
	// edition du jeu
	debut_boite_info();
	echo icone_horizontale(_T('jeux:modifier_jeu',array('id'=>$id_jeu)),generer_url_ecrire('jeux_edit','id_jeu='.$id_jeu),find_in_path('img/jeux-crayon.png'));
	
	echo icone_horizontale(_T('jeux:voir_resultats'),generer_url_ecrire('jeux_resultats_jeu','id_jeu='.$id_jeu),find_in_path('img/jeux-laurier.png'));
	fin_boite_info();
	
	
	debut_boite_info();
	echo _T("jeux:jeu_numero",array('id'=>$id_jeu));
	echo "<br /><strong>"._t("jeux:derniere_modif")."</strong><br />".affdate($date).' '.heures($date).":".minutes($date);
	fin_boite_info();
	
	
	debut_cadre_relief();
	echo _T('jeux:explication_jeu');
	fin_cadre_relief();
	
	// lien vers les resultats
	debut_cadre_relief();
	echo "<a href=".generer_url_ecrire('jeux_resultat','id_jeu='.$id_jeu).">"._T("jeux:voir_resultats")."</a>";
	fin_cadre_relief();
	
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:jeu_numero",array('id'=>$id_jeu)));
	echo propre($contenu);
	fin_cadre_relief();
	fin_gauche();
	fin_page();
	}


?>