<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_gerer_resultats(){
	
	// les boutons ... le pire ennemi des ados parait-il
	$supprimer_tout = _request('supprimer_tout');
	$supprimer_confirmer = _request('supprimer_confirmer');
	$compacter_tout = _request('compacter_tout');
	$compacter_confirmer = _request('compacter_confirmer');
	($supprimer_confirmer) ? $bouton = 'supprimer_confirmer' : $bouton = '';
	($bouton == '' and $supprimer_tout) ? $bouton = 'supprimer_tout' : $bouton = $bouton;
	($bouton == '' and $compacter_tout) ? $bouton = 'compacter_tout' : $bouton = $bouton;
	($bouton == '' and $compacter_confirmer) ? $bouton = 'compacter_confirmer' : $bouton = $bouton;
	
	$id_jeu 	= _request('id_jeu');
	$id_auteur  = _request('id_auteur');
	
	
	if ($id_jeu and autoriser('gererresultats')) { gerer_resultat_jeux($id_jeu,$bouton); return; }
	if ($id_auteur  and autoriser('gererresultats','auteur',$id_auteur)) { gerer_resultats_auteur($id_auteur,$bouton); return; }
	// ... et par defaut
	if (autoriser('gererresultats')) {gerer_resultat_tous($bouton); return;}
	interdit();
}

function gerer_resultat_tous($bouton){
	// aie, aie, on efface tout !
	if ($bouton == 'supprimer_confirmer'){
		include_spip('base/jeux_supprimer');
		jeux_supprimer_tout_tout();
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_tous'));
	}
	if ($bouton == 'compacter_confirmer'){
		include_spip('base/jeux_compacter');
		jeux_compacter_tout_tout();
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_tous'));
	}	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("jeux:gerer_resultats_tout"));
	echo debut_gauche('',true);
	echo boite_infos_accueil();
	
	echo creer_colonne_droite('',true);
	echo debut_droite('',true);
	if ($bouton == 'supprimer_tout'  or $bouton == 'compacter_tout') 
		echo gros_titre(_T("jeux:confirmation"), '', false);
	debut_cadre_relief();
	echo gros_titre(_T("jeux:gerer_resultats_tout"), '', false);
	formulaire_suppression($bouton, 'tout');

	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}

function gerer_resultats_auteur($id_auteur, $bouton){
	$requete = sql_fetsel('id_auteur,nom', 'spip_auteurs', "id_auteur=$id_auteur", '');
	$nom = $requete['nom'];
	$id_auteur = $requete['id_auteur'];
    $commencer_page = charger_fonction('commencer_page', 'inc');
	if(!$id_auteur){
		echo $commencer_page(_T("jeux:pas_d_auteur"));
		echo gros_titre(_T("jeux:pas_d_auteur"), '', false), fin_page();
		return;
	}
	
	// aïe, aïe, on efface tout pour $id_auteur
	if ($bouton == 'supprimer_confirmer'){
		include_spip('base/jeux_supprimer');
		jeux_supprimer_tout_auteur($id_auteur);
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_resultats_auteur', 'id_auteur='.$id_auteur, true));
	}
	if ($bouton == 'compacter_confirmer'){
		include_spip('base/jeux_compacter');
		jeux_compacter_tout_auteur($id_auteur);
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_resultats_auteur', 'id_auteur='.$id_auteur, true));
	}
	echo $commencer_page(_T("jeux:gerer_resultats_auteur",array('nom'=>$nom)));

	echo debut_gauche('',true);

	echo boite_infos_auteur($id_auteur);
	echo boite_infos_accueil($id_auteur);

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);
	if ($bouton == 'supprimer_tout'  or $bouton == 'compacter_tout') 
		echo gros_titre(_T("jeux:confirmation"), '', false);
	debut_cadre_relief();
	echo gros_titre(_T("jeux:gerer_resultats_auteur", array('nom'=>$nom)), '', false);
	formulaire_suppression($bouton, 'auteur');

	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}

