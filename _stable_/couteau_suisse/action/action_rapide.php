<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2008               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

function action_action_rapide_dist() {
cs_log("INIT : action_action_rapide_dist() - Une action rapide a ete demandee !");
	if (defined('_SPIP19200')) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	} else {
		include_spip('inc/actions');
		$var_f = charger_fonction('controler_action_auteur', 'inc');
		$var_f();
		$arg = _request('arg');
	}

	spip_log("action 'action_rapide' du Couteau suisse : $arg / "._request('submit'));
//	spip_log($_POST);

	switch ($arg) {

	// forms[0] : tout purger (cas SPIP < 2.0)
	case 'edit_urls_0':
		break;
	// forms[0] : tout purger (cas SPIP >= 2.0)
	case 'edit_urls2_0': 
		spip_query('DELETE FROM spip_urls');
		spip_log("OK purge");
		break;

	// forms[1] : editer un objet (cas SPIP < 2.0)
	case 'edit_urls_1':
		break;
	// forms[1] : editer un objet (cas SPIP >= 2.0)
	case 'edit_urls2_1': 
		$type = _request('ar_type_objet');
		$id = _request('ar_num_objet');
		$url = trim(_request('ar_url_objet'));
		$where = 'id_objet='.sql_quote($id).' AND type='.sql_quote($type);
		if(!$url) {
			sql_delete('spip_urls', $where);
			spip_log("L'URL $type#$id est supprimee");
		} else {
			$row = sql_fetsel("id_objet", "spip_urls", $where);
			if($row) {
				sql_updateq('spip_urls', array('date'=>'NOW()', 'url'=>$url), $where);
				spip_log("L'URL $type#$id est remplacee par : $url");
			} else {
				sql_insertq('spip_urls', array('date'=>'NOW()', 'url'=>$url, 'id_objet'=>$id, 'type'=>$type));
				spip_log("L'URL $type#$id a ete cree : $url");
			}
		}
		// ajout de l'objet en URL de redirection pour provoquer un reaffichage
		if($_POST['redirect']) $_POST['redirect'] .= "&ar_num_objet=$id&ar_type_objet=$type";
		break;

	}

}

?>
