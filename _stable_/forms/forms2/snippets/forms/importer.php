<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

function snippets_forms_importer($id_form,$source){
	include_spip('inc/forms');
	return Forms_importe_form($id_form,$source);
}

?>