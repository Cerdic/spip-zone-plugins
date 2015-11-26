<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_exporter_champs_extras_charger_dist(){
	if (!autoriser('webmestre')) return false;
	
	$valeurs = array();
	
	if (_request('exporter')) {
		// exporter la liste des champs
		include_spip('inc/cextras'); 
		$champs = pipeline('declarer_champs_extras', array());
		// recuperer la liste des champs geres par iextras
		$ichamps = array();
		include_spip('inc/iextras');
		if (function_exists('iextras_get_extras')) {
			$ichamps = iextras_get_extras();
		}
		$valeurs['champs'] = @serialize(array($champs, $ichamps));
		// effacer ce qui a pu deja etre poste
		set_request('champs', null);
	}
	return $valeurs;
}

function formulaires_exporter_champs_extras_verifier_dist(){
	$erreurs = array(true); // toujours reafficher
	return $erreurs;
}

# function formulaires_exporter_champs_extras_traiter_dist(){}

?>
