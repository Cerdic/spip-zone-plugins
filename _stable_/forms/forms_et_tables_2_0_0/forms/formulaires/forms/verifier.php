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

function formulaires_forms_verifier_dist($id_form = 0, $id_article = 0, $id_donnee = 0, $id_donnee_liee = 0, $class='', $script_validation = 'valide_form', $message_confirm='forms:avis_message_confirmation',$reponse_enregistree="forms:reponse_enregistree",$forms_obligatoires="",$retour=""){
	$erreurs = array();
	include_spip('inc/autoriser');

	if (_request('nobotnobot'))
		$erreurs['message_erreur']=' '; // soyons inhumain avec les robots : pas de message d'erreur !
	else {
	
		include_spip('inc/forms');
		include_spip("inc/forms_type_champs");
		$erreurs = forms_valide_champs_reponse_post($id_form, $id_donnee);
	}

	return $erreurs;
}

?>