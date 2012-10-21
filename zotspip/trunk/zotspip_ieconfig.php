<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function zotspip_ieconfig_metas($table){
	$table['zotspip']['titre'] = _T('zotspip:zotspip');
	$table['zotspip']['icone'] = 'zotspip-16.png';
	$table['zotspip']['metas_serialize'] = 'zotspip';
	return $table;
}

?>