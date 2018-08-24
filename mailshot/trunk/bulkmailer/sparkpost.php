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
function &bulkmailer_sparkpost_dist($to_send,$options=array()){
	static $config = null;
	static $mailer_defaut;
	if (is_null($config)){
		$config = lire_config("mailshot/");
		$mailer_defaut = charger_fonction("defaut","bulkmailer");
	}
	
	// on utilise une surcharge pour utiliser l'API http
	$options['sender_class'] = "FacteurSparkpost";
	return $mailer_defaut($to_send,$options);
}

/**
 * Configurer sparkpost : declarer le sender si besoin
 * appele depuis traiter() de formulaire_configurer_mailshot
 * @param $res
 */
function bulkmailer_sparkpost_config_dist(&$res){
	include_spip('inc/config');
	$config = lire_config('mailshot/');
	if ($config['adresse_envoi']=='oui')
		$sender_mail = $config['adresse_envoi_email'];
	else {
		include_spip("classes/facteur");
		$facteur = new Facteur("example@example.org","","","");
		$sender_mail = $facteur->From;
	}

	// si le sender n'est pas dans les emails de sparkpost l'ajouter
	if ($sender_mail){
		sparkpost_add_sender($sender_mail);
		//debug purpose :
		//bulkmailer_sparkpost_init_dist(0);
	}

}


/**
 * Prendre en charge le webhook sparkpost
 *
 *     "msys": {
         "message_event": {
           "type": "bounce",
           "bounce_class": "1",
           "campaign_id": "Example Campaign Name",
           "customer_id": "1",
           "error_code": "554",
           "ip_address": "127.0.0.1",
           "message_id": "0e0d94b7-9085-4e3c-ab30-e3f2cd9c273e",
           "msg_from": "sender@example.com",
           "msg_size": "1337",
           "num_retries": "2",
           "rcpt_meta": {},
           "rcpt_tags": [
             "male",
             "US"
           ],
           "rcpt_to": "recipient@example.com",
           "reason": "000 Example Remote MTA Bounce Message",
           "routing_domain": "example.com",
           "template_id": "templ-1234",
           "template_version": "1",
           "timestamp": 1427736822,
           "transmission_id": "65832150921904138"
         }
       }
 *
 * @param $arg
 */
function bulkmailer_sparkpost_webhook_dist($arg){

	// les donnes sont postees en JSON RAW
	if (isset($GLOBALS['HTTP_RAW_POST_DATA']) AND $GLOBALS['HTTP_RAW_POST_DATA']){
		$data = $GLOBALS['HTTP_RAW_POST_DATA'];
	}
	// PHP 5.6+ : $GLOBALS['HTTP_RAW_POST_DATA'] obsolete et non peuplee
	else {
		$data = file_get_contents('php://input');
	}

	spip_log("bulkmailer_sparkpost_webhook_dist $data","mailshot_feedback");

	// HEAD de test ? (a priori pas utilise par SparkPost)
	if ($_SERVER['REQUEST_METHOD'] == 'HEAD' OR !strlen($data)){
		http_status(200);
		exit;
	}

	include_spip('inc/json');
	if (!$events = json_decode($data, true)){
		http_status(403);
		exit;
	}

	// si un seul event, on le met dans un tableau pour tout traiter de la meme facon
	if (isset($events['msys'])) {
		$events = array($events);
	}

	foreach($events as $e){
		if (isset($e['msys']['track_event'])) {
			bulkmailer_sparkpost_webhook_track_event($e['msys']['track_event']);
		}
		if (isset($e['msys']['message_event'])) {
			bulkmailer_sparkpost_webhook_message_event($e['msys']['message_event']);
		}
	}

	// il faut finir par un status 200 sinon SparkPost considere que c'est un echec
	http_status(200);

}

function bulkmailer_sparkpost_webhook_track_event($event) {
	// array("open", "click", "bounce", "spam", "blocked");
	$quoi = $event['type'];
	if ($quoi == "open") {
		$quoi = "read"; // open chez sparkpost, read ici
	}
	if ($quoi == "click") {
		$quoi = "clic"; // click chez sparkpost, clic ici
	}
	if ($quoi == "bounce") {
		$quoi = "soft_bounce"; // bounce chez sparkpost, soft_bounce ici
		if (isset($event['bounce_class'])) {
			switch ($event['bounce_class']) {
				case "20":
				case "21":
				case "22":
				case "23":
				case "24":
				case "25":
				case "40":
				case "60":
				case "70":
				case "100":
					$quoi = "soft_bounce";
					break;
				default:
					$quoi = "hard_bounce";
					break;
			}
		}
	}
	if ($quoi == "out_of_band") {
		$quoi = "soft_bounce";
	}
	if ($quoi == "spam_complaint") {
		$quoi = "spam";
	}
	if ($quoi == "policy_rejection") {
		$quoi = "reject";
	}

	$email = $event['rcpt_to'];
	$tracking_id = $event['campaign_id'];
	if ($tracking_id) {
		$tracking_id = explode('/#', $tracking_id);
		if (reset($tracking_id) == protocole_implicite($GLOBALS['meta']['adresse_site'])) {
			$tracking_id = end($tracking_id);
			spip_log("tracking $quoi $email $tracking_id", 'mailshot_feedback');
			// appeler l'api webhook mailshot
			$feedback = charger_fonction("feedback", "newsletter");
			$feedback($quoi, $email, $tracking_id);
		}
	}
}

