<?php
function formulaires_sauvegarder_savecfg_charger_dist() {
	$valeurs = array(
		'nom'=>$nom,
		'fond'=>$fond
	);
	return $valeurs;
}
function formulaires_sauvegarder_savecfg_verifier_dist(){
	$erreurs = array();
	if (strlen(_request('nom')) < 1)
		$erreurs['message_erreur'] = 'champ obligatoire';
	return $erreurs;
}
function formulaires_sauvegarder_savecfg_traiter_dist() {
	$fond = _request('fond');
	$sfg = sql_getfetsel('valeur', 'spip_meta', 'nom='.sql_quote($fond));
	sql_insertq('spip_savecfg', array('id_savecfg' => '', 'fond' => $fond, 'valeur' => $sfg, 'titre' => _request('nom'), 'version' => 1, 'date' => date('Y-m-d H:m:s')));
	$message = 'Sauvegarde effectuée';
	return $message;
}
?>