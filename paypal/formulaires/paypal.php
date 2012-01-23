<?php

function formulaires_paypal_charger($options = array()) {
	/*
	$options = array(
		'prix' => 30,
		'duree' => 12, // uniquement si type = 'abonnement'
		'periode' => 'M', // uniquement si type = 'abonnement'
		'libelle' => "Ceci n'est pas une pipe",
		'identifiant' => 'abonnement-1',
		'type' => 'abonnement',
		'redirect_ok' => generer_url_public('paiement_ok'),
	);
	*/
	spip_log("options du formulaire paypal : ","paypal");
	spip_log($options,"paypal");
	
	$conf = lire_config('paypal/');
	$envr = $conf['environnement'];
	$confsup = lire_config('paypal_api_'.$envr);
	
	
	$valeurs = array(
		'identifiant_vendeur' => $confsup['account'],
		'soumission' => $conf['soumission'],
		'currency_code' => $conf['currency_code'],
	);
	
	$valeurs = array_merge($valeurs, $options);
	
	return $valeurs;
}
?>
