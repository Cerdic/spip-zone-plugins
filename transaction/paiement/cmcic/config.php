<?php

//code client
//define ("CMCIC_CLE", "votre cle fournit par la banque");
define ("CMCIC_CLE", "12345678901234567890123456789012345678P0");

//TPE
define ("CMCIC_TPE", "0000001");


//code socit fourni par votre tablissement bancaire 
define ("CMCIC_CODESOCIETE", "codesociete");


//ne pas toucher
define ("CMCIC_VERSION", "3.0");

//url de retour ok
define ("CMCIC_URLOK", $GLOBALS['meta']['adresse_site']."/?page=transaction_merci");


//url de retour ko
define ("CMCIC_URLKO", $GLOBALS['meta']['adresse_site']."/?page=transaction_regret");



?>
