<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2008               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

function action_action_rapide_dist() {
	$arg = _request('arg');
cs_log("INIT : action_action_rapide_dist() - Une action rapide '$arg' a ete demandee !");
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

// pour redirige_par_entete()
include_spip('inc/headers');
spip_log("action 'action_rapide' du Couteau suisse : $arg");
//	cs_log($_POST, 'action POST='); cs_log($_GET, 'action GET=');

	switch ($arg) {

	// pour inserer un pack de config dans config/mes_options.php
	case 'sauve_pack':
		include_spip('outils/pack_action_rapide');
		action_rapide_sauve_pack();
		break;
	// boite privee : tri les auteurs d'un article
	case 'tri_auteurs':
		include_spip('outils/boites_privees_action_rapide');
		action_rapide_tri_auteurs(_request('bp_article'), abs(_request('bp_auteur')), _request('bp_auteur')>0);
		break;

	// forcer la lecture des revisions distantes de plugins
	case 'maj_auto_forcer':
		ecrire_meta('tweaks_maj_auto', serialize(array()));
		ecrire_metas();
		break;
	// tester l'anti-spam
	case 'test_spam':
		// aucune action, le test est pris en charge par ?exec=action_rapide
		redirige_par_entete(parametre_url(urldecode(_request('redirect')), 'ar_message', _request('ar_message'), '&'));
		break;
	// purger la corbeille
	case 'corbeille':
		include_spip('outils/corbeille_action_rapide');
		action_rapide_purge_corbeille();
		break;
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
		redirige_par_entete(parametre_url(parametre_url(urldecode(_request('redirect'))
			, 'ar_num_objet', _request('ar_num_objet'), '&'), 'ar_type_objet', _request('ar_type_objet'), '&'));
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
				sql_updateq('spip_urls', array('date'=>date('Y-m-d H:i:s'), 'url'=>$url), $where);
				spip_log("L'URL $type#$id est remplacee par : $url");
			} else {
				sql_insertq('spip_urls', array('date'=>date('Y-m-d H:i:s'), 'url'=>$url, 'id_objet'=>$id, 'type'=>$type));
				spip_log("L'URL $type#$id a ete cree : $url");
			}
		}
		redirige_par_entete(parametre_url(parametre_url(urldecode(_request('redirect'))
			, 'ar_num_objet', _request('ar_num_objet'), '&'), 'ar_type_objet', _request('ar_type_objet'), '&'));
		break;

	}

}

?>