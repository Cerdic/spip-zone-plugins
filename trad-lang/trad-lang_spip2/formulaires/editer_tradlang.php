<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('tradlang_fonctions');

function formulaires_editer_tradlang_charger($id_tradlang='aucun',$retour){
	$valeurs = formulaires_editer_objet_charger('tradlang',$id_tradlang,0,'',$retour,$config_fonc,$row,$hidden);
	if (!intval($id_tradlang)) {
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('tradlang:erreur_id_tradlang_numerique');
	}
	return $valeurs;
}

function formulaires_editer_tradlang_verifier($module){
	$erreurs = formulaires_editer_objet_verifier('tradlang',0,array('str'));
	return $erreurs;
}

function formulaires_editer_tradlang_traiter($module){
	$ret = array();
	
	
	$ret['editable'] = $editable;
	return $ret;
}
?>