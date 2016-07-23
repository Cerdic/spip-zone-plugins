<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_supprimer_mailsubscribinglist_segment_dist($id_mailsubscribinglist = null, $id_segment = null){

	if (is_null($id_mailsubscribinglist)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($id_mailsubscribinglist,$id_segment) = explode('-', $arg);
	}

	include_spip('inc/autoriser');
	if (autoriser('segmenter','mailsubscribinglist', $id_mailsubscribinglist)) {

		$segments = sql_getfetsel('segments', 'spip_mailsubscribinglists', 'id_mailsubscribinglist=' . intval($id_mailsubscribinglist));
		$segments = unserialize($segments);

		if ($segments and isset($segments[$id_segment])) {
			include_spip('action/editer_objet');
			unset($segments[$id_segment]);
			objet_modifier('mailsubscribinglist', $id_mailsubscribinglist, array('segments' => serialize($segments)));
			sql_delete('spip_mailsubscriptions','id_mailsubscribinglist=' . intval($id_mailsubscribinglist) . ' AND id_segment=' . intval($id_segment));
		}

	}

}