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

function action_forms_supprime(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();

	include_spip('inc/autoriser');
	if ($id_form = intval($args)
	  AND autoriser('supprimer','form',$id_form)){
  	include_spip('base/forms_base_api');
  	Forms_supprimer_tables($id_form);
	}
}

?>