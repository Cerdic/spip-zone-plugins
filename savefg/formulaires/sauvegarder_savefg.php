<?php
function formulaires_sauvegarder_savefg_charger_dist() {
	$valeurs = array(
		'nom'=>$nom,
		'fond'=>$fond
	);
	return $valeurs;
}
function formulaires_sauvegarder_savefg_verifier_dist(){
	$erreurs = array();
	if (strlen(_request('nom')) < 1)
		$erreurs['message_erreur'] = 'champ obligatoire';
	return $erreurs;
}
function formulaires_sauvegarder_savefg_traiter_dist() {
	$fond = _request('fond');
	$sfg = sql_getfetsel('valeur', 'spip_meta', 'nom='.sql_quote($fond));
	sql_insertq('spip_savefg', array('id_savefg' => '', 'fond' => $fond, 'valeur' => $sfg, 'commentaire' => _request('nom'), 'version' => 1, 'date' => date('Y-m-d H:m:s')));
	$message = 'Sauvegarde effectuée';
	return $message;
}
?>