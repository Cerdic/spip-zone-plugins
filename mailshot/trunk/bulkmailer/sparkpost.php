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

	// on passe par l'API SMTP basique

	// on ecrase le smtp avec celui de sparkpost
	$options['smtp'] = array(
		"host" => "smtp.sparkpostmail.com",
		"port" => "587",
		"auth" => "oui",
		"username" => 'SMTP_Injection',
		"password" => $config['sparkpost_api_key'],
		"secure" => "non",
	);

	// on utilise une surcharge pour gerer le tracking
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
	$data = $GLOBALS['HTTP_RAW_POST_DATA'];
	spip_log("bulkmailer_sparkpost_webhook_dist $data","mailshot");

	// quand on ajoute le webhook Sparkpost fait un POST sans donnees
	if ($_SERVER['REQUEST_METHOD'] == 'HEAD' OR !strlen($data)){
		http_status(200);
		exit;
	}

	include_spip('inc/json');
	if (!$events = json_decode($data, true)){
		http_status(403);
		exit;
	}

	if (isset($events['msys'])) $events = $events['msys'];
	// si un seul event, on le met dans un tableau pour tout traiter de la meme facon
	if (isset($events['message_event'])) {
		$events = array($events['message_event']);
	}

	foreach($events as $event){
		// array("open", "click", "bounce", "spam", "blocked");
		$quoi = $event['type'];
		if ($quoi=="open") $quoi="read"; // open chez sparkpost, read ici
		if ($quoi=="click") $quoi="clic"; // click chez sparkpost, clic ici
		if ($quoi=="bounce") $quoi="soft_bounce"; // bounce chez sparkpost, soft_bounce ici
		//if ($quoi=="blocked") $quoi="reject"; // blocked chez sparkpost, reject ici

		$email = $event['rcpt_to'];
		$tracking_id = $event['campaign_id'];
		if ($tracking_id){
			$tracking_id = explode('/#',$tracking_id);
			if (reset($tracking_id)==protocole_implicite($GLOBALS['meta']['adresse_site'])){
				$tracking_id = end($tracking_id);
				spip_log("tracking $quoi $email $tracking_id",'mailshot');
				// appeler l'api webhook mailshot
				$feedback = charger_fonction("feedback","newsletter");
				$feedback($quoi,$email,$tracking_id);
			}
		}
	}

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

/*
	if (isset($res['Count'])
	  AND $res['Count']>0
		AND isset($res['Data'])
		AND $res['Data']){

		foreach($res['Data'] as $eventCallback){
			if (in_array($eventCallback['EventType'],$events)){
				if ($eventCallback['Url']===$url){
					// OK pour cet event, rien a faire
					$events = array_diff($events,array($eventCallback['EventType']));
				}
				else {
					// il faut supprimer cette callback qui n'est pas sur la bonne URL
					// et on la rajoutera ensuite avec la bonne URL (en dessous)
					$params = array(
						'path' => $eventCallback['ID'],
						'method' => 'DELETE',
					);
					$mj->eventcallbackurl($params);
				}
			}
		}
	}*/


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
 * @return array
 */
function sparkpost_api_call($method,$data=null) {
	static $api_key = null;
	if (is_null($api_key)){
		include_spip('inc/config');
		$config = lire_config('mailshot/');
		$api_key = $config['sparkpost_api_key'];
	}

	include_spip('inc/distant');
	$endpoint = "https://api.sparkpost.com/api/v1/";

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

	if (function_exists('recuperer_url')){
		$result = recuperer_url($url, array('datas'=>$post_data));
		$response = $result['page'];
		if ($result['status']>200){
			//var_dump($response);
		}
	}
	elseif(function_exists('curl_init')){
		$ch = curl_init();
		$headers = explode("\n",$headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if ($data){
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}
		$response = curl_exec($ch);
		curl_close($ch);
	}
	else {
		$response = recuperer_page($url, '', '', '', $post_data);
	}
	if (!$response
		OR !$response = json_decode($response, true)) {
		$response = array(
			'errors' => array(
				array(
					'code' => '???',
					'message' => 'Fail recuperer_page'
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
 * Utilise l'API SMTP valable dans toutes les versions d'API
 */
class FacteurSparkpost extends Facteur {

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

			$options = array(
				'options' => array(
					'open_tracking' => true,
					'click_tracking' => true,
					//'transactional' => true,
				),
				//'metadata' => array('key'=>'value'),
				//'tags' => array('tag1','tag2'),
				'campaign_id' => $campaign,
			);
			$this->AddCustomHeader("X-MSYS-API: ".json_encode($options));
		}

		return parent::Send();
	}
}