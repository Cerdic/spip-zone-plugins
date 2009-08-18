<?php
function formulaires_restaurer_savecfg_charger_dist() {
	$valeurs = array(
		'nom'=>$nom,
		'fond'=>$fond
	);
	return $valeurs;
}
function formulaires_restaurer_savecfg_verifier_dist(){
	$erreurs = array();
	if (_request('fond_rest') == 'none')
		$erreurs['message_erreur'] = 'champ obligatoire';
	return $erreurs;
}
function formulaires_restaurer_savecfg_traiter_dist() {
	include_spip('inc/meta');
	$fond = _request('fond');
	$sfg = sql_getfetsel('valeur', 'spip_savecfg', 'id_savecfg='.sql_quote(_request('fond_rest')).' AND fond='.sql_quote($fond));
	ecrire_meta($fond, $sfg);
	ecrire_metas();
	$message = 'Restauration effectuée';
	return $message;
}
?>