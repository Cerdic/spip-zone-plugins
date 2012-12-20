<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");
include_spip("classes/facteur");
include_spip("lib/mandrill-api-php/src/Mandrill");

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
function &bulkmailer_mandrill_dist($to_send,$options=array()){
	static $config = null;
	static $mailer_defaut;
	if (is_null($config)){
		$config = lire_config("mailshot/");
		$mailer_defaut = charger_fonction("defaut","bulkmailer");
	}

	// on ecrase le smtp avec celui de la config
	$options['sender_class'] = "FacteurMandrill";
	return $mailer_defaut($to_send,$options);

}


class FacteurMandrill extends Facteur {

	protected $message = array('to'=>array(),'headers'=>array());

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
			$this->message['to'][] = array('email'=>$address,'name'=>$name);
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
		if ($a = $this->cleanAdress($address,$name)){
			$this->message['bcc_address'] = $address;
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
	public function AddReplyTo($address, $name = '') {
		if ($a = $this->cleanAdress($address,$name)){
			$this->message['headers']['ReplyTo'] = $address;
			return true;
		}
		return false;
	}

	/**
	* Adds a custom header.
	* @access public
	* @return void
	*/
	public function AddCustomHeader($custom_header) {
		list($key,$value) = explode(':', $custom_header, 2);
		$this->message['headers'][$key] = trim($value);
	}

	public function Send() {
		$api_key = lire_config("mailshot/mandrill_api_key");

		/**
   * Send a new transactional message through Mandrill
   * @param struct $message the information on the message to send
   *     - html string the full HTML content to be sent
   *     - text string optional full text content to be sent
   *     - subject string the message subject
   *     - from_email string the sender email address.
   *     - from_name string optional from name to be used
   *     - to array an array of recipient information.
   *         - to[] struct a single recipient's information.
   *             - email string the email address of the recipient
   *             - name string the optional display name to use for the recipient
   *     - headers struct optional extra headers to add to the message (currently only Reply-To and X-* headers are allowed)
   *     - track_opens boolean whether or not to turn on open tracking for the message
   *     - track_clicks boolean whether or not to turn on click tracking for the message
   *     - auto_text boolean whether or not to automatically generate a text part for messages that are not given text
   *     - url_strip_qs boolean whether or not to strip the query string from URLs when aggregating tracked URL data
   *     - preserve_recipients boolean whether or not to expose all recipients in to "To" header for each email
   *     - bcc_address string an optional address to receive an exact copy of each recipient's email
   *     - merge boolean whether to evaluate merge tags in the message. Will automatically be set to true if either merge_vars or global_merge_vars are provided.
   *     - global_merge_vars array global merge variables to use for all recipients. You can override these per recipient.
   *         - global_merge_vars[] struct a single global merge variable
   *             - name string the global merge variable's name. Merge variable names are case-insensitive and may not start with _
   *             - content string the global merge variable's content
   *     - merge_vars array per-recipient merge variables, which override global merge variables with the same name.
   *         - merge_vars[] struct per-recipient merge variables
   *             - rcpt string the email address of the recipient that the merge variables should apply to
   *             - vars array the recipient's merge variables
   *                 - vars[] struct a single merge variable
   *                     - name string the merge variable's name. Merge variable names are case-insensitive and may not start with _
   *                     - content string the merge variable's content
   *     - tags array an array of string to tag the message with.  Stats are accumulated using tags, though we only store the first 100 we see, so this should not be unique or change frequently.  Tags should be 50 characters or less.  Any tags starting with an underscore are reserved for internal use and will cause errors.
   *         - tags[] string a single tag - must not start with an underscore
   *     - google_analytics_domains array an array of strings indicating for which any matching URLs will automatically have Google Analytics parameters appended to their query string automatically.
   *     - google_analytics_campaign array|string optional string indicating the value to set for the utm_campaign tracking parameter. If this isn't provided the email's from address will be used instead.
   *     - metadata array metadata an associative array of user metadata. Mandrill will store this metadata and make it available for retrieval. In addition, you can select up to 10 metadata fields to index and make searchable using the Mandrill search api.
   *     - recipient_metadata array Per-recipient metadata that will override the global values specified in the metadata parameter.
   *         - recipient_metadata[] struct metadata for a single recipient
   *             - rcpt string the email address of the recipient that the metadata is associated with
   *             - values array an associated array containing the recipient's unique metadata. If a key exists in both the per-recipient metadata and the global metadata, the per-recipient metadata will be used.
   *     - attachments array an array of supported attachments to add to the message
   *         - attachments[] struct a single supported attachment
   *             - type string the MIME type of the attachment - allowed types are text/*, image/*, and application/pdf
   *             - name string the file name of the attachment
   *             - content string the content of the attachment as a base64-encoded string
   * @param boolean $async enable a background sending mode that is optimized for bulk sending. In async mode, messages/send will immediately return a status of "queued" for every recipient. To handle rejections when sending in async mode, set up a webhook for the 'reject' event. Defaults to false for messages with fewer than 100 recipients; messages with more than 100 recipients are always sent asynchronously, regardless of the value of async.
   * @return array of structs for each recipient containing the key "email" with the email address and "status" as either "sent", "queued", or "rejected"
   *     - return[] struct the sending results for a single recipient
   *         - email string the email address of the recipient
   *         - status string the sending status of the recipient - either "sent", "queued", "rejected", or "invalid"
   */
		$this->message['html'] = $this->Body;
		$this->message['text'] = $this->AltBody;
		$this->message['subject'] = $this->Subject;
		$this->message['from_email'] = $this->From;
		$this->message['from_name'] = $this->FromName;

		$mandrill = new Mandrill($api_key);

		try {
			$res = $mandrill->messages->send($this->message, false);
		}
		catch (Exception $e) {
      $this->SetError($e->getMessage());
      return false;
    }

		switch ($res['status']){
			case 'error':
				$this->SetError($res['name'].": ".$res['message']);
				return false;
				break;
			case 'invalid':
				$this->SetError("invalid");
				return false;
				break;
			case 'rejected':
				$this->SetError("rejected");
				return false;
				break;
			case "sent":
			case "queued":
				return true;
				break;
		}

		$this->SetError("??????");
		return false;
	}

	public function CreateHeader(){}
}