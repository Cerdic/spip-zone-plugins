<?php
/**
 * Plugin Intranet
 *
 * (c) 2013-2016 kent1
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_intranet_sortir_dist($id = null, $objet = null, $action = 'plus') {
	if (is_null($id) or is_null($objet)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($id, $objet, $action) = array_pad(explode('/', $arg, 3), 3, null);
	}
	// appel incorrect ou depuis une url erronnée interdit
	if (is_null($id) or is_null($objet)) {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_interdit'));
		die();
	}
	if ($action == 'plus') {
		$existe = sql_getfetsel('objet', 'spip_intranet_ouverts', 'objet='.sql_quote($objet). ' AND id_objet='.intval($id));
		if (!$existe) {
			sql_insertq('spip_intranet_ouverts', array('objet' => $objet, 'id_objet' => $id));
		}
	} else {
		sql_delete('spip_intranet_ouverts', 'objet='.sql_quote($objet).' AND id_objet='.intval($id));
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id'");
	return array($id, $objet, $action);
}
