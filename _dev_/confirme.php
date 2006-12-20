<?php

/***************************************************************************
 *  BOUTIQUE : Plugin, version lite d'un e-commerce pour SPIP              *
 *                                                                         *
 *  Copyright (c) 2006-2007                                                *
 *  Laurent RIEFFEL : mailto:laurent.rieffel@laposte.net			   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 ***************************************************************************/

/*
 * Boutique
 * version plug-in d'un e-commerce
 *
 * Auteur : Laurent RIEFFEL
 * 
 * Module pour SPIP version 1.9.x
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */


/*****************************************************************************
 *
 * CM_CIC_Paiement "open source" kit for CyberMUT-P@iement(TM) and
 *                  P@iementCIC(TM).
 * Process CMCIC Payment. Sample RFC2104 compliant with PHP4 skeleton.
 *
 * File "aRenommerResultat.php":
 *
 * Author   : Euro-Information/e-Commerce (contact: centrecom@e-i.com)
 * Version  : 1.03
 * Date     : 18/12/2003
 *
 * Copyright: (c) 2003 Euro-Information. All rights reserved.
 * License  : see attached document "Licence.txt".
 *
 *----------------------------------------------------------------------------
 *
 * CM_CIC_Paiement: kit "open source" pour CyberMUT-P@iement(TM) et
 *                  P@iementCIC(TM).
 * Traitement Paiement CMCIC. Exemple compatible RFC2104, base en PHP4
 *
 * Fichier "aRenommerResultat.php" :
 *
 * Auteur   : Euro-Information/e-Commerce (contact: centrecom@e-i.com)
 * Version  : 1.03
 * Date     : 18/12/2003
 *
 * Copyright: (c) 2003 Euro-Information. Tous droits réservés.
 * Consulter le document de licence "Licence.txt" joint.
 *
 *****************************************************************************/

//
// DEBUGGING MODE
//
//	echo "<p><strong>".("confirme.php [phase 1]")."</strong> ";
//	exit;
//
// FIN
//

if (!defined('_INCLUDE_ECOMMERCE')) 
	{
	# include necessaire a la boutique?
	@define('_DIR_INCLUDE', 'include/');
	include_once _DIR_INCLUDE.'ecommerce_mysql_engine.php'; 
	}
if (!defined('_ECRIRE_INC_VERSION')) 
	{
	# ou est l'espace prive ?
	@define('_DIR_RESTREINT_ABS', 'ecrire/');
	include_once _DIR_RESTREINT_ABS.'inc_version.php';
	}

 
// --- Nothing to customize below before a first successfull receipt test ---
// --- Rien à changer ci-dessous avant un premier A/R test correct ---

// --- PHP implementation of RFC2104 hmac sha1 ---
// --- Implémentation PHP du RFC2104 hmac sha1 ---
@require_once("CMCIC_HMAC.inc.php");
if (!function_exists('CMCIC_hmac')) 
	{
	die ('cant require hmac function.');
	}
$coderetour=$_GET['code-retour'];
$textelibre=$_GET['texte-libre'];


// ----------------------------------------------------------------------------
// function CMCIC_getMyTpe
//
// IN: Code société / Company code
//     Code langue / Language code
//
// OUT: Paramètres du Tpe / Tpe parameters
// Description: Get TPE Number, 2nd part of Key and other Merchant
//              Configuration Datas from merchant DataBase
//              Rechercher le numéro de TPE, la 2nde partie cryptée de clef
//              et autres infos de configuration Marchand
// ----------------------------------------------------------------------------
function CMCIC_getMyTpe($soc="mysoc",$lang="")
{
     @require("MyTpeCMCIC.inc.php");
     if ( !is_array($MyTpe) ) { die ('cant require Tpe config.'); }
     return $MyTpe;
}

// ----------------------------------------------------------------------------
// function TesterHmac
//
// IN: Paramètres du Tpe / Tpe parameters
//     Champs du formulaire / Form fields
// OUT: Résultat vérification / Verification result
// description: Vérifier le MAC et préparer la Reponse
//              Perform MAC verification and create Receipt
// ----------------------------------------------------------------------------
function TesterHmac($CMCIC_Tpe, $CMCIC_bruteVars )
{
   @$php2_fields = sprintf(CMCIC_PHP2_FIELDS, $CMCIC_bruteVars['retourPLUS'], 
                                              $CMCIC_Tpe["tpe"], 
                                              $CMCIC_bruteVars["date"],
                                              $CMCIC_bruteVars['montant'],
                                              $CMCIC_bruteVars['reference'],
                                              $CMCIC_bruteVars['texte-libre'],
                                               CMCIC_VERSION,
                                              $CMCIC_bruteVars['code-retour']);


    if ( strtolower($CMCIC_bruteVars['MAC'] ) == CMCIC_hmac($CMCIC_Tpe, $php2_fields) ):
        $result  = $CMCIC_bruteVars['code-retour'].$CMCIC_bruteVars['retourPLUS'];
        $receipt = CMCIC_PHP2_MACOK;
    else: 
        $result  = 'None';
        $receipt = CMCIC_PHP2_MACNOTOK.$php2_fields;
    endif;

    $mnt_lth = strlen($CMCIC_bruteVars['montant'] ) - 3;
    if ($mnt_lth > 0):
        $currency = substr($CMCIC_bruteVars['montant'], $mnt_lth, 3 );
        $amount   = substr($CMCIC_bruteVars['montant'], 0, $mnt_lth );
    else:
        $currency = "";
        $amount   = $CMCIC_bruteVars['montant'];
    endif;

    return array( "resultatVerifie" => $result ,
                  "accuseReception" => $receipt ,
                  "tpe"             => $CMCIC_bruteVars['TPE'],
                  "reference"       => $CMCIC_bruteVars['reference'],
                  "texteLibre"      => $CMCIC_bruteVars['texte-libre'],
                  "devise"          => $currency,
                  "montant"         => $amount);
}

