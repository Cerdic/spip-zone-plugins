<?php

function dublin_core_ieconfig_metas($table){
	$table['dublin_core']['titre'] = _T('dublin_core:dublin_core');
	$table['dublin_core']['icone'] = 'images/dublin_core-24.png';
	$table['dublin_core']['metas_serialize'] = 'dublin_core';
	return $table;
}

?>