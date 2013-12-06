<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_camera_identifier_dist($id_camera='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_camera)));
}


function formulaires_editer_camera_charger_dist($id_camera='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('camera',$id_camera,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

function formulaires_editer_camera_verifier_dist($id_camera='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('camera', $id_camera, array('titre','lat','lon'));
}

function formulaires_editer_camera_traiter_dist($id_camera='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('camera',$id_camera,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>