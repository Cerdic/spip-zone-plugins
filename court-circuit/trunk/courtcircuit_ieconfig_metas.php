<?php

function courtcircuit_ieconfig_metas($table){
	$table['courtcircuit']['titre'] = _T('courtcircuit:courtcircuit');
	$table['courtcircuit']['icone'] = 'images/courtcircuit-24.png';
	$table['courtcircuit']['metas_serialize'] = 'courtcircuit';
	return $table;
}

?>