function bulkmailer_sparkpost_webhook_message_event($event) {
	return bulkmailer_sparkpost_webhook_track_event($event);
}

/**
 * Initialiser sparkpost : declarer un eventcallbackurl pour recuperer les retours sur bounce, reject, open, clic....
 *
 * @param int $id_mailshot
 * @return bool
 */
function bulkmailer_sparkpost_init_dist($id_mailshot=0){

	spip_log("bulkmailer_sparkpost_init_dist $id_mailshot","mailshot");

	$res = sparkpost_api_call('webhooks');
	#var_dump($res);

	// son webhook
	$url = url_absolue(_DIR_RACINE."mailshot_webhook.api/sparkpost/");
	$data = array(
		'name' => 'Mailshot WebHook',
		'target' => $url,
		'auth_type' => 'none',
		'events' => array(
			/*"delivery", "injection",*/ "open", "click", "bounce",
		),
	);

	// verifier si le webhook existe deja
	$found = false;
	if (isset($res['results'])){
		foreach($res['results'] as $webhook){
			if ($webhook['target']==$url){

				if (!$found
				  AND count(array_intersect($webhook['events'],$data['events']))==count($data['events'])
				  AND $webhook['auth_type']==$data['auth_type']){
					$found = true;
				}
				else {
					// sinon il faut supprimer ce webhook
					sparkpost_api_call('webhooks/'.$webhook['id'],null,'DELETE');
				}
			}
		}
	}

	if ($found) return true;

	// si le webhook n'existe pas on l'ajoute
	$res = sparkpost_api_call('webhooks',$data);
	#var_dump($res);


	return true;

}



/**
 * Verifier si le domaine de l'email d'envoi est dans la liste
 * des sending domains de sparkpost
 * et sinon l'ajoute (mais a valider manuellement par l'utilisateur)
 *
 * @param string $sender_email
 * @return bool
 */
function sparkpost_add_sender($sender_email){

	$domain = explode('@',$sender_email);
	$domain = end($domain);

	$res = sparkpost_api_call('sending-domains');

	if (!is_array($res)) return false;
	if (isset($res['errors'])) return false; // pas le droit ?
	if (!isset($res['results'])) return false;

	$domain_ok = false;
	foreach($res['results'] as $r){
		if (isset($r['domain']) AND $r['domain']==$domain){
			$domain_ok = true;
			break;
		}
	}

	if (!$domain_ok){
		$data = array('domain'=>$domain);
		$res = sparkpost_api_call('sending-domains',$data);
		//var_dump($res);
	}

	return true;
}


/**
 * Call on SparkPost API
 * @param string $method
 * @param array $data
 * @param string $http_req
 * @return array
 */
