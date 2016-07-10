<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_retirer_zone_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (preg_match(',^([0-9]+|-1)-([a-z]+)-([0-9]+|-1)$,', $arg, $regs)) {
		$id_zone = intval($regs[1]);
		$type = $regs[2];
		$id_objet = intval($regs[3]);
		include_spip('action/editer_zone');
		if ($id_objet=='-1') {
			zone_lier($id_zone, $type, array(), 'set');
		} elseif ($id_zone=='-1') {
			zone_lier(array(), $type, $id_objet, 'set');
		} else {
			zone_lier($id_zone, $type, $id_objet, 'del');
		}
	}
}
