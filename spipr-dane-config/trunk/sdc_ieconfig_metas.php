<?php
/**
 * Export des metas du plugin Spipr-Dane Config
 *
 * @plugin     Spipr-Dane Config
 * @copyright  2019
 * @author     Dominique Lepaisant
 * @licence    GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function sdc_ieconfig_metas($table){
	$table['sdc']['titre'] = _T('sdc:configuration_sdc');
	$table['sdc']['icone'] = 'prive/themes/spip/images/sdc-16.png';
	$table['sdc']['metas_serialize'] = 'sdc';
	return $table;
}

?>