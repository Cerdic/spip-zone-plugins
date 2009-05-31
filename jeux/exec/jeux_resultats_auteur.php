<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_resultats_auteur(){
	$id_auteur 	= _request('id_auteur');
	$par = _request('par');
	
	$requete = jeux_fetsel('id_auteur,nom', 'spip_auteurs', "id_auteur=$id_auteur", '');
	$nom = $requete['nom'];
	$id_auteur = $requete['id_auteur'];

	if(!$id_auteur){
		jeux_debut_page(_T("jeux:pas_d_auteur"));
		echo gros_titre(_T("jeux:pas_d_auteur"), '', false), fin_page();
		return;
	}

	jeux_debut_page(_T("jeux:resultats_auteur",array('nom'=>$nom)));

	jeux_compat_boite('debut_gauche');

	echo boite_infos_auteur($id_auteur);
	echo boite_infos_accueil($id_auteur);
	
	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:resultats_auteur", array('nom'=>$nom)), '', false);
	
	debut_cadre('liste');
	include_spip('public/assembler');
	echo recuperer_fond('fonds/resultats',array('id_auteur'=>$id_auteur,'par'=>$par));
	fin_cadre('liste');
	
	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}


?>
