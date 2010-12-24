<?php
/*****************************************************************************
 *
 * CM_CIC_Paiement "open source" kit for CyberMUT-P@iement(TM) and
 *                  P@iementCIC(TM).
 * Generate CMCIC Payment Form. Sample RFC2104 compliant with PHP4 skeleton.
 *
 * File "CheckOutStub.php":
 *
 * Author   : Euro-Information/e-Commerce (contact: centrecom@e-i.com)
 * Version  : 1.03
 * Date     : 18/12/2003
 *
 * Copyright: (c) 2003 Euro-Information. All rights reserved.
 * License  : see attached document "License.txt".
 *
 *----------------------------------------------------------------------------
 *
 * CM_CIC_Paiement: kit "open source" pour CyberMUT-P@iement(TM) et
 *                  P@iementCIC(TM).
 * Génération d'un formulaire de paiement CMCIC. Exemple compatible RFC2104,
 * base en PHP4.
 *
 * Fichier "CheckOutStub.php" :
 *
 * Auteur   : Euro-Information/e-Commerce (contact: centrecom@e-i.com)
 * Version  : 1.03
 * Date     : 18/12/2003
 *
 * Copyright: (c) 2003 Euro-Information. Tous droits réservés.
 * Consulter le document de licence "Licence.txt" joint.
 *
 *****************************************************************************/
 
	session_start();
	
?>

<?php
   $total = 200; // test
?>

<HTML>
<HEAD>
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<TITLE>
  Generate CMCIC Payment Form. Sample RFC2104 compliant with PHP4 skeleton
</TITLE>
</HEAD>
<BODY>
<?php

// Nothing to customize below before a first successfull receipt test
// Rien à changer ci-dessous avant un premier A/R test correct

// PHP implementation of RFC2104 hmac sha1 ---
// Implémentation en PHP de RFC2104 hmac sha1 ---
@require_once("CMCIC_HMAC.inc.php");
if ( !function_exists('CMCIC_hmac') ) { die ('cant require hmac function.'); }

// ----------------------------------------------------------------------------
// function CMCIC_getMyTpe
//
// IN:  Code société / Company code
//      Code langue / Language code
// OUT: Tableau contenant les champs suivants (paramètres du tpe):
//       tpe: Numéro de tpe / TPE number
//       soc: Code société / Company code
//       key: Clé / Key
//       retourok: Url retour ok / Return url ok
//       retourko: Url retour non ok / Return url non ok
//       submit: Texte du bouton pour accéder à la page de paiement /
//       Text button to access payment page
//
// Description: Get TPE Number, 2nd part of Key and other Merchant
//              Configuration. Datas from Merchant DataBase
//                           ********************
//              Rechercher le numéro de TPE, la 2nde partie de clef et autres
//              infos de configuration Marchand
// ----------------------------------------------------------------------------
function CMCIC_getMyTpe($soc="mysoc",$lang="")
{
    @require("MyTpeCMCIC.inc.php");
    if ( !is_array($MyTpe) ) { die ('cant require Tpe config.'); }
    return $MyTpe;
}

// ----------------------------------------------------------------------------
// function HtmlEncode
//
// IN:  chaine a encoder / String to encode
// OUT: Chaine encodée / Encoded string
//
// Description: Encode special characters under HTML format
//                           ********************
//              Encodage des caractères speciaux au format HTML
// ----------------------------------------------------------------------------
function HtmlEncode ($data)
{
    $SAFE_OUT_CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890._-";
    $encoded_data = "";
    $result = "";
    for ($i=0; $i<strlen($data); $i++)
    {
        if (strchr($SAFE_OUT_CHARS, $data{$i})) {
            $result .= $data{$i};
        }
        else if (($var = bin2hex(substr($data,$i,1))) <= "7F"){
            $result .= "&#x" . $var . ";";
        }
        else
            $result .= $data{$i};
            
    }
    return $result;
}

