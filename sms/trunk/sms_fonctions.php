<?php
/**
 * Fonctions utiles au plugin SMS SPIP
 *
 * @plugin	   SMS SPIP
 * @copyright  2015
 * @author	   tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Sms\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

//Utilisation de SMS factor
include_spip('inc/config');
if (lire_config('sms/prestataire') == "smsfactor") {
	function envoyer_sms($message,$destinataire,$arg=array()) {
		return smsfactor($message,$destinataire,$arg);
	}
} elseif (lire_config('sms/prestataire') == "octopush") {
	function envoyer_sms($message,$destinataire,$arg=array()) {
		return octopush($message,$destinataire,$arg);
	}
}

/**
 * Envoie le sms en utilisant l'API du prestataire sms factor
 *
 * @param string $message
 *		le texte d'envoie doit etre du texte et non du html
 * @param array $destinataire
 * @param array $arg
 *		utilise pour : $arg['sender']
 * @return boolean
**/
function smsfactor($message,$destinataire,$arg) {
	$username = lire_config('sms/login_smsfactor');
	$password = lire_config('sms/mdp_smsfactor');
	$sender   = ($arg['sender']) ? $arg['sender'] : lire_config('sms/expediteur_smsfactor');
	//$message  = nettoyer_xml($message);

	require_once('classes/smsfactor/sendSMSclass.php');
	$SENDSMS = new SendSMSclass();
	$retour  = $SENDSMS->SendSMS($username,$password,$sender,$message,$destinataire);

	$reponse = new SimpleXMLElement($retour);
	if ( $reponse->message == "OK" ) {
		if (count($destinataire)) {
			$cost = $reponse->cost;
			$sent = $reponse->sent;
			$nbr_sms = 0;
			if ($sent != 0) {
				$nbr_sms = $cost / $sent;
			}

			$type_sms = '';
			if (array_key_exists('type_sms', $arg)) {
				$type_sms = $arg['type_sms'];
			}

			foreach ($destinataire as $tel) {
				$set = array(
					'telephone' => md5($tel),
					'date'      => date("Y-m-d H:i:s"),
					'message'   => $message,
					'nbr_sms'   => $nbr_sms,
					'type_sms'  => $type_sms
				);
				sql_insertq('spip_sms_logs',$set);
			}
		}
		return true;
	} else {
		return false;
	}
}

/**
 * Envoie le sms en utilisant l'API du prestataire octopush-dm
 *
 * @param string $message
 *		le texte d'envoie doit etre du texte et non du html
 * @param array $destinataire
 * @param array $arg
 *		utilise pour : $arg['sms_sender']
 *		utilise pour : $arg['sms_mode'] => XXX = LowCost; FR = Premium; WWW = Monde
 *		utilise pour : $arg['sms_type'] => INSTANTANE (par defaut) ou DIFFERE (Non prévu pour le moment)
 * @return boolean
**/
function octopush($sms_text,$sms_recipients,$arg) {
	$user_login	= lire_config('sms/login_octopush');
	$api_key	= lire_config('sms/cle_api_octopush');
	$sms_text	= nettoyer_xml($sms_text);

	// Variable pour l'envoi
	$sms_type	= isset($arg['sms_type']) ? $arg['sms_type'] : 'XXX';
	$sms_mode	= isset($arg['sms_mode']) ? $arg['sms_mode'] : 'INSTANTANE';
	$sms_sender	= isset($arg['sms_sender']) ? $arg['sms_sender'] : lire_config('sms/expediteur_octopush');
	require_once('classes/octopush/sms.inc.php');

	$sms = new SMS_OCTOSPUSH();

	$sms->set_user_login($user_login);
	$sms->set_api_key($api_key);
	$sms->set_sms_mode($sms_mode);
	$sms->set_sms_text($sms_text);
	$sms->set_sms_recipients($sms_recipients);
	$sms->set_sms_type($sms_type);
	$sms->set_sms_sender($sms_sender);
	$sms->set_sms_request_id(uniqid());
	$sms->set_option_with_replies(0);
	$sms->set_option_transactional(1);
	$sms->set_sender_is_msisdn(0);
	//$sms->set_date(2016, 4, 17, 10, 19); // En cas d'envoi différé.
	$sms->set_request_keys('TRS');
	$xml = $sms->send();
	$xml = simplexml_load_string($xml);
	// Enregistrement pour suivi
	foreach ($sms_recipients as $tel) {
		$set = array(
			'telephone' => md5($tel),
			'date'      => date("Y-m-d H:i:s"),
			'message'   => $xml,
			'nbr_sms'   => '',
			'type_sms'  => $sms_type
		);
		sql_insertq('spip_sms_logs',$set);
	}
	return $xml;
}


/**
 * Afficher la balance de sms disponible
 *
 * <INCLURE{fond=inclure/octopush_balance} /> pour l'affichage
 *
 * @param string $type
 *		standard ou premium
 * @return boolean
 * 		valeur en nombre entier de sms restant
**/
function filtre_balance($type) {
	$username = lire_config('sms/login_octopush');
	$password = lire_config('sms/cle_api_octopush');

	require_once('classes/octopush/sms.inc.php');
	$sms = new SMS_OCTOSPUSH();

	$sms->set_user_login($username);
	$sms->set_api_key($password);

	$xml = $sms->getBalance();
	$xml = simplexml_load_string($xml);
	$balance = $xml->balance;
	$standard = $balance['1'];
	$premium = $balance['0'];
	$balance = array('standard' => $standard, 'premium' => $premium);
	$valeurs = intval($balance[$type]);
	return $valeurs;
}

function nettoyer_xml($texte) {
	$texte = str_replace('&', '&amp;',  $texte);
	$texte = str_replace('<', '&lt;',   $texte);
	$texte = str_replace('>', '&gt;',   $texte);
	$texte = str_replace('"', '&quot;', $texte);
	$texte = str_replace("'", "&apos;", $texte);

	return $texte;
}
