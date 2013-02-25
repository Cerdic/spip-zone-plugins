<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// On déclare ici la config du core
function feedbacks_ieconfig_metas($table){
	$table['feedbacks']['titre'] = _T('feedback:titre_feedback');
	$table['feedbacks']['icone'] = 'feedback-16.png';
	$table['feedbacks']['metas_brutes'] = 'activer_feedbacks';
	
	return $table;
}

?>