<?php

//-----------------------------------------------------------------------------
// Nothing to change within this function  
//-----------------------------------------------------------------------------

// urlencode (and use of str_replace because urlencode does not convert single
// quotes) to prevent mysql injection and compute a correct HMAC
 $Language        = str_replace("'", "''", urlencode($Language)); 
 $Merchant_Code   = str_replace("'", "''", urlencode($Merchant_Code));
 $Amount          = str_replace("'", "''", urlencode($Amount));
 $Currency        = str_replace("'", "''", urlencode($Currency));
 $Order_Reference = str_replace("'", "''", urlencode($Order_Reference));
 $Order_Comment   = str_replace("'", "''", urlencode($Order_Comment));

// Set a storage reference whose MD5 will be used to identify recorded row
 $Storage_Ref     = $Amount.$Currency.$Merchant_Code.$Order_Comment.$Order_Reference;

// Implementation of RFC2104 using MySQL MD5 or SHA1 hash function
// Generate Payment Form - CMCIC Compliant

 $CMCIC_exec_S  = "REPLACE INTO CMCICPaiement SELECT @sauveCMCIC:=MD5('$Storage_Ref'),date_format(now(),'%d/%m/%Y:%T'),'$Amount','$Currency','$Order_Reference','$Order_Comment','$Merchant_Code','$Language',NULL,NULL,'$Return_Context',NULL FROM CMCICConst;";
// Saving retrieved datas, setting @sauveCMCIC as storage key
@$CMCIC_done_S  = mysql_query($CMCIC_exec_S,$CMCIC_link);

 $CMCIC_exec_1 = CMCIC_exec("SELECT CONCAT(prendrePaiement,@sauveCMCIC,0x273B) FROM CMCICConst;",$CMCIC_link);
 $CMCIC_updt_D = CMCIC_exec($CMCIC_exec_1,$CMCIC_link);
// Generate datas to be hashed
@$CMCIC_done_D = mysql_query($CMCIC_updt_D,$CMCIC_link);

 $CMCIC_exec_2 = CMCIC_exec("SELECT CONCAT(calculerMAC,@sauveCMCIC,0x273B) FROM CMCICConst;",$CMCIC_link);
 $CMCIC_exec_3 = CMCIC_exec($CMCIC_exec_2,$CMCIC_link);
 $CMCIC_updt_M = CMCIC_exec($CMCIC_exec_3,$CMCIC_link);  // Compute HMAC
@$CMCIC_done_M = mysql_query($CMCIC_updt_M,$CMCIC_link);

 $CMCIC_exec_4 = CMCIC_exec("SELECT CONCAT(creerFormulaire,@sauveCMCIC,0x273B) FROM CMCICConst;",$CMCIC_link);
 $CMCIC_updt_G = CMCIC_exec($CMCIC_exec_4,$CMCIC_link);
// Generate Payment Form for CMCIC server
@$CMCIC_done_G = mysql_query($CMCIC_updt_G,$CMCIC_link);

?>