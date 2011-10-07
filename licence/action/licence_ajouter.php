<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_licence_ajouter_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	if ($id_article = intval($securiser_action())){
		include_spip('inc/modifier');
		$c = array('id_licence'=>_request('id_licence'));
		revision_article($id_article, $c);
	}
}

?>