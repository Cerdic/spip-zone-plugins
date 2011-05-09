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
	
	$valeurs = array(
		'identifiant_vendeur' => $conf['api'][$envr]['account'],
		'soumission' => $conf['soumission'],
		//'tax' => $conf['tax'], // ca ca va pas !
		'currency_code' => $conf['currency_code'],
	);

	$valeurs = array_merge($valeurs, $options);
	
	return $valeurs;
}
?>