function sparkpost_api_call($method,$data=null,$http_req=null) {
	static $api_key = null;
	static $api_endpoint = null;
	if (is_null($api_key)){
		include_spip('inc/config');
		$config = lire_config('mailshot/');
		$api_key = $config['sparkpost_api_key'];
		$api_endpoint = (isset($config['sparkpost_api_endpoint'])?$config['sparkpost_api_endpoint']:'');
		if (!in_array($api_endpoint, array('', 'eu'))) {
			$api_endpoint = '';
		}
		$api_endpoint = ltrim($api_endpoint . '.sparkpost.com', '.');
	}

	include_spip('inc/distant');
	$endpoint = "https://api.".$api_endpoint."/api/v1/";
	// for TLSv1.2 test purpose
	//$endpoint = "https://no-tlsv1-test-api.".$api_endpoint."/api/v1/";

	$headers =
		  "Authorization: $api_key\n"
	  . "Accept: application/json\n";
	$url = $endpoint . $method;

	$post_data = "";
	if ($data){
		$headers = "Content-Type: application/json\n".$headers;
		$post_data = json_encode($data);
	}
	$post_data = $headers . "\n" . $post_data;

	$debug = '';
	if(function_exists('curl_init')){
		$ch = curl_init();
		$headers = explode("\n",$headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if (in_array($http_req,array("DELETE","PUT"))){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_req);
		}
		if ($data){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}
		if (!defined('CURL_SSLVERSION_TLSv1_2')) {
		    define('CURL_SSLVERSION_TLSv1_2', 6);
		}

		curl_setopt ($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		$response = curl_exec($ch);
		if (!$response) {
			$debug = curl_error($ch);
		}
		curl_close($ch);
		$fonction_utilisee = 'curl';
	}
	elseif (function_exists('recuperer_url')){
		$options = array(
			'datas'=>$post_data
		);
		if (in_array($http_req,array("DELETE","PUT"))) {
			$options['methode'] = $http_req;
		}
		$result = recuperer_url($url, $options);
		$debug = var_export($result, true);
		$response = $result['page'];
		$fonction_utilisee = 'recuperer_url';
	}
	else {
		$response = recuperer_page($url, '', '', '', $post_data);
		$fonction_utilisee = 'recuperer_page';
	}
	if (!$response
		OR !$response = json_decode($response, true)) {
		$response = array(
			'errors' => array(
				array(
					'code' => '???',
					'message' => 'Fail ' . $fonction_utilisee . ' ' . $debug,
				)
			)
		);
	}

	if (isset($response['errors'])){
		foreach($response['errors'] as $err){
			spip_log("SparkPost API Call $method : Erreur ".$err['code'].' '.$err['message'],'mailshot'._LOG_ERREUR);
		}
	}

	return $response;
}


/**
 * Class FacteurSparkpost
 * Utilise l'API HTTP transmission
 */
class FacteurSparkpost extends Facteur {

	public $send_options = array();
	protected $message = array(
		'options' => array(
			'open_tracking' => false,
			'clic_tracking' => false,
		),
		'campaign_id' => '',
		#'return_path' => '',// do not provide if empty
		#'metadata' => array(), // not used here, need to be a JSON object, fail if empty
		#'substitution_data' => array(), // not used here, need to be a JSON object, fail if empty
		'recipients' => array(
			#array('address' => array('email'=>'','name'=>''))
		),
		'content' => array(
			'from' => array('email'=>'','name'=>''),
			'subject' => '',
			#'reply_to' => '', // do not provide if empty
			'headers' => array(),
			'text' => '',
			'html' => '',
		)
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
			$to = array('email'=>$address,'name'=>$name);
			$this->message['recipients'][] = array('address'=>$to);
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
			if ($name) $address = "$name <$address>";
			$this->message['content']['reply_to'] = $address;
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
		$this->message['content']['headers'][trim($name)] = trim($value);
	}

	/**
	 * utilise $this->send_options options d'envoi
	 *     string tracking_id
	 * @return bool
	 */
	public function Send() {

		$this->message['content']['html'] = $this->Body;
		$this->message['content']['text'] = $this->AltBody;
		$this->message['content']['subject'] = $this->Subject;
		$this->message['content']['from']['email'] = $this->From;
		$this->message['content']['from']['name'] = $this->FromName;

		// ajouter le tracking_id en tag, pour retrouver le message apres webhook
		if (isset($this->send_options['tracking_id'])
		  AND $id = $this->send_options['tracking_id']){
			$this->message['options']['open_tracking'] = true;
			$this->message['options']['clic_tracking'] = true;
			// prefixer le tracking par l'url du site (coupée à 45 caractères car campaign_id accepte 64 max) pour ne pas melanger les feedbacks
			$this->message['campaign_id'] = substr(protocole_implicite($GLOBALS['meta']['adresse_site']), 0, 45)."/#".$this->send_options['tracking_id'];
		}

		try {
			$response = sparkpost_api_call('transmissions',$this->message);
		}
		catch (Exception $e) {
      $this->SetError($e->getMessage());
      return false;
    }

		spip_log("FacteurSparkpost->Send resultat:".var_export($response,true),"mailshot");


		// statut d'erreur au premier niveau ?
		if (isset($response['errors'])){
			$err = "";
			foreach($response['errors'] as $e){
				$err .=  $e['code'].' '.$e['message']."\n";
			}
			$dump = $this->message;
			$dump['content']['html'] = '...';
			$dump['content']['text'] = '...';
			$this->SetError($err . var_export($dump,true));
			return false;
		}

		// sinon regarder le status du premier mail envoye (le to)
		/*
		{
		  "results": {
		    "total_rejected_recipients": 0,
		    "total_accepted_recipients": 1,
		    "id": "11668787484950529"
		  }
		}
		 */
		if (isset($response['results'])){
			if ($response['results']['total_accepted_recipients']>=1){
				return true;
			}
			if ($response['results']['total_rejected_recipients']>=1){
				$this->SetError("rejected");
				return false;
			}
		}

		// ici on ne sait pas ce qu'il s'est passe !
		$this->SetError("??????".var_export($response,true));
		spip_log("FacteurSparkpost->Send resultat inatendu : ".var_export($response,true),"mailshot"._LOG_ERREUR);
		return false;

	}

	public function CreateHeader(){}
}
