<?php

namespace SPIP\Migrateur;

class Serveur {

	private $auth_key;

	/** @var LoggerInterface */
	protected $logger;

	/** @var Crypter */
	protected $crypteur;


	public function __construct($auth_key, $aes_key) {
		$this->auth_key = $auth_key;

		$this->crypteur = new Crypteur($aes_key);
	}


	public function run() {
		$request = $this->get_json_request();

		// si erreur
		if (is_string($request)) {
			$this->log(">! " . $request);
			$this->transmettre_json(array('error' => $request));
			exit;
		}

		// pas d'erreur… mais pas d'action ?
		if (empty($request['message']['action']) OR !is_string($request['message']['action'])) {
			$this->log(">! Pas d'action.");
			$this->transmettre_json(array('error' => "Pas d'action"));
			exit;
		}

		$action = $request['message']['action'];
		$ActionClass = "SPIP\\Migrateur\\Serveur\\Action\\" . $action;

		if (!class_exists($ActionClass)) {
			$this->log("Action inconnue");
			$this->transmettre_json(array('error' => "Action inconnue"));
			return false;
		}

		$data = empty($request['message']['data']) ? null : $request['message']['data'];

		$act = new $ActionClass();
		$act->setServeur($this);
		$act->setSource(migrateur_infos());
		$act->setLogger($this->logger);

		// Note: certaines actions retournent directement du contenu (ie: GetFile)
		//      et quittent (exit) sans retourner de message
		$message = $act->run($data);

		$message = $this->crypteur->encrypt($action, $message);

		$reponse = array('transmission' => 'ok', 'message' => $message);
		$this->transmettre_json($reponse);
		exit;
	}

	/**
	 * Retourne le json transmis, si les clés d'authentification correspondent
	 *
	 * @return string|array
	 *     Texte si erreur, Tableau sinon.
	**/
	public function get_json_request() {

		//read post data
		$rawRequest = file_get_contents('php://input');

		if (empty($rawRequest)) {
			return 'No input';
		}

		$request = json_decode($rawRequest, true);
		if (!$request) {
			return 'Invalid JSON';
		}

		if (!$this->auth_key) {
			return 'Server Out';
		}

		if (false === $this->verifier_peremption_auth_key($this->auth_key)) {
			return 'Server Out';
		}

		if (empty($request['key']) || $request['key'] != $this->auth_key) {
			return 'Missing or invalid key';
		} elseif (empty($request['message'])) {
			return 'Missing message';
		}

		$request['message'] = $this->crypteur->decrypt($request['message']);

		return $request;
	}


	public static function transmettre_json($data) {
		header('Content-type: application/json');
		echo json_encode($data);
		exit;
	}


	/**
	 * Vérifier la péremption d'une clé d'authentification
	 *
	 * Validité de 12h.
	 * 
	 * @param string $cle
	 *     Clé au format '{date}@{hash}'
	 * @return false|int
	 *     false si la clé est périmée ou non renseignée,
	 *     nombre d'heures de validité restant sinon
	**/
	public static function verifier_peremption_auth_key($key) {
		if (!$key) {
			return false;
		}
		$date = explode('@', trim($key));
		$date = reset($date);
		if (!$date) {
			return false;
		}
		$date = new \DateTime($date);
		$date->modify("+ 12 hour");

		$today = new \DateTime();

		if ($date <= $today) {
			return false;
		}

		return round(($date->format('U') - $today->format('U'))/3600);
	}


	public function getCrypteur() {
		return $this->crypteur;
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
