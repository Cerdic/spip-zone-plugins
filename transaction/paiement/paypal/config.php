<?php

	$titre1="Paypal";
	$chapo1="Carte bancaire";
	$description1="";
	
	$titre2="";
	$chapo2="";
	$description2="";
	
	$titre3="";
	$chapo3="";
	$description3="";

	// Modifier la valeur ci-dessous avec l'e-mail de vote compte PayPal
	$compte_paypal = 'VOTRE E-MAIL PAYPAL';

	$Devise        = "EUR";
	$Code_Langue   = "FR";



	$urlsite = "http://urlsite";
	
	$serveur="https://www.paypal.com/cgi-bin/webscr";
        $confirm = $urlsite."/client/plugins/paypal/paiement_paypal_confirmation.php";
	$retourok = "http://urlsite/?page=transaction_merci";
	$retournok = "http://urlsite/?transaction_regret";
	

?>