function gerer_resultat_jeux($id_jeu, $bouton){
	$requete = sql_fetsel('id_jeu,type_jeu', 'spip_jeux', "id_jeu=$id_jeu");
	$id_jeu = $requete['id_jeu'];
	$type_jeu = $requete['type_jeu'];
	$commencer_page = charger_fonction('commencer_page', 'inc');
	if(!$id_jeu){
		echo $commencer_page(_T("jeux:pas_de_jeu"));
		echo gros_titre(_T("jeux:pas_de_jeu"), '', false), fin_page();
		return;
	}

	// aïe, aïe, on efface tout pour $id_jeu
	if ($bouton == 'supprimer_confirmer'){
		include_spip('base/jeux_supprimer');
		jeux_supprimer_tout_jeu($id_jeu);
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_resultats_jeu', 'id_jeu='.$id_jeu, true));
	}
	if ($bouton == 'compacter_confirmer'){
		include_spip('base/jeux_compacter');
		jeux_compacter_tout_jeu($id_jeu);
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('jeux_resultats_jeu', 'id_jeu='.$id_jeu, true));
	}


	echo $commencer_page(_T("jeux:gerer_resultats_jeu",array('id'=>$id_jeu,'nom'=>$type_jeu)));
			
	echo debut_gauche('',true);
	echo boite_infos_jeu($id_jeu);
	echo boite_infos_accueil($id_jeu);

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);
	if ($bouton == 'supprimer_tout' or $bouton == 'compacter_tout')
		echo gros_titre(_T("jeux:confirmation"), '', false);
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:gerer_resultats_jeu", array('id'=>$id_jeu,'nom'=>$type_jeu)), '', false);
	formulaire_suppression($bouton, 'jeu'); 
	
	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}

function formulaire_suppression($bouton, $type){
    $commencer_page = charger_fonction('commencer_page', 'inc');
	echo debut_cadre_formulaire('', true);
	echo "<form method='post'  name='supprimer_resultat'>";
	debut_cadre_relief();	
	
	if ($bouton == 'supprimer_tout'){
		$res = "<br/><input type='submit' name='supprimer_confirmer' value='"._T('jeux:supprimer_confirmer')."' class='fondo' />";
		echo
			_T('jeux:confirmation_supprimer_'.$type),
			"\n<div style='text-align: center'>",
			debut_boite_alerte(),
			"\n<div class='serif'>",
			"\n<b>"._T('avis_suppression_base')."&nbsp;!</b>",
			"\n</div>",
			$res,
			fin_boite_alerte(),
			"</div>";
	} 
	else if ($bouton == 'compacter_tout'){
		$res = "<br/><input type='submit' name='compacter_confirmer' value='"._T('jeux:compacter_confirmer')."' class='fondo' />";
		echo
			_T('jeux:confirmation_compacter_'.$type),
			"\n<div style='text-align: center'>",
			debut_boite_alerte(),
			"\n<div class='serif'>",
			"\n<b>"._T('avis_suppression_base')."&nbsp;!</b>",
			"\n</div>",
			$res,
			fin_boite_alerte(),
			"</div>";
	}
	
	else {
		$res = "<br/><input type='submit' name='supprimer_tout' value='"._T('jeux:supprimer_tout_'.$type)."' class='fondo' />";
		echo
			_T('jeux:explication_supprimer_'.$type),
			"\n<div style='text-align: center'>",
			debut_boite_alerte(),
			"\n<div class='serif'>",
			"\n<b>"._T('avis_suppression_base')."&nbsp;!</b>",
			"\n</div>",
			$res,
			fin_boite_alerte(),
			"</div>";
			
		$res = "<br/><input type='submit' name='compacter_tout' value='"._T('jeux:compacter_tout_'.$type)."' class='fondo' />";
		echo
			_T('jeux:explication_compacter_'.$type),
			"\n<div style='text-align: center'>",
			debut_boite_alerte(),
			"\n<div class='serif'>",
			"\n<b>"._T('avis_suppression_base')."&nbsp;!</b>",
			"\n</div>",
			$res,
			fin_boite_alerte(),
			"</div>";
	}

	fin_cadre_relief();
	echo "</form>";
	
	echo fin_cadre_formulaire(true);
}
function interdit(){
    $commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('avis_non_acces_page'));
	echo debut_gauche('',true);
	echo debut_droite('',true);
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
}

?>