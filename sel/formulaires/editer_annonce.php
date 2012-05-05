<?php
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_annonce_charger_dist($id_annonce='new',$id_auteur) {

	$valeurs = formulaires_editer_objet_charger('annonce', $id_annonce, '', '', $retour,'');
	// $valeurs = array();
	$valeurs['id_auteur'] = $id_auteur;
	$valeurs['direction_echange'] = '';
	$valeurs['nature'] = '';
	$valeurs['si_unite_heure']='';
	
	return $valeurs;
}

function formulaires_editer_annonce_verifier_dist($id_annonce='new') {

	// $erreurs = formulaires_editer_objet_verifier('annonce',$id_annonce,'','',$retour,''); // tableau des request
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_annonce_traiter_dist($id_annonce='new') {
	
	return formulaires_editer_objet_traiter('annonce',$id_annonce,'', '', $retour='?page=nouvelle_annonce_theme', '');
}
?>
