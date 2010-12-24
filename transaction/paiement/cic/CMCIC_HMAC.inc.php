<?php

include_once(realpath(dirname(__FILE__)) . "/config.php");

/*****************************************************************************
 *
 * CM_CIC_Paiement "open source" kit for CyberMUT-P@iement(TM) and
 *                  P@iementCIC(TM).
 * RFC 2104 HMAC implementation for PHP
 *
 * File "CMCIC_HMAC.inc.php":
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
 * Implémentation RFC 2104 HMAC pour PHP
 *
 * Fichier "CMCIC_HMAC.inc.php" :
 *
 * Auteur   : Euro-Information/e-Commerce (contact: centrecom@e-i.com)
 * Version  : 1.03
 * Date     : 18/12/2003
 *
 * Copyright: (c) 2003 Euro-Information. Tous droits réservés.
 * Consulter le document de licence "Licence.txt" joint.
 *
 *****************************************************************************/

define("CMCIC_CTLHMAC","V1.03.sha1.php4--CtlHmac-%s-[%s]-%s");
define("CMCIC_VERSION","1.2open");

define("CMCIC_PHP2_RECEIPT","Pragma: no-cache \nContent-type: text/plain \nVersion: 1 %s");
define("CMCIC_PHP2_MACOK","OK");
define("CMCIC_PHP2_MACNOTOK","Document Falsifie 0--");
define("CMCIC_PHP2_FIELDS", "%s%s+%s+%s+%s+%s+%s+%s+");
define("CMCIC_PHP1_FIELDS", "%s%s*%s*%s%s*%s*%s*%s*%s*%s*");

define("CMCIC_PHP1_FORM",
        "<form action=\"https&#x3a;&#x2f;&#x2f;%s%spaiement.cgi\"
        method=\"post\" name=\"PaymentRequest\" target=\"_top\">
        <input type=\"hidden\" name=\"version\"        value=\"%s\">
        <input type=\"hidden\" name=\"TPE\"            value=\"%s\">
        <input type=\"hidden\" name=\"date\"           value=\"%s\">
        <input type=\"hidden\" name=\"montant\"        value=\"%s%s\">
        <input type=\"hidden\" name=\"reference\"      value=\"%s\">
        <input type=\"hidden\" name=\"MAC\"            value=\"%s\">
        <input type=\"hidden\" name=\"url_retour\"     value=\"%s%s\">
        <input type=\"hidden\" name=\"url_retour_ok\"  value=\"%s%s\">
        <input type=\"hidden\" name=\"url_retour_err\" value=\"%s%s\">
        <input type=\"hidden\" name=\"lgue\"           value=\"%s\">
        <input type=\"hidden\" name=\"societe\"        value=\"%s\">
        <input type=\"hidden\" name=\"texte-libre\"    value=\"%s\">
        <input type=\"image\" src=\"logo.jpg\" name=\"bouton\"         value=\"%s\">
    </form>");

// <<<--- begin custom OverWrite --------------
define("CMCIC_DIR", "$dir");

define("CMCIC_SERVER", "$serveur");

function CMCIC_hmac($CMCIC_Tpe, $data="")
{
	global $motdepasse;
	
	$pass = "$motdepasse";
    // OverWrite with Your's !
    // A remplacer par votre paramétrage.

// --->>> end custom OverWrite ----------------

    $k1 = pack("H*",sha1($pass));
    $l1 = strlen($k1);
    $k2 = pack("H*",$CMCIC_Tpe['key']);
    $l2 = strlen($k2);
    if ($l1 > $l2):
        $k2 = str_pad($k2, $l1, chr(0x00));
    elseif ($l2 > $l1):
        $k1 = str_pad($k1, $l2, chr(0x00));
    endif;

    if ($data==""):
        $d = "CtlHmac".CMCIC_VERSION.$CMCIC_Tpe['tpe'];
    else:
        $d = $data;
    endif;

    return strtolower(hmac_sha1($k1 ^ $k2, $d));
}

// ----------------------------------------------------------------------------
// RFC 2104 HMAC implementation for PHP 4 >= 4.3.0 - Creates a SHA1 HMAC.
// Eliminates the need to install mhash to compute a HMAC
// Adjusted from the md5 version by Lance Rushing .

// Implémentation RFC 2104 HMAC pour PHP 4 >= 4.3.0 - Création d'un SHA1 HMAC.
// Elimine l'installation de mhash pour le calcul d'un HMAC
// Adaptée de la version MD5 de Lance Rushing.
// ----------------------------------------------------------------------------

function hmac_sha1 ($key, $data)
{
    $length = 64; // block length for SHA1
    if (strlen($key) > $length) { $key = pack("H*",sha1($key)); }
    $key  = str_pad($key, $length, chr(0x00));
    $ipad = str_pad('', $length, chr(0x36));
    $opad = str_pad('', $length, chr(0x5c));
    $k_ipad = $key ^ $ipad ;
    $k_opad = $key ^ $opad;

    return sha1($k_opad  . pack("H*",sha1($k_ipad . $data)));
}

function CMCIC_CtlHmac($CMCIC_Tpe)
{
    return sprintf( CMCIC_CTLHMAC, CMCIC_VERSION,
                                  $CMCIC_Tpe['tpe'],
                                   CMCIC_hmac($CMCIC_Tpe) );
}

?>