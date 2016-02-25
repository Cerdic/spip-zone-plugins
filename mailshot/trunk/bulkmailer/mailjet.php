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

	$api_version = 1;
	if (isset($config['mailjet_api_version']) AND intval($config['mailjet_api_version'])>1){
		$api_version = intval($config['mailjet_api_version']);
	}

	if ($api_version==3){
		// on utilise l'API REST
		$options['sender_class'] = "FacteurMailjetv3";
	}
	else {
		// on passe par l'API SMTP basique

		// on ecrase le smtp avec celui de mailjet
		$options['smtp'] = array(
			"host" => "in.mailjet.com",
			"port" => "587",
			"auth" => "oui",
			"username" => $config['mailjet_api_key'],
			"password" => $config['mailjet_secret_key'],
			"secure" => "non",
		);
		// support des API v2 et v3 de Mailjet
		if (isset($config['mailjet_api_version']) AND intval($config['mailjet_api_version'])>1){
			$options['smtp']['host'] = "in-v".intval($config['mailjet_api_version']).".mailjet.com";
		}

		// on utilise une surcharge pour gerer le tracking
		$options['sender_class'] = "FacteurMailjet";
	}
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

		$api_version = 1;
		if (isset($config['mailjet_api_version']) AND intval($config['mailjet_api_version'])>1){
			$api_version = intval($config['mailjet_api_version']);
		}

		if ($api_version==3) {
			include_spip('lib/mailjet-api-php/mailjet-3');
		}
		else {
			include_spip('lib/mailjet-api-php/mailjet-0.1');
		}
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

function mailjet_id_from_custom_campaign($tracking_id){
	$mj = mailjet_api();
	$params = array(
		'custom_campaign' => protocole_implicite($GLOBALS['meta']['adresse_site'])."/#".$tracking_id,
	);
	$res = (array)$mj->messageList($params);
	if (!isset($res['status']) OR $res['status']!=='OK') return null;
	if (!$res['total_cnt']) return false;
	$res = reset($res['result']);
	return $res->id;
}


/**
 * Class FacteurMailjet
 * Utilise l'API SMTP valable dans toutes les versions d'API
 */
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
			$this->AddCustomHeader("X-Mailjet-campaign: $campaign");
			$this->AddCustomHeader("X-Mailjet-DeduplicateCampaign: 1");
			$this->AddCustomHeader("X-Mailjet-TrackOpen: 1");
			$this->AddCustomHeader("X-Mailjet-TrackClick: 1");
			$this->AddCustomHeader("X-Mailjet-Prio: 0");
		}

		return parent::Send();
	}
}


/**
 * Utilise l'API REST en v3
 * Class FacteurMailjetv3
 */
class FacteurMailjetv3 extends Facteur {

	protected $api_version = "v3/";
	public $send_options = array();
	protected $message = array(
		'FromEmail' => '',
	  'FromName' => '',
    'Subject' => '',
    'Text-part' => '',
    'Html-part' => '',
    'Recipients' => array(
	    /*array(
		    'Email' => '',
		    'Name' => '',
	    )*/
    ),
    //'Mj-campaign' => '',
    //'Mj-deduplicatecampaign' => 1,
		//'Mj-CustomID' => '',
		'Headers' => array(
			//'Reply-To' => 'copilot@mailjet.com',
		),
	);

	protected function cleanAdress($address, $name = ''){
		$address = trim($address);
    $name = trim(preg_replace('/[\r\n]+/', '', $name)); //Strip breaks and trim
		if (!self::ValidateAddress($address)) {
			$this->SetError('invalid_address'.': '. $address);
			return false;
	  }
		return array($address,$name);
	}

	/**
	* Adds a "To" address.
	* @param string $address
	* @param string $name
	* @return boolean true on success, false if address already used
	*/
	public function AddAddress($address, $name = '') {
		if ($a = $this->cleanAdress($address,$name)){
			$this->message['Recipients'][] = array('Email'=>$address,'Name'=>$name);
			return true;
		}
		return false;
	}

