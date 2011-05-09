<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_traitement_paypal_dist() {
	
	spip_log('traitement paypal','paypal');

	#spip_log($_POST,'paypal');
	
	$conf = lire_config('paypal/');
	$envr = $conf['environnement'];
	
	if ($envr == 'test') {
		$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$url = "https://www.paypal.com/cgi-bin/webscr";
	}
	
	$datas = $_POST;
	$datas['cmd'] = "_notify-validate";

	include_spip('inc/distant');
	$retour = recuperer_page($url, false, false, 1048576, $datas);

	// retour peut etre "INVALID" ou "VERIFIED".
	spip_log($retour,'paypal');

	if ($retour == "VERIFIED") {

		// verifications :
		// 1) paiement ok
		// 2) email compte paypal ok
		// 3) identifiant unique de transaction
		if (($datas['payment_status'] == 'Completed')
		and ($datas['receiver_email'] == $conf['api'][$envr]['account'])) {
			// 3 ... on suppose que c'est OK
			// on pourrait imaginer creer une table speciale pour les transactions
			// mais bon...
			
			// c'est tout bon, on envoie ca au pipeline pour traitements
			pipeline('traitement_paypal', array(
				'args'=>array(
					'paypal' => $datas,
					'test' => (($envr == 'test') or ($datas['test_ipn'] == 1)),
				),
				'data'=>'')
			);
		}
	}

	// retourner un statut code 200 pour paypal
	// (et pas 204 pas de contenu)... aucazou
	include_spip('inc/headers');
	http_status(200);

}

?>
