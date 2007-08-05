<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_jeux_resultats_auteur(){
	$id_auteur 	= _request('id_auteur');
	
	$requete	= spip_fetch_array(spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur =".$id_auteur));
	$nom 	= $requete['nom'];
	
	debut_page(_T("jeux:resultat_auteur",array('nom'=>$nom)));
			
	debut_gauche();
	
	debut_boite_info();
	echo icone_horizontale(_T('jeux:retourner_auteur'),generer_url_ecrire('auteur_info','id_auteur='.$id_jeu),find_in_path('images/auteur-24.gif'));
	echo icone_horizontale(_T('jeux:gerer_resultats'),generer_url_ecrire('jeux_gerer_resultats','id_auteur='.$id_auteur),find_in_path('img/jeu-laurier.png'));
	fin_boite_info();

	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:resultat_auteur",array('nom'=>$nom)));
	
	include_spip('public/assembler');
	debut_cadre('liste');
	echo recuperer_fond('fond/resultats_auteur_detail',array('id_auteur'=>$id_auteur));
	fin_cadre();
	
	fin_cadre_relief();
	fin_gauche();
	fin_page();
	}


?>