// Begin Main : Retrieve Variables posted by CMCIC Payment Server 
//              Recevoir les variables postées par le serveur bancaire


//
// DEBUGGING MODE
//
//	echo "<p><strong>".("confirme.php [debut]")."</strong> ";
//	exit;
//
// FIN
//



$CMCIC_reqMethod  = $HTTP_SERVER_VARS["REQUEST_METHOD"];
if (($CMCIC_reqMethod == "GET") or ($CMCIC_reqMethod == "POST")) {
    $wCMCIC_bruteVars = "HTTP_".$CMCIC_reqMethod."_VARS";
    $CMCIC_bruteVars  = ${$wCMCIC_bruteVars};
}
else
    die ('Invalid REQUEST_METHOD (not GET, not POST).');

@$isVariableEmpty  = $CMCIC_bruteVars['TPE'];

// empty variables ?
if (!($isVariableEmpty > " "))
{
    // You should do your best to write your scripts so that they do not
    // require register_globals to be on. Using form variables as globals
    // can easily lead to possible security problems, if the code is not 
    // very well thought of.
    // Il est recommandé de ne pas écrire de scripts qui exige de paramétrer
    // register_globals à on. Utiliser les variables du formulaire comme
    // globales peut amener des problèmes de sécurité si votre script n'est
    // pas très bien conçu.

    // var_dump($CMCIC_bruteVars);
    echo "\r\nTrying PHP<=3 old style ! "."\r\n";

    settype($CMCIC_bruteVars , "array"); 

    @$CMCIC_bruteVars['MAC']         = $MAC;
    @$CMCIC_bruteVars['TPE']         = $TPE;
    @$CMCIC_bruteVars['date']        = $date;
    @$CMCIC_bruteVars['montant']     = $montant;
    @$CMCIC_bruteVars['reference']   = $reference;
    $URL_texte_libre                 = "texte-libre";
    @$CMCIC_bruteVars['texte-libre'] = $$URL_texte_libre;
    $URL_code_retour                 = "code-retour";
    @$CMCIC_bruteVars['code-retour'] = $$URL_code_retour;
    @$CMCIC_bruteVars['retourPLUS']   = $retourPLUS;

    // var_dump($CMCIC_bruteVars);
    echo "\r\n Is it Better ? "."\r\n";
}

// TPE init variables
// variables initiales TPE
@$CMCIC_Tpe = CMCIC_getMyTpe();

// Message Authentication
// Test d'authentification
@$CMCIC_authVars   = TesterHmac($CMCIC_Tpe, $CMCIC_bruteVars );

@$Verified_Result  = $CMCIC_authVars['resultatVerifie'];

// <<<--- code <<<--- 
// (Cas / Case : "None" , "Annulation" , "Payetest", "Paiement")

	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
		$transaction=urlencode($CMCIC_bruteVars['retourPLUS']);
		$coderetour=$CMCIC_bruteVars['code-retour'];
		$sql = "UPDATE `spip_ecommerce_sessions` SET 
			`statut` = '$coderetour',
			`transaction` = '$transaction',
			`maj` = NOW() 
			WHERE `code_session` = $reference"; 
		$result = mysql_query($sql, $link);
		if (!$result) 
			{
			echo "Erreur DB, impossible d'effectuer une requête\n";
			echo 'Erreur MySQL : ' . mysql_error();
			exit;
			}
	mysql_close($link);


//-----------------------------------------------------------------------------
// Dump variables may give you an idea about what to do
//                           ********************
// Vider ces variables peut vous aider à voir ce qui est à coder
//-----------------------------------------------------------------------------
// var_dump($Verified_Result_Array);
// var_dump($CMCIC_bruteVars);
// var_dump($CMCIC_authVars);
//-----------------------------------------------------------------------------
// Send receipt to CMCIC server
// Envoyer un A/R au serveur bancaire
//-----------------------------------------------------------------------------
@printf (CMCIC_PHP2_RECEIPT, $CMCIC_authVars['accuseReception']);

// Copyright (c) 2003 Euro-Information ( mailto:centrecom@e-i.com )
// All rights reserved. ---
?>
