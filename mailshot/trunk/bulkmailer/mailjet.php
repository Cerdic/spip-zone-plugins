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
 * @return Facteur
 */
function &bulkmailer_mailjet_dist($to_send,$options=array()){
	static $config = null;
	static $mailer_defaut;
	if (is_null($config)){
		$config = lire_config("mailshot/");
		$mailer_defaut = charger_fonction("defaut","bulkmailer");
	}

	// on ecrase le smtp avec celui de mailjet
	$options['smtp'] = array(
		"host" => "in.mailjet.com",
		"port" => "587",
		"auth" => "oui",
		"username" => $config['mailjet_api_key'],
		"password" => $config['mailjet_secret_key'],
		"secure" => "non",
	);

	// on utilise une surcharge pour gerer le tracking
	$options['sender_class'] = "FacteurMailjet";
	return $mailer_defaut($to_send,$options);
}

/**
 * Configurer mailjet : declarer le sender si besoin
 * @param $flux
 */
function bulkmailer_mailjet_config_dist(&$flux){
	$sender_mail = "";

	include_spip('inc/config');
	$config = lire_config('mailshot/');
	if ($config['adresse_envoi']=='oui')
		$sender_mail = $config['adresse_envoi_email'];
	else {
		include_spip("classes/facteur");
		$facteur = new Facteur("example@example.org","","","");
		$sender_mail = $facteur->From;
	}

	// si le sender n'est pas dans les emails de mailjet l'ajouter
	if ($sender_mail)
		mailjet_add_sender($sender_mail, true);
}


function &mailjet_api(){
	static $mj = null;
	if (is_null($mj)){
		include_spip('inc/config');
		$config = lire_config('mailshot/');

		include_spip('lib/mailjet-api-php/mailjet-0.1');
		$mj = new Mailjet($config['mailjet_api_key'],$config['mailjet_secret_key']);
		$mj->debug = 0;
	}

	return $mj;
}

function mailjet_sender_status($sender_email){

	$mj = mailjet_api();
	$res = (array)$mj->userSenderlist();
	if (!isset($res['status']) OR $res['status']!=='OK') return null;

	foreach($res['senders'] as $sender){
		if ($sender->email == $sender_email){
			if ($sender->enabled>0)
				return "active";
			else
				return "pending";
		}
	}

	return false;
}

/**
 * Verifier si un email d'envoi est dans la liste des senders mailjet
 * et sinon l'ajoute
 *
 * @param string $sender_email
 * @param bool $force
 * @return bool
 */
function mailjet_add_sender($sender_email, $force = false){

	$status = mailjet_sender_status($sender_email);

	if ($status=="active") return $status; // active
	if ($status AND !$force) return $status; // pending

	// si le sender n'est pas dans la liste ou en attente
	$mj = mailjet_api();

	// ajouter un sender
	$params = array(
		'method' => 'POST',
		'email' => $sender_email,
	);

	$res = (array)$mj->userSenderadd($params);
	if (!isset($res['status']) OR $res['status']!=='OK') return null;
	return mailjet_sender_status($sender_email);

}


class FacteurMailjet extends Facteur {

	public $send_options = array();

	/**
	 * utilise $this->send_options options d'envoi
	 *     string tracking_id
	 * @return bool
	 */
	public function Send() {
		if (isset($this->send_options['tracking_id'])
		  AND $id = $this->send_options['tracking_id']){

			$campaign = protocole_implicite($GLOBALS['meta']['adresse_site'])."/#".$this->send_options['tracking_id'];
			$this->AddCustomHeader("X-mailjet-campaign: $campaign");
			$this->AddCustomHeader("X-Mailjet-DeduplicateCampaign: true");
			$this->AddCustomHeader("X-Mailjet-TrackOpen: true");
			$this->AddCustomHeader("X-Mailjet-TrackClick: true");
		}

		return parent::Send();
	}
}