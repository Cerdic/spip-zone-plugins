<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_voir(){
	$id_jeu = _request('id_jeu');
	
	$requete = spip_fetch_array(spip_query("SELECT contenu,id_jeu,nom,titre,date FROM spip_jeux WHERE id_jeu =".$id_jeu));
	list($contenu, $id_jeu, $nom, $titre, $date) =
		array($requete['contenu'], $requete['id_jeu'], $requete['nom'], $requete['titre'], $requete['date']);
	
	if(!$id_jeu){
		debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"));
		fin_page();
		return;
	}
	
	debut_page(_T("jeux:jeu_numero",array('id'=>$id_jeu,'nom'=>$nom)));
			
	debut_gauche();
	
	boite_infos_jeu($id_jeu, $nom);
	boite_infos_accueil();
	
	debut_cadre_relief();
	echo "<strong>"._t("jeux:derniere_modif")."</strong><br />".affdate($date).' '.heures($date).":".minutes($date);
	fin_cadre_relief();
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$nom)));
	echo '<div style="font-weight:bold">'.propre($titre).'</div>';
	echo propre($contenu);

	fin_cadre_relief();
	fin_gauche();
	fin_page();
	}


?>