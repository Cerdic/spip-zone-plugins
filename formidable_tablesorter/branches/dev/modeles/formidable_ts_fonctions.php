<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/saisies');
include_spip('inc/saisies_lister');
include_spip('javascript/formidable_ts.json_fonctions');
/**
 * Calcul le nombre de th à mettre dans le html
 * @param int|val $id_formulaire
 * @return str
**/
function formidable_ts_insert_th($id_formulaire) {
	$object = new formidable_ts\table(array('id_formulaire'=>$id_formulaire));
	$object->setHeaders();
	$nb = $object->countHeaders();
	return str_repeat('<th></th>', $nb);
}
