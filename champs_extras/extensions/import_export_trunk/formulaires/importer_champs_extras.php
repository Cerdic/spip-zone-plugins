<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_importer_champs_extras_charger_dist(){
	if (!autoriser('webmestre')) return false;
	
	$valeurs = array('champs'=>'');
	return $valeurs;
}


function formulaires_importer_champs_extras_verifier_dist(){
	$erreurs = array();
	if (!$c = _request('champs')) {
		$erreurs['champs'] = _T('ie_extras:erreur_champ_vide');
	} else {
		include_spip('inc/cextras'); // pour classe ChampExtra
		if (!@unserialize(trim($c))) {
			$erreurs['champs'] = _T('ie_extras:erreur_champ_erronne');
		}
	}
	return $erreurs;
}


function formulaires_importer_champs_extras_traiter_dist(){
	
	include_spip('inc/cextras'); // pour classe ChampExtra
	list($champs, $ichamps) = @unserialize(trim(_request('champs')));
	
	// retablir les champs de l'interface
	include_spip('inc/iextras');
	if (function_exists('iextras_set_extras')) {
		iextras_set_extras($ichamps);
	}	
	
	// creer les champs manquants
	include_spip('inc/cextras_gerer');
	$retour = array('editable' => true);
	if (creer_champs_extras($champs)) {
		$retour['message_ok'] = _T('ie_extras:importation_effectuee');
	} else {
		$retour['message_erreur'] = _T('ie_extras:importation_erreurs');
	}
	
	return $retour;
}

?>
