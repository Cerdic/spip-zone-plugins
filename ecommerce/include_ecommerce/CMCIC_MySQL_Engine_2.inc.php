<?php
//-----------------------------------------------------------------------------
// Nothing to change within this function. This MySQL sequence takes the exact
// place of both.
// testerHMAC() and creerAccuseReception() functions.
//-----------------------------------------------------------------------------

// Escape malicious or incorrect chars using best available escape function.
// No encoding is done here, in order to retrieve previously sent values.
 
function cmcic_escape_string($string) 
{
  if (get_magic_quotes_gpc()) 
  {
   if(function_exists('mysql_escape_string')) 
        return(mysql_escape_string(stripslashes($string)));
   else return($string);
  } 
  else
  {
      if(function_exists('mysql_escape_string'))
           return(mysql_escape_string($string));
      else return(addslashes($string)); 
  }
}

 $Result_Date     = cmcic_escape_string($Result_Date); 
 $Merchant_ID     = cmcic_escape_string($Merchant_ID);
 $Amount_Currency = cmcic_escape_string($Amount_Currency);
 $Order_Reference = cmcic_escape_string($Order_Reference);
 $Order_Comment   = cmcic_escape_string($Order_Comment);
 $Msg_Auth_Code   = cmcic_escape_string($Msg_Auth_Code);
 $Result_Value    = cmcic_escape_string($Result_Value);
@$Reason_Value    = cmcic_escape_string($Reason_Value.''); // optional field

$Storage_Ref = $Amount_Currency.$Merchant_ID.$Result_Date.$Order_Comment.$Msg_Auth_Code.$Result_Value.$Order_Reference;

// Implementation of RFC2104 using MySQL MD5 or SHA1 hash function.
// Verify HMAC sent by CMCIC Server and prepare Receipt.

 $CMCIC_exec_S  = "REPLACE INTO CMCICResultat SELECT @sauveCMCIC:=MD5('$Storage_Ref'),'$Result_Date','$Amount_Currency','$Order_Reference','$Order_Comment','$Merchant_ID','$Result_Value','$Reason_Value','$Msg_Auth_Code',NULL,NULL,NULL FROM CMCICConst;";
// Saving retrieved datas, setting @sauveCMCIC as storage key
@$CMCIC_done_S  = mysql_query($CMCIC_exec_S, $CMCIC_link);

 $CMCIC_exec_1 = CMCIC_exec("select concat(prendreResultat,@sauveCMCIC,0x273B) from CMCICConst;",$CMCIC_link);
 $CMCIC_updt_D = CMCIC_exec($CMCIC_exec_1, $CMCIC_link);
// Generate datas to be hashed
 $CMCIC_done_D = mysql_query($CMCIC_updt_D, $CMCIC_link);

 $CMCIC_exec_2 = CMCIC_exec("select concat(verifierMAC,@sauveCMCIC,0x273B) from CMCICConst;",$CMCIC_link);
 $CMCIC_exec_3 = CMCIC_exec($CMCIC_exec_2, $CMCIC_link);
// Compute HMAC and compare to provided HMAC
 $CMCIC_updt_M = CMCIC_exec($CMCIC_exec_3, $CMCIC_link);
 $CMCIC_done_M = mysql_query($CMCIC_updt_M, $CMCIC_link);

 $CMCIC_exec_4 = CMCIC_exec("select concat(creerReponse,@sauveCMCIC,0x273B) from CMCICConst;",$CMCIC_link);
 $CMCIC_updt_G = CMCIC_exec($CMCIC_exec_4, $CMCIC_link);
// Generate receipt for CMCIC server
 $CMCIC_done_G = mysql_query($CMCIC_updt_G, $CMCIC_link);

?>