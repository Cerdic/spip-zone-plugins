<?php
/**
 * Plugin Feedburner
 * Licence GPL 2009-2011
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_feedburner_charger_dist ($feedId='') {
	if ($feedId=='') {
		include_spip('inc/config');
		$feedId=lire_config ('feedburner/feedId');
	}
	$valeurs=array('email'=>'','_feedId'=>$feedId);
	
	return $valeurs;
}

?>