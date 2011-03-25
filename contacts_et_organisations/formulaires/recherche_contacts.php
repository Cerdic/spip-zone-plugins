<?php

function formulaires_recherche_contacts_charger_dist() {
	$valeurs = array(
		'recherche' => _request('recherche'),
		'statut' =>  _request('statut')
	);
	return $valeurs;
}

function formulaires_recherche_contacts_verifier_dist() {
	$erreurs = array(
		'message_ok'=> _T('contacts:recherche_de', array('recherche'=>_request('recherche')))
	);
	return $erreurs;
}

?>