	/**
	* Adds a "Cc" address.
	* Note: this function works with the SMTP mailer on win32, not with the "mail" mailer.
	* @param string $address
	* @param string $name
	* @return boolean true on success, false if address already used
	*/
	public function AddCC($address, $name = '') {
		return $this->AddAddress($address, $name);
	}

	/**
	* Adds a "Bcc" address.
	* Note: this function works with the SMTP mailer on win32, not with the "mail" mailer.
	* @param string $address
	* @param string $name
	* @return boolean true on success, false if address already used
	*/
	public function AddBCC($address, $name = '') {
		return $this->AddAddress($address, $name);
	}

	/**
	* Adds a "Reply-to" address.
	* @param string $address
	* @param string $name
	* @return boolean
	*/
	public function AddReplyTo($address, $name = '') {
		if ($a = $this->cleanAdress($address,$name)){
			$this->message['Headers']['ReplyTo'] = $address;
			return true;
		}
		return false;
	}

	/**
	* Adds a custom header.
	* @access public
	* @return void
	*/
	public function AddCustomHeader($name, $value = null) {
		if ($value === null) {
			// Value passed in as name:value
			list($name, $value) = explode(':', $name, 2);
		}
		$this->message['Headers'][$name] = trim($value);
	}

	/**
	 * utilise $this->send_options options d'envoi
	 *     string tracking_id
	 * @return bool
	 */
	public function Send() {

		$this->message['Html-part'] = $this->Body;
		$this->message['Text-part'] = $this->AltBody;
		$this->message['Subject'] = $this->Subject;
		$this->message['FromEmail'] = $this->From;
		$this->message['FromName'] = $this->FromName;

		// ajouter le tracking_id en tag, pour retrouver le message apres webhook
		if (isset($this->send_options['tracking_id'])
		  AND $id = $this->send_options['tracking_id']){
			//$this->message['track_opens'] = true;
			//$this->message['track_clicks'] = true;

			// prefixer le tracking par l'url du site pour ne pas melanger les feedbacks
			$this->message['Mj-campaign'] = protocole_implicite($GLOBALS['meta']['adresse_site'])."/#".$this->send_options['tracking_id'];
			$this->message['Mj-deduplicatecampaign'] = 1;
		}

		$mj = mailjet_api();
		include_spip('inc/json');
		$res = $mj->send(array('data'=>json_encode($this->message)));
		if (!$res){
			$this->SetError($mj->_error);
			return false;
		}

		/*
		{
		    "ErrorInfo": "Bad Request",
		    "ErrorMessage": "Unknown resource: \"contacts\"",
		    "StatusCode": 400
		}
		*/

		// statut d'erreur au premier niveau ?
		if (isset($res['StatusCode'])
		  AND intval($res['StatusCode']/100)>2){

			$this->SetError("status ".$res['StatusCode']." - ".$res['ErrorInfo'].": ".$res['ErrorMessage']);
			return false;
		}

		// { "Sent" : [{ "Email" : "cedric@yterium.com", "MessageID" : 19140330729428381 }] }
		if (isset($res['Sent']) AND count($res['Sent'])){
			return true;
		}
		// les autres type de reponse sont non documentees. On essaye au hasard…
		if (isset($res['Queued']) AND count($res['Queued'])){
			return true;
		}
		if (isset($res['Invalid']) AND count($res['Invalid'])){
			$this->SetError("invalid");
			return false;
		}
		if (isset($res['Rejected']) AND count($res['Rejected'])){
			$this->SetError("rejected");
			return false;
		}

		// Erreur inconnue
		$this->SetError("mailjetERROR ".var_export($res,true));
		spip_log("mailjet/send resultat inatendu : ".json_encode($res),"mailshot_errors"._LOG_ERREUR);
		return false;
	}

	public function CreateHeader(){}
}
