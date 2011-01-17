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
	spipal_validation_arg($_POST, $GLOBALS['spipal_metas']['notify_url']);
}

function spipal_validation_arg($env, $url)
{
	unset($env['action']);
	if (!$env) {
		$env = $GLOBALS['spipal_test'];
		$test = true;
	} else $test = false;
	if ($test) {
		spip_log("\r\n-------------------------------------\r\n", 'paypal');
		spip_log(print_r($env, true)."\r\n", 'paypal');
	}
	$res = validation_pp_http_post($env, $url);
	$res = is_string($res) ?
	  validation_pp_http_ERREUR($res) :
	  validation_pp_http_VERIFIED($res);
	if ($test) 
		spip_log(print_r($res, true), 'paypal');
	return $res;
}

// a refaire avec "recuperer_page" qui gere les redirections et les port != 80
function validation_pp_http_post($env, $url)
{
	$fp = fsockopen ($url, 80, $errno, $errstr, 30);
	if (!$fp) return false;
	$body   = validation_pp_http_body($env);
	$header = validation_pp_http_header(strlen($body));

	# spip_log("\r\n----------------------------\r\n$header$body\r\n", 'paypal');
	fputs ($fp, $header . $body);
	$res = '';
	while (!feof($fp)) $res .= fgets ($fp, 1024);
	fclose ($fp);
	# spip_log("\r\n----------------------------\r\n$res", 'paypal');
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
// la transaction a ete faite si le statut est "completed"
// et on recupere la valeur de personnalisation pour valider.
// Il faudrait traiter le pb des notifications concurrentes, cf:
// https://www.x.com/docs/DOC-1084

function validation_pp_http_VERIFIED($env) {
	if ($env['payment_status'] !== 'Completed') {
	  spip_log("Retour paypal invalide " . serialize($env));
	  return array();
	}
        $custom = @unserialize($env['custom']);
	$f = isset($custom['validation']) ? charger_fonction($custom['validation'] . '_spipal', 'inc', true) : false;
	if (!$f) {
	  spip_log("Retour paypal sans continuation " . serialize($env));
	  return array();
	}
	return $f($env);
}

// Fonction de validation par defaut
function inc_valider_spipal_dist($env)
{
	$custom = @unserialize($env['custom']);
	$res = array('item_number' => $env['item_number'],
		     'id_auteur' => $custom['id_auteur'],
		     'versement_ht' => (($env['payment_gross']?$env['payment_gross']:$env['mc_gross']) - $env['tax']),
		     'versement_taxes' => $env['tax'],
		     'versement_charges' => $env['payment_fee']?$env['payment_fee']:$env['mc_fee'],
		     'devise' => $env['mc_currency']?$env['mc_currency']:'USD',
		     'date_versement' => "NOW()",
		     'notification' => $GLOBALS['spipal_metas']['garder_notification'] ? serialize($env) : '');
	$n = sql_insertq("spip_spipal_versements", $res);
	spip_log("Nouveau paiement $n par " . $custom['id_auteur']);
	return $res;
}

function validation_pp_http_ERREUR($erreur) {
	return "erreur plugin [spipal][validation_pp_http][$erreur]";
}

$GLOBALS['spipal_test'] = array(
	'custom' => serialize(array('validation' => 'valider', 'id_auteur' => 1)), 
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

// pour utiliser le simulateur de notification de Paypal
// https://developer.paypal.com/us/cgi-bin/devscr?cmd=_ipn-link-session
// y choisir "Web-Accept" comme type de transaction et mettre dans "custom":
// a:2:{s:10:"validation";s:7:"valider";s:9:"id_auteur";i:1;}
// echo $GLOBALS['spipal_test']['custom'];exit;
?>
