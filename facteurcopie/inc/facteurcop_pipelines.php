<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

function facteurcop_facteur_pre_envoi($facteur) {
	
	spip_log ("facteurcop_facteur_pre_envoi:".$GLOBALS['facteurcop_cc'].":".$GLOBALS['facteurcop_bcc'] , "facteurcop");


	$tabcopiescc = preg_split("/[\s,]+/", $GLOBALS['facteurcop_cc'] );
	foreach ( $tabcopiescc as $key => $val )
		{
		$facteur->AddCC( "$val");
		spip_log ("facteurcop_facteur_pre_envoi CC $val" , "facteurcop");
		}

	$tabcopiesbcc = preg_split("/[\s,]+/", $GLOBALS['facteurcop_bcc'] );
	foreach ( $tabcopiesbcc as $key => $val )
		{
		$facteur->AddBCC( "$val");
		spip_log ("facteurcop_facteur_pre_envoi BCC $val" , "facteurcop");
		}

	unset ( $tabcopiescc, $tabcopiesbcc);
	return $facteur;
}

?>
