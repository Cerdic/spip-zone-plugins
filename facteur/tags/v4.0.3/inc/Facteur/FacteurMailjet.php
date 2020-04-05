<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\FacteurSMTP
 */

namespace SPIP\Facteur;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use SPIP\Facteur\Api\Mailjetv3 as Mailjet;

if (!defined("_ECRIRE_INC_VERSION")){
	return;
}



include_spip('inc/Facteur/FacteurMail');


function checkMessagesSentStatus($ids, $apiCredentials, $sendFailFunction, $count=0) {
	$count++;
	$recheck = [];
	$failed = [];
	if ($ids
	  and $mj = FacteurMailjet::newMailjetApi($apiCredentials['version'], $apiCredentials['key'], $apiCredentials['secretKey'])) {
		foreach ($ids as $id) {
			FacteurMailjet::logDebug("checkMessagesSentStatus: check message id $id", 0);
			$status = $mj->message(['path' => $id]);
			if (!$status){
				$recheck[] = $id;
			}
			else {
				if (empty($status['Count']) or empty($status['Data'])) {
					FacteurMailjet::logDebug("checkMessagesSentStatus: FAIL message $id " . json_encode($status), 0);
					$failed[] = $id;
				}
				else {
					foreach ($status['Data'] as $message) {
						switch (strtolower($message['Status'])) {
							case 'unknown':
							case 'queued':
							case 'deferred':
								FacteurMailjet::logDebug("checkMessagesSentStatus: RECHECK message $id " . json_encode($message), 0);
								$recheck[] = $id;
								break;

							case 'bounce':
							case 'spam':
							case 'unsub':
							case 'blocked':
							case 'hardbounced':
							case 'softbounced':
								$failed[] = $id;
								FacteurMailjet::logDebug("checkMessagesSentStatus: FAIL message $id " . json_encode($message), 0);
								break;

							case 'sent':
							case 'opened':
							case 'clicked':
							default:
								FacteurMailjet::logDebug("checkMessagesSentStatus: OK message $id " . json_encode($message), 0);
								break;
						}
					}
				}
			}
			if (count($failed)) {
				break;
			}
		}
	}

	if ($failed
	  or ($recheck and $count>=5)) {
		$facteur_envoyer_alerte_fail = charger_fonction('facteur_envoyer_alerte_fail','inc');
		$facteur_envoyer_alerte_fail($sendFailFunction['function'], $sendFailFunction['args'], $sendFailFunction['include']);
	}
	elseif ($recheck) {
		// on re-essaye dans 5mn, 5 fois maxi en tout
		$delay = 5 * 60;
		FacteurMailjet::planCheckMessagesSent($delay, $ids, $apiCredentials, $sendFailFunction, $count);
	}
	// tout est bon, rien a faire on a fini
	FacteurMailjet::logDebug("checkMessagesSentStatus: Fini", 0);
}

/**
 * Utilise l'API REST en v3
 * Class FacteurMailjetv3
 */
class FacteurMailjet extends FacteurMail {

	protected $api_version = "v3/";
	protected $message = array(
		'FromEmail' => '',
		'FromName' => '',
		'Subject' => '',
		'Text-part' => '',
		'Html-part' => '',
		//'Mj-campaign' => '',
		//'Mj-deduplicatecampaign' => 1,
		//'Mj-CustomID' => '',
		'Headers' => array(//'Reply-To' => 'copilot@mailjet.com',
		),
		'Attachments' => array(// {"Content-type":"text/plain","Filename":"test.txt","content":"VGhpcyBpcyB5b3VyIGF0dGFjaGVkIGZpbGUhISEK"}]
		),
		'Inline_attachments' => array(// {"Content-type":"text/plain","Filename":"test.txt","content":"VGhpcyBpcyB5b3VyIGF0dGFjaGVkIGZpbGUhISEK"}]
		),

	);
	protected $message_dest = array(
		'To' => array(/*array(
	    'Email' => '',
	    'Name' => '',
	   )*/
		),
		'Cc' => array(/*array(
	    'Email' => '',
	    'Name' => '',
	   )*/
		),
		'Bcc' => array(/*array(
	    'Email' => '',
	    'Name' => '',
	   )*/
		),
	);

	// pour le tracking des campagne
	protected $trackingId;

