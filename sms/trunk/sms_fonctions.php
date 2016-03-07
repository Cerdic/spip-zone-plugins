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
if ( lire_config('sms/prestataire') == "smsfactor") {

	function envoyer_sms($message,$destinataire,$arg=array()){
		return smsfactor($message,$destinataire,$arg);
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
function smsfactor($message,$destinataire,$arg){
	$username = lire_config('sms/login_smsfactor');
	$password = lire_config('sms/mdp_smsfactor');
	$sender   = ($arg['sender']) ? $arg['sender'] : lire_config('sms/expediteur_smsfactor');
	$message  = nettoyer_xml($message);

	require_once('classes/smsfactor/sendSMSclass.php');
	$SENDSMS = new SendSMSclass();
	$retour  = $SENDSMS->SendSMS($username,$password,$sender,$message,$destinataire);

	$reponse = new SimpleXMLElement($retour);
	if ( $reponse->message == "OK" ){
		return true;
	}else{
		return false;
	}
}

function nettoyer_xml($texte){
	$texte = str_replace('&', '&amp;',  $texte);
	$texte = str_replace('<', '&lt;',   $texte);
	$texte = str_replace('>', '&gt;',   $texte);
	$texte = str_replace('"', '&quot;', $texte);
	$texte = str_replace("'", "&apos;", $texte);

	return $texte;
}
