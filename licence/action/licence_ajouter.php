<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

function action_licence_ajouter_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	if ($id_article = intval($securiser_action())){

		sql_updateq("spip_articles",array('id_licence'=>_request('id_licence')),'id_article='.intval($id_article));
	}
}

?>