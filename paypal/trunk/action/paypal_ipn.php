<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_paypal_ipn_dist() {

	spip_log("Entrer action_paypal_ipn_dist",'paypal');
    
    include_spip('inc/config');
	$conf = lire_config('paypal');

	$datas = $_POST;
	$datas['cmd'] = "_notify-validate";

	include_spip('inc/distant');
	$retour = recuperer_page(constant( '_URL_SOUMISSION_PAYPAL_'.$conf['environnement']), false, false, 1048576, $datas);

	// retour peut etre "INVALID" ou "VERIFIED".
	spip_log($retour,'paypal');

	// Si c'est bon, on vérifie aussi que l'email soit celui configuré dans le compte
	if (
		$retour == "VERIFIED"
		and $datas['receiver_email'] == $conf['account_'.$conf['environnement']]
	) {
		spip_log('Retour de Paypal vérifié, on peut passer aux traitements','paypal');

		// c'est tout bon, on envoie ca au pipeline pour traitements
		pipeline('traitement_paypal', array(
			'args'=>array(
				'paypal' => $datas,
				'test' => (($conf['environnement'] == 'test') or ($datas['test_ipn'] == 1)),
			),
			'data'=>'')
		);
	}

	// retourner un statut code 200 pour paypal
	// (et pas 204 pas de contenu)... aucazou
	include_spip('inc/headers');
	http_status(200);

}

?>
