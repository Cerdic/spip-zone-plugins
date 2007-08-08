<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_resultats_auteur(){
	$id_auteur 	= _request('id_auteur');
	$par = _request('par');
	($par == '') ? $par='resultat_court' : $par = $par;
	
	$requete = spip_fetch_array(spip_query("SELECT id_auteur,nom FROM spip_auteurs WHERE id_auteur =".$id_auteur));
	$nom = $requete['nom'];
	$id_auteur = $requete['id_auteur'];

	if(!$id_auteur){
		debut_page(_T("jeux:pas_d_auteur"));
		gros_titre(_T("jeux:pas_d_auteur"));
		fin_page();
		return;
	}

	debut_page(_T("jeux:resultats_auteur",array('nom'=>$nom)));
			
	debut_gauche();
	
	boite_infos_auteur($id_auteur, $nom);
	boite_infos_accueil();
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:resultats_auteur",array('nom'=>$nom)));
	
	include_spip('public/assembler');
	debut_cadre('liste');
	echo recuperer_fond('fonds/resultats_auteur_detail',array('id_auteur'=>$id_auteur,'par'=>$par));
	fin_cadre('liste');
	
	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}


?>