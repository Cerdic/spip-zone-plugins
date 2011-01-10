<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

// script dont l'execution est provoquee par le serveur Paypal
// ==> pas de securite SPIP, c'est le serveur Paypal qui la garantit au rappel
// 

function action_spipal_valider_paiement()
{
  //file_put_contents('paypal.log', "-------------------------------------\r\n");
//file_put_contents('paypal.log', print_r($_REQUEST, true)."\r\n", FILE_APPEND);
  $res = spipal_validation_arg($_POST, $GLOBALS['spipal_metas']['notify_url']);
  //file_put_contents('paypal.log', print_r($res, true), FILE_APPEND);
  if (!is_string($res)) $res = var_dump($res, true);
  spip_log($res);
#  echo $res;
}

function spipal_validation_arg($env, $url)
{
	unset($env['action']);
	$res = validation_pp_http_post($env, $url);
	return is_string($res) ?
	  validation_pp_http_ERREUR($res) :
	  validation_pp_http_VERIFIED($res, $GLOBALS['spipal_metas']['garder_notification']);
}

function validation_pp_http_post($env, $url)
{
	if (!$env) return $GLOBALS['spipal_test'];
	$fp = fsockopen ($url, 80, $errno, $errstr, 30);
	if (!$fp) return false;
	$body   = validation_pp_http_body($env);
	$header = validation_pp_http_header(strlen($body));

//file_put_contents('paypal.log', "----------------------------\r\n$header$body\r\n", FILE_APPEND);
	fputs ($fp, $header . $body);
	$res = '';
	while (!feof($fp)) {
	  $res .= fgets ($fp, 1024);
	}
	fclose ($fp);
//file_put_contents('paypal.log', "----------------------------\r\n$res", FILE_APPEND);
	return (strpos ($res, "VERIFIED") !== false) ? $env :
	  ((strpos ($res, "INVALID") !== false) ? "INVALID" : "???");
}

function validation_pp_http_body($env) {
    $body = 'cmd=_notify-validate';
    foreach ($env as $key => $value) {
        $value = urlencode(stripslashes($value));
        $body .= "&$key=$value";
    }
    return $body;
}

function validation_pp_http_header($length) {
  return  "POST /cgi-bin/webscr HTTP/1.0\r\n"
    . "Content-Type: application/x-www-form-urlencoded\r\n"
    . "Content-Length: " . $length . "\r\n\r\n";
}

// Quand le serveur paypal transmet un message comportant VERIFIED, 
// la transaction est valide et les informations sont fiables

function validation_pp_http_VERIFIED($env, $trace) {
	if ($env['payment_status'] !== 'Completed' ) return array();
        include_spip('base/abstract_sql');
        $custom = unserialize($env['custom']);
	$res = array('item_number' => $env['item_number'],
		     'id_auteur' => $custom['id_auteur'],
		     'versement_ht' => (($env['payment_gross']?$env['payment_gross']:$env['mc_gross']) - $env['tax']),
		     'versement_taxes' => $env['tax'],
		     'versement_charges' => $env['payment_fee']?$env['payment_fee']:$env['mc_fee'],
		     'devise' => $env['mc_currency']?$env['mc_currency']:'USD',
		     'date_versement' => "NOW()",
		     'notification' => $trace ? serialize($env) : '');
	sql_insertq("spip_spipal_versements", $res);
	return $res;
}

function validation_pp_http_ERREUR($erreur) {
	return "erreur plugin [spipal][validation_pp_http][$erreur]";
}

$GLOBALS['spipal_test'] = array(
	'custom' => serialize(array('id_auteur' => 1)),
	'item_number' => 'Essai',
	'tax' => 3,
	'payment_gross' => 4,
	'mc_gross' => 5,
	'payment_fee' => 6,
	'mc_fee' => 7,
	'mc_currency' => 'euro',
	'date_versement' => "NOW()",
	'payment_status' => 'Completed'
				);
?>
