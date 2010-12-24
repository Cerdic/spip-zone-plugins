<?php

include_once(realpath(dirname(__FILE__)) . "/config.php");

global $tpe, $soc, $key, $motdepasse, $retourok, $retourko, $dir, $serveur;

/*****************************************************************************
 *
 * CM_CIC_Paiement "open source" kit for CyberMUT-P@iement(TM) and
 *                  P@iementCIC(TM).
 * TPE functions for PHP.
 *
 * File "MyTpeCMCIC.inc.php":
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
 * fonctions TPE en PHP
 *
 * Fichier "MyTpeCMCIC.inc.php" :
 *
 * Auteur   : Euro-Information/e-Commerce (contact: centrecom@e-i.com)
 * Version  : 1.03
 * Date     : 18/12/2003
 *
 * Copyright: (c) 2003 Euro-Information. Tous droits réservés.
 * Consulter le document de licence "Licence.txt" joint.
 *
 *****************************************************************************/
// ----------------------------------------------------------------------------
// Note : typically to be rewrited to get configurations depending on 
//        $soc,$lang from your  database
// Note : à ré-écrire pour rechercher les configurations dépendant de
//        $soc,$lang dans votre database
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
// If the merchant suscribed several Terminal (TPE) numbers, distinct TPE 
// configurations may be retrieved from here, depending on $soc argument.
// Dans le cas ou un même marchand détient plusieurs numéros de TPE virtuels,
// plusieurs configurations de TPE peuvent être obtenues ici, 
// selon le paramètre $soc . 
// ----------------------------------------------------------------------------
switch ($soc) 
{
    case "doNotOverwrite":
       $MyTpe = array ( "tpe" =>"$tpe", "$soc" => "$soc", "key" => "$key" );

        break;
    default:
$MyTpe = array ( "tpe" =>"$tpe", "soc" => "$soc", "key" => "$key" );

    // <<<--- begin custom OverWrite ---

$MyTpe = array ( "tpe" =>"$tpe", "soc" => "$soc", "key" => "$key" );
    $MyTpe["retourok"] = "$retourok";
    $MyTpe["retourko"] = "$retourko";
    $MyTpe["submit"]   = "Paiement";

    // --->>>  end  custom OverWrite ---
}

// ----------------------------------------------------------------------------
// Additional TPE configuration datas may be retrieved from here, depending on
// $soc,$lang arguments
// Autres infos de configuration de TPE selon les paramètres $soc,$lang
// ----------------------------------------------------------------------------
switch ($lang)
{
    case "xx":
        $MyTpe["retourok"] = "http://www.google.cn";
        $MyTpe["retourko"] = "http://www.google.tv";
        break;
}

// ----------------------------------------------------------------------------
// Important : ReWrite to use secure databases under Merchant's responsibility.
// Important : Il est de la responsabilité du marchand de ré-écrire le code en
// fonction de ses bases et outils de sécurité.
// ----------------------------------------------------------------------------

?>
