<?php

function licence_ieconfig_metas($table){
	$table['licence']['titre'] = _T('licence:cfg_titre_licence');
	$table['licence']['icone'] = 'img_pack/licence_logo24.png';
	$table['licence']['metas_serialize'] = 'licence';
	return $table;
}

?>