	protected $apiVersion = 3;
	protected $apiKey;
	protected $apiSecretKey;

	public static function newMailjetApi($version, $key, $secretKey) {
		switch ($version) {
			case 3:
			default:
				include_spip('inc/Facteur/Api/Mailjetv3');
				$mj = new Mailjet($key, $secretKey);
		}
		$mj->debug = 0;

		return $mj;
	}

	public static function planCheckMessagesSent($delay, $ids, $apiCredentials, $sendFailFunction, $count=0) {
		$include = "inc/Facteur/FacteurMailjet";
		$time = time() + $delay;
		self::logDebug("planCheckMessagesSent: ids " . implode(', ', $ids), 0);
		job_queue_add('SPIP\Facteur\checkMessagesSentStatus', "Mailjet Important mail checkMessagesSentStatus", [$ids, $apiCredentials, $sendFailFunction, $count], $include, false, $time);
	}

	/**
	 * Facteur constructor.
	 * @param array $options
	 * @throws Exception
	 */
	public function __construct($options = array()){
		parent::__construct($options);
		$this->mailer = 'mailjet';

		if (!empty($options['mailjet_api_version'])){
			$this->apiVersion = $options['mailjet_api_version'];
		}
		if (!empty($options['mailjet_api_key'])){
			$this->apiKey = $options['mailjet_api_key'];
		}
		if (!empty($options['mailjet_api_version'])){
			$this->apiSecretKey = $options['mailjet_secret_key'];
		}
		if (!empty($options['tracking_id'])){
			$this->trackingId = $options['tracking_id'];
		}
	}

	/**
	 * Auto-configuration du mailer si besoin
	 * (rien a faire ici dans le cas par defaut)
	 * @return bool
	 */
	public function configure(){
		parent::configure();
		$this->addAuthorizedSender($this->From);
		return true;
	}

	/**
	 * @return Mailjet
	 */
	protected function &getMailjetAPI(){
		static $mj = null;
		if (is_null($mj)){
			$mj = self::newMailjetApi($this->apiVersion, $this->apiKey, $this->apiSecretKey);
		}
		return $mj;
	}


	/**
	 * Verifier si un email d'envoi est dans la liste des senders mailjet
	 * et sinon l'ajoute
	 *
	 * @param string $sender_email
	 * @param bool $force
	 * @return bool
	 */
	protected function addAuthorizedSender($sender_email, $force = false){

		$status = $this->readSenderStatus($sender_email);

		if ($status=="active"){
			return $status;
		} // active
		if ($status AND !$force){
			return $status;
		} // pending

		// si le sender n'est pas dans la liste ou en attente
		$mj = $this->getMailjetAPI();

		$params = array(
			'data' => array('Email' => $sender_email),
		);
		$res = $mj->sender($params);

		return $this->readSenderStatus($sender_email);
	}

	/**
	 * Lire le status d'un sender chez mailjet
	 * @param string $sender_email
	 * @return bool|string
	 */
	protected function readSenderStatus($sender_email){

		$mj = $this->getMailjetAPI();
		$params = array(
			'filters' => array('Email' => $sender_email),
		);
		$res = (array)$mj->sender($params);
		if (!isset($res['Count'])){
			return null;
		}
		if (isset($res['Data'])){
			foreach ($res['Data'] as $sender){
				if ($sender['Email']==$sender_email){
					if (in_array($sender['Status'], array('Active', 'Inactive'))){
						return strtolower($sender['Status']);
					}
				}
			}
		}

		return false;
	}


	protected function cleanAdress($address, $name = ''){
		$address = trim($address);
		$name = trim(preg_replace('/[\r\n]+/', ' ', $name)); //Strip breaks and trim
		if (!self::ValidateAddress($address)){
			$this->SetError('invalid_address' . ': ' . $address);
			return false;
		}
		return array('Email' => $address, 'Name' => $name);
	}

	/**
	 * Mettre en forme une addresse email
	 * @param $dest
	 * @return string
	 */
	protected function formatEmailDest($dest){
		$d = $dest['Email'];
		if (!empty($dest['Name'])){
			$name = $dest['Name'];
			if (preg_match(",\W,", $name)){
				$name = '"' . $name . '"';
			}
			$d = $name . " <$d>";
		}
		return $d;
	}

