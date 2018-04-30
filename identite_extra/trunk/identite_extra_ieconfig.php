<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

//proposer l'export de la config via le plugin ieconfig
function identite_extra_ieconfig_metas($table) {
	if (identite_extra_champs()) {
		$table['identite_extra']['titre'] = _T('identite_extra:identite_extra');;
		$table['identite_extra']['icone'] = 'identite_extra-16.png';
		$table['identite_extra']['metas_brutes'] = 'identite_extra';
	}
	
	return $table;
}
