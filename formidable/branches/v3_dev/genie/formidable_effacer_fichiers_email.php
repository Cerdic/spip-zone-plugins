<?php


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/formidable_fichiers');
/**
 * Effacer régulièrement les fichiers des réponses envoyées par email
**/
function genie_formidable_effacer_fichiers_email($t){
	return formidable_effacer_fichiers_email();
}
