<?php
/**
 * Petit cochon
 *
 * @plugin     Petit Cochon
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\action\vider_vote
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_vider_vote_dist() {

	sql_delete('spip_petitcochon');

	return false;

}