	/**
	 * Clear all recipients
	 */
	public function clearAllRecipients(){
		$this->message_dest['To'] = [];
		$this->message_dest['Cc'] = [];
		$this->message_dest['Bcc'] = [];
		parent::clearAllRecipients();
	}

	/**
	 * Adds a "To" address.
	 * @param string $address
	 * @param string $name
	 * @return boolean true on success, false if address already used
	 */
	public function AddAddress($address, $name = ''){
		if ($a = $this->cleanAdress($address, $name)){
			$this->message_dest['To'][] = $a;
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
	public function AddCC($address, $name = ''){
		if ($a = $this->cleanAdress($address, $name)){
			$this->message_dest['Cc'][] = $a;
			return true;
		}
		return false;
	}

	/**
	 * Adds a "Bcc" address.
	 * Note: this function works with the SMTP mailer on win32, not with the "mail" mailer.
	 * @param string $address
	 * @param string $name
	 * @return boolean true on success, false if address already used
	 */
	public function AddBCC($address, $name = ''){
		if ($a = $this->cleanAdress($address, $name)){
			$this->message_dest['Bcc'][] = $a;
			return true;
		}
		return false;
	}

	/**
	 * Adds a "Reply-to" address.
	 * @param string $address
	 * @param string $name
	 * @return boolean
	 */
	public function AddReplyTo($address, $name = ''){
		if ($a = $this->cleanAdress($address, $name)){
			$this->message['Headers']['Reply-To'] = $this->formatEmailDest($a);
			return true;
		}
		return false;
	}

	/**
	 * Adds a custom header.
	 * @access public
	 * @return void
	 */
	public function AddCustomHeader($name, $value = null){
		if ($value===null){
			// Value passed in as name:value
			list($name, $value) = explode(':', $name, 2);
		}
		$this->message['Headers'][$name] = trim($value);
	}


	/**
	 * Ne sert pas, sauf aux logs internes
	 * @return string|void
	 */
	public function CreateHeader(){
		$header = "";

		$header .= "Date: " . date('Y-m-d H:i:s') . "\n";

		$from = $this->formatEmailDest(['Email' => $this->From, 'Name' => $this->FromName]);
		$header .= "From: $from\n";

		foreach (['To', 'Cc', 'Bcc'] as $dest_type){
			if (!empty($this->message_dest[$dest_type]) and count($this->message_dest[$dest_type])){
				$dests = [];
				foreach ($this->message_dest[$dest_type] as $dest){
					$dests[] = $this->formatEmailDest($dest);
				}
				$header .= "$dest_type: " . implode(',', $dests) . "\n";
			}
		}

		if (!empty($this->message['Headers'])){
			foreach ($this->message['Headers'] as $k => $h){
				$header .= "$k: $h\n";
			}
		}

		return $header;
	}


	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function Send(){
		$this->forceFromIfNeeded();

		$this->message['Html-part'] = $this->Body;
		$this->message['Text-part'] = $this->AltBody;
		$this->message['Subject'] = $this->Subject;
		$this->message['FromEmail'] = $this->From;
		$this->message['FromName'] = $this->FromName;


		if (count($this->attachment)){
			$inline_attachements = [];
			$attachments = [];
			foreach ($this->attachment as $attachment){
				$bString = $attachment[5];
				if ($bString){
					$string = $attachment[0];
				} else {
					$path = $attachment[0];
					$string = file_get_contents($path);
				}
				$string = base64_encode($string);

				if ($attachment[6]==='inline'){
					$inline_attachements[] = array(
						"Content-type" => $attachment[4],
						"Filename" => $attachment[7], // cid
						"content" => $string,
					);
				} else {
					$attachments[] = array(
						"Content-type" => $attachment[4],
						"Filename" => $attachment[1],
						"content" => $string
					);
				}
				// {"Content-type":"text/plain","Filename":"test.txt","content":"VGhpcyBpcyB5b3VyIGF0dGFjaGVkIGZpbGUhISEK"}]
			}
			$this->message['Attachments'] = $attachments;
			$this->message['Inline_attachments'] = $inline_attachements;
		}

		foreach (['To', 'Cc', 'Bcc'] as $dest_type){
			if (!empty($this->message_dest[$dest_type]) and count($this->message_dest[$dest_type])){
				$dests = array();
				foreach ($this->message_dest[$dest_type] as $dest){
					$dests[] = $this->formatEmailDest($dest);
				}
				$this->message[$dest_type] = implode(',', $dests);
			}
		}

		// ajouter le trackingId en tag, pour retrouver le message apres webhook
		if (!empty($this->trackingId)
			and $id = $this->trackingId){
			// prefixer le tracking par l'url du site pour ne pas melanger les feedbacks
			$this->message['Mj-campaign'] = protocole_implicite($GLOBALS['meta']['adresse_site']) . "/#" . $id;
			$this->message['Mj-deduplicatecampaign'] = 1;
		}


		// pas de valeur vide dans le message
		foreach (array_keys($this->message) as $k){
			if (empty($this->message[$k])){
				unset($this->message[$k]);
			}
		}

		/*
		$trace = $this->message;
		unset($trace['Html-part']);
		unset($trace['Text-part']);
		if (!empty($trace['Attachments'])) {
			$trace['Attachments'] = "Array(".count($trace['Attachments']) .")";
		}
		if (!empty($trace['Inline_attachments'])) {
			$trace['Inline_attachments'] = "Array(".count($trace['Inline_attachments']) .")";
		}
		$this->log($trace, _LOG_DEBUG);
		*/

		$mj = $this->getMailjetAPI();
		$res = $mj->send(array('data' => $this->message));
		if (!$res){
			$this->SetError($mj->_error);
			if ($this->exceptions){
				throw new \Exception($mj->_error);
			}
			return $this->sendAlertIfNeeded(false);
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

			$error = "status " . $res['StatusCode'] . " - " . $res['ErrorInfo'] . ": " . $res['ErrorMessage'];
			$this->SetError($error);
			if ($this->exceptions){
				throw new \Exception($error);
			}
			return $this->sendAlertIfNeeded(false);
		}

		// { "Sent" : [{ "Email" : "cedric@yterium.com", "MessageID" : 19140330729428381 }] }
		if (isset($res['Sent']) AND count($res['Sent'])){
			return $this->sendAlertIfNeeded($res);
		}
		// les autres type de reponse sont non documentees. On essaye au hasard?
		if (isset($res['Queued']) AND count($res['Queued'])){
			return $this->sendAlertIfNeeded($res);
		}
		if (isset($res['Invalid']) AND count($res['Invalid'])){
			$this->SetError($error = "invalid");
			if ($this->exceptions){
				throw new \Exception($error);
			}
			return $this->sendAlertIfNeeded(false);
		}
		if (isset($res['Rejected']) AND count($res['Rejected'])){
			$this->SetError($error = "rejected");
			if ($this->exceptions){
				throw new \Exception($error);
			}
			return $this->sendAlertIfNeeded(false);
		}

		// Erreur inconnue
		$this->SetError("mailjetERROR " . var_export($res, true));
		$this->log($error = "mailjet/send resultat inatendu : " . json_encode($res), _LOG_ERREUR);
		if ($this->exceptions){
			throw new \Exception($error);
		}
		return $this->sendAlertIfNeeded(false);
	}


	/**
	 * Verifier si il faut envoyer le mail d'alerte
	 * @param mixed $res
	 * @return mixed
	 */
	protected function sendAlertIfNeeded($res) {
		if ($res === false) {
			return parent::sendAlertIfNeeded($res);
		}
		if ($this->important and !empty($this->sendFailFunction)){
			// sinon chercher les ids des message a verifier un peu plus tard
			if (isset($res['Sent']) AND count($res['Sent'])){
				$message_ids = [];
				$all_dests = array_column($this->message_dest['To'], 'Email');
				foreach ($res['Sent'] as $message){
					if (!empty($message['Email']) and !empty($message['MessageID'])){
						$dest = $message['Email'];
						$id = $message['MessageID'];
						if (in_array($dest, $all_dests)){
							$message_ids[] = $id;
						}
					}
				}
				if ($message_ids){
					// verifier ces ids dans 60s
					$apiCredentials = ['version' => $this->apiVersion, 'key' => $this->apiKey, 'secretKey' => $this->apiSecretKey];
					FacteurMailjet::planCheckMessagesSent(60, $message_ids, $apiCredentials, $this->sendFailFunction);
				}
			}
		}

		return $res;
	}

}
