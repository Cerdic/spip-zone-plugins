<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

function exec_forms_donnees_liantes(){
	$id = _request("id_donnee");
	include_spip('inc/forms');
	list($out,$les_donnees,$nombre_donnees) = forms_afficher_liste_donnees_liees(
		"donnee_liee", 
		$id_donnee, 
		"donnee", 
		"",
		"forms_donnees_liantes", 
		"forms_donnees_liantes-$id_donnee", 
		"id_donnee=$id_donnee", 
		self());

	ajax_retour($out);
}

?>