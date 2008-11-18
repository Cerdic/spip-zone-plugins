<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2008               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

// compatibilite SPIP < 1.92
if(defined('_SPIP19100')) {
	if(!function_exists('_q')) { function _q($t) {return spip_abstract_quote($t);} }
}

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

//	spip_log("action 'action_rapide' du Couteau suisse : $arg / "._request('submit'));
//	spip_log($_POST); spip_log($_GET);

	switch ($arg) {

	// pour purger le cache de SPIP...
	case 'cache':
		include_spip('action/purger');
		action_purger_dist(); // beurk
		break;
	// forms[0] : tout purger (cas SPIP < 2.0)
	case 'edit_urls_0':
		foreach(array('articles', 'rubriques', 'breves', 'auteurs', 'mots', 'syndic') as $t)
			if($table=_request("purger_$t")) spip_query("UPDATE spip_$table SET url_propre = ''");
		spip_log("OK purge");
		break;
	// forms[0] : tout purger (cas SPIP >= 2.0)
	case 'edit_urls2_0': 
		spip_query('DELETE FROM spip_urls');
		spip_log("OK purge");
		break;

	// forms[1] : editer un objet (cas SPIP < 2.0)
	case 'edit_urls_1':
		$type = _request('ar_type_objet');
		$table = $type.($type=='syndic'?'':'s');
		$id = intval(_request('ar_num_objet'));
		$url = trim(_request('ar_url_objet'));
		$q = "UPDATE spip_$table SET url_propre="._q($url)." WHERE id_$type=$id";
		spip_query($q);
		break;
	// forms[1] : editer un objet (cas SPIP >= 2.0)
	case 'edit_urls2_1': 
		$type = _request('ar_type_objet');
		$id = intval(_request('ar_num_objet'));
		$url = trim(_request('ar_url_objet'));
		$where = 'id_objet='.$id.' AND type='.sql_quote($type);
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
		break;

	}

}

?>
