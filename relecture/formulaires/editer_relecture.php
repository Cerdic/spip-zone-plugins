<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_editer_relecture_charger_dist($id_relecture, $redirect){
	$valeurs = formulaires_editer_objet_charger('relecture', $id_relecture, 0, 0, $redirect, 'relectures_edit_config');
	return $valeurs;
}

function formulaires_editer_relecture_verifier_dist($id_relecture, $redirect){
	$erreurs = formulaires_editer_objet_verifier('relecture', $id_relecture, array('description'));
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_relecture_traiter_dist($id_relecture, $redirect){
	return formulaires_editer_objet_traiter('relecture', $id_relecture, 0, 0, $redirect);
}

function relectures_edit_config($row)
{
	global $spip_ecran, $spip_lang;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large") ? 8 : 5;
	$config['langue'] = $spip_lang;
	return $config;
}

?>