// ----------------------------------------------------------------------------
// function CreerFormulaireHmac
//
// IN: Numéro de TPE / TPE number
//     Référence commande/ Order reference
//     Code langue / Language code
//     Code société / Company code
//     Montant / Amount
//     Devise / Currency
//     Texte libre / Order Comment
//     Texte du bouton / Button Text
// OUT: Formulaire de paiement / Payment form
//
// Description: Génération du formulaire / Format CMCIC Payment Form
// ----------------------------------------------------------------------------
function CreerFormulaireHmac($CMCIC_Tpe,
                               $Amount,
                               $Currency,
                               $Order_Reference,
                               $Order_Comment,
                               $Language_Code,
                               $Merchant_Code,
                               $Button_Text)
{
    // Prepare the return link. Context will be added to return Url
    // Préparation du lien de retour. Un contexte est ajouté au lien.
    $Return_Context = "?order_ref=".$Order_Reference;

    if ($Order_Comment == "") { $Order_Comment .= "-"; }

    $Order_Date = date("d/m/Y:H:i:s");
    $Language_2 = substr($Language_Code, 0, 2);

    $PHP1_FIELDS = sprintf(CMCIC_PHP1_FIELDS, "",
                                              $CMCIC_Tpe["tpe"],
                                              $Order_Date,
                                              $Amount,
                                              $Currency,
                                              $Order_Reference,
                                              $Order_Comment,
                                              CMCIC_VERSION,
                                              $Language_2,
                                              $Merchant_Code);

    $keyedMAC = CMCIC_hmac($CMCIC_Tpe, $PHP1_FIELDS);

    return sprintf(CMCIC_PHP1_FORM, HtmlEncode( CMCIC_SERVER ),
                                    HtmlEncode( CMCIC_DIR ),
                                    HtmlEncode( CMCIC_VERSION ), 
                                    HtmlEncode( $CMCIC_Tpe["tpe"] ),
                                    HtmlEncode( $Order_Date ),
                                    HtmlEncode( $Amount ),
                                    HtmlEncode( $Currency ),
                                    HtmlEncode( $Order_Reference ),
                                    HtmlEncode( $keyedMAC ),
                                    HtmlEncode( $CMCIC_Tpe["retourko"] ),
                                    HtmlEncode( $Return_Context ),
                                    HtmlEncode( $CMCIC_Tpe["retourok"] ),
                                    HtmlEncode( $Return_Context ),
                                    HtmlEncode( $CMCIC_Tpe["retourko"] ),
                                    HtmlEncode( $Return_Context ),
                                    HtmlEncode( $Language_2 ),
                                    HtmlEncode( $Merchant_Code ),
                                    HtmlEncode($Order_Comment),
                                    HtmlEncode( $Button_Text ));
}

// ----------------------------------------------------------------------------
// Begin Main : Build payment variables from order context and format
//              CMCIC-compliant Payment form.
//                           ********************
//              Créer les variables du paiement à partir du contexte commande
//              et créer le formulaire de paiement CMCIC.
// ----------------------------------------------------------------------------
$CMCIC_Tpe = CMCIC_getMyTpe();               // TPE init variables
$CtlHmac   = CMCIC_CtlHmac($CMCIC_Tpe);      // TPE ok feedback

// ----------------------------------------------------------------------------
//  CheckOut Stub setting fictious Merchant and Order datas.
//  That's your job to set actual order fields. Here is a stub.
//                           ********************
//  Valorisation arbitraire des données commandes pour faire tourner un
//  exemple. Il vous appartient de donner les valeurs réelles associées à une
//  commande.
// -----------------------------------------------------------------------------

$stub_method = $_SERVER["REQUEST_METHOD"];
if (($stub_method == "GET") or ($stub_method == "POST")) {
    $wstub_order  = "HTTP_" . $stub_method . "_VARS";
    $stub_order  = ${$wstub_order};
}
else
    die ('Invalid REQUEST_METHOD (not GET, not POST).');

// Référence: unique, alphaNum (A-Z a-z 0-9), longueur maxi 12 caractères
@$Reference_12 = $_SESSION['navig']->commande->transaction;
$Reference_Cde = urlencode(substr($Reference_12, 0, 12));

// Langue: page de paiement "FR","EN","DE","IT","ES" selon options souscrites
@$Language_2   = $stub_order['language']."FR";   
$Code_Langue   = urlencode(substr($Language_2 , 0, 2));

// Code société: fourni par CM-CIC
$Code_Societe     = $CMCIC_Tpe['soc'];

// Montant: format  "xxxxx.yy" (pas de blancs))
$Montant          = "$total";

// Devise: norme ISO 4217 
$Devise           = "EUR";

// texte libre: une référence longue, des contextes de session pour le retour .
$Texte_Libre      = "";

// Texte du bouton pour accéder au serveur CM-CIC
$Texte_Bouton     = $CMCIC_Tpe['submit']; 

// --------------------------------------------------- End Stub ---------------


// ----------------------------------------------------------------------------
// Calling payment form function
//                           ********************
// Appel de la fonction formulaire de paiement
// ----------------------------------------------------------------------------
$Formulaire_Paiement = CreerFormulaireHmac($CMCIC_Tpe,
                                             $Montant,
                                             $Devise,
                                             $Reference_Cde,
                                             $Texte_Libre,
                                             $Code_Langue,
                                             $Code_Societe,
                                             $Texte_Bouton);

// ----------------------------------------------------------------------------
// Your Page displaying payment button to be customized  
//                           ********************
// Votre page à personnaliser affichant le bouton
// ----------------------------------------------------------------------------
?>
<TABLE BORDER="0" WIDTH="60%" ALIGN="CENTER">

<TR>
  <TD ALIGN="CENTER">
  	   <?php echo $Formulaire_Paiement;?><BR>
  </TD>
</TR>

</TABLE>
<?php
// --- continue with your own code .....  <<<<<------- votre code .....
//
// - Copyright (c) 2003 Euro-Information
//   (http://commerce.e-i.com/news/ mailto:centrecom@e-i.com).
//   All rights reserved. -
?>
	<script type="text/javascript">
	        document.forms['PaymentRequest'].submit();
	</script>
</BODY>
</HTML>
