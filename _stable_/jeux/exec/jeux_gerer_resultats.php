<?
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');



function exec_jeux_gerer_resultats(){
	//les boutons ... le pire ennemi des ados parait-il
	$supprimer_tout = _request('supprimer_tout');
	$supprimer_tout_confirm = _request('supprimer_tout_confirm');
	
	($supprimer_tout_confirm) ? $bouton = 'supprimer_tout_confirm' : $bouton ='';
	($bouton == '' and $supprimer_tout) ? $bouton='supprimer_tout' : $bouton =$bouton;
	
	$id_jeu 	= _request('id_jeu');
	if ($id_jeu) {gerer_resultat_jeux($id_jeu,$bouton);
	return;}
	
	$id_auteur  = _request('id_auteur');
	if ($id_auteur) {gerer_resultat_auteur($id_auteur,$bouton);
	$return;
	}
	
	$tous = _request('tous');
	if ($tous == 'oui') {gerer_resultat_tous($bouton);}
	}
function gerer_resultat_tous($bouton){
	
	
	
	debut_page(_T("jeux:gerer_resultats_tout"));
			
	debut_gauche();
	
	debut_boite_info();
	
	echo icone_horizontale(_T('jeux:jeux_tous'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png'));
	fin_boite_info();
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:gerer_resultats_tout"));
	formulaire_suppression($bouton);
	if ($bouton == 'supprimer_tout_confirm'){
		include_spip('base/jeux_supprimer');
		
		jeux_supprimer_tout_tout();
		}

	
	fin_cadre_relief();
	fin_gauche();
	fin_page();
	}


function gerer_resultat_auteur($id_auteur,$bouton){
	
	$requete	= spip_fetch_array(spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur =".$id_auteur));
	$nom 	= $requete['nom'];
	
	debut_page(_T("jeux:gerer_resultats_auteur",array('nom'=>$nom)));
			
	debut_gauche();
	
	debut_boite_info();
	
	echo icone_horizontale(_T('jeux:voir_resultats'),generer_url_ecrire('jeux_resultats_auteur','id_auteur='.$id_auteur),find_in_path('img/jeu-laurier.png'));
	fin_boite_info();
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	gros_titre(_T("jeux:gerer_resultats_auteur",array('nom'=>$nom)));
	formulaire_suppression($bouton);
	if ($bouton == 'supprimer_tout_confirm'){
		include_spip('base/jeux_supprimer');
		
		jeux_supprimer_tout_auteur($id_auteur);
		}

	
	fin_cadre_relief();
	fin_gauche();
	fin_page();
	}




function gerer_resultat_jeux($id_jeu,$bouton){
	//determination du bouton enclencher
	
	
	$requete	= spip_fetch_array(spip_query("SELECT id_jeu FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$id_jeu		= $requete['id_jeu'];
	if(!$id_jeu){
		debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"));
		fin_page();
		return;
		}
	debut_page(_T("jeux:gerer_resultats_jeu",array('id'=>$id_jeu)));
			
	debut_gauche();
	
	
	debut_boite_info();
	echo icone_horizontale(_T('jeux:voir_jeu'),generer_url_ecrire('jeux_voir','id_jeu='.$id_jeu),find_in_path('img/jeu-loupe.png'));
	echo icone_horizontale(_T('jeux:voir_resultats'),generer_url_ecrire('jeux_resultats_jeu','id_jeu='.$id_jeu),find_in_path('img/jeu-laurier.png'));
	fin_boite_info();
	
	
		
	
	creer_colonne_droite();
	debut_droite();
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:gerer_resultats_jeu",array('id'=>$id_jeu)));
	formulaire_suppression($bouton); 
	
	
	//aïe, aïe, on efface tout
	if ($bouton == 'supprimer_tout_confirm'){
		include_spip('base/jeux_supprimer');
		
		jeux_supprimer_tout_jeu($id_jeu);
		}
	
	
	fin_cadre_relief();
	fin_gauche();
	fin_page();
}

function formulaire_suppression($bouton=''){
	debut_cadre_formulaire();
	echo "<form method='post'  name='supprimer_resultat'>";
	
	if ($bouton == 'supprimer_tout'){
		debut_cadre_relief();	
		echo _T('jeux:confirmation_supprimer_tout');
		echo "<p align='left'><input type='submit' name='supprimer_tout_confirm' value='"._T('jeux:supprimer_tout_confirm')."' class='fondo' /></p>";
		fin_cadre_relief();
		}
	
	debut_cadre_relief();	
	echo _T('jeux:explication_supprimer_tout');
	echo "<p align='left'><input type='submit' name='supprimer_tout' value='"._T('jeux:supprimer_tout')."' class='fondo' /></p>";
	fin_cadre_relief();
	echo "</form>";
	
	fin_cadre_formulaire();
}

?>