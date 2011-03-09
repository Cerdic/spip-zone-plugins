<?php

function priveperso_autoriser(){}

function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt){

	include_spip('inc/inscrire_priveperso');

	$id_rubrique = priveperso_recupere_id_rubrique();
	if ($id_rubrique!==NULL){

// On vérifie si la rubrique en cours ou une des rubriques parentes est personnalisée
		if (!priveperso_rubrique_deja_perso($id_rubrique)){
			$id_rub = priveperso_trouver_rubrique_parent_perso($id_rubrique);
			if (($id_rub!==NULL) && ($id_rub!=='0')) $id_rubrique = $id_rub;
		}

		if (priveperso_rubrique_deja_perso($id_rubrique)){
			$priveperso = priveperso_recuperer_valeurs($id_rubrique);
			if ($priveperso['autoriser_articles']=='non') return false;
		}
	}
	return true;
}

function autoriser_rubrique_creerrubriquedans($faire, $type, $id, $qui, $opt){
	
	include_spip('inc/inscrire_priveperso');

	$id_rubrique = priveperso_recupere_id_rubrique();
	if ($id_rubrique!==NULL){

// On vérifie si la rubrique en cours ou une des rubriques parentes est personnalisée
		if (!priveperso_rubrique_deja_perso($id_rubrique)){
			$id_rub = priveperso_trouver_rubrique_parent_perso($id_rubrique);
			if (($id_rub!==NULL) && ($id_rub!=='0')) $id_rubrique = $id_rub;
		}

		if (priveperso_rubrique_deja_perso($id_rubrique)){
			$priveperso = priveperso_recuperer_valeurs($id_rubrique);
			if ($priveperso['autoriser_sous_rubriques']=='non') return false;
		}
	}	
	return true;
	
}

?>