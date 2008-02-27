<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_gerer_resultats(){
	
	// les boutons ... le pire ennemi des ados parait-il
	$supprimer_tout = _request('supprimer_tout');
	$supprimer_confirmer = _request('supprimer_confirmer');
	$compacter_tout 	 = _request('compacter_tout');
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
	jeux_debut_page(_T("jeux:gerer_resultats_tout"));
	jeux_compat_boite('debut_gauche');
	boite_infos_accueil();
	
	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	if ($bouton == 'supprimer_tout'  or $bouton == 'compacter_tout') gros_titre(_T("jeux:confirmation"), '', false);
	debut_cadre_relief();
	gros_titre(_T("jeux:gerer_resultats_tout"), '', false);
	formulaire_suppression($bouton, 'tout');

	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}

function gerer_resultats_auteur($id_auteur, $bouton){
	$requete = spip_fetch_array(spip_query("SELECT id_auteur,type_jeu FROM spip_auteurs WHERE id_auteur =".$id_auteur));
	$type_jeu = $requete['type_jeu'];
	$id_auteur = $requete['id_auteur'];

	if(!$id_auteur){
		jeux_debut_page(_T("jeux:pas_d_auteur"));
		gros_titre(_T("jeux:pas_d_auteur"), '', false);
		fin_page();
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
	jeux_debut_page(_T("jeux:gerer_resultats_auteur",array('nom'=>$type_jeu)));
		
	jeux_compat_boite('debut_gauche');

	boite_infos_auteur($id_auteur, $type_jeu);
	boite_infos_accueil();

	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	if ($bouton == 'supprimer_tout'  or $bouton == 'compacter_tout') gros_titre(_T("jeux:confirmation"), '', false);
	debut_cadre_relief();
	gros_titre(_T("jeux:gerer_resultats_auteur",array('nom'=>$type_jeu)), '', false);
	formulaire_suppression($bouton, 'auteur');

	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}

function gerer_resultat_jeux($id_jeu, $bouton){
	$requete	= spip_fetch_array(spip_query('SELECT id_jeu,type_jeu FROM spip_jeux WHERE id_jeu ='.$id_jeu));
	$id_jeu		= $requete['id_jeu'];
	$type_jeu		= $requete['type_jeu'];
	if(!$id_jeu){
		jeux_debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"), '', false);
		fin_page();
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


	jeux_debut_page(_T("jeux:gerer_resultats_jeu",array('id'=>$id_jeu,'nom'=>$type_jeu)));
			
	jeux_compat_boite('debut_gauche');
	boite_infos_jeu($id_jeu, $type_jeu);
	boite_infos_accueil();

	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	if ($bouton == 'supprimer_tout' or $bouton == 'compacter_tout') gros_titre(_T("jeux:confirmation"), '', false);
	debut_cadre_relief();
	
	echo gros_titre(_T("jeux:gerer_resultats_jeu",array('id'=>$id_jeu,'nom'=>$type_jeu)), '', false);
	formulaire_suppression($bouton, 'jeu'); 
	
	fin_cadre_relief();
	echo fin_gauche(), fin_page();
}

function formulaire_suppression($bouton, $type){
	if(defined('_SPIP19100'))debut_cadre_formulaire();else echo debut_cadre_formulaire('', true);
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
	
	if(defined('_SPIP19100'))fin_cadre_formulaire();else echo fin_cadre_formulaire(true);
}
function interdit(){
	jeux_debut_page(_T('avis_non_acces_page'));
	jeux_compat_boite('debut_gauche');
	jeux_compat_boite('debut_droite');
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
}

?>