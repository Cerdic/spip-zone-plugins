<?php

function rssconfig_ieconfig_metas($table){
	$table['rssconfig']['titre'] = _T('rssconfig:rssconfig');
	$table['rssconfig']['icone'] = 'prive/themes/spip/images/rssconfig-16.png';
	$table['rssconfig']['metas_serialize'] = 'rssconfig';
	return $table;
}

?>