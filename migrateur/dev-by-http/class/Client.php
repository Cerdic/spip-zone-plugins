<?php

namespace SPIP\Migrateur;


class Client {

	private $auth_key;
	private $url_source;

	/** @var LoggerInterface */
	protected $logger;

	/** @var Crypteur */
	protected $crypteur;



	public function __construct($auth_key, $aes_key, $url_source, $script = "spip.php?action=migrateur_serveur") {
		$this->auth_key = $auth_key;
		$this->url_source = rtrim($url_source, "/") . "/" . ltrim($script, "/");

		$this->crypteur = new Crypteur($aes_key);
	}


	public function action($action, $args = null) {
		if (!is_string($action)) {
			$this->log("Action incomprise");
			return false;
		}

		$ActionClass = "SPIP\\Migrateur\\Client\\Action\\" . $action;

		if (!class_exists($ActionClass)) {
			$this->log("Action inconnue");
			return false;
		}

		$act = new $ActionClass();
		$act->setLogger($this->logger);
		$act->setParent($this);
		$act->setDestination(migrateur_infos());
		return $act->run($args);
	}


	/**
	 * Envoie une demande (cryptée) au serveur
	 *
	 * @param string $action Nom de la demande
	 * @param mixed $data Données envoyées
	 * @param string $retour Type de retour attendu : json | file
	 * @return false|array
	 *     - false : une erreur…
	 *     - array : dans la clé `message`, le retour du serveur.
	**/
	public function ask($action, $data = null, $retour = 'json') {
		return $this->send_request($action, $data, $retour);
	}


	/**
	 * Analyse et décrypte une réponse du serveur 
	 *
	 * @param array $reponse
	 * @param bool $decrypt
	 * @return false|array
	**/
	private function analyser_reponse($reponse, $decrypt = true) {
		if (!$reponse) {
			$this->log("Réponse en échec");
			return false;
		}

		if (!is_array($reponse)) {
			$this->log("Type de réponse erroné");
			var_dump($reponse);
			return false;
		}

		if (isset($reponse['error'])) {
			$this->log("Retour d'erreur indiqué");
			$this->log($reponse['error']);
			return false;
		}

		if (!isset($reponse['message'])) {
			$this->log("Retour incompris ou absence de message");
			return false;
		}

		// decrypter le message
		if ($decrypt) {
			$reponse['message'] = $this->crypteur->decrypt($reponse['message']);
		}

		return $reponse;
	}

	/**
	 * Envoie une requête cryptée au serveur et récupère une réponse cryptée
	 *
	 * @param string $action
	 * @param mixed $data
	 * @param string $retour
	 *     Type de retour attendu (json | file)
	 * @return mixed|false
	 *     false en cas d'erreur.
	 *     mixed (array probablement) qui est la réponse du serveur.
	**/
	private function send_request($action, $data = null, $retour = 'json') {

		if (!$json = $this->prepare_json_request_message($action, $data)) {
			return false;
		}

		switch ($retour) {
			case 'json':
				$reponse = $this->send_json_request($json);
				return $this->analyser_reponse($reponse, true);
			case 'file':
				// data = chemin du fichier
				$reponse = $this->send_json_request_file($json, $data); 
				return $this->analyser_reponse($reponse, false);
			default:
				$this->log("Type de retour attendu inconnu : " . $retour);
				return false;
		}
	}

	/**
	 * Prépare le message json qui correspond à la demande faite au serveur
	 *
	 * Le message contient une clé d'authentification (en clair)
	 * et un message (crypté)
	 *
	 * @param string $action
	 * @param mixed $data
	 * @return mixed|false
	 *     - false en cas d'erreur.
	 *     - string message json 
	**/
	private function prepare_json_request_message($action, $data) {
		if (!$this->auth_key OR !$this->url_source) {
			$this->log("Clé d'authentification ou URL du site source non défini");
			return false;
		}

		// on crypte le contenu du message envoyé
		$message = $this->crypteur->encrypt($action, $data);

		$json = json_encode(array(
			'key' => $this->auth_key,
			'message' => $message
		));

		return $json;
	}


	/**
	 * Envoie une requête au serveur et récupère la réponse au format json
	 *
	 * Le serveur crypte la réponse que l'on décrypte au passage.
	 * 
	 * @param string $json
	 * @return mixed|false
	 *     false en cas d'erreur.
	 *     mixed (array probablement) qui est la réponse du serveur.
	**/
	private function send_json_request($json) {

		$curl = curl_init($this->url_source);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

		list($headers, $body) = explode("\r\n\r\n", curl_exec($curl), 2);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($code != 200) {
			throw new \RuntimeException('HTTP error: '.$code);
		}

		if (stripos($headers, 'Content-type: application/json') !== false) {
			$body = json_decode($body, 1);
		}

		return $body;
	}




	/**
	 * Envoie une requête au serveur et récupère une réponse binary (fichier en stream)
	 *
	 * Le serveur crypte la réponse que l'on décrypte au passage.
	 * 
	 * @param string $json
	 * @param string $file Chemin du fichier à écrire
	 * @return mixed|false
	 *     false en cas d'erreur.
	 *     mixed (array probablement) qui est la réponse du serveur.
	**/
	private function send_json_request_file($json, $file = null) {

		$destination = migrateur_infos();
		$chemin = $destination->dir . DIRECTORY_SEPARATOR . $file;

		if (!file_exists(dirname($chemin))) {
			mkdir(dirname($chemin), 0777, true);
		}

		if (file_exists($chemin)) {
			@unlink($chemin);
		}

		$options = array(
			"http" => array(
				"method" => "POST",
				"header" => array(
					"Content-Type: application/json"
				),
				"content" => $json
			)
		);

		$this->log("Demande du fichier <em>$file</em>");

		if (!stream_filter_register('crypteur.decrypt', '\SPIP\Migrateur\Crypteur\DecryptFilter')) {
			return "Filtre de decryptage introuvable";
		}

		spip_timer('fichier');
		$context = stream_context_create($options);

		$fp = fopen($this->url_source, 'rb', false, $context);
		stream_filter_append($fp, 'crypteur.decrypt', STREAM_FILTER_READ, array('crypteur' => $this->crypteur));
		file_put_contents($chemin, $fp);

		$t = spip_timer('fichier');
		include_spip('inc/filtres');
		$taille = filesize($chemin);
		$to = taille_en_octets($taille);
		$this->log("Téléchargement de $to en $t");

		return array(
			'message' => array(
				'data' => array(
					'time' => $t,
					'fichier' => $file,
					'chemin'  => $chemin,
					'taille'  => $taille,
					'taille_octets' => taille_en_octets($taille),
					'hash'  => hash_file('sha256', $chemin)
				)
			)
		);
	}



	/**
	 * Sets a logger.
	 * 
	 * @param LoggerInterface $logger
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Log a message
	 * 
	 * @param string message
	 */
	public function log($message)
	{
		if ($this->logger) {
			$this->logger->info($message);
		}
	}
}
