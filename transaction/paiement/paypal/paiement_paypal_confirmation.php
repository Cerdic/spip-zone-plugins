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
		die("<strong>Echec :</strong> SPIP ne peut pas etre demarr&eacute;.<br />
			Vous utilisez certainement un lien symbolique dans votre r&eacute;pertoire plugins.");
	}


	include_spip('base/abstract_sql');


require_once("plugins/transaction/paiement/paypal/paiement.php");



$chaine = ''; 
$reponse = '';
$donnees = '';
  
$url = parse_url($serveur);        

foreach ($_POST as $champs=>$valeur) { 
   $donnes["$champs"] = $valeur;
   $chaine .= $champs.'='.urlencode(stripslashes($valeur)).'&'; 
}
$chaine.="cmd=_notify-validate";

// open the connection to paypal
$fp = fsockopen($url['host'],"80",$err_num,$err_str,30); 
if(!$fp) {
     return false;
 } else { 

   fputs($fp, "POST $url[path] HTTP/1.1\r\n"); 
   fputs($fp, "Host: $url[host]\r\n"); 
   fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
   fputs($fp, "Content-length: ".strlen($chaine)."\r\n"); 
   fputs($fp, "Connection: close\r\n\r\n"); 
   fputs($fp, $chaine . "\r\n\r\n"); 

   while(!feof($fp))  
      $reponse .= fgets($fp, 1024); 
  
   fclose($fp); 

}

if(strstr($reponse, "VERIFIED")){
	$reference = $_POST['invoice'];

	sql_updateq('spip_formulaires_transactions', array('statut_transaction' => 1), 'ref_transaction=' . sql_quote($reference));

}
?>
