<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
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
	spip_log("options du formulaire paypal : ".print_r($options,true),"paypal");
	
    include_spip('inc/config');
	$conf = lire_config('paypal');	
	
	$valeurs = array(
		'identifiant_vendeur' => $conf['account_'.$conf['environnement']],
		'soumission' => constant( '_URL_SOUMISSION_PAYPAL_'.$conf['environnement']),
		'currency_code' => $conf['currency_code'],
	);
	
	$valeurs = array_merge($valeurs, $options);
	
	return $valeurs;
}
?>
