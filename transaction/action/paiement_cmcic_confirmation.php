<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_paiement_cmcic_confirmation_dist($arg=null) {
spip_log("debugarn1");
	include_spip('base/abstract_sql');
	/*****************************************************************************
 *
 * "Open source" kit for CM-CIC P@iement(TM).
 * Process CMCIC Payment. Sample RFC2104 compliant with PHP4 skeleton.
 *
 * File "Phase2Retour.php":
 *
 * Author   : Euro-Information/e-Commerce (contact: centrecom@e-i.com)
 * Version  : 1.04
 * Date     : 01/01/2009
 *
 * Copyright: (c) 2009 Euro-Information. All rights reserved.
 * License  : see attached document "Licence.txt".
 *
 *****************************************************************************/
header("Pragma: no-cache");
header("Content-type: text/plain");

			// TPE Settings
			// Warning !! CMCIC_Config contains the key, you have to protect this file with all the mechanism available in your development environment.
			// You may for instance put this file in another directory and/or change its name. If so, don't forget to adapt the include path below.
			require_once("plugins/transaction/paiement/cmcic/config.php");

			// --- PHP implementation of RFC2104 hmac sha1 ---
			require_once("plugins/transaction/paiement/cmcic/CMCIC_Tpe.inc.php");


			// Begin Main : Retrieve Variables posted by CMCIC Payment Server 
			$CMCIC_bruteVars = getMethode();

			// TPE init variables
			$oTpe = new CMCIC_Tpe();
			$oHmac = new CMCIC_Hmac($oTpe);


			// Message Authentication
			$cgi2_fields = sprintf(CMCIC_CGI2_FIELDS, $oTpe->sNumero,
								  $CMCIC_bruteVars["date"],
								  $CMCIC_bruteVars['montant'],
								  $CMCIC_bruteVars['reference'],
								  $CMCIC_bruteVars['texte-libre'],
								  $oTpe->sVersion,
								  $CMCIC_bruteVars['code-retour'],
								  $CMCIC_bruteVars['cvx'],
								  $CMCIC_bruteVars['vld'],
								  $CMCIC_bruteVars['brand'],
								  $CMCIC_bruteVars['status3ds'],
								  $CMCIC_bruteVars['numauto'],
								  $CMCIC_bruteVars['motifrefus'],
								  $CMCIC_bruteVars['originecb'],
								  $CMCIC_bruteVars['bincb'],
								  $CMCIC_bruteVars['hpancb'],
								  $CMCIC_bruteVars['ipclient'],
								  $CMCIC_bruteVars['originetr'],
								  $CMCIC_bruteVars['veres'],
								  $CMCIC_bruteVars['pares']
								);


			
			if ($oHmac->computeHmac($cgi2_fields) == strtolower($CMCIC_bruteVars['MAC']))
				{
switch($CMCIC_bruteVars['code-retour']) {
					case "Annulation" :
						
// Payment has been refused
						// put your code here (email sending / Database update)
						// Attention : an autorization may still be delivered for this payment
						break;

					case "payetest":
						
						sql_updateq('spip_formulaires_reponses', array('statut_transaction' => 1), 'ref_transaction=' . sql_quote($CMCIC_bruteVars['reference']));
						break;

					case "paiement":
spip_updateq('spip_formulaires_reponses', array('statut_transaction' => 1), 'ref_transaction=' . $CMCIC_bruteVars['reference']);
						break;
				}

				
$receipt = CMCIC_CGI2_MACOK;

			}
			else
			{
				// your code if the HMAC doesn't match
				$receipt = CMCIC_CGI2_MACNOTOK.$cgi2_fields;spip_log("debugarn8");

			}

		//-----------------------------------------------------------------------------
		// Send receipt to CMCIC server
		//-----------------------------------------------------------------------------
		printf (CMCIC_CGI2_RECEIPT, $receipt);

		// Copyright (c) 2009 Euro-Information ( mailto:centrecom@e-i.com )
		// All rights reserved. ---

}
?>
