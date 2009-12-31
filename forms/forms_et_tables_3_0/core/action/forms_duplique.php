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

function action_forms_duplique_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();

	if ($id_form = intval($args)){
		include_spip('base/forms_base_api_v2');
		forms_copier_tables($id_form);		
	}
}

?>