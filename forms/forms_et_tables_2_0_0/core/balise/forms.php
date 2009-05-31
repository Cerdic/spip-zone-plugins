<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation
global $balise_FORMS_collecte;
$balise_FORMS_collecte = array('id_form','id_article','id_donnee', 'id_donnee_liee');

function balise_FORMS ($p) {
	$p->descr['session'] = true;

	return calculer_balise_dynamique($p,'FORMS', array('id_form', 'id_article', 'id_donnee','id_donnee_liee', 'class'));
}

function balise_FORMS_stat($args, $filtres) {
	return $args;
}

function balise_FORMS_dyn($id_form = 0, $id_article = 0, $id_donnee = 0, $id_donnee_liee = 0, $class='', $script_validation = 'valide_form', $message_confirm='forms:avis_message_confirmation',$reponse_enregistree="forms:reponse_enregistree",$forms_obligatoires="",$retour="") {
	// on s'appelle pas #FORMULAIRE_FORMS, ce branchement n'est pas automatique !
	include_spip('balise/formulaire_');
	return balise_FORMULAIRE__dyn('forms',$id_form,$id_article,$id_donnee,$id_donnee_liee,$class,$script_validation,$message_confirm,$reponse_enregistree,$forms_obligatoires,$retour);
}

?>