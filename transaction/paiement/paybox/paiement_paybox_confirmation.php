<?php

	//Note : aprs avoir renommŽ ce fichier avec un nom personnalisŽ, vous devez communiquer son url ˆ PAYBOX pour reevoir les confirmations de paiement
	
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

	if($_REQUEST['erreur']=="00000" && $_REQUEST['auto']!="XXXXXX"){
	  sql_updateq('spip_formulaires_transactions', array('statut_transaction' => 1), 'ref_transaction=' . sql_quote($_REQUEST['ref']));
	}
?>
