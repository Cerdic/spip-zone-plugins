<?php
foreach ($_POST as $key => $value) $$key = $value;
foreach ($_GET as $key => $value) $$key = $value;
?>
<?php
	//Charger SPIP
	if (!defined('_ECRIRE_INC_VERSION')) {
		// recherche du loader SPIP.
		$deep = 2;
		$lanceur ='ecrire/inc_version.php';
		$include = '../../'.$lanceur;
		while (!defined('_ECRIRE_INC_VERSION') && $deep++ < 6) { 
			// attention a pas descendre trop loin tout de meme ! 
			// plugins/zone/stable/nom/version/tests/ maximum cherche
			$include = '../' . $include;
			if (file_exists($include)) {
				chdir(dirname(dirname($include)));
				require $lanceur;
			}
		}	
	}
	if (!defined('_ECRIRE_INC_VERSION')) {
		die("<strong>Echec :</strong> SPIP ne peut pas etre demarre.<br />
			Vous utilisez certainement un lien symbolique dans votre repertoire plugins.");
	}


	include_spip('base/abstract_sql');
	
	
	// Récupération de la variable cryptée DATA

	$DATA = escapeshellcmd($_POST["DATA"]);
	$message="message=$DATA";

	// Initialisation du chemin du fichier pathfile (à modifier)
    //   ex :
    //    -> Windows : $pathfile="pathfile=c:\\repertoire\\pathfile"
    //    -> Unix    : $pathfile="pathfile=/home/repertoire/pathfile"
    //
    // Cette variable est facultative. Si elle n'est pas renseignée,
    // l'API positionne la valeur à "./pathfile".

	$pathfile="pathfile=conf/pathfile";

	//Initialisation du chemin de l'executable response (à modifier)
	//ex :
	//-> Windows : $path_bin = "c:\\repertoire\\bin\\response"
	//-> Unix    : $path_bin = "/home/repertoire/bin/response"
	//

	$path_bin = "bin/response";

	// Appel du binaire response

	$result=exec("$path_bin $message $pathfile");

	//	Sortie de la fonction : !code!error!v1!v2!v3!...!v29
	//		- code=0	: la fonction retourne les données de la transaction dans les variables v1, v2, ...
	//				: Ces variables sont décrites dans le GUIDE DU PROGRAMMEUR
	//		- code=-1 	: La fonction retourne un message d'erreur dans la variable error


	//	on separe les differents champs et on les met dans une variable tableau

	$tableau = explode ("!", $result);

	$code = $tableau[1];
	$error = $tableau[2];
	$merchant_id = $tableau[3];
	$merchant_country = $tableau[4];
	$amount = $tableau[5];
	$transaction_id = $tableau[6];
	$payment_means = $tableau[7];
	$transmission_date= $tableau[8];
	$payment_time = $tableau[9];
	$payment_date = $tableau[10];
	$response_code = $tableau[11];
	$payment_certificate = $tableau[12];
	$authorisation_id = $tableau[13];
	$currency_code = $tableau[14];
	$card_number = $tableau[15];
	$cvv_flag = $tableau[16];
	$cvv_response_code = $tableau[17];
	$bank_response_code = $tableau[18];
	$complementary_code = $tableau[19];
	$return_context = $tableau[20];
	$caddie = $tableau[21];
	$receipt_complement = $tableau[22];
	$merchant_language = $tableau[23];
	$language = $tableau[24];
	$customer_id = $tableau[25];
	$order_id = $tableau[26];
	$customer_email = $tableau[27];
	$customer_ip_address = $tableau[28];
	$capture_day = $tableau[29];
	$capture_mode = $tableau[30];
	$data = $tableau[31];


	 sql_updateq('spip_formulaires_transactions', array('statut_transaction' => 1), 'ref_transaction=' . sql_quote($transaction_id));

	// Initialisation du chemin du fichier de log (à modifier)
    //   ex :
    //    -> Windows : $logfile="c:\\repertoire\\log\\logfile.txt";
    //    -> Unix    : $logfile="/home/repertoire/log/logfile.txt";
    //

	$logfile="/var/log/atos/site";

	// Ouverture du fichier de log en append

	$fp=fopen($logfile, "a");
	//  analyse du code retour
fputs($fp, "code $customer_ip_address \n");

  if (( $code == "" ) && ( $error == "" ) )
 	{
  	fwrite($fp, "erreur appel response\n");
  	print ("executable response non trouve $path_bin\n");
 	}

	//	Erreur, sauvegarde le message d'erreur

	else if ( $code != 0 ){
        fwrite($fp, " API call error.\n");
        fwrite($fp, "Error message :  $error\n");
 	}
	else {

	// OK, Sauvegarde des champs de la réponse

	fwrite( $fp, "merchant_id : $merchant_id\n");
	fwrite( $fp, "merchant_country : $merchant_country\n");
	fwrite( $fp, "amount : $amount\n");
	fwrite( $fp, "transaction_id : $transaction_id\n");
	fwrite( $fp, "transmission_date: $transmission_date\n");
	fwrite( $fp, "payment_means: $payment_means\n");
	fwrite( $fp, "payment_time : $payment_time\n");
	fwrite( $fp, "payment_date : $payment_date\n");
	fwrite( $fp, "response_code : $response_code\n");
	fwrite( $fp, "payment_certificate : $payment_certificate\n");
	fwrite( $fp, "authorisation_id : $authorisation_id\n");
	fwrite( $fp, "currency_code : $currency_code\n");
	fwrite( $fp, "card_number : $card_number\n");
	fwrite( $fp, "cvv_flag: $cvv_flag\n");
	fwrite( $fp, "cvv_response_code: $cvv_response_code\n");
	fwrite( $fp, "bank_response_code: $bank_response_code\n");
	fwrite( $fp, "complementary_code: $complementary_code\n");
	fwrite( $fp, "return_context: $return_context\n");
	fwrite( $fp, "caddie : $caddie\n");
	fwrite( $fp, "receipt_complement: $receipt_complement\n");
	fwrite( $fp, "merchant_language: $merchant_language\n");
	fwrite( $fp, "language: $language\n");
	fwrite( $fp, "customer_id: $customer_id\n");
	fwrite( $fp, "order_id: $order_id\n");
	fwrite( $fp, "customer_email: $customer_email\n");
	fwrite( $fp, "customer_ip_address: $customer_ip_address\n");
	fwrite( $fp, "capture_day: $capture_day\n");
	fwrite( $fp, "capture_mode: $capture_mode\n");
	fwrite( $fp, "data: $data\n");
	fwrite( $fp, "-------------------------------------------\n");
	}

	fclose ($fp);


?>
