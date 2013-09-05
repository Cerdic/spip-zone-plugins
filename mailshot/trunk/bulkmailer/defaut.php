<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");
include_spip("classes/facteur");

/**
 * @param array $to_send
 *   string email
 *   string sujet
 *   string html
 *   string texte
 * @param array $options
 *   bool filtre_images
 *   array smtp
 *     string host
 *     string port
 *     string auth
 *     string username
 *     string password
 *     string secure
 *     string errorsto
 *   string adresse_envoi_nom
 *   string adresse_envoi_email
 *   string sender_class : permet de specifier une autre class que "Facteur" (surcharges)
 * @return Facteur
 */
function &bulkmailer_defaut_dist($to_send,$options=array()){
	static $config = null;
	if (is_null($config)){
		$config = lire_config("mailshot/");
	}

	if (!isset($options['smtp'])
	  AND !isset($options['sender_class'])
	  AND lire_config("facteur_smtp")=='non'){
		spip_log("Pas de smtp configure et envoi par mail() refuse pour le bulk","mailshot"._LOG_ERREUR);
		return false;
	}

	$defaut = array(
		'filtre_images' => false,
		'filtre_iso_8859' => false, // le passage en iso fait foirer les envois propres par smtp et mandrill
	);
	// envoyeur
	if ($config['adresse_envoi']=='oui'){
		$defaut['adresse_envoi_nom'] = $config['adresse_envoi_nom'];
		$defaut['adresse_envoi_email'] = $config['adresse_envoi_email'];
	}

	$options = array_merge($defaut,$options);

	if (isset($options['adresse_envoi_email']))
		$options['adresse_envoi'] = 'oui';

	// regler le smtp au format facteur
	if (isset($options['smtp'])){
		foreach (array('host','port','auth','username','password','secure','errorsto') as $quoi){
			$options['smtp_'.$quoi] = (isset($options['smtp'][$quoi])?$options['smtp'][$quoi]:'');
		}
		$options['smtp_sender'] = $options['smtp_errorsto'];
		$options['smtp'] = 'oui';
	}

	// desactiver les cc&bcc automatique eventuel de facteur
	if (!isset($options['bcc']))
		$options['bcc'] = '';
	if (!isset($options['cc']))
		$options['cc'] = '';

	$sender_class = "Facteur";
	if (isset($options['sender_class']))
		$sender_class = $options['sender_class'];
	$facteur = new $sender_class($to_send['email'], $to_send['sujet'], $to_send['html'], $to_send['texte'], $options);

	// We are Bulk : https://support.google.com/mail/bin/answer.py?hl=en&answer=81126
	$facteur->AddCustomHeader("Precedence: bulk");

	return $facteur;
}