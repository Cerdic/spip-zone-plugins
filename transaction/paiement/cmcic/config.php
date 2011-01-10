<?
/***************************************************************************************
* Warning !! CMCIC_Config contains the key, you have to protect this file with all     *   
* the mechanism available in your development environment.                             *
* You may for instance put this file in another directory and/or change its name       *
***************************************************************************************/
//code client
//define ("CMCIC_CLE", "votre cle fournit par la banque");
define ("CMCIC_CLE", "12345678901234567890123456789012345678P0");

//TPE
define ("CMCIC_TPE", "0000001");


//code sociŽtŽ fourni par votre Žtablissement bancaire 
define ("CMCIC_CODESOCIETE", "codesociete");


//ne pas toucher
define ("CMCIC_VERSION", "3.0");

//url de retour ok
define ("CMCIC_URLOK", "http://urlsite/?page=transaction_merci");


//url de retour ko
define ("CMCIC_URLKO", "http://urlsite/?transaction_regret");


?>
