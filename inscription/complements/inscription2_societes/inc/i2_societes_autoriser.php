<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
/**
 * Autorisation a administrer les societes
 *
 * @param unknown_type $faire
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opts
 * @return unknown
 */
function autoriser_societe_administrer($faire, $quoi, $id, $qui, $opts) {
	if ($qui['statut'] == '0minirezo' and !$qui['restreint']) {
		return true;
	}
	return false;
}
