<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function contact_ieconfig_metas($table) {
	$table['contact']['titre'] = _T('contact:titre');
	$table['contact']['icone'] = 'contact-16.png';
	$table['contact']['metas_serialize'] = 'contact';
	return $table;
}
