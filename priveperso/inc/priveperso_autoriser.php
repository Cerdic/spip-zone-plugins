<?php

function priveperso_autoriser(){}

if (!function_exists('autoriser_rubrique_creerrubriquedans')) {
function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt){

	include_spip('inc/inscrire_priveperso');

	$id_rubrique = $id;
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
	return autoriser_rubrique_creerarticledans_dist($faire, $type, $id, $qui, $opt);
}
}

if (!function_exists('autoriser_rubrique_creerrubriquedans')) {
function autoriser_rubrique_creerrubriquedans($faire, $type, $id, $qui, $opt){
	
	include_spip('inc/inscrire_priveperso');

	$id_rubrique = $id;
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

	return autoriser_rubrique_creerrubriquedans_dist($faire, $type, $id, $qui, $opt);
	
}
}

if (!function_exists('autoriser_rubrique_creerbrevedans')) {
function autoriser_rubrique_creerbrevedans($faire, $type, $id, $qui, $opt) {
	
	include_spip('inc/inscrire_priveperso');

	$id_rubrique = $id;
	if ($id_rubrique!==NULL){
// On vérifie si la rubrique en cours ou une des rubriques parentes est personnalisée
		if (!priveperso_rubrique_deja_perso($id_rubrique)){
			$id_rub = priveperso_trouver_rubrique_parent_perso($id_rubrique);
			if (($id_rub!==NULL) && ($id_rub!=='0')) $id_rubrique = $id_rub;
		}

		if (priveperso_rubrique_deja_perso($id_rubrique)){
			$priveperso = priveperso_recuperer_valeurs($id_rubrique);
			if ($priveperso['activer_breves']=='oui') return true;
		}
	}		
	
	$r = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=".sql_quote($id));
	return autoriser_rubrique_creerbrevedans_dist($faire, $type, $id, $qui, $opt);
}
}

// Autoriser a voir la rubrique $id
// Autorisation détournée pour limiter les rubriques visibles
// dans le menu de choix de rubrique
// lors de l'édition d'un article, d'une brève ou d'un site.
if (!function_exists('autoriser_rubrique_voir')) {
function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {

	include_spip('inc/inscrire_priveperso');

	$id_rubrique = $id;
	if ($id_rubrique!==NULL){
// On vérifie si la rubrique en cours ou une des rubriques parentes est personnalisée
		if (!priveperso_rubrique_deja_perso($id_rubrique)){
			$id_rub = priveperso_trouver_rubrique_parent_perso($id_rubrique);
			if (($id_rub!==NULL) && ($id_rub!=='0')) $id_rubrique = $id_rub;
		}

		if (priveperso_rubrique_deja_perso($id_rubrique)){
			$priveperso = priveperso_recuperer_valeurs($id_rubrique);
			$quoi = $_GET['exec'];
			if (strpos($quoi, 'breve')!==false && $priveperso['activer_breves']=='non') return false;
			if (strpos($quoi, 'breve')!==false && $priveperso['activer_breves']=='oui') return true;
		   if (strpos($quoi, 'article')!==false && $priveperso['autoriser_articles']=='non') return false;
		   if (strpos($quoi, 'site')!==false && $priveperso['activer_sites']=='non') return false;
		}
	}
	$quoi = $_GET['exec'];
	if (strpos($quoi, 'breve')!==false) {	
		$r = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=".sql_quote($id));
		return ($r['id_parent']==0);
	}

	return autoriser_voir_dist($faire, $type, $id, $qui, $opt);
}
}

?>